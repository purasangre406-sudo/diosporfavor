# Usa un servidor web ultra rápido y ligero
FROM nginx:alpine

# Copia TODOS los archivos de tu carpeta actual dentro del servidor
COPY . /usr/share/nginx/html/

# Expone el puerto estándar de internet
EXPOSE 80

# Enciende el servidor automáticamente
CMD ["nginx", "-g", "daemon off;"]
