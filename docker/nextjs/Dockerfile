FROM node:22.14-alpine AS base

# Install dependencies only when needed
FROM base AS deps

RUN apk add --no-cache libc6-compat
WORKDIR /www/nextjs

# Install dependencies
COPY nextjs/package.json nextjs/package-lock.json ./
RUN npm ci

# Rebuild the source code only when needed
FROM base AS builder
WORKDIR /www/nextjs
COPY --from=deps /www/nextjs/node_modules /www/nextjs/node_modules
COPY nextjs ./

RUN npm run build

# Production image, copy all the files and run next
FROM base AS runner
WORKDIR /www/nextjs

ENV NODE_ENV=production

RUN addgroup --system --gid 1001 nodejs
RUN adduser --system --uid 1001 nextjs

COPY --from=builder /www/nextjs/public /www/nextjs/public

COPY --from=builder --chown=nextjs:nodejs /www/nextjs/.next/standalone /www/nextjs/
COPY --from=builder --chown=nextjs:nodejs /www/nextjs/.next/static /www/nextjs/.next/static

USER nextjs

EXPOSE 3000

ENV PORT=3000

ENV HOSTNAME="0.0.0.0"
CMD ["node", "server.js"]

FROM base AS dev
WORKDIR /www/nextjs

CMD ["npm", "run", "dev"]