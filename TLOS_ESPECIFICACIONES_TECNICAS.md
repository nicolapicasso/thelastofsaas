# TLOS - Especificaciones Técnicas del Sistema
## The Last of SaaS - Plataforma de Gestión de Eventos B2B

**Versión:** 2.0  
**Fecha:** Enero 2025  
**Propósito:** Documento de referencia para desarrollo con Claude Code

---

## 1. VISIÓN GENERAL

### 1.1 Descripción del Sistema

TLOS es una plataforma integral para la gestión de eventos B2B SaaS que facilita:
- Networking entre sponsors (empresas SaaS) y participantes (empresas cliente potenciales)
- Sistema de matching bidireccional con selección mutua
- Planificación y asignación de reuniones 1-to-1
- Venta y gestión de entradas (gratuitas y de pago)
- Votaciones y awards durante eventos
- Matching en tiempo real durante el evento vía QR

### 1.2 Arquitectura

- **Backend:** CMS a medida (consolidado, no multi-plugin)
- **Pagos:** Integración con Stripe
- **Frontend:** Sistema de bloques para landing pages personalizadas
- **Mobile:** PWA para matching en tiempo real

---

## 2. MODELO DE DATOS

### 2.1 Diagrama de Entidades Principal

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                              EVENTOS                                         │
├─────────────────────────────────────────────────────────────────────────────┤
│  events                                                                      │
│  ├── event_sponsors (sponsors asociados al evento con nivel)                │
│  ├── event_features (características/items del evento)                      │
│  ├── ticket_types (tipos de entrada con precios)                            │
│  └── tickets (entradas vendidas/registradas)                                │
└─────────────────────────────────────────────────────────────────────────────┘
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│                         MATCHING Y REUNIONES                                 │
├─────────────────────────────────────────────────────────────────────────────┤
│  sponsors ◄──────────────────────────────────────────────► companies        │
│      │              sponsor_selections                           │          │
│      │              company_selections                           │          │
│      │                      │                                    │          │
│      │                      ▼                                    │          │
│      │              MATCHES MUTUOS                                │          │
│      │                      │                                    │          │
│      └──────────────────────┼────────────────────────────────────┘          │
│                             ▼                                               │
│                    meeting_assignments                                      │
│                             │                                               │
│                             ▼                                               │
│              meeting_slots ◄── meeting_blocks                               │
└─────────────────────────────────────────────────────────────────────────────┘
```

### 2.2 Entidades del Sistema

#### 2.2.1 EVENTOS

```sql
-- Tabla principal de eventos
CREATE TABLE events (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    venue_name VARCHAR(255),
    venue_address TEXT,
    event_date DATE,
    total_capacity INT NOT NULL,
    status ENUM('draft', 'published', 'active', 'finished', 'cancelled') DEFAULT 'draft',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Sponsors asociados a un evento con su nivel
CREATE TABLE event_sponsors (
    id INT PRIMARY KEY AUTO_INCREMENT,
    event_id INT NOT NULL,
    sponsor_id INT NOT NULL,
    priority_level ENUM('platinum', 'gold', 'silver', 'bronze') NOT NULL,
    display_order INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (sponsor_id) REFERENCES sponsors(id) ON DELETE CASCADE,
    UNIQUE KEY unique_event_sponsor (event_id, sponsor_id)
);

-- Características/Items informativos del evento
CREATE TABLE event_features (
    id INT PRIMARY KEY AUTO_INCREMENT,
    event_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    display_order INT DEFAULT 0,
    icon VARCHAR(100),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
);
```

#### 2.2.2 ENTRADAS (TICKETS)

```sql
-- Tipos de entrada disponibles
CREATE TABLE ticket_types (
    id INT PRIMARY KEY AUTO_INCREMENT,
    event_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    is_free BOOLEAN DEFAULT FALSE,
    sponsor_id INT NULL,                    -- NULL = entrada de organización
    quantity_available INT NULL,            -- NULL = ilimitado
    quantity_sold INT DEFAULT 0,
    sale_start_date DATETIME,
    sale_end_date DATETIME,
    status ENUM('active', 'inactive', 'sold_out') DEFAULT 'active',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (sponsor_id) REFERENCES sponsors(id) ON DELETE SET NULL
);

-- Entradas vendidas/registradas
CREATE TABLE tickets (
    id INT PRIMARY KEY AUTO_INCREMENT,
    ticket_type_id INT NOT NULL,
    event_id INT NOT NULL,
    
    -- Datos del participante
    attendee_first_name VARCHAR(100) NOT NULL,
    attendee_last_name VARCHAR(100) NOT NULL,
    attendee_email VARCHAR(255) NOT NULL,
    attendee_phone VARCHAR(50),
    attendee_job_title VARCHAR(100),
    
    -- Datos de la empresa del participante
    attendee_company_name VARCHAR(255),
    attendee_company_website VARCHAR(500),
    attendee_company_size ENUM('1-10', '11-50', '51-200', '201-500', '500+'),
    
    -- Quién invita/suscribe la entrada
    invited_by_type ENUM('organization', 'sponsor') NOT NULL,
    invited_by_sponsor_id INT NULL,         -- Si es invitado por sponsor
    
    -- Estado y pago
    status ENUM('pending', 'confirmed', 'cancelled', 'checked_in') DEFAULT 'pending',
    payment_status ENUM('pending', 'paid', 'refunded', 'free') DEFAULT 'pending',
    stripe_payment_intent_id VARCHAR(255),
    stripe_charge_id VARCHAR(255),
    amount_paid DECIMAL(10,2) DEFAULT 0.00,
    
    -- Códigos
    ticket_code VARCHAR(50) NOT NULL UNIQUE,    -- Código único para QR
    confirmation_code VARCHAR(20) NOT NULL,
    
    -- Metadata
    registration_ip VARCHAR(45),
    user_agent TEXT,
    notes TEXT,
    
    -- Fechas
    event_date DATE NOT NULL,               -- Día para el que es válida la entrada
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    confirmed_at DATETIME,
    checked_in_at DATETIME,
    
    FOREIGN KEY (ticket_type_id) REFERENCES ticket_types(id),
    FOREIGN KEY (event_id) REFERENCES events(id),
    FOREIGN KEY (invited_by_sponsor_id) REFERENCES sponsors(id) ON DELETE SET NULL,
    INDEX idx_ticket_code (ticket_code),
    INDEX idx_attendee_email (attendee_email),
    INDEX idx_event_date (event_id, event_date)
);
```

#### 2.2.3 SPONSORS

```sql
CREATE TABLE sponsors (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    category VARCHAR(255),
    description TEXT,
    website VARCHAR(500),
    logo_url VARCHAR(500),
    contact_emails TEXT,                    -- Emails separados por coma
    unique_code VARCHAR(100) NOT NULL UNIQUE,
    active BOOLEAN DEFAULT TRUE,
    
    -- Configuración de reuniones
    max_simultaneous_meetings INT DEFAULT 1,
    can_send_messages BOOLEAN DEFAULT FALSE,
    
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_unique_code (unique_code),
    INDEX idx_active (active)
);
```

#### 2.2.4 EMPRESAS (COMPANIES)

```sql
CREATE TABLE companies (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    website VARCHAR(500),
    logo_url VARCHAR(500),
    contact_emails TEXT,                    -- Emails separados por coma
    notes TEXT,
    unique_code VARCHAR(100) NOT NULL UNIQUE,
    active BOOLEAN DEFAULT TRUE,
    
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_unique_code (unique_code),
    INDEX idx_active (active)
);

-- SaaS que utiliza cada empresa (para mostrar en matching)
CREATE TABLE company_saas_usage (
    id INT PRIMARY KEY AUTO_INCREMENT,
    company_id INT NOT NULL,
    sponsor_id INT NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (sponsor_id) REFERENCES sponsors(id) ON DELETE CASCADE,
    UNIQUE KEY unique_usage (company_id, sponsor_id)
);
```

#### 2.2.5 SISTEMA DE MATCHING

```sql
-- Selecciones de sponsors hacia empresas
CREATE TABLE sponsor_selections (
    id INT PRIMARY KEY AUTO_INCREMENT,
    sponsor_id INT NOT NULL,
    company_id INT NOT NULL,
    selected_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sponsor_id) REFERENCES sponsors(id) ON DELETE CASCADE,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    UNIQUE KEY unique_selection (sponsor_id, company_id),
    INDEX idx_sponsor (sponsor_id),
    INDEX idx_company (company_id)
);

-- Selecciones de empresas hacia sponsors
CREATE TABLE company_selections (
    id INT PRIMARY KEY AUTO_INCREMENT,
    company_id INT NOT NULL,
    sponsor_id INT NOT NULL,
    selected_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    FOREIGN KEY (sponsor_id) REFERENCES sponsors(id) ON DELETE CASCADE,
    UNIQUE KEY unique_selection (company_id, sponsor_id),
    INDEX idx_company (company_id),
    INDEX idx_sponsor (sponsor_id)
);

-- Registro de notificaciones enviadas (anti-duplicados)
CREATE TABLE email_notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    notification_type VARCHAR(50) NOT NULL,
    sender_type VARCHAR(20) NOT NULL,
    sender_id INT NOT NULL,
    recipient_type VARCHAR(20) NOT NULL,
    recipient_id INT NOT NULL,
    sent_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_notification (notification_type, sender_id, recipient_id),
    INDEX idx_sender (sender_id),
    INDEX idx_recipient (recipient_id)
);

-- Mensajes de sponsors a empresas
CREATE TABLE sponsor_messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    sponsor_id INT NOT NULL,
    company_id INT NOT NULL,
    message TEXT NOT NULL,
    sent_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sponsor_id) REFERENCES sponsors(id) ON DELETE CASCADE,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    UNIQUE KEY unique_message (sponsor_id, company_id)
);
```

#### 2.2.6 SISTEMA DE REUNIONES

```sql
-- Bloques horarios
CREATE TABLE meeting_blocks (
    id INT PRIMARY KEY AUTO_INCREMENT,
    event_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    event_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    meeting_duration INT NOT NULL,          -- Duración en minutos
    simultaneous_meetings INT NOT NULL,     -- Número de mesas disponibles
    description TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    INDEX idx_event_date (event_id, event_date)
);

-- Slots individuales de reunión
CREATE TABLE meeting_slots (
    id INT PRIMARY KEY AUTO_INCREMENT,
    block_id INT NOT NULL,
    slot_time TIME NOT NULL,
    room_number INT NOT NULL,               -- Número de mesa
    is_available BOOLEAN DEFAULT TRUE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (block_id) REFERENCES meeting_blocks(id) ON DELETE CASCADE,
    UNIQUE KEY unique_slot (block_id, slot_time, room_number),
    INDEX idx_block (block_id),
    INDEX idx_time (slot_time)
);

-- Asignaciones de reuniones
CREATE TABLE meeting_assignments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    slot_id INT NOT NULL,
    sponsor_id INT NOT NULL,
    company_id INT NOT NULL,
    status ENUM('confirmed', 'pending', 'cancelled', 'completed') DEFAULT 'confirmed',
    notes TEXT,
    assigned_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    assigned_by ENUM('admin', 'live_matching', 'auto') DEFAULT 'admin',
    FOREIGN KEY (slot_id) REFERENCES meeting_slots(id) ON DELETE CASCADE,
    FOREIGN KEY (sponsor_id) REFERENCES sponsors(id) ON DELETE CASCADE,
    FOREIGN KEY (company_id) REFERENCES companies(id) ON DELETE CASCADE,
    UNIQUE KEY unique_assignment (slot_id),
    INDEX idx_sponsor (sponsor_id),
    INDEX idx_company (company_id)
);
```

#### 2.2.7 SISTEMA DE VOTACIONES

```sql
-- Votaciones
CREATE TABLE votings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    event_id INT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    status ENUM('active', 'inactive', 'finished') DEFAULT 'active',
    show_vote_counts BOOLEAN DEFAULT TRUE,
    voting_start DATETIME,
    voting_end DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE SET NULL
);

-- Candidatos
CREATE TABLE voting_candidates (
    id INT PRIMARY KEY AUTO_INCREMENT,
    voting_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    logo_url VARCHAR(500),
    website_url VARCHAR(500),
    votes INT DEFAULT 0,
    base_votes INT DEFAULT 0,               -- Votos base para ajustes
    display_order INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (voting_id) REFERENCES votings(id) ON DELETE CASCADE,
    INDEX idx_voting (voting_id)
);

-- Registro de votos (para control anti-duplicados)
CREATE TABLE votes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    voting_id INT NOT NULL,
    candidate_id INT NOT NULL,
    voter_ip VARCHAR(45),
    voter_fingerprint VARCHAR(255),
    voted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (voting_id) REFERENCES votings(id) ON DELETE CASCADE,
    FOREIGN KEY (candidate_id) REFERENCES voting_candidates(id) ON DELETE CASCADE
);
```

#### 2.2.8 LIVE MATCHING (PWA)

```sql
-- Log de actividad en tiempo real
CREATE TABLE live_activity_log (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    event_type VARCHAR(50) NOT NULL,
    user_type VARCHAR(20) NOT NULL,
    user_id BIGINT NOT NULL,
    related_user_type VARCHAR(20),
    related_user_id BIGINT,
    slot_id BIGINT,
    device_type VARCHAR(20),
    ip_address VARCHAR(45),
    user_agent TEXT,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    metadata JSON,
    INDEX idx_event_type (event_type),
    INDEX idx_user (user_type, user_id),
    INDEX idx_timestamp (timestamp)
);
```

---

## 3. MÓDULOS FUNCIONALES

### 3.1 MÓDULO: Gestión de Eventos

#### 3.1.1 Funcionalidades Admin

| Función | Descripción |
|---------|-------------|
| Crear evento | Nombre, descripción, lugar (nombre + dirección), aforo total |
| Editar evento | Modificar todos los campos |
| Gestionar sponsors del evento | Asociar sponsors existentes y definir su nivel (Platinum/Gold/Silver/Bronze) |
| Gestionar características | Crear items (título + descripción) que describen el evento |
| Cambiar estado | draft → published → active → finished |
| Ver estadísticas | Entradas vendidas, aforo disponible, ingresos |

#### 3.1.2 Reglas de Negocio

```
1. Un evento debe tener al menos nombre y aforo total
2. Los sponsors asociados deben existir previamente en el sistema
3. El aforo no puede reducirse por debajo de las entradas ya vendidas
4. El estado 'active' habilita la venta de entradas
5. El estado 'finished' bloquea nuevas operaciones
```

### 3.2 MÓDULO: Sistema de Entradas

#### 3.2.1 Tipos de Entrada

| Tipo | Precio | Creador | Uso |
|------|--------|---------|-----|
| Entrada de Sponsor | Gratis (0€) | Se genera automáticamente para cada sponsor del evento | Landing personalizada del sponsor |
| Entrada de Organización | Configurable (>0€) | Admin | Página de venta oficial |

#### 3.2.2 Flujo de Entrada Gratuita (Sponsor)

```
1. Sponsor se asocia al evento con nivel (Platinum/Gold/Silver/Bronze)
2. Sistema genera automáticamente un ticket_type gratuito para ese sponsor
3. Sponsor tiene landing page personalizada (con su logo, descripción)
4. En esa landing hay un bloque "Registro de Entrada"
5. El bloque muestra formulario pidiendo datos del participante
6. Al enviar: se crea ticket con invited_by_type='sponsor' y invited_by_sponsor_id
7. Se envía email de confirmación al participante
8. Participante recibe código QR para check-in
```

#### 3.2.3 Flujo de Entrada de Pago (Organización)

```
1. Admin crea ticket_type con precio > 0
2. Visitante accede a página de compra
3. Completa formulario con sus datos
4. Procede a pago con Stripe
5. Al confirmar pago:
   - Se crea ticket con invited_by_type='organization'
   - payment_status = 'paid'
   - stripe_payment_intent_id = ID de Stripe
6. Email de confirmación con QR
```

#### 3.2.4 Datos Requeridos en Formulario de Entrada

```yaml
Datos del Participante (obligatorios):
  - Nombre
  - Apellidos
  - Email
  - Teléfono (opcional)
  - Cargo/Puesto (opcional)

Datos de la Empresa del Participante (opcionales):
  - Nombre de empresa
  - Web de empresa
  - Tamaño de empresa (1-10, 11-50, 51-200, 201-500, 500+)

Día del Evento:
  - Fecha seleccionada (si el evento tiene múltiples días)
```

#### 3.2.5 Integración Stripe

```javascript
// Crear Payment Intent
const paymentIntent = await stripe.paymentIntents.create({
  amount: ticketType.price * 100, // Stripe usa céntimos
  currency: 'eur',
  metadata: {
    ticket_type_id: ticketType.id,
    event_id: event.id,
    attendee_email: formData.email
  }
});

// Webhook para confirmar pago
// POST /api/webhooks/stripe
// Event: payment_intent.succeeded
// Acción: Crear ticket, enviar confirmación
```

### 3.3 MÓDULO: Matching Bidireccional

#### 3.3.1 Flujo de Selección

```
SPONSOR → EMPRESAS:
1. Sponsor accede con código único: /seleccion-sponsor?code=ABC123
2. Ve listado de empresas activas ordenadas por nombre
3. Puede ver descripción, web, SaaS que usan
4. Marca checkbox en empresas que le interesan
5. Guarda selecciones
6. Sistema envía email a empresas seleccionadas (solo nuevas selecciones)

EMPRESA → SPONSORS:
1. Empresa accede con código único: /seleccion-empresa?code=XYZ789
2. Ve listado de sponsors ordenados por nivel de prioridad (Platinum primero)
3. Ve destacados los sponsors que ya la seleccionaron ("Recomendados")
4. Marca checkbox en sponsors que le interesan
5. Guarda selecciones
6. Sistema envía email a sponsors seleccionados (solo nuevos)

MATCH MUTUO:
- Cuando sponsor Y empresa se seleccionan mutuamente
- Se detecta automáticamente al consultar
- Habilita asignación de reunión
```

#### 3.3.2 Ordenamiento de Listados

```javascript
// Para empresas viendo sponsors:
ORDER BY 
  CASE priority_level 
    WHEN 'platinum' THEN 1 
    WHEN 'gold' THEN 2 
    WHEN 'silver' THEN 3 
    WHEN 'bronze' THEN 4 
  END,
  name ASC

// Sponsors que ya seleccionaron a la empresa van primero (sección "Recomendados")
```

#### 3.3.3 Mensajería Sponsor → Empresa

```
Condiciones:
- Sponsor debe tener can_send_messages = TRUE
- Sistema global de mensajería debe estar habilitado
- Un único mensaje por par sponsor-empresa

Flujo:
1. Sponsor ve botón "Enviar mensaje" junto a cada empresa
2. Modal con textarea (máx 500 caracteres)
3. Al enviar: email a la empresa con el mensaje
4. BCC a administradores configurados
5. Registro en sponsor_messages para evitar duplicados
```

### 3.4 MÓDULO: Planificación de Reuniones

#### 3.4.1 Configuración de Bloques

```yaml
Bloque Horario:
  - nombre: "Sesión Mañana"
  - fecha: 2025-03-15
  - hora_inicio: 10:00
  - hora_fin: 13:00
  - duracion_reunion: 15 minutos
  - mesas_simultaneas: 10

Slots Generados Automáticamente:
  - 10:00 - Mesas 1 a 10
  - 10:15 - Mesas 1 a 10
  - 10:30 - Mesas 1 a 10
  - ... (cada 15 min hasta las 13:00)
```

#### 3.4.2 Sistema de Simultaneidad

```javascript
// Máximo reuniones simultáneas por SPONSOR
// Configurable en ficha del sponsor (default: 1)
// Ejemplo: Sponsor Platinum puede tener 3 reuniones a la vez

// Máximo reuniones simultáneas por EMPRESA
// Calculado automáticamente: número de emails de contacto
// Si empresa tiene 2 emails → puede tener 2 reuniones simultáneas

function isSlotAvailableForMatch(slotId, sponsorId, companyId) {
  // 1. ¿Slot ya ocupado?
  if (slotHasAssignment(slotId)) return false;
  
  // 2. Obtener info del slot
  const slot = getSlot(slotId);
  
  // 3. ¿Sponsor tiene capacidad en este horario?
  const sponsorMax = getSponsorMaxSimultaneous(sponsorId);
  const sponsorCurrent = countSponsorMeetingsAtTime(sponsorId, slot.blockId, slot.time);
  if (sponsorCurrent >= sponsorMax) return false;
  
  // 4. ¿Empresa tiene capacidad en este horario?
  const companyMax = countCompanyEmails(companyId); // Nº de emails
  const companyCurrent = countCompanyMeetingsAtTime(companyId, slot.blockId, slot.time);
  if (companyCurrent >= companyMax) return false;
  
  return true;
}
```

#### 3.4.3 Asignación de Reuniones

```
Desde Admin:
1. Ver lista de matches mutuos sin asignar
2. Seleccionar match
3. Ver slots disponibles (filtrados por disponibilidad de ambos)
4. Asignar a slot específico
5. Opcionalmente añadir notas
6. Sistema envía notificación a ambas partes

Desde Live Matching (PWA):
1. Participantes escanean QR mutuamente
2. Sistema crea match bidireccional automáticamente
3. Redirige a pantalla de asignación
4. Muestra slots disponibles
5. Participantes eligen horario
```

### 3.5 MÓDULO: Live Matching (PWA)

#### 3.5.1 Flujo Completo

```
1. IDENTIFICACIÓN:
   - Participante abre app PWA
   - Escanea su propio QR (o se identifica con código)
   - Sistema lo reconoce como sponsor o empresa

2. ESCANEO MUTUO:
   - Participante A escanea QR de Participante B
   - Sistema crea selección A → B
   - Sistema crea selección B → A (match automático)

3. REUNIÓN INMEDIATA:
   - Si hay match, ofrece asignar reunión
   - Muestra slots disponibles para ambos
   - Al seleccionar, asigna reunión

4. CONFIRMACIÓN:
   - Muestra detalles: hora, mesa, duración
   - Ambos participantes ven la reunión en su agenda
```

#### 3.5.2 Características PWA

```javascript
// manifest.json
{
  "name": "TLOS Live Matching",
  "short_name": "TLOS",
  "start_url": "/live",
  "display": "standalone",
  "background_color": "#ffffff",
  "theme_color": "#1a1a2e",
  "icons": [
    { "src": "/icon-192.png", "sizes": "192x192", "type": "image/png" },
    { "src": "/icon-512.png", "sizes": "512x512", "type": "image/png" }
  ]
}

// Service Worker para funcionamiento offline básico
// Cachear assets estáticos
// Queue de acciones para sincronizar cuando haya conexión
```

### 3.6 MÓDULO: Votaciones

#### 3.6.1 Funcionalidades

```yaml
Crear Votación:
  - Título
  - Descripción
  - Fecha inicio/fin (opcional)
  - Mostrar conteo de votos: sí/no

Gestionar Candidatos:
  - Nombre
  - Descripción
  - Logo
  - Web
  - Votos base (para ajustes)
  - Orden de aparición

Control Anti-Fraude:
  - Cookie por votación
  - Opcionalmente: fingerprint del navegador
  - Rate limiting por IP
```

#### 3.6.2 Shortcode/Bloque

```html
<!-- Embeber votación en cualquier página -->
[voting id="5"]

<!-- O mediante bloque del editor -->
<VotingBlock votingId={5} />
```

### 3.7 MÓDULO: Notificaciones Email

#### 3.7.1 Tipos de Notificaciones

| Evento | Destinatario | Contenido |
|--------|--------------|-----------|
| Nueva selección recibida | Sponsor/Empresa | "X ha mostrado interés en ti" |
| Reunión asignada | Ambas partes | Fecha, hora, mesa, con quién |
| Confirmación de entrada | Participante | QR, detalles del evento |
| Mensaje de sponsor | Empresa | Mensaje + datos del sponsor |

#### 3.7.2 Plantillas Personalizables

```
Variables disponibles:
- {SELECTOR_NAME} - Nombre de quien selecciona
- {SELECTED_NAME} - Nombre del seleccionado
- {DATE} - Fecha
- {TIME} - Hora
- {ROOM} - Mesa/Sala
- {DURATION} - Duración
- {PANEL_URL} - URL del panel personalizado
- {BLOCK_NAME} - Nombre del bloque horario
- {WITH_NAME} - Nombre de la otra parte en reunión
```

#### 3.7.3 Sistema Anti-Duplicados

```javascript
// Antes de enviar notificación de selección
function notificationAlreadySent(type, senderId, recipientId) {
  return db.exists(
    'email_notifications',
    { notification_type: type, sender_id: senderId, recipient_id: recipientId }
  );
}

// Registrar después de enviar
function registerNotification(type, senderType, senderId, recipientType, recipientId) {
  db.insert('email_notifications', {
    notification_type: type,
    sender_type: senderType,
    sender_id: senderId,
    recipient_type: recipientType,
    recipient_id: recipientId
  });
}
```

---

## 4. API ENDPOINTS

### 4.1 Eventos

```
GET    /api/events                    Lista eventos
POST   /api/events                    Crear evento
GET    /api/events/:id                Detalle evento
PUT    /api/events/:id                Actualizar evento
DELETE /api/events/:id                Eliminar evento

POST   /api/events/:id/sponsors       Asociar sponsor a evento
DELETE /api/events/:id/sponsors/:sid  Desasociar sponsor
PUT    /api/events/:id/sponsors/:sid  Actualizar nivel de sponsor

GET    /api/events/:id/features       Listar características
POST   /api/events/:id/features       Crear característica
PUT    /api/events/:id/features/:fid  Actualizar característica
DELETE /api/events/:id/features/:fid  Eliminar característica

GET    /api/events/:id/stats          Estadísticas del evento
```

### 4.2 Entradas

```
GET    /api/ticket-types              Lista tipos de entrada
POST   /api/ticket-types              Crear tipo de entrada
PUT    /api/ticket-types/:id          Actualizar tipo
DELETE /api/ticket-types/:id          Eliminar tipo

POST   /api/tickets                   Registrar entrada (gratuita)
POST   /api/tickets/purchase          Iniciar compra (con pago)
GET    /api/tickets/:code             Obtener ticket por código
PUT    /api/tickets/:id/check-in      Marcar check-in

POST   /api/webhooks/stripe           Webhook de Stripe
```

### 4.3 Sponsors y Empresas

```
GET    /api/sponsors                  Lista sponsors
POST   /api/sponsors                  Crear sponsor
GET    /api/sponsors/:id              Detalle sponsor
PUT    /api/sponsors/:id              Actualizar sponsor
DELETE /api/sponsors/:id              Eliminar sponsor
POST   /api/sponsors/import           Importar CSV

GET    /api/companies                 Lista empresas
POST   /api/companies                 Crear empresa
GET    /api/companies/:id             Detalle empresa
PUT    /api/companies/:id             Actualizar empresa
DELETE /api/companies/:id             Eliminar empresa
POST   /api/companies/import          Importar CSV
```

### 4.4 Matching

```
GET    /api/selections/sponsor/:code  Obtener selecciones de sponsor
POST   /api/selections/sponsor/:code  Guardar selecciones de sponsor
GET    /api/selections/company/:code  Obtener selecciones de empresa
POST   /api/selections/company/:code  Guardar selecciones de empresa

GET    /api/matches                   Lista de matches mutuos
GET    /api/matches/unassigned        Matches sin reunión asignada
POST   /api/matches/export            Exportar matches a CSV

POST   /api/messages/send             Enviar mensaje sponsor→empresa
GET    /api/messages/sponsor/:code    Obtener mensajes enviados por sponsor
```

### 4.5 Reuniones

```
GET    /api/meeting-blocks            Lista bloques horarios
POST   /api/meeting-blocks            Crear bloque
PUT    /api/meeting-blocks/:id        Actualizar bloque
DELETE /api/meeting-blocks/:id        Eliminar bloque
POST   /api/meeting-blocks/:id/generate-slots  Generar slots

GET    /api/meeting-slots             Lista slots
GET    /api/meeting-slots/available   Slots disponibles para match

POST   /api/meeting-assignments       Asignar reunión
DELETE /api/meeting-assignments/:id   Desasignar reunión
GET    /api/meeting-assignments/export Exportar calendario CSV

POST   /api/meetings/check-availability  Verificar disponibilidad de slot
```

### 4.6 Live Matching

```
POST   /api/live/identify             Identificar por código QR
POST   /api/live/quick-match          Crear match rápido bidireccional
GET    /api/live/match-status         Verificar estado de match
POST   /api/live/assign               Asignar reunión desde PWA
```

### 4.7 Votaciones

```
GET    /api/votings                   Lista votaciones
POST   /api/votings                   Crear votación
PUT    /api/votings/:id               Actualizar votación
DELETE /api/votings/:id               Eliminar votación

GET    /api/votings/:id/candidates    Lista candidatos
POST   /api/votings/:id/candidates    Añadir candidato
PUT    /api/votings/:id/candidates/:cid  Actualizar candidato
DELETE /api/votings/:id/candidates/:cid  Eliminar candidato

POST   /api/votings/:id/vote          Registrar voto
GET    /api/votings/:id/results       Obtener resultados
```

### 4.8 Exportaciones

```
GET    /api/export/sponsors           CSV de sponsors
GET    /api/export/companies          CSV de empresas
GET    /api/export/matches            CSV de matches confirmados
GET    /api/export/schedule           CSV del calendario de reuniones
GET    /api/export/tickets            CSV de entradas
GET    /api/export/selections/:type/:id  CSV de selecciones específicas
```

---

## 5. FRONTEND: Páginas y Bloques

### 5.1 Páginas del Sistema

| Ruta | Descripción | Autenticación |
|------|-------------|---------------|
| /seleccion-sponsor?code=XXX | Panel de selección para sponsors | Código único |
| /seleccion-empresa?code=XXX | Panel de selección para empresas | Código único |
| /live | PWA de Live Matching | QR/Código |
| /asignar-reunion | Asignación de reunión (desde PWA) | Sesión activa |
| /entrada/:sponsor-slug | Landing de entrada gratuita de sponsor | Público |
| /entradas | Página de compra de entradas oficiales | Público |

### 5.2 Bloques para Landing Pages

```yaml
Bloque: TicketRegistration
  Props:
    - sponsorId: ID del sponsor (null = organización)
    - eventId: ID del evento
    - ticketTypeId: Tipo de entrada específico
    - showCompanyFields: boolean
    - redirectUrl: URL post-registro

Bloque: VotingEmbed
  Props:
    - votingId: ID de la votación
    - showResults: boolean

Bloque: EventInfo
  Props:
    - eventId: ID del evento
    - showFeatures: boolean
    - showSponsors: boolean

Bloque: SponsorShowcase
  Props:
    - eventId: ID del evento
    - level: 'all' | 'platinum' | 'gold' | 'silver' | 'bronze'
    - layout: 'grid' | 'carousel'
```

### 5.3 Componentes de Interfaz

```yaml
SponsorCard:
  - Logo con borde según nivel (Platinum: dorado, Gold: amarillo, etc.)
  - Nombre
  - Descripción truncada
  - Web (enlace)
  - Badge de nivel
  - Checkbox de selección (si aplica)

CompanyCard:
  - Logo
  - Nombre
  - Descripción
  - Web
  - SaaS que utilizan (badges)
  - Badge "Te ha seleccionado" (si aplica)
  - Checkbox de selección

MeetingAgenda:
  - Agrupado por fecha
  - Para cada reunión: hora, duración, mesa, con quién
  - Botón de descarga PDF/ICS

TicketQR:
  - Código QR grande
  - Datos del participante
  - Datos del evento
  - Código de confirmación
```

---

## 6. CONFIGURACIÓN GLOBAL

### 6.1 Settings del Sistema

```yaml
General:
  - site_name: "The Last of SaaS"
  - admin_emails: "admin@thelastofsaas.es, otro@email.com"
  
Email:
  - email_from: "noreply@thelastofsaas.es"
  - email_from_name: "The Last of SaaS"
  - notify_sponsors: true/false
  - notify_companies: true/false
  - notify_meetings: true/false
  - email_template_selection: "Plantilla personalizable..."
  - email_template_meeting: "Plantilla personalizable..."

Matching:
  - sponsor_page_url: "/seleccion-sponsor"
  - company_page_url: "/seleccion-empresa"
  - hide_inactive: true/false
  - allow_sponsor_messages: true/false

Stripe:
  - stripe_public_key: "pk_..."
  - stripe_secret_key: "sk_..."
  - stripe_webhook_secret: "whsec_..."
  - currency: "eur"
```

---

## 7. SEGURIDAD Y VALIDACIONES

### 7.1 Autenticación

```yaml
Admin:
  - Login con credenciales del CMS
  - Roles: admin, editor, viewer

Frontend (Sponsors/Empresas):
  - Acceso mediante código único en URL
  - Códigos generados automáticamente (UUID o similar)
  - Sin contraseña requerida

Live Matching:
  - Identificación por escaneo de QR
  - Sesión temporal en localStorage
```

### 7.2 Validaciones de Datos

```javascript
// Validar email
function isValidEmail(email) {
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

// Validar múltiples emails separados por coma
function validateEmails(emailString) {
  const emails = emailString.split(',').map(e => e.trim()).filter(Boolean);
  return emails.every(isValidEmail);
}

// Sanitizar entrada
function sanitizeInput(input) {
  return input.trim().replace(/<[^>]*>/g, '');
}

// Validar código único
function isValidUniqueCode(code) {
  return /^[A-Za-z0-9_-]{8,100}$/.test(code);
}
```

### 7.3 Rate Limiting

```yaml
API General:
  - 100 requests/minuto por IP

Votaciones:
  - 1 voto por votación por cookie
  - 10 votos/hora por IP

Registro de Entradas:
  - 5 registros/minuto por IP

Stripe Webhooks:
  - Validar firma del webhook
  - Idempotencia por payment_intent_id
```

---

## 8. IMPORTACIÓN CSV

### 8.1 Formato para Sponsors

```csv
name;category;description;website;logo_url;contact_emails
"Sponsor A";"CRM";"Descripción...";"https://sponsor-a.com";"https://cdn.../logo.png";"email1@a.com,email2@a.com"
```

### 8.2 Formato para Empresas

```csv
name;description;website;notes;logo_url;contact_emails
"Empresa X";"Descripción...";"https://empresa-x.com";"Notas internas";"https://cdn.../logo.png";"contacto@x.com"
```

### 8.3 Detección Automática de Delimitador

```javascript
function detectDelimiter(firstLine) {
  const delimiters = [';', ',', '\t'];
  let best = ',';
  let maxCount = 0;
  
  for (const d of delimiters) {
    const count = (firstLine.match(new RegExp(d, 'g')) || []).length;
    if (count > maxCount) {
      maxCount = count;
      best = d;
    }
  }
  return best;
}
```

---

## 9. GENERACIÓN DE CÓDIGOS

### 9.1 Códigos Únicos

```javascript
// Para sponsors/empresas (acceso a paneles)
function generateUniqueCode() {
  return crypto.randomUUID(); // ej: "550e8400-e29b-41d4-a716-446655440000"
}

// Para tickets (más corto, legible)
function generateTicketCode() {
  const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // Sin confusiones (0/O, 1/I/L)
  let code = '';
  for (let i = 0; i < 10; i++) {
    code += chars[Math.floor(Math.random() * chars.length)];
  }
  return code; // ej: "AB3D5FGH2K"
}

// Código de confirmación (6 caracteres)
function generateConfirmationCode() {
  return Math.random().toString(36).substring(2, 8).toUpperCase();
}
```

---

## 10. CHECKLIST DE IMPLEMENTACIÓN

### Fase 1: Core
- [ ] Modelo de datos (migraciones)
- [ ] CRUD de Eventos
- [ ] CRUD de Sponsors
- [ ] CRUD de Empresas
- [ ] Importación CSV

### Fase 2: Matching
- [ ] Selecciones bidireccionales
- [ ] Detección de matches mutuos
- [ ] Notificaciones email
- [ ] Sistema anti-duplicados
- [ ] Mensajería sponsor→empresa

### Fase 3: Reuniones
- [ ] Bloques horarios
- [ ] Generación de slots
- [ ] Lógica de simultaneidad
- [ ] Asignación de reuniones
- [ ] Exportación de agenda

### Fase 4: Entradas
- [ ] Tipos de entrada
- [ ] Registro de entradas gratuitas
- [ ] Integración Stripe
- [ ] Generación de QR
- [ ] Check-in

### Fase 5: Live Matching
- [ ] PWA básica
- [ ] Escaneo QR
- [ ] Match rápido
- [ ] Asignación inmediata

### Fase 6: Votaciones
- [ ] CRUD votaciones
- [ ] Sistema de votos
- [ ] Control anti-fraude
- [ ] Bloque embebible

### Fase 7: Frontend
- [ ] Landing pages de sponsors
- [ ] Página de compra de entradas
- [ ] Paneles de selección
- [ ] Agenda de reuniones

---

## APÉNDICE A: Estilos Visuales por Nivel de Sponsor

```css
/* Platinum */
.sponsor-platinum {
  border: 3px solid #E5C100;
  background: linear-gradient(135deg, #FFFEF0, #FFF9E0);
  box-shadow: 0 0 20px rgba(229, 193, 0, 0.3);
}

/* Gold */
.sponsor-gold {
  border: 2px solid #FFD700;
  background: linear-gradient(135deg, #FFFBF0, #FFF5E0);
}

/* Silver */
.sponsor-silver {
  border: 2px solid #C0C0C0;
  background: linear-gradient(135deg, #FAFAFA, #F5F5F5);
}

/* Bronze */
.sponsor-bronze {
  border: 1px solid #CD7F32;
  background: #FFFFFF;
}
```

---

## APÉNDICE B: Estructura de QR

```javascript
// Contenido del QR para tickets
const qrContent = JSON.stringify({
  type: 'ticket',
  code: ticket.ticket_code,
  event: event.id,
  date: ticket.event_date
});

// Contenido del QR para Live Matching
const qrContent = JSON.stringify({
  type: 'participant',
  userType: 'sponsor', // o 'company'
  code: user.unique_code,
  name: user.name
});
```

---

*Documento generado para desarrollo con Claude Code*
*The Last of SaaS © 2025*
