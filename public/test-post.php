<?php
echo "<h1>Test POST</h1>";
echo "<p>REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD'] . "</p>";
echo "<p>REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "</p>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<p style='color:green'>POST recibido correctamente!</p>";
    echo "<pre>" . print_r($_POST, true) . "</pre>";
} else {
    echo "<h2>Test 1: POST a esta misma página</h2>";
    echo "<form method='POST' action=''>
        <input type='text' name='test' value='hola'>
        <button type='submit'>Enviar POST aquí</button>
    </form>";

    echo "<h2>Test 2: POST a /admin/do-login (nueva ruta)</h2>";
    echo "<form method='POST' action='/admin/do-login'>
        <input type='email' name='email' value='admin@thelastofsaas.es'>
        <input type='password' name='password' value='admin123'>
        <input type='hidden' name='_csrf_token' value='test'>
        <button type='submit'>Enviar POST a /admin/do-login</button>
    </form>";
}
