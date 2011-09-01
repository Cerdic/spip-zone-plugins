<?php

/**
 * Plugin Livrables
 * Licence GPL (c) 2011 Cyril Marion
 *
 */


function balise_TITRE_PROJET($p) {
	$id_projet = interprete_argument_balise (1, $p);
	$p->code = "trouve_titre(".$id_projet.")";
	$p->statut = 'php';
	return $p;
}
function trouve_titre($id_projet) {
	$titre = sql_getfetsel("titre","spip_projets", "id_projet=" . intval($id_projet));
	if (!empty($titre))
		return $titre;
	return '';
}


/**
 * Retrouve l'icone des statuts :
 * 	non_livre 	=> pas encore commence, pas livre
 * 	non_vue		=> pas vue, pas visible
 * 	alerte		=> dev commence, en difficulte
 * 	dev			=> en cours de developpement
 * 	test		=> en cours de test, recette en cours
 * 	prod		=> en production
 * 	abandonne	=> abandonne, arrete
 * 	avec_bugs	=> bugs a corriger
 * 	accepte		=> valide, accepte,
 */
function livrables_icone_statut($niveau) {
	$img = array(
		"non_livre" 	=> "puce-blanche.gif",
		"non_vue" 		=> "puce-rouge.gif",
		"alerte" 		=> "puce-rouge.gif",
		"test" 			=> "puce-jaune.gif",
		"dev" 			=> "puce-orange.gif",
		"avec_bugs" 	=> "puce-orange.gif",
		"prod" 			=> "puce-verte.gif",
		"accepte" 		=> "puce-verte.gif",
		"abandonne" 	=> "puce-noire.gif"
		);
	return $img[$niveau];
}


/**
 * Retrouve les libelles des statuts :
 */
function livrables_texte_statut($id_livrable = null){
	$statuts = array(
		"non_livre" 	=> _T("livrables:libelle_statut_non_livre"),
		"non_vue" 		=> _T("livrables:libelle_statut_non_vue"),
		"alerte" 		=> _T("livrables:libelle_statut_alerte"),
		"test" 			=> _T("livrables:libelle_statut_test"),
		"dev" 			=> _T("livrables:libelle_statut_dev"),
		"avec_bugs" 	=> _T("livrables:libelle_statut_avec_bugs"),
		"prod" 			=> _T("livrables:libelle_statut_prod"),
		"accepte" 		=> _T("livrables:libelle_statut_accepte"),
		"abandonne" 	=> _T("livrables:libelle_statut_abandonne"),
	);
	return $statuts;
}

/**
 * Retrouve les explications des statuts :
 */
function livrables_explications_statut($id_livrable = null){
	$explication = array(
		"non_livre" 	=> _T("livrables:explication_statut_non_livre"),
		"non_vue" 		=> _T("livrables:explication_statut_non_vue"),
		"alerte" 		=> _T("livrables:explication_statut_alerte"),
		"test" 			=> _T("livrables:explication_statut_test"),
		"dev" 			=> _T("livrables:explication_statut_dev"),
		"avec_bugs" 	=> _T("livrables:explication_statut_avec_bugs"),
		"prod" 			=> _T("livrables:explication_statut_prod"),
		"accepte" 		=> _T("livrables:explication_statut_accepte"),
		"abandonne" 	=> _T("livrables:explication_statut_abandonne"),
	);
	return $explication;
}


/**
 * Retrouve le nombre total de tickets concernant ce livrable
 */
function balise_TICKETS_TOTAL($p) {
	$id_livrable = interprete_argument_balise (1, $p);
	$p->code = "calcule_total_tickets(".$id_livrable.")";
	$p->statut = 'php';
	return $p;
}
function calcule_total_tickets($id_livrable) {
	$total = sql_getfetsel("COUNT(id_ticket)","spip_tickets", "id_livrable=" . intval($id_livrable));
	if (!empty($total))
		return $total;
	return '';
}

/**
 * Retrouve le nombre de tickets termines
 */
function balise_TICKETS_FINIS($p) {
	$id_livrable = interprete_argument_balise (1, $p);
	$p->code = "calcule_tickets_finis(".$id_livrable.")";
	$p->statut = 'php';
	return $p;
}
function calcule_tickets_finis($id_livrable) {
	$finis = sql_countsel('spip_tickets LEFT JOIN spip_livrables USING (id_livrable)',"spip_tickets.id_livrable=".intval($id_livrable)." AND spip_tickets.statut IN('termine','resolu')");
	if (!empty($finis))
		return $finis;
	return '';
}

/**
 * Retrouve le nombre de tickets en cours
 */
function balise_TICKETS_EN_COURS($p) {
	$id_livrable = interprete_argument_balise (1, $p);
	$p->code = "calcule_tickets_en_cours(".$id_livrable.")";
	$p->statut = 'php';
	return $p;
}
function calcule_tickets_en_cours($id_livrable) {
	$en_cours = sql_countsel('spip_tickets LEFT JOIN spip_livrables USING (id_livrable)',"spip_tickets.id_livrable=".intval($id_livrable)." AND spip_tickets.statut NOT IN('termine','resolu')");
	if (!empty($en_cours))
		return $en_cours;
	return '';
}

		
?>