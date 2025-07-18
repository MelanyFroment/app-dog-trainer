import "@testing-library/jest-dom";
import { render, screen } from "@testing-library/react";
import Page from "../page";

// 🚨 Ajout du mock pour corriger l'erreur
jest.mock("next/navigation", () => ({
  useRouter: () => ({
    push: jest.fn(),
    replace: jest.fn(),
    refresh: jest.fn(),
    prefetch: jest.fn(),
    back: jest.fn(),
  }),
  usePathname: () => "/",
  useSearchParams: () => new URLSearchParams(),
}));

describe("Page", () => {
  it("renders a heading", () => {
    render(<Page />);
    const heading = screen.getByRole("heading", { level: 1 });
    expect(heading).toBeInTheDocument();
  });
});
