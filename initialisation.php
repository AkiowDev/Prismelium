<?php
declare(strict_types=1);

namespace {
    require_once __DIR__ . '/load_database.php';

    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    $projectRoot = realpath(__DIR__);
    $documentRoot = realpath((string) ($_SERVER['DOCUMENT_ROOT'] ?? ''));
    $projectPath = $projectRoot !== false && $documentRoot !== false
        ? str_replace('\\', '/', substr($projectRoot, strlen($documentRoot)))
        : '';
    $projectPath = '/' . trim($projectPath, '/');
    if ($projectPath === '/') {
        $projectPath = '';
    }

    if (!defined('APP_BASE_URL')) {
        define('APP_BASE_URL', $projectPath);
    }

    spl_autoload_register(static function (string $class): void {
        $prefix = 'Prismelium\\';
        if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
            return;
        }

        if ($class === 'Prismelium\\Controllers\\ConnexionController') {
            $file = __DIR__ . '/Connexion/connexionController.php';
        } elseif ($class === 'Prismelium\\Models\\ConnexionModel') {
            $file = __DIR__ . '/Connexion/connexionModel.php';
        } else {
            $relativeClass = substr($class, strlen($prefix));
            $file = __DIR__ . '/Enseignant-Chercheur/' . str_replace('\\', '/', $relativeClass) . '.php';
        }

        if (is_file($file)) {
            require_once $file;
        }
    });

    function app_url(string $path): string
    {
        return APP_BASE_URL . '/' . ltrim($path, '/');
    }

    function csrf_token(): string
    {
        $token = $_SESSION['csrf_token'] ?? null;
        if (!is_string($token) || strlen($token) !== 64) {
            $token = bin2hex(random_bytes(32));
            $_SESSION['csrf_token'] = $token;
        }

        return $token;
    }

    function csrf_is_valid(mixed $token): bool
    {
        return is_string($token)
            && isset($_SESSION['csrf_token'])
            && is_string($_SESSION['csrf_token'])
            && hash_equals($_SESSION['csrf_token'], $token);
    }
}

namespace Prismelium {
    abstract class Controleur
    {
        protected function render(string $template, array $data = []): void
        {
            $templatePath = $template === 'connexionView'
                ? __DIR__ . '/Connexion/connexionView.php'
                : __DIR__ . '/Enseignant-chercheur/Views/' . $template . '.php';
            if (!is_file($templatePath)) {
                throw new \RuntimeException('View not found: ' . $template);
            }

            extract($data, EXTR_SKIP);
            require $templatePath;
        }

        protected function redirect(string $path): never
        {
            header('Location: ' . \app_url($path));
            exit;
        }

        protected function escape(mixed $value): string
        {
            return htmlspecialchars(
                is_scalar($value) ? (string) $value : '',
                ENT_QUOTES | ENT_SUBSTITUTE,
                'UTF-8'
            );
        }
    }

    final class GardeEnseignantChercheur
    {
        public static function requireResearcher(): array
        {
            $userId = filter_var($_SESSION['user_id'] ?? null, FILTER_VALIDATE_INT);
            if ($userId === false || $userId < 1) {
                header('Location: ' . \app_url('index.php?page=connexion'));
                exit;
            }

            $user = (new \Prismelium\Models\UtilisateurModele())->findResearcherById((int) $userId);
            if ($user === null) {
                session_unset();
                session_destroy();
                header('Location: ' . \app_url('index.php?page=connexion'));
                exit;
            }

            if ((int) $user['Id_Role'] !== 1 || empty($user['Id_Equipe'])) {
                http_response_code(403);
                exit('Cette page est réservée aux enseignants-chercheurs associés à une équipe.');
            }

            return $user;
        }
    }
}