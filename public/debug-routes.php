<?php
/**
 * Debug script to check routes registration
 * DELETE THIS FILE AFTER DEBUGGING
 */

// Only allow access with a secret key
if (($_GET['key'] ?? '') !== 'tlos2026debug') {
    die('Access denied');
}

define('ROOT_PATH', dirname(__DIR__));
define('PUBLIC_PATH', __DIR__);
define('CONFIG_PATH', ROOT_PATH . '/config');
define('STORAGE_PATH', ROOT_PATH . '/storage');
define('TEMPLATES_PATH', ROOT_PATH . '/templates');

require ROOT_PATH . '/vendor/autoload.php';

echo "<h1>TLOS Routes Debug</h1>";

// Check if routes.php exists
$routesFile = ROOT_PATH . '/config/routes.php';
echo "<h2>1. Routes file check</h2>";
echo "File exists: " . (file_exists($routesFile) ? 'YES' : 'NO') . "<br>";
echo "File size: " . filesize($routesFile) . " bytes<br>";
echo "Last modified: " . date('Y-m-d H:i:s', filemtime($routesFile)) . "<br>";

// Check for rooms/activities in routes.php
echo "<h2>2. Routes content check</h2>";
$content = file_get_contents($routesFile);
echo "Contains '/admin/rooms': " . (strpos($content, "'/admin/rooms'") !== false ? 'YES' : 'NO') . "<br>";
echo "Contains '/admin/activities': " . (strpos($content, "'/admin/activities'") !== false ? 'YES' : 'NO') . "<br>";
echo "Contains 'RoomsController': " . (strpos($content, 'RoomsController') !== false ? 'YES' : 'NO') . "<br>";
echo "Contains 'ActivitiesController': " . (strpos($content, 'ActivitiesController') !== false ? 'YES' : 'NO') . "<br>";

// Check if controller files exist
echo "<h2>3. Controller files check</h2>";
$roomsController = ROOT_PATH . '/src/Controllers/Admin/RoomsController.php';
$activitiesController = ROOT_PATH . '/src/Controllers/Admin/ActivitiesController.php';
echo "RoomsController exists: " . (file_exists($roomsController) ? 'YES' : 'NO') . "<br>";
echo "ActivitiesController exists: " . (file_exists($activitiesController) ? 'YES' : 'NO') . "<br>";

// Check if classes can be loaded
echo "<h2>4. Class loading check</h2>";
try {
    echo "Loading RoomsController... ";
    class_exists('App\\Controllers\\Admin\\RoomsController');
    echo "OK<br>";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "<br>";
}

try {
    echo "Loading ActivitiesController... ";
    class_exists('App\\Controllers\\Admin\\ActivitiesController');
    echo "OK<br>";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "<br>";
}

// Check models
echo "<h2>5. Model files check</h2>";
$roomModel = ROOT_PATH . '/src/Models/Room.php';
$activityModel = ROOT_PATH . '/src/Models/Activity.php';
echo "Room model exists: " . (file_exists($roomModel) ? 'YES' : 'NO') . "<br>";
echo "Activity model exists: " . (file_exists($activityModel) ? 'YES' : 'NO') . "<br>";

// Check templates
echo "<h2>6. Template files check</h2>";
$roomsIndex = ROOT_PATH . '/templates/admin/rooms/index.php';
$activitiesIndex = ROOT_PATH . '/templates/admin/activities/index.php';
echo "rooms/index.php exists: " . (file_exists($roomsIndex) ? 'YES' : 'NO') . "<br>";
echo "activities/index.php exists: " . (file_exists($activitiesIndex) ? 'YES' : 'NO') . "<br>";

// Test router
echo "<h2>7. Router test</h2>";
try {
    $router = new \App\Core\Router();
    require ROOT_PATH . '/config/routes.php';

    // Use reflection to check registered routes
    $reflection = new ReflectionClass($router);
    $routesProperty = $reflection->getProperty('routes');
    $routesProperty->setAccessible(true);
    $routes = $routesProperty->getValue($router);

    echo "Total routes registered: " . count($routes) . "<br><br>";

    // Find admin/rooms and admin/activities routes
    echo "<strong>Searching for rooms/activities routes:</strong><br>";
    foreach ($routes as $route) {
        if (strpos($route['path'], '/admin/rooms') === 0 || strpos($route['path'], '/admin/activities') === 0) {
            echo "- [{$route['method']}] {$route['path']} => {$route['controller']}::{$route['action']}<br>";
        }
    }
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "<br>";
    echo "Stack trace: <pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<br><br><strong style='color:red'>DELETE THIS FILE AFTER DEBUGGING!</strong>";
