<?php
// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Compile le critère {mesfavoris}
 *
 * Permet de sélectionner les éléments favoris de l'id_auteur en cours
 * 
 * Si l'utilisateur est connecté {mesfavoris} retourne les éléments favoris
 * Si l'utilisateur est connecté {!mesfavoris} retourne les éléments non favoris
 * Si l'utilisateur n'est pas connecté, on retourne tout, pas de modification de la boucle
 * 
 * Ce critère est sessionné, il retournera donc un résultat différent pour chaque auteur
 * 
 * @param string $idb     Identifiant de la boucle
 * @param array $boucles  AST du squelette
 * @param Critere $crit   Paramètres du critère dans cette boucle
 * @return void
**/
function critere_mesfavoris_dist($idb,&$boucles,$crit){
	$boucle = &$boucles[$idb];
    $id_table = $boucle->id_table;
	$primary = $boucles[$idb]->primary;
	
	$objet = objet_type($primary,$boucle->serveur);
	$not = $crit->not;
	
	if(!$not && ($GLOBALS['visiteur_session']['id_auteur'] >= 1)){
		$boucle->join['favoris'] = array("'".$boucle->id_table."'", "'id_objet'", "'".$boucle->primary."'", "'favoris.objet='.sql_quote('$objet')");
		$boucle->from['favoris'] = 'spip_favoris';
		$boucle->group[] = $primary;
		$boucle->where[] = array("'='","'favoris.id_auteur'",$GLOBALS['visiteur_session']['id_auteur']);
	}
	else if($not && ($GLOBALS['visiteur_session']['id_auteur'] >= 1)){
		$in =  implode(',',prepare_mesfavoris($objet,$boucle->serveur));
		$c = "sql_in('".$id_table.'.'.$primary."','$in', '')";
		$boucle->where[] = array("'NOT'", $c);
	}
	
	$boucles[$idb]->descr['session'] = true;
	
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
function prepare_mesfavoris($objet,$server=''){
	$objets_favoris = sql_select('id_objet','spip_favoris','objet='.sql_quote($objet).' AND id_auteur='.intval($GLOBALS['visiteur_session']['id_auteur']));
	$objet= array();
	while($objet = sql_fetch($objets_favoris)){
		$objets[] = $objet['id_objet'];
	}
	return $objets;
}
?>