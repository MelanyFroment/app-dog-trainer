'use client'

import Image from 'next/image'
import Link from 'next/link'

import logo from '../../images/dog.svg'
import Login from '@/components/login'

export default function LoginPage() {
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
          <Login />
        </div>
        <p className="text-muted-foreground mt-[20px] text-sm">
          Vous n&apos;avez pas de compte ?{' '}
          <Link className="text-blue-500" href="/registration">
            En créer un !
          </Link>{' '}
        </p>
      </div>
    </div>
  )
}
