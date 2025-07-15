"use client";

import Link from "next/link";
import { useRouter } from "next/navigation";

export default function Page() {
  const router = useRouter();

  const handleLogout = () => {
    localStorage.removeItem("token");
    router.push("/login");
  };

  return (
    <div className="p-4">
      <h1 className="text-2xl font-bold mb-4">Home</h1>
      <div className="flex gap-4">
        <Link className="text-blue-500 hover:underline" href="/login">
          Se connecter
        </Link>
        <Link className="text-blue-500 hover:underline" href="/about">
          About
        </Link>
        <button onClick={handleLogout} className="text-red-500 hover:underline">
          Se déconnecter
        </button>
      </div>
    </div>
  );
}
