// jest.config.ts
import nextJest from 'next/jest.js';

// Création du Jest config avec next
const createJestConfig = nextJest({
  dir: './',
});

// Fichier de config personnalisé
const customJestConfig = {
  testEnvironment: 'jsdom',
  setupFilesAfterEnv: ['<rootDir>/jest.setup.ts'],
  moduleNameMapper: {
    '^@/(.*)$': '<rootDir>/src/$1',
  },
};

// Export final
export default createJestConfig(customJestConfig);
