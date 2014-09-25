<?php
/**
 * Fichier gérant l'action de mise à jour d'une activité
 *
 * @plugin     DayFill
 * @copyright  2014
 * @author     Cyril Marion
 * @licence    GNU/GPL
 * @package    SPIP\Dayfill\Action
 */

if (!defined('_ECRIRE_INC_VERSION')) {
    return;
}

/**
 * Action permettant soit d'ajouter une nouvelle activité avec une date de départ
 * soit de mettre à jour une activité 'en cours'
 * A utiliser avec une interface par 'boutons' où un bouton est un projet.
 *
 */
function action_update_dayfill_dist ()
{
    include_spip('base/abstract_sql');
    include_spip('inc/session');
    include_spip('dayfill_fonctions');
    $securiser_action = charger_fonction('securiser_action', 'inc');
    $id_projet = $securiser_action();
    $id_projet = intval($id_projet);
    $date = date_format(date_create(), 'Y-m-d H:i:s');

    if ($end_activite = sql_fetsel(
        'id_projets_activite,date_debut',
        'spip_projets_activites',
        'id_projet='
        . $id_projet
        . " AND id_auteur="
        . session_get('id_auteur')
        . " AND date_fin='0000-00-00 00:00:00' AND date_debut<>'0000-00-00 00:00:00'"
    )) {
        sql_updateq(
            'spip_projets_activites',
            array('date_fin' => $date, 'nb_heures_passees' => calcul_duree($date,$end_activite['date_debut'],true), 'nb_heures_decomptees' => calcul_duree($date,$end_activite['date_debut'],true)),
            'id_projets_activite='
            . $end_activite['id_projets_activite']
        );
    } elseif ($start = sql_fetsel('nom', 'spip_projets', 'id_projet=' . $id_projet)) {
        sql_insertq(
            'spip_projets_activites',
            array(
                'id_projet' => $id_projet,
                'descriptif' => $start['nom'],
                'id_auteur' => session_get('id_auteur'),
                'date_debut' => $date
            )
        );
    }
}
?>