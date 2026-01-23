FROM node:20

WORKDIR /app

# Instalar dependencias
COPY gef/package*.json ./
RUN npm install

# Copiar código
COPY gef/ .

# Exponer Vite
EXPOSE 5173

# Arrancar Vite
CMD ["npm", "run", "dev", "--", "--host"]
