# TLOS - The Last of SaaS

Plataforma integral para la gestión de eventos B2B SaaS.

## Características Principales

- **Gestión de Eventos** - Crear y administrar eventos con aforo, fechas y características
- **Sistema de Entradas** - Entradas gratuitas (sponsors) y de pago (Stripe)
- **Matching Bidireccional** - Conexión entre sponsors (empresas SaaS) y participantes
- **Planificación de Reuniones** - Asignación de reuniones 1-to-1 con slots horarios
- **Votaciones/Awards** - Sistema de votaciones durante eventos
- **Live Matching (PWA)** - Matching en tiempo real vía QR durante el evento

## Requisitos

- Docker y Docker Compose
- Git

## Instalación con Docker

### 1. Clonar el repositorio

```bash
git clone [url-del-repositorio]
cd thelastofsaas
```

### 2. Configurar variables de entorno

```bash
cp .env.example .env
# Editar .env con tus configuraciones (Stripe keys, email, etc.)
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
  - Email: `admin@thelastofsaas.es`
  - Password: `admin123`

## Comandos útiles

```bash
# Ver logs
docker-compose logs -f

# Reiniciar servicios
docker-compose restart

# Detener servicios
docker-compose down

# Reconstruir contenedores (después de cambios en schema)
docker-compose down -v
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
thelastofsaas/
├── config/           # Configuración (routes, app settings)
├── database/         # Schema SQL base y seeds
├── migrations/       # Migraciones SQL (incluye TLOS schema)
├── docker/           # Configuración Docker
├── public/           # Archivos públicos (index.php, assets)
├── src/
│   ├── Controllers/  # Controladores (Admin, Frontend, API)
│   ├── Core/         # Clases base (Router, Model, etc.)
│   ├── Models/       # Modelos de datos
│   └── Services/     # Servicios (Email, Stripe, QR, etc.)
├── templates/        # Plantillas PHP
│   ├── admin/        # Templates del panel admin
│   └── frontend/     # Templates del sitio público
├── storage/          # Archivos generados (logs, cache)
├── docker-compose.yml
└── Dockerfile
```

## Módulos TLOS

### Eventos
- CRUD completo de eventos
- Estados: draft → published → active → finished
- Gestión de características del evento
- Asociación de sponsors por nivel (Platinum/Gold/Silver/Bronze)

### Sponsors & Empresas
- Gestión de sponsors (empresas SaaS)
- Gestión de empresas participantes
- Códigos únicos para acceso a paneles
- Importación masiva CSV

### Sistema de Matching
- Selecciones bidireccionales
- Detección automática de matches mutuos
- Notificaciones por email
- Mensajería sponsor → empresa

### Entradas
- Tipos de entrada configurables
- Entradas gratuitas de sponsors
- Integración Stripe para pagos
- Generación de QR codes
- Check-in en evento

### Reuniones
- Bloques horarios configurables
- Generación automática de slots
- Lógica de simultaneidad (por sponsor y empresa)
- Asignación manual y automática
- Exportación de agenda

### Votaciones
- Votaciones con candidatos
- Control anti-fraude (cookies, fingerprint, IP)
- Embebible en cualquier página

## API Endpoints

Ver documentación completa en `TLOS_ESPECIFICACIONES_TECNICAS.md`

## Troubleshooting

### Error de permisos en uploads
```bash
docker-compose exec web chown -R www-data:www-data /var/www/html/public/uploads
```

### Regenerar autoload
```bash
docker-compose exec web composer dump-autoload
```

### Limpiar caché y reiniciar BD
```bash
docker-compose down -v
docker system prune -f
docker-compose up -d --build
```

---

**TLOS - The Last of SaaS** © 2025
