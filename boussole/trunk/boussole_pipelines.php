<?php
/**
 * Ce fichier contient les cas d'utilisation des pipelines d'affichage.
 *
 * @package SPIP\BOUSSOLE\Pipelines
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 * Affichage, dans la fiche d'un objet site, d'un bloc identifiant
 * la boussole à laquelle appartient le site édité.
 *
 * @pipeline affiche_milieu
 *
 * @param array $flux
 * 		Données de contexte fournies au pipeline
 * @return array
 * 		Données de contexte complétées par la fonction
 */
function boussole_affiche_milieu($flux){
	if (($flux['args']['exec'] == 'site') AND $flux['args']['id_syndic']) {
		$id_syndic = $flux['args']['id_syndic'];
		$info = recuperer_fond('prive/squelettes/inclure/site_boussole', array('id_syndic'=>$id_syndic));

		if ($info){
			if ($p = strpos($flux['data'],'<!--affiche_milieu-->'))
				$flux['data'] = substr_replace($flux['data'], $info, $p, 0);
			else
				$flux['data'] .= $info;
		}
	}

	return $flux;
}


/**
 * Affiche dans la page d'accueil des sites référencés un raccourci pour importer l'ensemble
 * des sites d'une boussole déjà installée.
 *
 * @pipeline affiche_gauche
 *
 * @param object $flux
 * @return $flux
 */
function boussole_affiche_gauche($flux){
	if ($flux['args']['exec'] == 'sites') {
		$boussoles_ajoutees = sql_allfetsel('valeur', 'spip_meta', array('nom LIKE ' . sql_quote('boussole_infos%')));
		if ($boussoles_ajoutees) {
			$flux['data'] .= recuperer_fond('prive/squelettes/inclure/sites_importer_boussole', array());
		}
	}
	return $flux;
}


/**
 * Permet de déclencher la mise à jour de la boussole si un site de la base appartenant à cette boussole
 * est refusé ou réhabilité si il était déjà refusé.
 *
 * @pipeline post_edition
 *
 * @param object $flux
 * @return $flux
 */
function boussole_post_edition($flux){

    if (isset($flux['args']['table'])) {
        $table = $flux['args']['table'];
       	$id = intval($flux['args']['id_objet']);
       	$action = $flux['args']['action'];

       	if (($table == 'spip_syndic')
       	AND ($id)
        AND ($action == 'instituer')) {
       		if (($flux['args']['statut_ancien'] == 'refuse')
    		OR ($flux['data']['statut'] == 'refuse')) {
                // Il faut détecter si le site appartient à une boussole en se basant sur l'url uniquement
                $urls = array();
                $url_site = sql_getfetsel('url_site', 'spip_syndic', 'id_syndic=' . sql_quote($id));
                $urls[] = $url_site;
              	$urls[] = (substr($url_site, -1, 1) == '/') ? substr($url_site, 0, -1) : $url_site . '/';
           		if ($id_site = sql_getfetsel('id_site', 'spip_boussoles', sql_in('url_site', $urls))) {
                    // Le site appartient bien à une boussole et son id dans la boussole est id_site.
                    // Il suffit maintenant de mettre à jour son id_syndic en fonction du type de changement de statut
                    $id_maj = ($flux['args']['statut_ancien'] == 'refuse') ? $id : 0;
                    sql_updateq('spip_boussoles',	array('id_syndic' => $id_maj), 'id_site='. sql_quote($id_site));
                }
       		}
       	}
    }

	return $flux;
}

?>
