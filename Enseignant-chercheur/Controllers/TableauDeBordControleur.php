<?php
declare(strict_types=1);

namespace Prismelium\Controllers;

use DateTimeImmutable;
use Prismelium\Controleur;
use Prismelium\GardeEnseignantChercheur;
use Prismelium\Models\TableauDeBordModele;

final class TableauDeBordControleur extends Controleur
{
    public function index(): void
    {
        $user = GardeEnseignantChercheur::requireResearcher();
        $model = new TableauDeBordModele();

        $planningDays = [];
        foreach ($model->getUpcomingPlanning((int) $user['Id_Equipe']) as $day) {
            $date = new DateTimeImmutable($day['Date_Ses']);
            $planningDays[] = [
                'date' => $date->format('d/m/Y'),
                'events' => (int) $day['SessionCount'] . ' séance(s)',
                'current' => $date->format('Y-m-d') === date('Y-m-d'),
            ];
        }

        $recentActivities = [];
        foreach ($model->getRecentActivities((int) $user['Id_User']) as $activity) {
            $type = (string) $activity['Type_Log'];
            $normalizedType = strtolower($type);
            $category = str_contains($normalizedType, 'inscription')
                ? 'inscription'
                : (str_contains($normalizedType, 'retribution') || str_contains($normalizedType, 'rétribution')
                    ? 'retribution'
                    : (str_contains($normalizedType, 'planning') || str_contains($normalizedType, 'séance')
                        ? 'planning'
                        : 'experimentation'));

            $recentActivities[] = [
                'type' => $category,
                'title' => $type,
                'description' => $activity['Erreur_Log'] ?: 'Activité enregistrée pour votre compte.',
                'time' => (new DateTimeImmutable($activity['Date_Log']))->format('d/m/Y H:i'),
            ];
        }

        $this->render('tableau-de-bord/enseignant-chercheur', [
            'planningDays' => $planningDays,
            'recentActivities' => $recentActivities,
        ]);
    }
}
