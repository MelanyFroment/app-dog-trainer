import React from "react";
import { render, screen, fireEvent, waitFor } from "@testing-library/react";
import '@testing-library/jest-dom';
import LoginForm from "@/components/login";

// ✅ Mock useRouter de Next.js
jest.mock('next/navigation', () => ({
    useRouter: () => ({
        push: jest.fn(),
    }),
}));

describe("LoginForm", () => {
    beforeEach(() => {
        jest.clearAllMocks();
    });

    it("affiche le formulaire de connexion", () => {
        render(<LoginForm />);
        expect(screen.getByLabelText(/email/i)).toBeInTheDocument();
        expect(screen.getByLabelText(/mot de passe/i)).toBeInTheDocument();
    });

    it("envoie les données de connexion", async () => {
        // ✅ Mock fetch
        global.fetch = jest.fn().mockResolvedValueOnce({
            ok: true,
            json: async () => ({ token: "fake-token" }),
        } as Response);

        render(<LoginForm />);

        fireEvent.change(screen.getByLabelText(/email/i), {
            target: { value: "test@example.com" },
        });
        fireEvent.change(screen.getByLabelText(/mot de passe/i), {
            target: { value: "password" },
        });

        fireEvent.click(screen.getByRole("button", { name: /se connecter/i }));

        await waitFor(() => {
            expect(global.fetch).toHaveBeenCalledWith(
                expect.any(String),
                expect.objectContaining({
                    method: "POST",
                    headers: expect.any(Object),
                })
            );
        });
    });
});
