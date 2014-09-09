<?php
/**
 * Définit les actions du plugin Commits de projet
 *
 * @plugin     Commits de projet
 * @copyright  2014
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\RSSCommits\Action
 */

if (!defined('_ECRIRE_INC_VERSION')) {
    return;
}

/**
 * Mise à jour des commits d'un projet
 *
 * @param null|int $id
 *     `id` : son identifiant. En absence de `id` utilise l'argument de l'action sécurisée.
**/
function action_maj_commits_projet_dist($id = null)
{
    if (is_null($id)) {
        $securiser_action = charger_fonction('securiser_action', 'inc');
        $id = $securiser_action();
    }

    $id_projet = intval($id);

    if ($id_projet) {
        $log = array();
        $log[] = "\n ----------"
        . date_format(date_create(), 'Y-m-d H:i:s')
        . ' : on lance '
        . __FUNCTION__
        . ' pour le projet n#'
        . $id_projet;
        $commits_anciens = array();
        $commits_nouveaux = array();
        $commits_en_bdd   = sql_allfetsel(
            'id_projet,url_revision',
            'spip_commits',
            "url_revision !='' AND id_projet=$id_projet"
        );
        if (count($commits_en_bdd) > 0) {
            foreach ($commits_en_bdd as $key => $value) {
                $commits_anciens[] = $value['id_projet'] . '|' . $value['url_revision'];
            }
        }

        $commits = lister_rss_commits($id_projet);
        if (count($commits) > 0) {
            foreach ($commits as $key => $value) {
                if (!in_array($value['id_projet'] . '|' . $value['url_revision'], $commits_anciens)) {
                    // On stocke dans le tableau, les nouveaux commits qui doivent être ajoutés en BDD.
                    $commits_nouveaux[] = $value;
                    $log[] = 'Le commit '
                    . $value['url_revision']
                    . ' va être enregistré pour le projet n#'
                    . $value['id_projet'];
                } else {
                    $log[] = 'Le commit '
                    . $value['url_revision']
                    . ' est déjà enregistré pour le projet n#'
                    . $value['id_projet'];
                }
            }
            // On insère par lot les nouveaux commits pour éviter un débordement de mémoire
            // cf. http://programmer.spip.net/sql_insertq_multi,591
            if (count($commits_nouveaux) > 0) {
                sql_insertq_multi('spip_commits', $commits_nouveaux);
            } else {
                $log[] = 'Pas d\'ajout de nouveaux commits dans la BDD';
            }
        } else {
            $log[] = 'Il n\'y a pas de nouveaux commits';
        }
        $log[] = date_format(date_create(), 'Y-m-d H:i:s')
        . ' : '
        . __FUNCTION__
        . ' a fini son travail'
        . ' pour le projet n#'
        . $id_projet
        . "\n ----------";

        spip_log(implode("\n", $log), 'rss_commits');

    } else {
        spip_log(__FUNCTION__ . " $id pas compris", 'rss_commits');
    }
}

?>