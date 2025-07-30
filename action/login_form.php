<?php

session_start();

require_once __DIR__ . '/../inc/func.php';
require_once __DIR__. '/../vendor/autoload.php';

// load .env
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../');
$dotenv->load();

$arrayAccountAllowed = array('sa', 'cu', 'ca');

$account_type = htmlspecialchars($_POST['account_type']);
$password = htmlspecialchars($_POST['password']);

if (!in_array($account_type, $arrayAccountAllowed)) {
    echo "Vous n'avez pas le droit de vous connecter sur ce site. (Err:1)";
    die;
}

switch ($account_type) {
    case 'sa':
        $passwordFromENV = $_ENV['super_admin_mdp'];
        break;
    case 'cu':
        $passwordFromENV = $_ENV['cuisinier_mdp'];
        break;
    case 'ca':
        $passwordFromENV = $_ENV['caissier_mdp'];
        break;
    default:
        echo "Vous n'avez pas le droit de vous connecter sur ce site. (Err:2)";
        break;
}

if(password_verify($password, $passwordFromENV)) {
    session_start();
    $_SESSION['account_type'] = $account_type;
    $password = encryptValue($password);
    $_SESSION['acc_pss'] = $password;
    header('Location: ../index.php');
} else {
    echo "Vous n'avez pas le droit de vous connecter sur ce site. (Err:3)";
}
?>