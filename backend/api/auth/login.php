<?php
// CORS headers - set only once
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Max-Age: 3600');
header('Content-Type: application/json; charset=UTF-8');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once '../../config/database.php';
require_once '../../classes/User.php';

$data = json_decode(file_get_contents("php://input"));

if(!empty($data->username) && !empty($data->password)) {

    try {
        $database = new Database();
        $db = $database->getConnection();
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(array(
            "message" => "Erreur de connexion au serveur.",
            "error" => $e->getMessage()
        ));
        exit();
    }

    $user = new User($db);

    $user->login = $data->username;
    $user->mdp = $data->password;

    if($user->authenticate()) {
        
        http_response_code(200);

        $token = base64_encode($user->login . ':' . time());

        echo json_encode(array(
            "message" => "Connexion réussie.",
            "user" => array(
                "id" => $user->id,
                "username" => $user->login,
                "name" => $user->nom,
                "role" => $user->profil
            ),
          
        ));
    }
    else {
        http_response_code(401);

        echo json_encode(array("message" => "Échec de connexion. Identifiants invalides."));
    }
}
else {
    http_response_code(400);

    echo json_encode(array("message" => "Impossible de se connecter. Données incomplètes."));
}
?>
