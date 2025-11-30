# BoxCenter 🏋️‍♂️

**Sistema de Gestión para Gimnasios Box/CrossFit**

Aplicación web completa desarrollada en Laravel 12 + Livewire 3 para la gestión integral de gimnasios especializados en CrossFit y entrenamiento funcional.

---

## 📋 Características

- ✅ **Gestión de Usuarios**: Admin, Coach, Atleta con roles diferenciados
- ✅ **Sistema de Membresías**: Planes, pagos y control de vencimientos
- ✅ **WODs (Workout of the Day)**: Creación y gestión de entrenamientos
- ✅ **Asistencias**: Control de asistencia a clases
- ✅ **Integración MercadoPago**: Pagos en línea
- ✅ **Autenticación con Laravel Breeze**
- ✅ **Tests Automatizados**: por el momento solo 28 tests (cobertura 5.9%)
- ✅ **CI/CD con GitHub Actions**: Deploy automático a Railway

---

## 🛠️ Stack Tecnológico

- **Backend**: Laravel 12 (PHP 8.2)
- **Frontend**: Livewire 3 + Tailwind CSS + Vite
- **Base de Datos**: MySQL 8 (desarrollo) / PostgreSQL (producción)
- **Testing**: PHPUnit 11.5 + Pest 3.8 + Xdebug
- **Deployment**: Railway
- **CI/CD**: GitHub Actions

---

## 📦 Requisitos

### Opción 1: Desarrollo Local (XAMPP/WAMP)
- PHP >= 8.2
- Composer
- MySQL >= 8.0
- Node.js >= 18.x
- npm o pnpm

### Opción 2: Desarrollo con Docker
- [Docker Desktop](https://www.docker.com/products/docker-desktop)
- [Git](https://git-scm.com/)

---

## 🚀 Instalación y Configuración

### Opción 1: Instalación Local

#### 1. Clonar el repositorio

```bash
git clone https://github.com/FrancoEscobar/BoxCenter.git
cd BoxCenter
```

#### 2. Instalar dependencias de PHP

```bash
composer install
```

#### 3. Instalar dependencias de Node.js

```bash
npm install
```

#### 4. Configurar variables de entorno

```bash
cp .env.example .env
```

Edita el archivo `.env` y configura:

```env
APP_NAME=BoxCenter
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=boxcenter
DB_USERNAME=root
DB_PASSWORD=tu_password
```

#### 5. Generar clave de aplicación

```bash
php artisan key:generate
```

#### 6. Crear base de datos

Crea una base de datos llamada `boxcenter` en MySQL:

```sql
CREATE DATABASE boxcenter CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

#### 7. Ejecutar migraciones

```bash
php artisan migrate
```

#### 8. Ejecutar seeders

```bash
php artisan db:seed
```

#### 9. Compilar assets

```bash
npm run dev
```

#### 10. Iniciar servidor de desarrollo

En otra terminal:

```bash
php artisan serve
```

#### 11. Abrir en el navegador

```
http://localhost:8000
```

---

### Opción 2: Instalación con Docker

#### 1. Clonar el repositorio

```bash
git clone https://github.com/FrancoEscobar/BoxCenter.git
cd BoxCenter
```

#### 2. Crear archivo .env

```bash
cp .env.example .env
```

#### 3. Levantar contenedores

```bash
docker compose up -d --build
```

#### 4. Instalar dependencias dentro del contenedor

```bash
docker exec -it boxcenter_app bash
composer install
npm install
php artisan key:generate
php artisan migrate
exit
```

#### 5. Compilar assets

```bash
docker exec -it boxcenter_app npm run dev
```

#### 6. Acceder a la aplicación

```
http://localhost:8000
```

---

## 🧪 Testing

### Ejecutar todos los tests

```bash
php artisan test
```

### Tests con cobertura de código

```bash
php artisan test --coverage --min=5
```

### Generar reporte HTML de cobertura

```bash
php artisan test --coverage-html coverage-report
```

El reporte estará disponible en `coverage-report/index.html`

### Ejecutar solo tests unitarios

```bash
php artisan test --testsuite=Unit
```

### Ejecutar solo tests de integración

```bash
php artisan test --testsuite=Feature
```

### Tests disponibles

- **28 tests totales** (21 unitarios + 7 integración)
- **48 assertions**
- **Cobertura: 5.9%**

---

## 📁 Estructura del Proyecto

```
BoxCenter/
├── app/
│   ├── Http/
│   │   ├── Controllers/     # Controladores HTTP
│   │   ├── Middleware/      # Middlewares personalizados
│   │   └── Requests/        # Form Requests
│   ├── Livewire/           # Componentes Livewire
│   │   ├── Coach/
│   │   ├── Wod/
│   │   └── PlanSelection.php
│   ├── Models/             # Modelos Eloquent
│   ├── Services/           # Lógica de negocio
│   │   ├── MembershipService.php
│   │   └── PaymentGateway/
│   └── Providers/
├── database/
│   ├── factories/          # Factories para testing
│   ├── migrations/         # Migraciones de BD
│   └── seeders/           # Seeders
├── resources/
│   ├── css/               # Estilos Tailwind
│   ├── js/                # JavaScript/Alpine.js
│   └── views/             # Vistas Blade
├── routes/
│   ├── web.php            # Rutas públicas
│   ├── auth.php           # Rutas de autenticación
│   ├── admin.php          # Rutas de admin
│   ├── coach.php          # Rutas de coach
│   └── athlete.php        # Rutas de atleta
├── tests/
│   ├── Unit/              # Tests unitarios
│   └── Feature/           # Tests de integración
├── .github/
│   └── workflows/         # GitHub Actions CI/CD
│       ├── tests.yml
│       └── deploy.yml
├── phpunit.xml            # Configuración PHPUnit
├── composer.json          # Dependencias PHP
├── package.json           # Dependencias Node.js
└── .env.example           # Plantilla de variables de entorno
```

---

## 🔐 Roles y Permisos

### Roles Disponibles

1. **Admin** (`/admin/dashboard`)
   - Gestión completa del sistema
   - CRUD de usuarios, planes, clases
   - Reportes y estadísticas

2. **Coach** (`/coach/dashboard`)
   - Crear y gestionar WODs
   - Registrar asistencias
   - Ver información de atletas

3. **Atleta** (`/atleta/dashboard`)
   - Ver WODs del día
   - Registrar resultados
   - Gestionar membresía y pagos
   - Sin membresía activa → redirige a `/planes/seleccionar`

---

## 🚀 Deploy en Producción

### Railway (Configurado)

El proyecto está configurado para deployment automático en Railway:

1. **Push a `main`** → Ejecuta tests en GitHub Actions
2. **Si tests pasan** → Railway despliega automáticamente
3. **URL de producción**: `https://boxcenter-production.railway.app`

### Variables de Entorno en Railway

Configurar en Railway Dashboard:

```env
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:... (generar con php artisan key:generate)
DATABASE_URL=postgresql://... (provisto por Railway)
MERCADOPAGO_ACCESS_TOKEN=...
MERCADOPAGO_PUBLIC_KEY=...
```
