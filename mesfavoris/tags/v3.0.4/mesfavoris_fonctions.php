<?php
/**
 * Plugin mesfavoris
 * (c) 2009-2012 Olivier Sallou, Cedric Morin
 * Distribue sous licence GPL
 *
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Compile le critère {mesfavoris}
 *
 * Permet de sélectionner les éléments favoris de l'id_auteur en cours
 * 
 * Si l'utilisateur est connecté {mesfavoris} retourne les éléments favoris
 * Si l'utilisateur est connecté {!mesfavoris} retourne les éléments non favoris
 * Si l'utilisateur n'est pas connecté, on retourne tout, pas de modification de la boucle
 * 
 * On accepte également les paramètres suivants :
 * {mesfavoris oui} : agit comme {mesfavoris}
 * {mesfavoris non} : agit comme {!mesfavoris}
 * {mesfavoris ignore} : n'agit pas du tout
 * 
 * On peu également utiliser l'écriture {mesfavoris #ENV{favs,oui}}
 * 
 * Attention :ce critère est sessionné, il retournera donc un résultat différent pour chaque auteur
 * 
 * @param string $idb     Identifiant de la boucle
 * @param array $boucles  AST du squelette
 * @param Critere $crit   Paramètres du critère dans cette boucle
 * @return void
 */
function critere_mesfavoris_dist($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	$id_table = $boucle->id_table;
	$primary = $boucles[$idb]->primary;
	
	$objet = objet_type($primary, $boucle->serveur);
	$id_table_objet = $primary;
	$table_objet = table_objet_sql($primary);
	$not = ($crit->not == '!') ? 'non':'oui';
	
	/**
	 * On récupère un paramètre potentiel
	 */
	$type = !isset($crit->param[0][0]) ? "'$not'"
		: calculer_liste(array($crit->param[0][0]), array(), $boucles, $boucle->id_parent);

	$boucle->where[] = mesfavoris_critere_where($primary, $id_table, $table_objet, $objet, $type);
	$boucles[$idb]->descr['session'] = true;
	
}

function mesfavoris_critere_where($primary, $id_table, $table_objet, $objet, $type) {
	$in = "sql_in('$primary', prepare_mesfavoris($objet,$type), '')";
	$type1 = "mesfavoris_definir_type($type)";
	
	return "$type1 ? array($type1,'$id_table.$primary','(SELECT * FROM('.sql_get_select('zzza.$primary','$table_objet as zzza',$in,'','','','',\$connect).') AS subquery)'):''";
}

function mesfavoris_definir_type($type) {
	if($type == 'oui') {
		return 'IN';
	}
	elseif($type == 'non') {
		return 'NOT IN';
	}
	else {
		return false;
	}
}

/**
 * Fonction de préparation du critère {!mesfavoris}
 * 
 * Retourne les objets favoris pour les éviter dans la boucle
 * 
 * @param string $objet Le type d'objet de la boucle
 * @param string $server Le serveur
 * @return array $objets Les id des objets à éviter 
 */
function prepare_mesfavoris($objet, $type, $server='') {
	$objets_favoris = sql_allfetsel('id_objet', 'spip_favoris', 'objet='.sql_quote($objet).' AND id_auteur='.intval($GLOBALS['visiteur_session']['id_auteur']));
	$objet= array();
	
	foreach($objets_favoris as $objet) {
		$objets[] = $objet['id_objet'];
	}
	
	return $objets;
}
