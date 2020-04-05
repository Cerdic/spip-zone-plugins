<?php
/**
 * Pipelines utiles au plugin Statistiques des objets éditoriaux.
 *
 * @plugin    Statistiques des objets éditoriaux
 * @copyright 2016
 * @author    tcharlss
 * @licence   GNU/GPL
 * @package   SPIP\Statistiques_objets\Pipelines
 */

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}


/**
 * Recherche des objets pointes par un referer
 * 
 * Déclinaison de la fonction referes() dans inc/referenceurs.php
 * Ajout d'un paramètre $objets pour restreindre à des types d'objets
 * 
 * @param              $referermd5
 * @param string       $serveur
 * @param array|string $objets
 * @return string
 */
function referes_objets($referermd5, $serveur = '', $objets='') {

	include_spip('base/objets'); // au cas où
	$trouver_table = charger_fonction('trouver_table','base');
	
	// si aucun type d'objet n'est donné en paramètre,
	// on va chercher tous les types d'objets référencés dans les tables des referers
	if (!$objets){
		if ($objets = sql_fetsel('DISTINCT objet', 'spip_referers_objets')){
			$objets = array_values($objets);
		} else {
			$objets = array();
		}
		if (sql_countsel('spip_visites_articles')) {
			$objets[] = 'article';
		}
	}
	// sinon on s'assure d'avoir un array
	elseif (is_string($objets)){
		$objets = array($objets);
	}

	$retours = array();
	$res_objets = array();
	foreach($objets as $objet){
		$table_objet_sql = table_objet_sql($objet); // spip_articles
		$id_table_objet = id_table_objet($objet); // id_article
		$desc = $trouver_table($table_objet_sql);
		$champ_titre = isset($desc['titre']) ? $desc['titre'] : 'titre'; // titre, nom...
		if ($objet == 'article'){
			$table_referers = 'spip_referers_articles';
		} else {
			$table_referers = 'spip_referers_objets';
		}
		$on = ($objet == 'article' ?
			"J1.id_article = J2.id_article" :
			'(J1.objet='.sql_quote($objet)." AND J1.id_objet = J2.$id_table_objet)");
		if ($res = sql_allfetsel(
				"J2.$id_table_objet, J2.$champ_titre",
				"$table_referers AS J1 LEFT JOIN $table_objet_sql AS J2 ON $on",
				"(referer_md5='$referermd5' AND J1.maj>=DATE_SUB(" . sql_quote(date('Y-m-d H:i:s')) . ", INTERVAL 2 DAY))",
				'',
				"titre",
				'',
				'',
				$serveur
		)) {
			$res_objets[$objet] = $res;
		}
	}

	foreach($res_objets as $objet => $res){
		$id_table_objet = id_table_objet($objet);
		foreach ($res as $k => $ligne) {
			$titre = typo($ligne['titre']);
			$url = generer_url_entite($ligne[$id_table_objet], $objet, '', '', true);
			$retours[$k] = "<a href='$url'><i>$titre</i></a>";
		}
	}

	if (count($retours) > 1) {
		return '&rarr; ' . join(',<br />&rarr; ', $retours);
	}
	if (count($retours) == 1) {
		return '&rarr; ' . array_shift($retours);
	}

	return '';
}