<?php
/**
 * FontAwesome Icon Picker Partial
 * Extended Library - Omniwallet CMS
 */
?>
<div class="icon-picker-modal" id="iconPickerModal" style="display: none;">
    <div class="icon-picker-overlay"></div>
    <div class="icon-picker-content">
        <div class="icon-picker-header">
            <h4>Seleccionar Icono</h4>
            <input type="text" class="icon-picker-search" placeholder="Buscar icono..." id="iconPickerSearch">
            <button type="button" class="icon-picker-close">&times;</button>
        </div>
        <div class="icon-picker-categories">
            <button type="button" class="icon-category active" data-category="all">Todos</button>
            <button type="button" class="icon-category" data-category="business">Negocio</button>
            <button type="button" class="icon-category" data-category="commerce">Comercio</button>
            <button type="button" class="icon-category" data-category="food">Comida</button>
            <button type="button" class="icon-category" data-category="health">Salud</button>
            <button type="button" class="icon-category" data-category="tech">Tecnología</button>
            <button type="button" class="icon-category" data-category="interface">Interfaz</button>
            <button type="button" class="icon-category" data-category="arrows">Flechas</button>
            <button type="button" class="icon-category" data-category="media">Media</button>
            <button type="button" class="icon-category" data-category="files">Archivos</button>
            <button type="button" class="icon-category" data-category="travel">Viajes</button>
            <button type="button" class="icon-category" data-category="nature">Naturaleza</button>
            <button type="button" class="icon-category" data-category="sports">Deportes</button>
            <button type="button" class="icon-category" data-category="education">Educación</button>
            <button type="button" class="icon-category" data-category="security">Seguridad</button>
            <button type="button" class="icon-category" data-category="social">Redes</button>
            <button type="button" class="icon-category" data-category="brands">Marcas</button>
        </div>
        <div class="icon-picker-grid" id="iconPickerGrid">
            <!-- Icons will be loaded here -->
        </div>
        <div class="icon-picker-footer">
            <span id="iconCount">0 iconos</span>
        </div>
    </div>
</div>

<style>
.icon-picker-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 10000;
    display: flex;
    align-items: center;
    justify-content: center;
}

.icon-picker-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
}

.icon-picker-content {
    position: relative;
    background: white;
    border-radius: 12px;
    width: 95%;
    max-width: 900px;
    max-height: 85vh;
    display: flex;
    flex-direction: column;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

.icon-picker-header {
    padding: 20px;
    border-bottom: 1px solid #eee;
    display: flex;
    align-items: center;
    gap: 15px;
}

.icon-picker-header h4 {
    margin: 0;
    font-size: 18px;
    white-space: nowrap;
}

.icon-picker-search {
    flex: 1;
    padding: 10px 15px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 14px;
}

.icon-picker-close {
    width: 36px;
    height: 36px;
    border: none;
    background: #f5f5f5;
    border-radius: 50%;
    font-size: 20px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}

.icon-picker-close:hover {
    background: #e0e0e0;
}

.icon-picker-categories {
    padding: 15px 20px;
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    border-bottom: 1px solid #eee;
    max-height: 200px;
    overflow-y: auto;
    background: #fafafa;
}

.icon-category {
    padding: 6px 12px;
    border: 1px solid #ddd;
    background: white;
    border-radius: 20px;
    font-size: 12px;
    cursor: pointer;
    transition: all 0.2s;
}

.icon-category:hover {
    border-color: var(--color-primary, #5046e5);
    color: var(--color-primary, #5046e5);
}

.icon-category.active {
    background: var(--color-primary, #5046e5);
    border-color: var(--color-primary, #5046e5);
    color: white;
}

.icon-picker-grid {
    padding: 20px;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(70px, 1fr));
    gap: 8px;
    overflow-y: auto;
    flex: 1;
    max-height: 450px;
}

.icon-picker-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 12px 6px;
    border: 1px solid #eee;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
    gap: 6px;
}

.icon-picker-item:hover {
    border-color: var(--color-primary, #5046e5);
    background: #f8f7ff;
}

.icon-picker-item i {
    font-size: 22px;
    color: #333;
}

.icon-picker-item span {
    font-size: 9px;
    color: #666;
    text-align: center;
    word-break: break-word;
    line-height: 1.2;
}

.icon-picker-footer {
    padding: 12px 20px;
    border-top: 1px solid #eee;
    text-align: right;
    font-size: 12px;
    color: #999;
}

/* Icon input field styling */
.icon-input-wrapper {
    display: flex;
    gap: 10px;
    align-items: center;
}

.icon-input-wrapper input {
    flex: 1;
}

.icon-input-preview {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f5f5f5;
    border-radius: 8px;
    font-size: 18px;
}

.icon-input-btn {
    padding: 10px 15px;
    background: var(--color-primary, #5046e5);
    color: white;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    font-size: 13px;
    white-space: nowrap;
}

.icon-input-btn:hover {
    opacity: 0.9;
}
</style>

<script>
(function() {
    'use strict';

    // Extended FontAwesome icon library
    const iconLibrary = {
        business: [
            { class: 'fas fa-store', name: 'Tienda' },
            { class: 'fas fa-store-alt', name: 'Tienda Alt' },
            { class: 'fas fa-building', name: 'Edificio' },
            { class: 'fas fa-city', name: 'Ciudad' },
            { class: 'fas fa-industry', name: 'Industria' },
            { class: 'fas fa-landmark', name: 'Banco' },
            { class: 'fas fa-briefcase', name: 'Maletín' },
            { class: 'fas fa-suitcase', name: 'Maleta' },
            { class: 'fas fa-chart-line', name: 'Gráfico Línea' },
            { class: 'fas fa-chart-bar', name: 'Gráfico Barras' },
            { class: 'fas fa-chart-pie', name: 'Gráfico Circular' },
            { class: 'fas fa-chart-area', name: 'Gráfico Área' },
            { class: 'fas fa-coins', name: 'Monedas' },
            { class: 'fas fa-euro-sign', name: 'Euro' },
            { class: 'fas fa-dollar-sign', name: 'Dólar' },
            { class: 'fas fa-pound-sign', name: 'Libra' },
            { class: 'fas fa-yen-sign', name: 'Yen' },
            { class: 'fas fa-credit-card', name: 'Tarjeta' },
            { class: 'fas fa-wallet', name: 'Cartera' },
            { class: 'fas fa-money-bill', name: 'Billete' },
            { class: 'fas fa-money-bill-wave', name: 'Dinero' },
            { class: 'fas fa-money-check', name: 'Cheque' },
            { class: 'fas fa-money-check-alt', name: 'Cheque Alt' },
            { class: 'fas fa-hand-holding-usd', name: 'Pago' },
            { class: 'fas fa-piggy-bank', name: 'Ahorro' },
            { class: 'fas fa-calculator', name: 'Calculadora' },
            { class: 'fas fa-receipt', name: 'Recibo' },
            { class: 'fas fa-file-invoice', name: 'Factura' },
            { class: 'fas fa-file-invoice-dollar', name: 'Factura $' },
            { class: 'fas fa-users', name: 'Usuarios' },
            { class: 'fas fa-user-tie', name: 'Ejecutivo' },
            { class: 'fas fa-user-friends', name: 'Amigos' },
            { class: 'fas fa-people-carry', name: 'Equipo' },
            { class: 'fas fa-handshake', name: 'Acuerdo' },
            { class: 'fas fa-hands-helping', name: 'Ayuda' },
            { class: 'fas fa-award', name: 'Premio' },
            { class: 'fas fa-trophy', name: 'Trofeo' },
            { class: 'fas fa-medal', name: 'Medalla' },
            { class: 'fas fa-crown', name: 'Corona' },
            { class: 'fas fa-gem', name: 'Diamante' },
            { class: 'fas fa-balance-scale', name: 'Balanza' },
            { class: 'fas fa-gavel', name: 'Mazo' },
            { class: 'fas fa-project-diagram', name: 'Diagrama' },
            { class: 'fas fa-sitemap', name: 'Organigrama' },
            { class: 'fas fa-network-wired', name: 'Red' },
            { class: 'fas fa-clipboard-list', name: 'Lista' },
            { class: 'fas fa-tasks', name: 'Tareas' },
            { class: 'fas fa-bullseye', name: 'Objetivo' },
            { class: 'fas fa-crosshairs', name: 'Meta' },
            { class: 'fas fa-funnel-dollar', name: 'Embudo' },
        ],
        commerce: [
            { class: 'fas fa-shopping-cart', name: 'Carrito' },
            { class: 'fas fa-shopping-bag', name: 'Bolsa' },
            { class: 'fas fa-shopping-basket', name: 'Cesta' },
            { class: 'fas fa-cart-plus', name: 'Añadir Carrito' },
            { class: 'fas fa-cart-arrow-down', name: 'Descargar' },
            { class: 'fas fa-tags', name: 'Etiquetas' },
            { class: 'fas fa-tag', name: 'Etiqueta' },
            { class: 'fas fa-barcode', name: 'Código Barras' },
            { class: 'fas fa-qrcode', name: 'QR' },
            { class: 'fas fa-box', name: 'Caja' },
            { class: 'fas fa-box-open', name: 'Caja Abierta' },
            { class: 'fas fa-boxes', name: 'Cajas' },
            { class: 'fas fa-archive', name: 'Archivo' },
            { class: 'fas fa-truck', name: 'Camión' },
            { class: 'fas fa-truck-loading', name: 'Cargando' },
            { class: 'fas fa-shipping-fast', name: 'Envío Rápido' },
            { class: 'fas fa-dolly', name: 'Carretilla' },
            { class: 'fas fa-dolly-flatbed', name: 'Plataforma' },
            { class: 'fas fa-pallet', name: 'Palet' },
            { class: 'fas fa-warehouse', name: 'Almacén' },
            { class: 'fas fa-percent', name: 'Descuento' },
            { class: 'fas fa-percentage', name: 'Porcentaje' },
            { class: 'fas fa-gift', name: 'Regalo' },
            { class: 'fas fa-gifts', name: 'Regalos' },
            { class: 'fas fa-cash-register', name: 'Caja Reg.' },
            { class: 'fas fa-store-slash', name: 'Cerrado' },
            { class: 'fas fa-conveyor-belt', name: 'Cinta' },
            { class: 'fas fa-hand-holding-box', name: 'Entrega' },
        ],
        food: [
            { class: 'fas fa-utensils', name: 'Cubiertos' },
            { class: 'fas fa-utensil-spoon', name: 'Cuchara' },
            { class: 'fas fa-hamburger', name: 'Hamburguesa' },
            { class: 'fas fa-hotdog', name: 'Hot Dog' },
            { class: 'fas fa-pizza-slice', name: 'Pizza' },
            { class: 'fas fa-bacon', name: 'Bacon' },
            { class: 'fas fa-coffee', name: 'Café' },
            { class: 'fas fa-mug-hot', name: 'Taza Caliente' },
            { class: 'fas fa-wine-glass', name: 'Copa Vino' },
            { class: 'fas fa-wine-glass-alt', name: 'Vino' },
            { class: 'fas fa-wine-bottle', name: 'Botella Vino' },
            { class: 'fas fa-beer', name: 'Cerveza' },
            { class: 'fas fa-cocktail', name: 'Cóctel' },
            { class: 'fas fa-glass-martini', name: 'Martini' },
            { class: 'fas fa-glass-martini-alt', name: 'Copa' },
            { class: 'fas fa-glass-whiskey', name: 'Whiskey' },
            { class: 'fas fa-glass-cheers', name: 'Brindis' },
            { class: 'fas fa-ice-cream', name: 'Helado' },
            { class: 'fas fa-cookie', name: 'Galleta' },
            { class: 'fas fa-cookie-bite', name: 'Galleta Mordida' },
            { class: 'fas fa-birthday-cake', name: 'Pastel' },
            { class: 'fas fa-candy-cane', name: 'Caramelo' },
            { class: 'fas fa-apple-alt', name: 'Manzana' },
            { class: 'fas fa-lemon', name: 'Limón' },
            { class: 'fas fa-carrot', name: 'Zanahoria' },
            { class: 'fas fa-pepper-hot', name: 'Picante' },
            { class: 'fas fa-seedling', name: 'Brote' },
            { class: 'fas fa-fish', name: 'Pescado' },
            { class: 'fas fa-drumstick-bite', name: 'Pollo' },
            { class: 'fas fa-bread-slice', name: 'Pan' },
            { class: 'fas fa-cheese', name: 'Queso' },
            { class: 'fas fa-egg', name: 'Huevo' },
            { class: 'fas fa-stroopwafel', name: 'Gofre' },
            { class: 'fas fa-blender', name: 'Licuadora' },
            { class: 'fas fa-mortar-pestle', name: 'Mortero' },
        ],
        health: [
            { class: 'fas fa-heart', name: 'Corazón' },
            { class: 'fas fa-heartbeat', name: 'Latido' },
            { class: 'fas fa-hospital', name: 'Hospital' },
            { class: 'fas fa-hospital-alt', name: 'Clínica' },
            { class: 'fas fa-clinic-medical', name: 'Centro Médico' },
            { class: 'fas fa-ambulance', name: 'Ambulancia' },
            { class: 'fas fa-first-aid', name: 'Primeros Aux.' },
            { class: 'fas fa-medkit', name: 'Botiquín' },
            { class: 'fas fa-stethoscope', name: 'Estetoscopio' },
            { class: 'fas fa-syringe', name: 'Jeringa' },
            { class: 'fas fa-pills', name: 'Pastillas' },
            { class: 'fas fa-tablets', name: 'Tabletas' },
            { class: 'fas fa-capsules', name: 'Cápsulas' },
            { class: 'fas fa-prescription-bottle', name: 'Medicina' },
            { class: 'fas fa-prescription-bottle-alt', name: 'Frasco' },
            { class: 'fas fa-thermometer', name: 'Termómetro' },
            { class: 'fas fa-band-aid', name: 'Curita' },
            { class: 'fas fa-tooth', name: 'Diente' },
            { class: 'fas fa-teeth', name: 'Dientes' },
            { class: 'fas fa-teeth-open', name: 'Boca' },
            { class: 'fas fa-brain', name: 'Cerebro' },
            { class: 'fas fa-lungs', name: 'Pulmones' },
            { class: 'fas fa-bone', name: 'Hueso' },
            { class: 'fas fa-x-ray', name: 'Rayos X' },
            { class: 'fas fa-dna', name: 'ADN' },
            { class: 'fas fa-virus', name: 'Virus' },
            { class: 'fas fa-virus-slash', name: 'Anti Virus' },
            { class: 'fas fa-bacteria', name: 'Bacteria' },
            { class: 'fas fa-disease', name: 'Enfermedad' },
            { class: 'fas fa-biohazard', name: 'Peligro Bio' },
            { class: 'fas fa-user-md', name: 'Doctor' },
            { class: 'fas fa-user-nurse', name: 'Enfermera' },
            { class: 'fas fa-procedures', name: 'Paciente' },
            { class: 'fas fa-wheelchair', name: 'Silla Ruedas' },
            { class: 'fas fa-crutch', name: 'Muleta' },
            { class: 'fas fa-notes-medical', name: 'Historial' },
            { class: 'fas fa-file-medical', name: 'Ficha' },
            { class: 'fas fa-spa', name: 'Spa' },
            { class: 'fas fa-hot-tub', name: 'Jacuzzi' },
        ],
        tech: [
            { class: 'fas fa-desktop', name: 'Escritorio' },
            { class: 'fas fa-laptop', name: 'Laptop' },
            { class: 'fas fa-laptop-code', name: 'Código' },
            { class: 'fas fa-tablet-alt', name: 'Tablet' },
            { class: 'fas fa-mobile-alt', name: 'Móvil' },
            { class: 'fas fa-phone', name: 'Teléfono' },
            { class: 'fas fa-phone-alt', name: 'Teléfono Alt' },
            { class: 'fas fa-phone-square', name: 'Tel Cuadrado' },
            { class: 'fas fa-tv', name: 'TV' },
            { class: 'fas fa-keyboard', name: 'Teclado' },
            { class: 'fas fa-mouse', name: 'Ratón' },
            { class: 'fas fa-mouse-pointer', name: 'Cursor' },
            { class: 'fas fa-print', name: 'Impresora' },
            { class: 'fas fa-fax', name: 'Fax' },
            { class: 'fas fa-server', name: 'Servidor' },
            { class: 'fas fa-database', name: 'Base Datos' },
            { class: 'fas fa-hdd', name: 'Disco Duro' },
            { class: 'fas fa-sd-card', name: 'SD Card' },
            { class: 'fas fa-memory', name: 'Memoria' },
            { class: 'fas fa-microchip', name: 'Chip' },
            { class: 'fas fa-sim-card', name: 'SIM' },
            { class: 'fas fa-usb', name: 'USB' },
            { class: 'fas fa-plug', name: 'Enchufe' },
            { class: 'fas fa-power-off', name: 'Power' },
            { class: 'fas fa-battery-full', name: 'Batería' },
            { class: 'fas fa-battery-half', name: 'Media Bat.' },
            { class: 'fas fa-battery-quarter', name: 'Baja Bat.' },
            { class: 'fas fa-wifi', name: 'WiFi' },
            { class: 'fas fa-broadcast-tower', name: 'Antena' },
            { class: 'fas fa-satellite', name: 'Satélite' },
            { class: 'fas fa-satellite-dish', name: 'Parabólica' },
            { class: 'fas fa-signal', name: 'Señal' },
            { class: 'fas fa-ethernet', name: 'Ethernet' },
            { class: 'fas fa-network-wired', name: 'Red Cable' },
            { class: 'fas fa-cloud', name: 'Nube' },
            { class: 'fas fa-cloud-upload-alt', name: 'Subir Nube' },
            { class: 'fas fa-cloud-download-alt', name: 'Bajar Nube' },
            { class: 'fas fa-code', name: 'Código' },
            { class: 'fas fa-code-branch', name: 'Branch' },
            { class: 'fas fa-terminal', name: 'Terminal' },
            { class: 'fas fa-bug', name: 'Bug' },
            { class: 'fas fa-robot', name: 'Robot' },
            { class: 'fas fa-vr-cardboard', name: 'VR' },
            { class: 'fas fa-gamepad', name: 'Gamepad' },
            { class: 'fas fa-headset', name: 'Headset' },
        ],
        interface: [
            { class: 'fas fa-check', name: 'Check' },
            { class: 'fas fa-check-circle', name: 'Check Círculo' },
            { class: 'fas fa-check-square', name: 'Check Cuadrado' },
            { class: 'fas fa-check-double', name: 'Doble Check' },
            { class: 'fas fa-times', name: 'X' },
            { class: 'fas fa-times-circle', name: 'X Círculo' },
            { class: 'fas fa-plus', name: 'Más' },
            { class: 'fas fa-plus-circle', name: 'Más Círculo' },
            { class: 'fas fa-plus-square', name: 'Más Cuadrado' },
            { class: 'fas fa-minus', name: 'Menos' },
            { class: 'fas fa-minus-circle', name: 'Menos Círculo' },
            { class: 'fas fa-minus-square', name: 'Menos Cuadrado' },
            { class: 'fas fa-cog', name: 'Config' },
            { class: 'fas fa-cogs', name: 'Configs' },
            { class: 'fas fa-sliders-h', name: 'Ajustes' },
            { class: 'fas fa-wrench', name: 'Llave' },
            { class: 'fas fa-tools', name: 'Herramientas' },
            { class: 'fas fa-search', name: 'Buscar' },
            { class: 'fas fa-search-plus', name: 'Zoom +' },
            { class: 'fas fa-search-minus', name: 'Zoom -' },
            { class: 'fas fa-filter', name: 'Filtro' },
            { class: 'fas fa-sort', name: 'Ordenar' },
            { class: 'fas fa-sort-up', name: 'Orden Asc' },
            { class: 'fas fa-sort-down', name: 'Orden Desc' },
            { class: 'fas fa-bell', name: 'Campana' },
            { class: 'fas fa-bell-slash', name: 'Sin Notif' },
            { class: 'fas fa-envelope', name: 'Email' },
            { class: 'fas fa-envelope-open', name: 'Email Abierto' },
            { class: 'fas fa-inbox', name: 'Inbox' },
            { class: 'fas fa-paper-plane', name: 'Enviar' },
            { class: 'fas fa-comment', name: 'Comentario' },
            { class: 'fas fa-comments', name: 'Chat' },
            { class: 'fas fa-comment-dots', name: 'Escribiendo' },
            { class: 'fas fa-comment-alt', name: 'Mensaje' },
            { class: 'fas fa-globe', name: 'Globo' },
            { class: 'fas fa-globe-americas', name: 'América' },
            { class: 'fas fa-globe-europe', name: 'Europa' },
            { class: 'fas fa-globe-asia', name: 'Asia' },
            { class: 'fas fa-link', name: 'Enlace' },
            { class: 'fas fa-unlink', name: 'Desenlazar' },
            { class: 'fas fa-external-link-alt', name: 'Externo' },
            { class: 'fas fa-star', name: 'Estrella' },
            { class: 'fas fa-star-half-alt', name: 'Media Estrella' },
            { class: 'fas fa-thumbs-up', name: 'Like' },
            { class: 'fas fa-thumbs-down', name: 'Dislike' },
            { class: 'fas fa-share', name: 'Compartir' },
            { class: 'fas fa-share-alt', name: 'Share' },
            { class: 'fas fa-share-square', name: 'Share Cuadrado' },
            { class: 'fas fa-retweet', name: 'Retweet' },
            { class: 'fas fa-bookmark', name: 'Marcador' },
            { class: 'fas fa-flag', name: 'Bandera' },
            { class: 'fas fa-hashtag', name: 'Hashtag' },
            { class: 'fas fa-at', name: 'Arroba' },
            { class: 'fas fa-info', name: 'Info' },
            { class: 'fas fa-info-circle', name: 'Info Círculo' },
            { class: 'fas fa-question', name: 'Pregunta' },
            { class: 'fas fa-question-circle', name: 'Ayuda' },
            { class: 'fas fa-exclamation', name: 'Exclamación' },
            { class: 'fas fa-exclamation-circle', name: 'Alerta' },
            { class: 'fas fa-exclamation-triangle', name: 'Advertencia' },
            { class: 'fas fa-ban', name: 'Prohibido' },
            { class: 'fas fa-ellipsis-h', name: 'Más Horiz' },
            { class: 'fas fa-ellipsis-v', name: 'Más Vert' },
            { class: 'fas fa-bars', name: 'Menú' },
            { class: 'fas fa-th', name: 'Grid' },
            { class: 'fas fa-th-large', name: 'Grid Grande' },
            { class: 'fas fa-th-list', name: 'Lista' },
            { class: 'fas fa-list', name: 'Lista Simple' },
            { class: 'fas fa-list-ul', name: 'Lista Puntos' },
            { class: 'fas fa-list-ol', name: 'Lista Números' },
            { class: 'fas fa-table', name: 'Tabla' },
            { class: 'fas fa-columns', name: 'Columnas' },
            { class: 'fas fa-grip-horizontal', name: 'Grip Horiz' },
            { class: 'fas fa-grip-vertical', name: 'Grip Vert' },
        ],
        arrows: [
            { class: 'fas fa-arrow-up', name: 'Arriba' },
            { class: 'fas fa-arrow-down', name: 'Abajo' },
            { class: 'fas fa-arrow-left', name: 'Izquierda' },
            { class: 'fas fa-arrow-right', name: 'Derecha' },
            { class: 'fas fa-arrow-circle-up', name: 'Arriba Círc' },
            { class: 'fas fa-arrow-circle-down', name: 'Abajo Círc' },
            { class: 'fas fa-arrow-circle-left', name: 'Izq Círc' },
            { class: 'fas fa-arrow-circle-right', name: 'Der Círc' },
            { class: 'fas fa-arrow-alt-circle-up', name: 'Arriba Alt' },
            { class: 'fas fa-arrow-alt-circle-down', name: 'Abajo Alt' },
            { class: 'fas fa-arrow-alt-circle-left', name: 'Izq Alt' },
            { class: 'fas fa-arrow-alt-circle-right', name: 'Der Alt' },
            { class: 'fas fa-chevron-up', name: 'Chevron Arriba' },
            { class: 'fas fa-chevron-down', name: 'Chevron Abajo' },
            { class: 'fas fa-chevron-left', name: 'Chevron Izq' },
            { class: 'fas fa-chevron-right', name: 'Chevron Der' },
            { class: 'fas fa-chevron-circle-up', name: 'Chev Círc Up' },
            { class: 'fas fa-chevron-circle-down', name: 'Chev Círc Down' },
            { class: 'fas fa-chevron-circle-left', name: 'Chev Círc Izq' },
            { class: 'fas fa-chevron-circle-right', name: 'Chev Círc Der' },
            { class: 'fas fa-angle-up', name: 'Ángulo Arriba' },
            { class: 'fas fa-angle-down', name: 'Ángulo Abajo' },
            { class: 'fas fa-angle-left', name: 'Ángulo Izq' },
            { class: 'fas fa-angle-right', name: 'Ángulo Der' },
            { class: 'fas fa-angle-double-up', name: 'Doble Arriba' },
            { class: 'fas fa-angle-double-down', name: 'Doble Abajo' },
            { class: 'fas fa-angle-double-left', name: 'Doble Izq' },
            { class: 'fas fa-angle-double-right', name: 'Doble Der' },
            { class: 'fas fa-caret-up', name: 'Caret Arriba' },
            { class: 'fas fa-caret-down', name: 'Caret Abajo' },
            { class: 'fas fa-caret-left', name: 'Caret Izq' },
            { class: 'fas fa-caret-right', name: 'Caret Der' },
            { class: 'fas fa-caret-square-up', name: 'Caret Sq Up' },
            { class: 'fas fa-caret-square-down', name: 'Caret Sq Down' },
            { class: 'fas fa-caret-square-left', name: 'Caret Sq Izq' },
            { class: 'fas fa-caret-square-right', name: 'Caret Sq Der' },
            { class: 'fas fa-long-arrow-alt-up', name: 'Larga Arriba' },
            { class: 'fas fa-long-arrow-alt-down', name: 'Larga Abajo' },
            { class: 'fas fa-long-arrow-alt-left', name: 'Larga Izq' },
            { class: 'fas fa-long-arrow-alt-right', name: 'Larga Der' },
            { class: 'fas fa-arrows-alt', name: 'Expandir' },
            { class: 'fas fa-arrows-alt-h', name: 'Horiz' },
            { class: 'fas fa-arrows-alt-v', name: 'Vert' },
            { class: 'fas fa-exchange-alt', name: 'Intercambio' },
            { class: 'fas fa-random', name: 'Aleatorio' },
            { class: 'fas fa-sync', name: 'Sincronizar' },
            { class: 'fas fa-sync-alt', name: 'Refrescar' },
            { class: 'fas fa-redo', name: 'Rehacer' },
            { class: 'fas fa-redo-alt', name: 'Rehacer Alt' },
            { class: 'fas fa-undo', name: 'Deshacer' },
            { class: 'fas fa-undo-alt', name: 'Deshacer Alt' },
            { class: 'fas fa-reply', name: 'Responder' },
            { class: 'fas fa-reply-all', name: 'Responder Todos' },
            { class: 'fas fa-level-up-alt', name: 'Subir Nivel' },
            { class: 'fas fa-level-down-alt', name: 'Bajar Nivel' },
            { class: 'fas fa-download', name: 'Descargar' },
            { class: 'fas fa-upload', name: 'Subir' },
            { class: 'fas fa-sign-in-alt', name: 'Entrar' },
            { class: 'fas fa-sign-out-alt', name: 'Salir' },
            { class: 'fas fa-expand', name: 'Expandir' },
            { class: 'fas fa-compress', name: 'Comprimir' },
            { class: 'fas fa-expand-alt', name: 'Expandir Alt' },
            { class: 'fas fa-compress-alt', name: 'Comprimir Alt' },
            { class: 'fas fa-expand-arrows-alt', name: 'Full Screen' },
            { class: 'fas fa-compress-arrows-alt', name: 'Exit Full' },
        ],
        media: [
            { class: 'fas fa-play', name: 'Play' },
            { class: 'fas fa-play-circle', name: 'Play Círculo' },
            { class: 'fas fa-pause', name: 'Pausa' },
            { class: 'fas fa-pause-circle', name: 'Pausa Círculo' },
            { class: 'fas fa-stop', name: 'Stop' },
            { class: 'fas fa-stop-circle', name: 'Stop Círculo' },
            { class: 'fas fa-forward', name: 'Adelantar' },
            { class: 'fas fa-backward', name: 'Retroceder' },
            { class: 'fas fa-fast-forward', name: 'Avance Rápido' },
            { class: 'fas fa-fast-backward', name: 'Retro Rápido' },
            { class: 'fas fa-step-forward', name: 'Siguiente' },
            { class: 'fas fa-step-backward', name: 'Anterior' },
            { class: 'fas fa-eject', name: 'Expulsar' },
            { class: 'fas fa-volume-up', name: 'Volumen +' },
            { class: 'fas fa-volume-down', name: 'Volumen -' },
            { class: 'fas fa-volume-mute', name: 'Mute' },
            { class: 'fas fa-volume-off', name: 'Sin Sonido' },
            { class: 'fas fa-music', name: 'Música' },
            { class: 'fas fa-headphones', name: 'Auriculares' },
            { class: 'fas fa-headphones-alt', name: 'Auriculares Alt' },
            { class: 'fas fa-microphone', name: 'Micrófono' },
            { class: 'fas fa-microphone-alt', name: 'Micro Alt' },
            { class: 'fas fa-microphone-slash', name: 'Micro Off' },
            { class: 'fas fa-podcast', name: 'Podcast' },
            { class: 'fas fa-record-vinyl', name: 'Vinilo' },
            { class: 'fas fa-compact-disc', name: 'CD' },
            { class: 'fas fa-camera', name: 'Cámara' },
            { class: 'fas fa-camera-retro', name: 'Cámara Retro' },
            { class: 'fas fa-video', name: 'Video' },
            { class: 'fas fa-video-slash', name: 'Video Off' },
            { class: 'fas fa-film', name: 'Película' },
            { class: 'fas fa-photo-video', name: 'Multimedia' },
            { class: 'fas fa-image', name: 'Imagen' },
            { class: 'fas fa-images', name: 'Imágenes' },
            { class: 'fas fa-portrait', name: 'Retrato' },
            { class: 'fas fa-file-image', name: 'Archivo Img' },
            { class: 'fas fa-file-video', name: 'Archivo Video' },
            { class: 'fas fa-file-audio', name: 'Archivo Audio' },
            { class: 'fas fa-broadcast-tower', name: 'Broadcast' },
            { class: 'fas fa-rss', name: 'RSS' },
            { class: 'fas fa-rss-square', name: 'RSS Cuadrado' },
        ],
        files: [
            { class: 'fas fa-file', name: 'Archivo' },
            { class: 'fas fa-file-alt', name: 'Documento' },
            { class: 'fas fa-file-pdf', name: 'PDF' },
            { class: 'fas fa-file-word', name: 'Word' },
            { class: 'fas fa-file-excel', name: 'Excel' },
            { class: 'fas fa-file-powerpoint', name: 'PowerPoint' },
            { class: 'fas fa-file-code', name: 'Código' },
            { class: 'fas fa-file-archive', name: 'ZIP' },
            { class: 'fas fa-file-csv', name: 'CSV' },
            { class: 'fas fa-file-download', name: 'Descargar' },
            { class: 'fas fa-file-upload', name: 'Subir' },
            { class: 'fas fa-file-export', name: 'Exportar' },
            { class: 'fas fa-file-import', name: 'Importar' },
            { class: 'fas fa-file-contract', name: 'Contrato' },
            { class: 'fas fa-file-signature', name: 'Firma' },
            { class: 'fas fa-folder', name: 'Carpeta' },
            { class: 'fas fa-folder-open', name: 'Carpeta Abierta' },
            { class: 'fas fa-folder-plus', name: 'Nueva Carpeta' },
            { class: 'fas fa-folder-minus', name: 'Quitar Carpeta' },
            { class: 'fas fa-copy', name: 'Copiar' },
            { class: 'fas fa-paste', name: 'Pegar' },
            { class: 'fas fa-cut', name: 'Cortar' },
            { class: 'fas fa-clone', name: 'Clonar' },
            { class: 'fas fa-save', name: 'Guardar' },
            { class: 'fas fa-trash', name: 'Papelera' },
            { class: 'fas fa-trash-alt', name: 'Eliminar' },
            { class: 'fas fa-trash-restore', name: 'Restaurar' },
            { class: 'fas fa-edit', name: 'Editar' },
            { class: 'fas fa-pen', name: 'Lápiz' },
            { class: 'fas fa-pencil-alt', name: 'Escribir' },
            { class: 'fas fa-eraser', name: 'Borrador' },
            { class: 'fas fa-highlighter', name: 'Resaltador' },
            { class: 'fas fa-marker', name: 'Marcador' },
            { class: 'fas fa-paperclip', name: 'Clip' },
            { class: 'fas fa-thumbtack', name: 'Chincheta' },
            { class: 'fas fa-sticky-note', name: 'Nota' },
            { class: 'fas fa-clipboard', name: 'Portapapeles' },
            { class: 'fas fa-clipboard-check', name: 'Verificado' },
            { class: 'fas fa-clipboard-list', name: 'Lista' },
        ],
        travel: [
            { class: 'fas fa-plane', name: 'Avión' },
            { class: 'fas fa-plane-departure', name: 'Despegue' },
            { class: 'fas fa-plane-arrival', name: 'Aterrizaje' },
            { class: 'fas fa-helicopter', name: 'Helicóptero' },
            { class: 'fas fa-car', name: 'Coche' },
            { class: 'fas fa-car-alt', name: 'Auto Alt' },
            { class: 'fas fa-car-side', name: 'Auto Lado' },
            { class: 'fas fa-taxi', name: 'Taxi' },
            { class: 'fas fa-bus', name: 'Bus' },
            { class: 'fas fa-bus-alt', name: 'Bus Alt' },
            { class: 'fas fa-shuttle-van', name: 'Van' },
            { class: 'fas fa-train', name: 'Tren' },
            { class: 'fas fa-subway', name: 'Metro' },
            { class: 'fas fa-tram', name: 'Tranvía' },
            { class: 'fas fa-bicycle', name: 'Bicicleta' },
            { class: 'fas fa-motorcycle', name: 'Moto' },
            { class: 'fas fa-ship', name: 'Barco' },
            { class: 'fas fa-anchor', name: 'Ancla' },
            { class: 'fas fa-route', name: 'Ruta' },
            { class: 'fas fa-road', name: 'Carretera' },
            { class: 'fas fa-map', name: 'Mapa' },
            { class: 'fas fa-map-marked', name: 'Mapa Marcado' },
            { class: 'fas fa-map-marked-alt', name: 'Mapa Pin' },
            { class: 'fas fa-map-marker', name: 'Marcador' },
            { class: 'fas fa-map-marker-alt', name: 'Ubicación' },
            { class: 'fas fa-map-pin', name: 'Pin' },
            { class: 'fas fa-map-signs', name: 'Señales' },
            { class: 'fas fa-compass', name: 'Brújula' },
            { class: 'fas fa-directions', name: 'Direcciones' },
            { class: 'fas fa-location-arrow', name: 'Flecha Ubic' },
            { class: 'fas fa-street-view', name: 'Street View' },
            { class: 'fas fa-hotel', name: 'Hotel' },
            { class: 'fas fa-bed', name: 'Cama' },
            { class: 'fas fa-concierge-bell', name: 'Conserje' },
            { class: 'fas fa-suitcase', name: 'Maleta' },
            { class: 'fas fa-suitcase-rolling', name: 'Maleta Ruedas' },
            { class: 'fas fa-passport', name: 'Pasaporte' },
            { class: 'fas fa-ticket-alt', name: 'Ticket' },
            { class: 'fas fa-luggage-cart', name: 'Carrito' },
            { class: 'fas fa-globe', name: 'Globo' },
            { class: 'fas fa-mountain', name: 'Montaña' },
            { class: 'fas fa-umbrella-beach', name: 'Playa' },
            { class: 'fas fa-campground', name: 'Camping' },
            { class: 'fas fa-caravan', name: 'Caravana' },
        ],
        nature: [
            { class: 'fas fa-sun', name: 'Sol' },
            { class: 'fas fa-moon', name: 'Luna' },
            { class: 'fas fa-star', name: 'Estrella' },
            { class: 'fas fa-cloud', name: 'Nube' },
            { class: 'fas fa-cloud-sun', name: 'Parcial' },
            { class: 'fas fa-cloud-moon', name: 'Noche Nublada' },
            { class: 'fas fa-cloud-rain', name: 'Lluvia' },
            { class: 'fas fa-cloud-showers-heavy', name: 'Tormenta' },
            { class: 'fas fa-bolt', name: 'Rayo' },
            { class: 'fas fa-snowflake', name: 'Nieve' },
            { class: 'fas fa-wind', name: 'Viento' },
            { class: 'fas fa-rainbow', name: 'Arcoíris' },
            { class: 'fas fa-temperature-high', name: 'Calor' },
            { class: 'fas fa-temperature-low', name: 'Frío' },
            { class: 'fas fa-thermometer', name: 'Termómetro' },
            { class: 'fas fa-smog', name: 'Smog' },
            { class: 'fas fa-fire', name: 'Fuego' },
            { class: 'fas fa-fire-alt', name: 'Llama' },
            { class: 'fas fa-water', name: 'Agua' },
            { class: 'fas fa-tint', name: 'Gota' },
            { class: 'fas fa-leaf', name: 'Hoja' },
            { class: 'fas fa-seedling', name: 'Brote' },
            { class: 'fas fa-tree', name: 'Árbol' },
            { class: 'fas fa-cannabis', name: 'Planta' },
            { class: 'fas fa-spa', name: 'Flor' },
            { class: 'fas fa-mountain', name: 'Montaña' },
            { class: 'fas fa-globe', name: 'Tierra' },
            { class: 'fas fa-globe-africa', name: 'África' },
            { class: 'fas fa-globe-americas', name: 'América' },
            { class: 'fas fa-globe-asia', name: 'Asia' },
            { class: 'fas fa-globe-europe', name: 'Europa' },
            { class: 'fas fa-paw', name: 'Huella' },
            { class: 'fas fa-dog', name: 'Perro' },
            { class: 'fas fa-cat', name: 'Gato' },
            { class: 'fas fa-horse', name: 'Caballo' },
            { class: 'fas fa-dove', name: 'Paloma' },
            { class: 'fas fa-crow', name: 'Cuervo' },
            { class: 'fas fa-kiwi-bird', name: 'Kiwi' },
            { class: 'fas fa-fish', name: 'Pez' },
            { class: 'fas fa-frog', name: 'Rana' },
            { class: 'fas fa-spider', name: 'Araña' },
            { class: 'fas fa-hippo', name: 'Hipopótamo' },
            { class: 'fas fa-otter', name: 'Nutria' },
            { class: 'fas fa-dragon', name: 'Dragón' },
            { class: 'fas fa-feather', name: 'Pluma' },
            { class: 'fas fa-feather-alt', name: 'Pluma Alt' },
        ],
        sports: [
            { class: 'fas fa-futbol', name: 'Fútbol' },
            { class: 'fas fa-basketball-ball', name: 'Baloncesto' },
            { class: 'fas fa-volleyball-ball', name: 'Voleibol' },
            { class: 'fas fa-football-ball', name: 'Fútbol Amer' },
            { class: 'fas fa-baseball-ball', name: 'Béisbol' },
            { class: 'fas fa-golf-ball', name: 'Golf' },
            { class: 'fas fa-bowling-ball', name: 'Bowling' },
            { class: 'fas fa-table-tennis', name: 'Ping Pong' },
            { class: 'fas fa-hockey-puck', name: 'Hockey' },
            { class: 'fas fa-running', name: 'Correr' },
            { class: 'fas fa-walking', name: 'Caminar' },
            { class: 'fas fa-biking', name: 'Ciclismo' },
            { class: 'fas fa-swimmer', name: 'Natación' },
            { class: 'fas fa-skiing', name: 'Esquí' },
            { class: 'fas fa-skiing-nordic', name: 'Esquí Nórdico' },
            { class: 'fas fa-snowboarding', name: 'Snowboard' },
            { class: 'fas fa-skating', name: 'Patinaje' },
            { class: 'fas fa-dumbbell', name: 'Pesas' },
            { class: 'fas fa-weight', name: 'Peso' },
            { class: 'fas fa-medal', name: 'Medalla' },
            { class: 'fas fa-trophy', name: 'Trofeo' },
            { class: 'fas fa-chess', name: 'Ajedrez' },
            { class: 'fas fa-chess-board', name: 'Tablero' },
            { class: 'fas fa-chess-pawn', name: 'Peón' },
            { class: 'fas fa-chess-rook', name: 'Torre' },
            { class: 'fas fa-chess-knight', name: 'Caballo' },
            { class: 'fas fa-chess-bishop', name: 'Alfil' },
            { class: 'fas fa-chess-queen', name: 'Reina' },
            { class: 'fas fa-chess-king', name: 'Rey' },
            { class: 'fas fa-dice', name: 'Dados' },
            { class: 'fas fa-dice-d20', name: 'D20' },
            { class: 'fas fa-gamepad', name: 'Gamepad' },
            { class: 'fas fa-puzzle-piece', name: 'Puzzle' },
        ],
        education: [
            { class: 'fas fa-graduation-cap', name: 'Graduación' },
            { class: 'fas fa-school', name: 'Escuela' },
            { class: 'fas fa-university', name: 'Universidad' },
            { class: 'fas fa-book', name: 'Libro' },
            { class: 'fas fa-book-open', name: 'Libro Abierto' },
            { class: 'fas fa-book-reader', name: 'Lector' },
            { class: 'fas fa-bookmark', name: 'Marcador' },
            { class: 'fas fa-atlas', name: 'Atlas' },
            { class: 'fas fa-bible', name: 'Biblia' },
            { class: 'fas fa-quran', name: 'Quran' },
            { class: 'fas fa-scroll', name: 'Pergamino' },
            { class: 'fas fa-pen', name: 'Pluma' },
            { class: 'fas fa-pencil-alt', name: 'Lápiz' },
            { class: 'fas fa-pen-fancy', name: 'Pluma Fancy' },
            { class: 'fas fa-pen-nib', name: 'Punta' },
            { class: 'fas fa-highlighter', name: 'Resaltador' },
            { class: 'fas fa-marker', name: 'Marcador' },
            { class: 'fas fa-eraser', name: 'Borrador' },
            { class: 'fas fa-ruler', name: 'Regla' },
            { class: 'fas fa-ruler-combined', name: 'Escuadra' },
            { class: 'fas fa-ruler-horizontal', name: 'Regla H' },
            { class: 'fas fa-ruler-vertical', name: 'Regla V' },
            { class: 'fas fa-drafting-compass', name: 'Compás' },
            { class: 'fas fa-calculator', name: 'Calculadora' },
            { class: 'fas fa-chalkboard', name: 'Pizarra' },
            { class: 'fas fa-chalkboard-teacher', name: 'Profesor' },
            { class: 'fas fa-user-graduate', name: 'Graduado' },
            { class: 'fas fa-lightbulb', name: 'Idea' },
            { class: 'fas fa-brain', name: 'Cerebro' },
            { class: 'fas fa-atom', name: 'Átomo' },
            { class: 'fas fa-microscope', name: 'Microscopio' },
            { class: 'fas fa-flask', name: 'Matraz' },
            { class: 'fas fa-vial', name: 'Tubo Ensayo' },
            { class: 'fas fa-vials', name: 'Tubos' },
            { class: 'fas fa-dna', name: 'ADN' },
            { class: 'fas fa-award', name: 'Premio' },
            { class: 'fas fa-certificate', name: 'Certificado' },
            { class: 'fas fa-diploma', name: 'Diploma' },
            { class: 'fas fa-globe', name: 'Globo' },
            { class: 'fas fa-language', name: 'Idioma' },
            { class: 'fas fa-spell-check', name: 'Ortografía' },
        ],
        security: [
            { class: 'fas fa-lock', name: 'Candado' },
            { class: 'fas fa-lock-open', name: 'Abierto' },
            { class: 'fas fa-unlock', name: 'Desbloquear' },
            { class: 'fas fa-unlock-alt', name: 'Desbloq Alt' },
            { class: 'fas fa-key', name: 'Llave' },
            { class: 'fas fa-shield-alt', name: 'Escudo' },
            { class: 'fas fa-shield-virus', name: 'Escudo Virus' },
            { class: 'fas fa-user-shield', name: 'Usuario Escudo' },
            { class: 'fas fa-user-lock', name: 'Usuario Bloq' },
            { class: 'fas fa-user-secret', name: 'Espía' },
            { class: 'fas fa-fingerprint', name: 'Huella' },
            { class: 'fas fa-eye', name: 'Ojo' },
            { class: 'fas fa-eye-slash', name: 'Ocultar' },
            { class: 'fas fa-mask', name: 'Máscara' },
            { class: 'fas fa-id-badge', name: 'Credencial' },
            { class: 'fas fa-id-card', name: 'Tarjeta ID' },
            { class: 'fas fa-id-card-alt', name: 'ID Alt' },
            { class: 'fas fa-passport', name: 'Pasaporte' },
            { class: 'fas fa-door-closed', name: 'Puerta Cerrada' },
            { class: 'fas fa-door-open', name: 'Puerta Abierta' },
            { class: 'fas fa-dungeon', name: 'Calabozo' },
            { class: 'fas fa-skull-crossbones', name: 'Peligro' },
            { class: 'fas fa-radiation', name: 'Radiación' },
            { class: 'fas fa-radiation-alt', name: 'Nuclear' },
            { class: 'fas fa-biohazard', name: 'Peligro Bio' },
            { class: 'fas fa-exclamation-triangle', name: 'Advertencia' },
            { class: 'fas fa-ban', name: 'Prohibido' },
            { class: 'fas fa-fire-extinguisher', name: 'Extintor' },
            { class: 'fas fa-hard-hat', name: 'Casco' },
            { class: 'fas fa-vest', name: 'Chaleco' },
            { class: 'fas fa-traffic-light', name: 'Semáforo' },
            { class: 'fas fa-video', name: 'Cámara Seg' },
            { class: 'fas fa-bell', name: 'Alarma' },
        ],
        social: [
            { class: 'fab fa-facebook', name: 'Facebook' },
            { class: 'fab fa-facebook-f', name: 'Facebook F' },
            { class: 'fab fa-facebook-messenger', name: 'Messenger' },
            { class: 'fab fa-twitter', name: 'Twitter' },
            { class: 'fab fa-x-twitter', name: 'X Twitter' },
            { class: 'fab fa-instagram', name: 'Instagram' },
            { class: 'fab fa-linkedin', name: 'LinkedIn' },
            { class: 'fab fa-linkedin-in', name: 'LinkedIn In' },
            { class: 'fab fa-youtube', name: 'YouTube' },
            { class: 'fab fa-tiktok', name: 'TikTok' },
            { class: 'fab fa-whatsapp', name: 'WhatsApp' },
            { class: 'fab fa-telegram', name: 'Telegram' },
            { class: 'fab fa-telegram-plane', name: 'Telegram Plane' },
            { class: 'fab fa-pinterest', name: 'Pinterest' },
            { class: 'fab fa-pinterest-p', name: 'Pinterest P' },
            { class: 'fab fa-snapchat', name: 'Snapchat' },
            { class: 'fab fa-snapchat-ghost', name: 'Snapchat Ghost' },
            { class: 'fab fa-reddit', name: 'Reddit' },
            { class: 'fab fa-reddit-alien', name: 'Reddit Alien' },
            { class: 'fab fa-tumblr', name: 'Tumblr' },
            { class: 'fab fa-discord', name: 'Discord' },
            { class: 'fab fa-slack', name: 'Slack' },
            { class: 'fab fa-slack-hash', name: 'Slack Hash' },
            { class: 'fab fa-skype', name: 'Skype' },
            { class: 'fab fa-viber', name: 'Viber' },
            { class: 'fab fa-line', name: 'Line' },
            { class: 'fab fa-wechat', name: 'WeChat' },
            { class: 'fab fa-weibo', name: 'Weibo' },
            { class: 'fab fa-vk', name: 'VK' },
            { class: 'fab fa-twitch', name: 'Twitch' },
            { class: 'fab fa-medium', name: 'Medium' },
            { class: 'fab fa-medium-m', name: 'Medium M' },
            { class: 'fab fa-quora', name: 'Quora' },
            { class: 'fab fa-flickr', name: 'Flickr' },
            { class: 'fab fa-vimeo', name: 'Vimeo' },
            { class: 'fab fa-vimeo-v', name: 'Vimeo V' },
            { class: 'fab fa-soundcloud', name: 'SoundCloud' },
            { class: 'fab fa-spotify', name: 'Spotify' },
            { class: 'fab fa-mixcloud', name: 'Mixcloud' },
            { class: 'fab fa-behance', name: 'Behance' },
            { class: 'fab fa-dribbble', name: 'Dribbble' },
            { class: 'fab fa-deviantart', name: 'DeviantArt' },
        ],
        brands: [
            { class: 'fab fa-google', name: 'Google' },
            { class: 'fab fa-google-drive', name: 'Drive' },
            { class: 'fab fa-google-play', name: 'Play Store' },
            { class: 'fab fa-apple', name: 'Apple' },
            { class: 'fab fa-app-store', name: 'App Store' },
            { class: 'fab fa-app-store-ios', name: 'iOS Store' },
            { class: 'fab fa-android', name: 'Android' },
            { class: 'fab fa-windows', name: 'Windows' },
            { class: 'fab fa-microsoft', name: 'Microsoft' },
            { class: 'fab fa-amazon', name: 'Amazon' },
            { class: 'fab fa-aws', name: 'AWS' },
            { class: 'fab fa-paypal', name: 'PayPal' },
            { class: 'fab fa-stripe', name: 'Stripe' },
            { class: 'fab fa-stripe-s', name: 'Stripe S' },
            { class: 'fab fa-cc-visa', name: 'Visa' },
            { class: 'fab fa-cc-mastercard', name: 'Mastercard' },
            { class: 'fab fa-cc-amex', name: 'Amex' },
            { class: 'fab fa-cc-paypal', name: 'CC PayPal' },
            { class: 'fab fa-cc-stripe', name: 'CC Stripe' },
            { class: 'fab fa-cc-apple-pay', name: 'Apple Pay' },
            { class: 'fab fa-bitcoin', name: 'Bitcoin' },
            { class: 'fab fa-btc', name: 'BTC' },
            { class: 'fab fa-ethereum', name: 'Ethereum' },
            { class: 'fab fa-github', name: 'GitHub' },
            { class: 'fab fa-github-alt', name: 'GitHub Alt' },
            { class: 'fab fa-gitlab', name: 'GitLab' },
            { class: 'fab fa-bitbucket', name: 'Bitbucket' },
            { class: 'fab fa-jira', name: 'Jira' },
            { class: 'fab fa-trello', name: 'Trello' },
            { class: 'fab fa-confluence', name: 'Confluence' },
            { class: 'fab fa-docker', name: 'Docker' },
            { class: 'fab fa-jenkins', name: 'Jenkins' },
            { class: 'fab fa-wordpress', name: 'WordPress' },
            { class: 'fab fa-wordpress-simple', name: 'WP Simple' },
            { class: 'fab fa-shopify', name: 'Shopify' },
            { class: 'fab fa-magento', name: 'Magento' },
            { class: 'fab fa-wix', name: 'Wix' },
            { class: 'fab fa-squarespace', name: 'Squarespace' },
            { class: 'fab fa-weebly', name: 'Weebly' },
            { class: 'fab fa-mailchimp', name: 'Mailchimp' },
            { class: 'fab fa-hubspot', name: 'HubSpot' },
            { class: 'fab fa-salesforce', name: 'Salesforce' },
            { class: 'fab fa-dropbox', name: 'Dropbox' },
            { class: 'fab fa-html5', name: 'HTML5' },
            { class: 'fab fa-css3', name: 'CSS3' },
            { class: 'fab fa-css3-alt', name: 'CSS3 Alt' },
            { class: 'fab fa-js', name: 'JavaScript' },
            { class: 'fab fa-js-square', name: 'JS Square' },
            { class: 'fab fa-node', name: 'Node' },
            { class: 'fab fa-node-js', name: 'Node.js' },
            { class: 'fab fa-npm', name: 'NPM' },
            { class: 'fab fa-react', name: 'React' },
            { class: 'fab fa-vuejs', name: 'Vue.js' },
            { class: 'fab fa-angular', name: 'Angular' },
            { class: 'fab fa-sass', name: 'Sass' },
            { class: 'fab fa-less', name: 'Less' },
            { class: 'fab fa-bootstrap', name: 'Bootstrap' },
            { class: 'fab fa-php', name: 'PHP' },
            { class: 'fab fa-laravel', name: 'Laravel' },
            { class: 'fab fa-symfony', name: 'Symfony' },
            { class: 'fab fa-python', name: 'Python' },
            { class: 'fab fa-java', name: 'Java' },
            { class: 'fab fa-swift', name: 'Swift' },
            { class: 'fab fa-rust', name: 'Rust' },
            { class: 'fab fa-linux', name: 'Linux' },
            { class: 'fab fa-ubuntu', name: 'Ubuntu' },
            { class: 'fab fa-redhat', name: 'Red Hat' },
            { class: 'fab fa-centos', name: 'CentOS' },
            { class: 'fab fa-fedora', name: 'Fedora' },
            { class: 'fab fa-suse', name: 'SUSE' },
            { class: 'fab fa-stack-overflow', name: 'Stack Overflow' },
            { class: 'fab fa-figma', name: 'Figma' },
            { class: 'fab fa-sketch', name: 'Sketch' },
            { class: 'fab fa-invision', name: 'InVision' },
            { class: 'fab fa-adobe', name: 'Adobe' },
            { class: 'fab fa-chrome', name: 'Chrome' },
            { class: 'fab fa-firefox', name: 'Firefox' },
            { class: 'fab fa-firefox-browser', name: 'Firefox B' },
            { class: 'fab fa-safari', name: 'Safari' },
            { class: 'fab fa-edge', name: 'Edge' },
            { class: 'fab fa-opera', name: 'Opera' },
            { class: 'fab fa-internet-explorer', name: 'IE' },
            { class: 'fab fa-bluetooth', name: 'Bluetooth' },
            { class: 'fab fa-bluetooth-b', name: 'Bluetooth B' },
            { class: 'fab fa-usb', name: 'USB' },
            { class: 'fab fa-xbox', name: 'Xbox' },
            { class: 'fab fa-playstation', name: 'PlayStation' },
            { class: 'fab fa-steam', name: 'Steam' },
            { class: 'fab fa-steam-symbol', name: 'Steam Symbol' },
            { class: 'fab fa-nintendo-switch', name: 'Nintendo' },
        ]
    };

    let currentCallback = null;
    let currentInput = null;
    let currentInputId = null;

    window.iconPicker = {
        open: function(callback, inputElement) {
            currentCallback = callback;
            currentInput = inputElement;
            currentInputId = inputElement ? inputElement.id : null;
            this.render('all');
            document.getElementById('iconPickerModal').style.display = 'flex';
            document.getElementById('iconPickerSearch').value = '';
            document.getElementById('iconPickerSearch').focus();
        },

        close: function() {
            document.getElementById('iconPickerModal').style.display = 'none';
            currentCallback = null;
            currentInput = null;
            currentInputId = null;
        },

        render: function(category, search = '') {
            const grid = document.getElementById('iconPickerGrid');
            grid.innerHTML = '';

            let icons = [];
            if (category === 'all') {
                Object.values(iconLibrary).forEach(cat => icons = icons.concat(cat));
            } else if (iconLibrary[category]) {
                icons = iconLibrary[category];
            }

            // Filter by search
            if (search) {
                const searchLower = search.toLowerCase();
                icons = icons.filter(icon =>
                    icon.name.toLowerCase().includes(searchLower) ||
                    icon.class.toLowerCase().includes(searchLower)
                );
            }

            // Update count
            document.getElementById('iconCount').textContent = icons.length + ' iconos';

            const self = this;
            icons.forEach(icon => {
                const item = document.createElement('div');
                item.className = 'icon-picker-item';
                item.innerHTML = `<i class="${icon.class}"></i><span>${icon.name}</span>`;
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const selectedIcon = icon.class;

                    // Set value via callback first
                    if (currentCallback) {
                        currentCallback(selectedIcon);
                    }

                    // Get the input element - prefer getElementById for reliability
                    let targetInput = null;
                    if (currentInputId) {
                        targetInput = document.getElementById(currentInputId);
                    }
                    if (!targetInput && currentInput) {
                        targetInput = currentInput;
                    }

                    // Set value on input
                    if (targetInput) {
                        targetInput.value = selectedIcon;
                        targetInput.setAttribute('value', selectedIcon);
                        targetInput.dispatchEvent(new Event('change', { bubbles: true }));
                        targetInput.dispatchEvent(new Event('input', { bubbles: true }));

                        // Update preview if exists
                        const wrapper = targetInput.closest('.icon-input-wrapper');
                        if (wrapper) {
                            const preview = wrapper.querySelector('.icon-input-preview i');
                            if (preview) {
                                preview.className = selectedIcon;
                            }
                        }
                    }

                    // Close modal
                    self.close();
                });
                grid.appendChild(item);
            });

            if (icons.length === 0) {
                grid.innerHTML = '<div style="grid-column: 1/-1; text-align:center; padding: 40px; color: #999;">No se encontraron iconos</div>';
            }
        },

        bindInputs: function() {
            document.querySelectorAll('.icon-input-btn').forEach(btn => {
                // Skip if already bound or has onclick handler
                if (btn.dataset.bound || btn.hasAttribute('onclick')) return;
                btn.dataset.bound = 'true';

                btn.addEventListener('click', () => {
                    const wrapper = btn.closest('.icon-input-wrapper');
                    const input = wrapper ? wrapper.querySelector('input[data-item-field="icon"], input[data-slide-field="icon"], input[data-content="icon"]') : null;
                    this.open(null, input);
                });
            });
        }
    };

    // Initialize event listeners
    document.addEventListener('DOMContentLoaded', function() {
        // Close button
        document.querySelector('.icon-picker-close')?.addEventListener('click', () => iconPicker.close());
        document.querySelector('.icon-picker-overlay')?.addEventListener('click', () => iconPicker.close());

        // Category buttons
        document.querySelectorAll('.icon-category').forEach(btn => {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.icon-category').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                iconPicker.render(this.dataset.category, document.getElementById('iconPickerSearch').value);
            });
        });

        // Search input
        document.getElementById('iconPickerSearch')?.addEventListener('input', function() {
            const activeCategory = document.querySelector('.icon-category.active')?.dataset.category || 'all';
            iconPicker.render(activeCategory, this.value);
        });

        // ESC to close
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && document.getElementById('iconPickerModal').style.display !== 'none') {
                iconPicker.close();
            }
        });

        // Bind initial inputs
        iconPicker.bindInputs();
    });
})();
</script>
