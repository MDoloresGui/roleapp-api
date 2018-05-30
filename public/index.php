<?php
if (PHP_SAPI == 'cli-server') {
    // To help the built-in PHP dev server, check if the request was actually for
    // something which should probably be served as a static file
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) {
        return false;
    }
}

require __DIR__ . '/../vendor/autoload.php';

session_start();

// Instantiate the app
$settings = require __DIR__ . '/../src/settings.php';
$app = new \Slim\App($settings);

// Set up dependencies
require __DIR__ . '/../src/dependencies.php';

// Register middleware
require __DIR__ . '/../src/middleware.php';

// Register routes
require __DIR__ . '/../src/routes.php';

// Run app
$app->run();

function getConnection() {
    $dbhost="localhost";
    $dbuser="root";
    $dbpass="";
    $dbname="rol_app_db";
    $dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $dbh;
}

function getUsers($response) {
    $sql = "SELECT * FROM USERS";
    try {
        $stm = getConnection()->query($sql);
        $users = $stm->fetchAll(PDO::FETCH_OBJ);
        
        return json_encode($users);
    } catch(PDOExeption $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function getUser($request) {
    $name = $request->getAttribute('name');
    $sql = "SELECT * FROM USERS WHERE use_name='".$name."'";
    try {
        $stm = getConnection()->query($sql);
        $user = $stm->fetchAll(PDO::FETCH_OBJ);
        
        return json_encode($user);
    } catch(PDOExeption $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}
