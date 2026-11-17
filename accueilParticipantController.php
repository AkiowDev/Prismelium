<?php

include "connexionController.php";


if (!isset($_SESSION['user_id'])) {
    header('Location: connexionController.php');
    exit();
}

$current_user_id = $_SESSION['user_id'];
$current_user_mail = $_SESSION['user_mail'];

require_once 'connexionModel.php';
$user = get_user_by_mail($current_user_mail);

?>