# 📝 Tasks App Vue – README

Proyecto full-stack desarrollado con **Laravel (API REST)** y **Vue.js 3**, con gestión de usuarios, roles y un sistema completo de tareas.

---

## 🚀 Requisitos

Asegurate de tener instalado:

- PHP 8.1+
- Composer
- MySQL o MariaDB
- Node.js 18+
- NPM
- Laravel 10+
- Git (opcional)

---

## 📥 Instalación del Backend (Laravel)

### 1️⃣ Clonar el repositorio en la carpeta de tu cliente php
```bash
git clone https://github.com/CristianLeyton/app-tasks-vue.git
cd app-tasks-vue
```

### 2️⃣ Instalar dependencias
```bash
composer install
```

### 3️⃣ Copiar archivo de entorno
```bash
cp .env.example .env
```

### 4️⃣ Configurar base de datos  
Editar el archivo **.env**:
Primero debes crear una base de datos con el mismo nombre que vayas a poner aqui en tu gestor de bases de datos
```
DB_DATABASE="app-tasks-vue"
DB_USERNAME=root
DB_PASSWORD=
```

### 5️⃣ Generar key de la app  
```bash
php artisan key:generate
```

### 6️⃣ Migrar tablas y ejecutar seeders  
```bash
php artisan migrate --seed
```

Esto creará los roles y tres usuarios de prueba.

### 7️⃣ Iniciar el servidor  
```bash
php artisan serve
```

API disponible en  
👉 http://127.0.0.1:8000

---

## 💻 Instalación del Frontend (Vue 3)

### 1️⃣ Entrar a la carpeta con cualquier terminal:
```bash
cd app-tasks-vue
```

### 2️⃣ Instalar dependencias:
```bash
npm install
```

### 3️⃣ Iniciar servidor de desarrollo:
```bash
npm run dev
```

Aplicacion disponible en  
👉 http://127.0.0.1:8000

---

## 🔐 Usuarios de prueba

| Rol    | Email            | Password |
|--------|------------------|----------|
| Admin  | admin@mail.com   | admin    |
| Editor | editor@mail.com  | editor   |
| Viewer | viewer@mail.com  | viewer   |

Cada uno tiene permisos diferentes según las reglas implementadas.

- `admin` → puede ver, crear, editar y eliminar todas las tareas.
- `editor` → puede crear, editar y eliminar **solo sus tareas**.
- `viewer` → puede **solo ver** sus tareas.
---

## 🧩 Características del Proyecto

- Login con tokens **Laravel Sanctum**
- Roles con **spatie/laravel-permission**
- CRUD de tareas según permisos
- Filtros por estado: *pending, in_progress, completed*
- Panel de usuarios (solo admin)
- Frontend en Vue 3 con Composition API
- Diseño responsive
- Rutas API protegidas con middleware
