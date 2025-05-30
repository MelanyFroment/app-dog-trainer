import Link from 'next/link'

export default function Page() {
  return (
    <div>
      <h1>Home</h1>
      <Link href="/login">Se connecter</Link>
      <Link href="/about">About</Link>
    </div>
  )
}
