<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

// Insérer le CSS nécessaire au bon affichage des marque-pages
function marquepages_insert_head($flux) {
	$css = "\n<link rel=\"stylesheet\" href=\""
		 . direction_css(find_in_path('css/marquepages.css'))
		 . "\" type=\"text/css\" media=\"all\" />\n";
	
	return $css.$flux;
}

// Ajouter le javascript qui permet de demander confirmation
function marquepages_jquery_plugins($scripts) {
	$scripts[] = "javascript/marquepages.js";
	return $scripts;
}

// Si ya pas de critère "statut" on affiche que les forums normaux
function marquepages_pre_boucle($boucle){
	if ($boucle->type_requete == 'forums') {
		$id_table = $boucle->id_table;
		$statut = "$id_table.statut";
		if (!isset($boucle->modificateur['criteres']['statut'])){
			$boucle->where[] = array(sql_quote('!='), sql_quote($statut), sql_quote("'mppublic'"));
			$boucle->where[] = array(sql_quote('!='), sql_quote($statut), sql_quote("'mpprive'"));
		}
	}
	if ($boucle->type_requete == 'marquepages') {
		$id_table = $boucle->id_table;
		$statut = "$id_table.statut";
		if (!isset($boucle->modificateur['criteres']['statut'])){
			$boucle->where[] = array(sql_quote('!='), sql_quote($statut), sql_quote("'prive'"));
			$boucle->where[] = array(sql_quote('!='), sql_quote($statut), sql_quote("'publie'"));
		}
	}
	return $boucle;
}

// Déclarer l'alias MARQUEPAGES pour la boucle FORUMS
function marquepages_declarer_tables_interfaces($interfaces){
	$interfaces['table_des_tables']['marquepages'] = 'forum';
	return $interfaces;
}
function marquepages_declarer_tables_objets_surnoms($surnoms) {
	$surnoms['marquepage'] = 'forums';
	return $surnoms;
}

// Ajouter les mots-clés pour la recherche
function marquepages_rechercher_liste_des_jointures($jointures){
	$jointures['forum'] = array(
		'mot' => array('titre' => 3)
	);
	return $jointures;
}

?>
