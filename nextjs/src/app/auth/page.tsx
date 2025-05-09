'use client'

import { zodResolver } from '@hookform/resolvers/zod'
import { useForm } from 'react-hook-form'
import { z } from 'zod'
import Image from 'next/image'
import logo from '../../images/dog.svg'

import { Button } from '@/components/ui/button'
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
import Link from 'next/link'

const formSchema = z.object({
  email: z.string(),
  password: z.string(),
})

export default function Page() {
  const form = useForm<z.infer<typeof formSchema>>({
    resolver: zodResolver(formSchema),
    defaultValues: {
      email: '',
      password: '',
    },
  })

  function onSubmit(values: z.infer<typeof formSchema>) {
    console.log(values)
  }

  return (
    <div className="flex h-screen flex-col md:flex-row">
      <div className="dark:bg-primary/50 flex h-full w-full flex-col items-center justify-center bg-neutral-300 md:w-1/2">
        <Image
          className="mb-[30px]"
          src={logo}
          width={100}
          height={100}
          alt="Dog logo"
        />
        <h1 className="text-5xl font-extrabold">Cani&apos;Planner</h1>
      </div>
      <div className="flex h-full w-full flex-col items-center justify-center p-[20px] md:w-1/2">
        <h1 className="mb-[30px] text-4xl">Se connecter</h1>
        <div className="w-full max-w-[353px]">
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
                      <Input
                        placeholder="jean.dupont@entreprise.com"
                        {...field}
                      />
                    </FormControl>
                    <FormDescription>
                      Entrez votre adresse email
                    </FormDescription>
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
                      <Input placeholder="**********" {...field} />
                    </FormControl>
                    <FormDescription>Entrez votre mot de passe</FormDescription>
                    <FormMessage />
                  </FormItem>
                )}
              />
              <Button type="submit">Continuer</Button>
            </form>
          </Form>
        </div>
        <p className="text-muted-foreground mt-[20px] text-sm">
          Vous n&apos;avez pas de compte ?{' '}
          <Link className="text-blue-500" href="./">
            En créer un !
          </Link>{' '}
        </p>
      </div>
    </div>
  )
}
