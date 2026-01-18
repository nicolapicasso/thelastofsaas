<?php
echo "<h1>Test POST</h1>";
echo "<p>REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD'] . "</p>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<p style='color:green'>POST recibido correctamente!</p>";
    echo "<pre>" . print_r($_POST, true) . "</pre>";
} else {
    echo "<form method='POST' action=''>
        <input type='text' name='test' value='hola'>
        <button type='submit'>Enviar POST</button>
    </form>";
}
