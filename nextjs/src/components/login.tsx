'use client'

import {
    Form,
    FormControl,
    FormDescription,
    FormField,
    FormItem,
    FormLabel,
    FormMessage,
} from '@/components/ui/form'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'
import { z } from 'zod'
import { useForm } from 'react-hook-form'
import { zodResolver } from '@hookform/resolvers/zod'
import { useRouter } from 'next/navigation'
import { useState } from 'react'

const formSchema = z.object({
    email: z.string().email('Adresse email invalide.'),
    password: z
        .string()
        .min(5, 'Le mot de passe doit contenir au moins 5 caractères.')
        .max(16, 'Le mot de passe doit faire moins de 16 caractères.'),
})

const Login = () => {
    const router = useRouter()
    const [isSubmitting, setIsSubmitting] = useState(false)
    const [generalError, setGeneralError] = useState('')

    const form = useForm<z.infer<typeof formSchema>>({
        resolver: zodResolver(formSchema),
        defaultValues: {
            email: '',
            password: '',
        },
    })

    async function onSubmit(values: z.infer<typeof formSchema>) {
        setIsSubmitting(true)
        setGeneralError('')

        try {
            const response = await fetch('http://localhost:8080/api/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(values),
            })

            if (!response.ok) {
                const errorBody = await response.text()
                console.error('HTTP Error:', response.status, errorBody)
                throw new Error('Erreur de connexion')
            }

            const data = await response.json()
            if (data.token) {
                localStorage.setItem('token', data.token)
                router.push('/')
            } else {
                throw new Error('Token manquant dans la réponse')
            }
        } catch (error) {
            console.error(error)
            setGeneralError("Email ou mot de passe incorrect.")
            form.setError('email', { message: '' })
        } finally {
            setIsSubmitting(false)
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
                    name="password"
                    render={({ field }) => (
                        <FormItem>
                            <FormLabel>Mot de passe</FormLabel>
                            <FormControl>
                                <Input type="password" placeholder="**********" {...field} />
                            </FormControl>
                            <FormDescription>Entrez votre mot de passe</FormDescription>
                            <FormMessage />
                        </FormItem>
                    )}
                />

                {generalError && (
                    <p className="text-red-600 text-sm mt-[-10px]">{generalError}</p>
                )}

                <Button type="submit" disabled={isSubmitting}>
                    {isSubmitting ? 'Connexion...' : 'Se connecter'}
                </Button>
            </form>
        </Form>
    )
}

export default Login
