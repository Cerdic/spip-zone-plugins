<?php
/**
 * Utilisations de pipelines par Commits de projet
 *
 * @plugin     Commits de projet
 * @copyright  2014
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\RSSCommits\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
    return;
}

/**
 * Ajouter les tâches de CRON du plugin RSS Commits
 *
 * @param  array  $taches Tableau des tâches et leur périodicité en seconde
 * @return array         Tableau des tâches et leur périodicité en seconde
 */
function rss_commits_taches_generales_cron($taches)
{
    $import_auto = lire_config('rss_commits/import_auto', 'non');
    if ($import_auto == 'oui') {
        $taches['import_commits'] = 1*3600; // toutes les heures
    }
    return $taches;
}

/**
 * Ajout de contenu sur certaines pages,
 * notamment des formulaires de liaisons entre objets
 *
 * @pipeline affiche_milieu
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function rss_commits_affiche_enfants($flux)
{
    $texte = "";
    $e = trouver_objet_exec($flux['args']['exec']);
    $lister_objets = charger_fonction('lister_objets', 'inc');

    // commits sur les projets
    if (!$e['edition'] and in_array($e['type'], array('projet'))) {
        $id_projet = $flux['args']['id_projet'];
        $flux['data'] .= $lister_objets(
            'commits',
            array(
                'sinon'=>_T('commit:aucun_commit_projet'),
                'id_projet'=>$id_projet,
                'par'=>'date_creation'
            )
        );
    }

    return $flux;
}

/**
 * Afficher les interventions et objets en lien
 * avec un auteur (sur sa page)
 *
 * @param array $flux
 * @return array
 */
function rss_commits_affiche_auteurs_interventions($flux)
{
    if ($id_auteur = intval($flux['args']['id_auteur'])) {
        $auteur = sql_fetsel('email', 'spip_auteurs', 'id_auteur=' . $id_auteur);
        $auteur = explode('@', $auteur['email']);
        $auteur_commit = $auteur[0];
        $flux['data'] .= recuperer_fond(
            'prive/objets/liste/commits',
            array(
                'par'=>'date_creation',
                'where' => "commits.auteur LIKE '%" . $auteur_commit . "%'"
            )
        );
    }

    return $flux;
}

/**
 * Afficher le nombre de documents dans chaque rubrique
 *
 * @param array $flux
 * @return array
 */
function rss_commits_boite_infos($flux)
{
    // Dans le $flux de la boite infos, on a un array :
    // 'data' => contient toutes les données, textes du bloc d'infos
    // 'args' => 'type' => le type d'objet
    // 'args' => 'id' => l'id de l'objet

    if ($flux['args']['type']=='projet'
        and $id_projet = $flux['args']['id']){
        $flux['data'] .= recuperer_fond(
            'prive/objets/infos/projet_commits',
            array(
                'id_projet'=>$id_projet
            )
        );
    }

    return $flux;
}

?>