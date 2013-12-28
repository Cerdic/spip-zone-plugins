<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
/**
 * saveauto : plugin de sauvegarde automatique de la base de donnees de SPIP
 *
 * Ce programme est un logiciel libre distribue sous licence GNU/GPL.
 *
 */

/**
 * Insertion dans le pipeline "mes_fichiers_a_sauver"
 * Permettre de rajouter des fichiers a sauvegarder dans le plugin Mes Fichiers 2
 */
function saveauto_mes_fichiers_a_sauver($flux){
    /**
     * Determination du repertoire de sauvegarde et du prefixe
     */
	$rep_save = lire_config('saveauto/repertoire_save');
    $prefixe = lire_config('saveauto/prefixe_save');

    /**
     * le dernier fichier de dump de la base cree par saveauto
     * - commence par le prefixe de la configuration
     * - a pour extension zip ou sql
     * - on ne conserve que le dernier en date
     */
    $dump = preg_files($rep_save,"$prefixe.+[.](zip|sql)$");
    $fichier_dump = '';
    $mtime = 0;
    foreach ($dump as $_fichier_dump) {
        if (($_mtime = filemtime($_fichier_dump)) > $mtime) {
            $fichier_dump = $_fichier_dump;
            $mtime = $_mtime;
        }
    }
    if ($fichier_dump)
        $flux[] = $fichier_dump;

    return $flux;
}

/**
 * On s'insère dans le cron de SPIP
 * Par défaut une fois par jour (peut être modifié dans la conf)
 *
 * @param array $taches_generales
 */
function saveauto_affiche_milieu($flux) {

	// on exclut le cas d'affichage de la page après le dump SQLite
	if ((($type = $flux['args']['type-page'])=='sauvegarder')
	AND (!$flux['args']['status'])) {
		$contexte = array();
		if (isset($flux['args']['etat']))
			$contexte['etat'] = $flux['args']['etat'];
		$flux['data'] .= recuperer_fond('prive/squelettes/contenu/sauvegarder_saveauto', $contexte);
	}

	return $flux;
}

/**
 * Surcharge de la fonction charger du formulaire de configuration :
 * - permet de fournir au formulaire la liste de toutes les tables de la base et celles des tables exportees par defaut
 *
 * @param array $flux
 * @return array
 *
**/
function saveauto_formulaire_charger($flux){

	$form = $flux['args']['form'];

	if ($form == 'configurer_saveauto') {
		include_spip('base/dump');

		// Liste de toutes les tables de la base pour que le formulaire boucle dessus
		$tables = base_lister_toutes_tables('', array(), array(), true);
		$flux['data']['_toutes_tables'] = $tables;

		// Liste des tables exportables.
		// On a besoin de cette liste si l'option tout_saveauto est à 'oui' : en effet, dans ce cas
		// la liste en stockée en base de données n'est pas forcément bonne car on a pas la possibilité
		// de forcer un mise à jour lors du traiter
		// TODO : s'insérer plutôt dans le traiter du formulaire
		$exclude = lister_tables_noexport();
		$flux['data']['_tables_export'] = base_lister_toutes_tables('', array(), $exclude, true);
		$flux['data']['_noexport'] = implode(', ', $exclude);
	}

	return $flux;
}

?>
