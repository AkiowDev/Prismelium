```php
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../Connexion/connexionModel.php';
require_once __DIR__ . '/experimentationParticipantModel.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || (int) $_SESSION['user_role'] !== 3) {
    header('Location: ../../Connexion/connexionController.php');
    exit();
}
$current_user_id = (int) $_SESSION['user_id'];


$exp_id = $_POST['id_exp'] ?? $_GET['id'] ?? null;
$validExpId = filter_var($exp_id, FILTER_VALIDATE_INT);

if ($validExpId === false || $validExpId === null || $validExpId <= 0) {
    header('Location: ../experimentationsParticipant/experimentationsParticipantController.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'inscription') {

    $statut_insc = get_statutinsc_by_user_exp($current_user_id, $validExpId);

    if ($statut_insc === false || $statut_insc === null) {
        insert_insc($current_user_id, $validExpId);
    }

    header('Location: experimentationParticipantController.php?id=' . urlencode((string) $validExpId));
    exit();
}

$expe = get_exp_by_id($validExpId);

if (!$expe) {
    header('Location: ../experimentationsParticipant/experimentationsParticipantController.php');
    exit();
}

$statut_insc = get_statutinsc_by_user_exp($current_user_id, $validExpId);

if ($statut_insc === false || $statut_insc === null) {
    $statut_insc = "Non";
}

require_once __DIR__ . '/experimentationParticipantView.php';
?>
```