<?php



function fonds_affiche_droite($flux){
	$exec = $flux["args"]["exec"];

	if ($exec == "article") {
	
		$id_article = $flux["args"]["id_article"];
	
		$contexte = array('id_article' => $id_article);
		$flux["data"] .= recuperer_fond("prive/fonds_article", $contexte);
	
	}
	return $flux;

}

function fonds_declarer_tables_objets_sql ($tables){

	$tables['spip_articles']['field']['fond_couleur'] = "varchar(6) DEFAULT '' NOT NULL";
	$tables['spip_articles']['field']['credit_haut'] = "varchar(255) DEFAULT '' NOT NULL";
	$tables['spip_articles']['field']['credit_bas'] = "varchar(255) DEFAULT '' NOT NULL";
	$tables['spip_articles']['field']['remplir_vertical'] = "varchar(3) DEFAULT '' NOT NULL";

	return $tables;
}

function fonds_header_prive($flux) {
	$flux .= recuperer_fond("prive/fonds_header_prive");
	return $flux;
}


function fonds_insert_head($flux) {
	$flux .= "<script  type='text/javascript' src='".find_in_path("squelettes/avec_fonds.js")."'></script>";
	return $flux;
}

function fonds_insert_head_css($flux) {
	$flux .= recuperer_fond("head_fonds");
	return $flux;
}


