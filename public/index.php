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
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function getUser($response) {
    $name = $response->getAttribute('name');
    $sql = "SELECT * FROM USERS WHERE use_email='".$name."'";
    try {
        $stm = getConnection()->query($sql);
        $user = $stm->fetchAll(PDO::FETCH_OBJ);
        
        return json_encode($user);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function postUser($request) {
    $user = json_decode($request->getBody());

    $sql = "INSERT INTO USERS (use_name, use_email, use_password, use_signup_date)
            VALUES (:name, :email, SHA1(:password), DATE(NOW()))";
    try {
        $db = getConnection();
        $stm = $db->prepare($sql);
        $stm->bindParam("name", $user->name);
        $stm->bindParam("email", $user->email);
        $stm->bindParam("password", $user->password);
        $stm->execute();
        return $db->lastInsertId();
    } catch (PDOException $e) {
        return json_encode($e);
    }
}

function changePassword($request) {
    $name = $request->getAttribute('name');
    $user = json_decode($request->getBody());

    $sql = "UPDATE USERS SET use_password=SHA1(:password) WHERE use_name='".$name."'";
    try {
        $db = getConnection();
        $stm = $db->prepare($sql);
        $stm->bindParam("password", $user->password);
        $stm->execute();
        return '{"result":"200"}';
    } catch (PDOExpeption $e) {
        return '{"result":'.$e.'}';
    }
}

function getUniverses($response) {
    $sql = "SELECT * FROM UNIVERSES ORDER BY uni_name";
    try {
        $stm = getConnection()->query($sql);
        $universes = $stm->fetchAll(PDO::FETCH_OBJ);
        
        return json_encode($universes);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function getUniverseByName($response) {
    $name = $response->getAttribute('name');
    $sql = "SELECT * FROM UNIVERSES WHERE uni_name='".$name."'";
    try {
        $stm = getConnection()->query($sql);
        $universe = $stm->fetchAll(PDO::FETCH_OBJ);
        
        return json_encode($universe);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function getUniverseById($response) {
    $name = $response->getAttribute('id');
    $sql = "SELECT * FROM UNIVERSES WHERE uni_id='".$name."'";
    try {
        $stm = getConnection()->query($sql);
        $universe = $stm->fetchAll(PDO::FETCH_OBJ);
        
        return json_encode($universe);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function postUniverse($request) {
    $uni = json_decode($request->getBody());
    $sql = "INSERT INTO UNIVERSES (uni_name, uni_image, uni_description)
        VALUES(:name, 'img', 'desc')";
    try {
        $db = getConnection();
        $stm = $db->prepare($sql);
        $stm->bindParam("name", $uni->name);
        $stm->execute();
        return $db->lastInsertId();
    } catch (PDOExpeption $e) {
        echo 'error -> '.$e->getMessage();
    }
}

function getCharacters($response) {
    $sql = "SELECT * FROM CHARACTERS";

    try {
        $stm = getConnection()->query($sql);
        $characters = $stm->fetchAll(PDO::FETCH_OBJ);
        
        return json_encode($characters);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function getCharactersByUser($response) {
    $id = $response->getAttribute('id');
    $sql = "SELECT * FROM CHARACTERS WHERE cha_id_user=".$id." ORDER BY cha_name";

    try {
        $stm = getConnection()->query($sql);
        $characters = $stm->fetchAll(PDO::FETCH_ASSOC);
        
        return json_encode($characters);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function getCharacter($response) {
    $id = $response->getAttribute('id');
    $sql = "SELECT * FROM CHARACTERS WHERE cha_id=".$id." ORDER BY cha_name";

    try {
        $stm = getConnection()->query($sql);
        $characters = $stm->fetchAll(PDO::FETCH_OBJ);
        
        return json_encode($characters);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
}

function postCharacter($request) {
    $char = json_decode($request->getBody());
    $sql = "INSERT INTO CHARACTERS (cha_name, cha_avatar, cha_biography, cha_index_card, cha_id_universe, cha_id_user)
        VALUES(:name, :avatar, :biography, :index_card, :id_universe, :id_user)";
    try {
        $db = getConnection();
        $stm = $db->prepare($sql);
        $stm->bindParam("name", $char->name);
        $stm->bindParam("avatar", $char->avatar);
        $stm->bindParam("biography", $char->biography);
        $stm->bindParam("index_card", $char->index_card);
        $stm->bindParam("id_universe", $char->id_universe);
        $stm->bindParam("id_user", $char->id_user);
        $stm->execute();
        return $db->lastInsertId();
    } catch (PDOException $e) {
        echo 'error -> '.$e->getMessage();
    }
}

function deleteCharacter($request) {
    $id = $request->params('id');
    $sql = "DELETE FROM CHARACTERS WHERE cha_id=".$id;
    try {
        $db = getConnection();
        $stm = $db->prepare($sql);
        $stm->execute();
        return '200';
    } catch(PDOException $e) {
        return $e;
    }
}

function patchCharacter($request) {
    $cha = json_decode($request->getBody());
    $id = $request->params('id');
    $sql = "UPDATE CHARACTERS WHERE cha_id=".$id."
        SET cha_name=:name, cha_avatar=:avatar, cha_biography=:biography,
        cha_index_card=:index_card";

    try {
        $db = getConnection();
        $stm = $db->prepare($sql);
        $stm->bindParam("name", $char->name);
        $stm->bindParam("avatar", $char->avatar);
        $stm->bindParam("index_card", $char->index_card);
        $stm->execute();
        return '200';
    } catch (PDOException $e) {
        return $e;
    }
}

function getRoleLines($response) {
    $sql = "SELECT * FROM ROLE_LINES ORDER BY rol_last_update_date DESC";
    try {
        $stm = getConnection()->query($sql);
        $rolelines = $stm->fetchAll(PDO::FETCH_OBJ);
        
        return json_encode($rolelines);
    } catch (PDOException $e) {
        return $e;
    }
}

function getRoleLinesByCharacter($response) {
    $id = $response->params('id');
    $sql = "SELECT * FROM ROLE_LINES WHERE rol_id=ANY(
        SELECT rol_cha_id_rol FROM ROLE_CHARACTER WHERE rol_cha_id_char=".$id.")";
    try {
        $stm = getConnection()->query($sql);
        $res = $stm->fetchAll(PDO::FETCH_OBJ);

        return json_encode($res);
    } catch (PDOException $e) {
        return $e;
    }
}

function getRoleLinesByUser($response) {
    $id = $response->getAttribute('id');
    $sql = "SELECT * FROM role_lines WHERE role_lines.rol_id = 
	ANY(SELECT role_character.rol_cha_id_rol FROM role_character WHERE role_character.rol_cha_id_char = 
     	ANY(SELECT characters.cha_id FROM characters WHERE characters.cha_id_user = ".$id.")
     )";

     try {
        $stm = getConnection()->query($sql);
        $res = $stm->fetchAll(PDO::FETCH_OBJ);

        return json_encode($res);
    } catch (PDOException $e) {
        return $e;
    }
}

function getRoleLinesByRegex($response) {
    $cad = $response->getAttribute('regex');
    $sql = "SELECT * FROM `role_lines` WHERE rol_title REGEXP '".$cad."' ORDER BY rol_last_update_date DESC";
    try {
        $stm = getConnection()->query($sql);
        $res = $stm->fetchAll(PDO::FETCH_OBJ);

        return json_encode($res);
    } catch (PDOException $e) {
        return $e;
    }
}

function getRoleLinesByMaster($response) {
    $id = $response->params('id');
    $sql = "SELECT * FROM ROLE_LINES WHERE rol_id_master=".$id;
    try {
        $stm = getConnection()->query($sql);
        $res = $stm->fetchAll(PDO::FETCH_OBJ);

        return json_encode($res);
    } catch (PDOException $e) {
        return $e;
    }
}

function postRoleLine($request) {
    $role = json_decode($request->getBody());
    $sql = "INSERT INTO ROLE_LINES(rol_title, rol_creation_date, rol_last_update_date, rol_id_master, rol_id_universe, rol_description)
        VALUES(:title, DATE(NOW()), DATE(NOW()), :master_id, :universe_id, :rol_desc)";
    try {
        $db = getConnection();
        $stm = $db->prepare($sql);
        $stm->bindParam("title", $role->title);
        $stm->bindParam("master_id", $role->master_id);
        $stm->bindParam("universe_id", $role->universe_id);
        $stm->bindParam("rol_desc", $role->rol_desc);
        $stm->execute();

        return $db->lastInsertId();
    } catch (PDOException $e) {
        return 'error -> '.$e->getMessage();
    }
}

function postCharRoleLine($request) {
    $rolchar = json_decode($request->getBody());
    $sql = "INSERT INTO ROLE_CHARACTER(rol_cha_id_rol, rol_cha_id_char)
    VALUES(:id_rol, :id_char)";
    try {
        $db = getConnection();
        $stm = $db->prepare($sql);
        $stm->bindParam("id_rol", $rolchar->id_rol);
        $stm->bindParam("id_char", $rolchar->id_char);
        $stm->execute();

        return $db->lastInsertId();
    } catch (PDOException $e) {
        return 'error -> '.$e->getMessage();
    }
}