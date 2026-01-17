# We're Sinapsis CMS

Sistema de gestión de contenidos para el sitio web de We're Sinapsis.

## Requisitos

- Docker y Docker Compose
- Git

## Instalación con Docker

### 1. Clonar el repositorio

```bash
git clone [url-del-repositorio]
cd sinapsis-web
```

### 2. Configurar variables de entorno

```bash
cp .env.example .env
```

### 3. Iniciar los contenedores

```bash
docker-compose up -d
```

Esto iniciará:
- **Web server**: http://localhost:8080
- **phpMyAdmin**: http://localhost:8081
- **MySQL**: localhost:3306

### 4. Instalar dependencias

```bash
docker-compose exec web composer install
```

### 5. Acceder al CMS

- **Frontend**: http://localhost:8080
- **Admin**: http://localhost:8080/admin/login
  - Email: `admin@weresinapsis.com`
  - Password: `admin123`

## Comandos útiles

```bash
# Ver logs
docker-compose logs -f

# Reiniciar servicios
docker-compose restart

# Detener servicios
docker-compose down

# Reconstruir contenedores
docker-compose up -d --build

# Acceder al contenedor web
docker-compose exec web bash

# Ejecutar composer
docker-compose exec web composer install
docker-compose exec web composer dump-autoload

# Ver estado de contenedores
docker-compose ps
```

## Estructura del proyecto

```
sinapsis-web/
├── config/           # Configuración (routes, app settings)
├── database/         # Schema SQL y seeds
├── docker/           # Configuración Docker
├── public/           # Archivos públicos (index.php, assets)
├── src/
│   ├── Controllers/  # Controladores (Admin y Frontend)
│   ├── Core/         # Clases base (Router, Model, etc.)
│   ├── Models/       # Modelos de datos
│   └── Services/     # Servicios (SEO, Media, etc.)
├── templates/        # Plantillas PHP
│   ├── admin/        # Templates del panel admin
│   └── frontend/     # Templates del sitio público
├── docker-compose.yml
└── Dockerfile
```

## Funcionalidades

### Panel de Administración
- Dashboard con estadísticas
- Gestión de páginas con editor de bloques
- Blog con categorías
- Casos de éxito y portfolio
- Equipo
- Biblioteca de medios
- Sistema de traducciones (ES/EN)
- Configuración del sitio

### Frontend Público
- Páginas dinámicas con bloques
- Blog con categorías
- Portfolio de proyectos
- Equipo
- SEO optimizado (meta tags, Schema.org, sitemap)

## Troubleshooting

### Error de permisos en uploads
```bash
docker-compose exec web chown -R www-data:www-data /var/www/html/public/uploads
```

### Regenerar autoload
```bash
docker-compose exec web composer dump-autoload
```

### Limpiar caché de Docker
```bash
docker-compose down -v
docker system prune -f
docker-compose up -d --build
```
