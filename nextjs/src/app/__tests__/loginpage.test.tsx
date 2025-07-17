import React from "react";
import { render, screen } from "@testing-library/react";
import LoginPage from "../login/page";

import type { Config } from "jest";

const config: Config = {
  preset: "ts-jest",
  testEnvironment: "jsdom",
  moduleNameMapper: {
    "^@/(.*)$": "<rootDir>/src/$1",
  },
};

// Mock du composant Login
jest.mock("@/components/login", () => () => (
  <div data-testid="login-component">Mocked Login</div>
));

describe("LoginPage", () => {
  it("renders title, heading, login form and registration link", () => {
    render(<LoginPage />);

    // Vérifie le titre principal
    expect(screen.getByText("Cani'Planner")).toBeInTheDocument();

    // Vérifie le sous-titre
    expect(screen.getByText("Se connecter")).toBeInTheDocument();

    // Vérifie que le formulaire Login est mocké
    expect(screen.getByTestId("login-component")).toBeInTheDocument();

    // Vérifie le lien d'inscription
    const link = screen.getByRole("link", { name: /en créer un/i });
    expect(link).toHaveAttribute("href", "/registration");
  });
});
