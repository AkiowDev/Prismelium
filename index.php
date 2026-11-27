<?php
declare(strict_types=1);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/initialisation.php';

$page = is_string($_GET['page'] ?? null) ? $_GET['page'] : 'connexion';

switch ($page) {
    case 'connexion':
        (new \Prismelium\Controllers\ConnexionController())->login();
        break;
    case 'enseignant-chercheur':
        (new \Prismelium\Controllers\TableauDeBordControleur())->index();
        break;
    case 'experimentations':
        (new \Prismelium\Controllers\ExperimentationControleur())->index();
        break;
    case 'experimentations-tableau':
        (new \Prismelium\Controllers\ExperimentationControleur())->dashboard();
        break;
    case 'experimentations-creer':
        (new \Prismelium\Controllers\ExperimentationControleur())->create();
        break;
    case 'experimentations-modifier':
        (new \Prismelium\Controllers\ExperimentationControleur())->edit();
        break;
    case 'inscriptions':
        (new \Prismelium\Controllers\InscriptionControleur())->index();
        break;
    case 'retributions':
        (new \Prismelium\Controllers\GestionRetributionsControleur())->index();
        break;
    default:
        http_response_code(404);
        echo 'Page introuvable.';
}
?>


