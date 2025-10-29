# BoxCenter

Este repositorio contiene la aplicación web **BoxCenter**, desarrollada en Laravel, con Docker para levantar un entorno de desarrollo completo que incluye PHP, MySQL, Redis, Nginx y Vite.

---

## Requisitos

Antes de empezar, asegurate de tener instalados:

- [Docker Desktop](https://www.docker.com/products/docker-desktop)
- [Git](https://git-scm.com/)

---

## Primeros pasos después de clonar el repositorio

1. **Clonar el repositorio**

```bash
git clone https://github.com/<tu-usuario>/<tu-repo>.git
cd BoxCenter
```

2. **Crear el archivo .env**

```bash
cp .env.example .env
```

3. **Levantar los contenedores con Docker**

```bash
docker compose up -d --build
```
4. **Entrar en el contenedor de la app**

```bash
docker exec -it boxcenter_app bash
```
5. **Levantar el servidor de desarrollo de Vite**

```bash
npm run dev
```

4. **Abrir la aplicación en el navegador**

http://localhost:8000
