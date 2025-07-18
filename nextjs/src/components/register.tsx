"use client";

import {
    Form,
    FormControl,
    FormDescription,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from "@/components/ui/form";
import { Input } from "@/components/ui/input";
import { Button } from "@/components/ui/button";
import { z } from "zod";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { useRouter } from "next/navigation";
import { useState } from "react";

const formSchema = z
    .object({
        email: z.string().email("Adresse email invalide"),
        phone: z
            .string()
            .min(10, "Le numéro doit contenir au moins 10 chiffres")
            .regex(/^\d+$/, "Le numéro doit contenir uniquement des chiffres"),
        password: z
            .string()
            .min(5, "Le mot de passe doit contenir au moins 5 caractères")
            .max(16, "Le mot de passe doit faire moins de 16 caractères"),
        confirmPassword: z.string(),
    })
    .refine((data) => data.password === data.confirmPassword, {
        message: "Les mots de passe ne correspondent pas",
        path: ["confirmPassword"],
    });

const Register = () => {
    const router = useRouter();
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [generalError, setGeneralError] = useState("");

    const form = useForm<z.infer<typeof formSchema>>({
        resolver: zodResolver(formSchema),
        defaultValues: {
            email: "",
            phone: "",
            password: "",
            confirmPassword: "",
        },
    });

    async function onSubmit(values: z.infer<typeof formSchema>) {
        setIsSubmitting(true);
        setGeneralError("");

        try {
            const response = await fetch("http://localhost:8080/api/register", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    email: values.email,
                    phone: values.phone,
                    password: values.password,
                }),
            });

            const data = await response.json(); // lire une seule fois

            if (!response.ok) {
                console.error("HTTP Error:", response.status, data);
                throw new Error(data.message || "Erreur lors de l’inscription");
            }

            console.log("Inscription réussie :", data);
            router.push("/login");
        } catch (error: any) {
            console.error("Erreur dans la requête :", error);
            setGeneralError(error.message || "Une erreur est survenue.");
        } finally {
            setIsSubmitting(false);
        }
    }

    return (
        <Form {...form}>
            <form
                onSubmit={form.handleSubmit(onSubmit)}
                className="grid gap-y-[20px]"
            >
                <FormField
                    control={form.control}
                    name="email"
                    render={({ field }) => (
                        <FormItem>
                            <FormLabel>Email</FormLabel>
                            <FormControl>
                                <Input placeholder="jean.dupont@entreprise.com" {...field} />
                            </FormControl>
                            <FormDescription>Entrez votre adresse email</FormDescription>
                            <FormMessage />
                        </FormItem>
                    )}
                />
                <FormField
                    control={form.control}
                    name="phone"
                    render={({ field }) => (
                        <FormItem>
                            <FormLabel>Téléphone</FormLabel>
                            <FormControl>
                                <Input placeholder="0612345678" {...field} />
                            </FormControl>
                            <FormDescription>Votre numéro de téléphone</FormDescription>
                            <FormMessage />
                        </FormItem>
                    )}
                />
                <FormField
                    control={form.control}
                    name="password"
                    render={({ field }) => (
                        <FormItem>
                            <FormLabel>Mot de passe</FormLabel>
                            <FormControl>
                                <Input type="password" placeholder="********" {...field} />
                            </FormControl>
                            <FormDescription>
                                Choisissez un mot de passe sécurisé
                            </FormDescription>
                            <FormMessage />
                        </FormItem>
                    )}
                />
                <FormField
                    control={form.control}
                    name="confirmPassword"
                    render={({ field }) => (
                        <FormItem>
                            <FormLabel>Confirmer le mot de passe</FormLabel>
                            <FormControl>
                                <Input type="password" placeholder="********" {...field} />
                            </FormControl>
                            <FormMessage />
                        </FormItem>
                    )}
                />

                {generalError && (
                    <p className="text-red-600 text-sm mt-[-10px]">{generalError}</p>
                )}

                <Button type="submit" disabled={isSubmitting}>
                    {isSubmitting ? "Inscription..." : "S'inscrire"}
                </Button>
            </form>
        </Form>
    );
};

export default Register;
