# Étape de build
FROM node:20-alpine as build-stage

WORKDIR /app

# Copier les fichiers de dépendances
COPY package*.json ./

# Installer les dépendances
RUN npm install

# Copier le reste des fichiers
COPY . .

# Construire l'application pour la production
RUN npm run build

# Étape de production
FROM nginx:stable-alpine as production-stage

# Copier les fichiers buildés depuis l'étape de build vers le dossier par défaut de nginx
COPY --from=build-stage /app/dist /usr/share/nginx/html

# Copier la configuration nginx personnalisée si nécessaire
# COPY nginx.conf /etc/nginx/conf.d/default.conf

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
