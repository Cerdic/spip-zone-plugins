<?php
if (!defined("_ECRIRE_INC_VERSION")) return;


/* 
 * Déclarer les nouveaux champs et 
 * les nouvelles infos des objets éditoriaux
 * 
 * /!\ Ne pas utiliser table_objet() qui ferait une reentrance et des calculs faux.
 */
function cextras_declarer_tables_objets_sql($tables){

	// pouvoir utiliser la class ChampExtra
	include_spip('inc/cextras');
	
	// recuperer les champs crees par les plugins
	$champs = pipeline('declarer_champs_extras', array());
	
	// ajoutons les champs un par un
	foreach ($champs as $c){
		$table = $c->table;
		if (isset($tables[$table]) and $c->champ and $c->sql) {
			$tables[$table]['field'][$c->champ] = $c->sql;
			// ajouter le champ dans la fonction de recherche de SPIP
			if ($c->rechercher) {
				// priorite 2 par defaut, sinon sa valeur.
				// Plus le chiffre est grand, plus les points de recherche
				// attribues pour ce champ seront eleves
				if ($c->rechercher === true
				OR  $c->rechercher === 'oui'
				OR  $c->rechercher === 'on') {
					$priorite = 2;
				} else {
					$priorite = intval($c->rechercher);
				}
				if ($priorite) {
					$tables[$table]['rechercher_champs'][$c->champ] = $priorite;
				}
			}
		}
	}
	
	return $tables;
}


/**
 * Déclarer les nouvelles infos sur les champs extras ajoutés
 * en ce qui concerne les traitements automatiques sur les balises.
 *
**/
function cextras_declarer_tables_interfaces($interface){

	// pouvoir utiliser la class ChampExtra
	include_spip('inc/cextras');
	
	// recuperer les champs crees par les plugins
	$champs = pipeline('declarer_champs_extras', array());

	// ajoutons les filtres sur les champs
	foreach ($champs as $c){
		if ($c->traitements and $c->champ and $c->sql) {
			$balise = strtoupper($c->champ);
			// definir
			if (!isset($interface['table_des_traitements'][$balise])) {
				$interface['table_des_traitements'][$balise] = array();
			}
			// le traitement peut etre le nom d'un define
			$traitement = defined($c->traitements) ? constant($c->traitements) : $c->traitements;
			
			// SPIP 3 permet de declarer par la table sql directement.
			$interface['table_des_traitements'][$balise][$c->table] = $traitement;
		}
	}
	// ajouter les champs au tableau spip
	return $interface;
}


?>
