import Link from 'next/link'

export default function Page() {
    return (
        <nav className="flex flex-wrap items-center gap-6 p-6">
            <h1>Home</h1>
            <Link href="/login">Se connecter</Link>
            <Link href="/about">About</Link>
        </nav>
    )
}
