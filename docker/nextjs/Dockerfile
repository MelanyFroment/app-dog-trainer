FROM node:18-alpine AS base

RUN apk add --no-cache \
    g++ make py3-pip libc6-compat \
    nginx curl git bash mongodb-cli

WORKDIR /app

COPY package*.json ./

EXPOSE 3000 80

FROM base AS builder
COPY . .
RUN npm install && npm run build

FROM base AS production

ENV NODE_ENV=production

RUN addgroup -g 1001 -S nodejs && adduser -S nextjs -u 1001

USER nextjs

# Copier les fichiers nécessaires pour l'exécution de l'application
COPY --from=builder /app/.next /app/.next
COPY --from=builder /app/public /app/public
COPY --from=builder /app/package.json /app/package.json
COPY --from=builder /app/node_modules /app/node_modules

# Copier la configuration Nginx
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

# Commande pour démarrer Nginx
CMD ["nginx", "-g", "daemon off;"]
