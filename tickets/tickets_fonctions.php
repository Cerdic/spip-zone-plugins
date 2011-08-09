<?php

/**
 * Crée la liste des options du select des champs :
 * -* jalon
 * -* version
 * -* projet
 * -* composant
 * 
 * @param string $nom
 */
function tickets_champ_optionnel_actif($nom){
	$constante = '_TICKETS_LISTE_' . strtoupper($nom);
	if (!defined($constante) && !lire_config('tickets/general/'.$nom)) {
		return false;
	}
	if(defined($constante))
		$liste = constant($constante);
	else 
		$liste = lire_config('tickets/general/'.$nom,'');
	spip_log($liste,'ticket');
	if ($liste == '') return false;

	return explode(':', $liste);
}


// Creation de la liste des options du select d'assignation
function tickets_select_assignation($en_cours){
	$options = NULL;

	include_spip('inc/tickets_autoriser');
	$select = array('nom','id_auteur');
	$from = array('spip_auteurs AS t1');
	$autorises = definir_autorisations_tickets('assigner');
	if ($autorises['statut'])
		$where = array(sql_in('t1.statut', $autorises['statut']), 't1.email LIKE '.sql_quote('%@%'));
	else
		$where = array(sql_in('t1.id_auteur', $autorises['auteur']), 't1.email LIKE '.sql_quote('%@%'));
	$query_auteurs = sql_select($select, $from, $where);
	while ($row_auteur = sql_fetch($query_auteurs)) {
		$selected = ($row_auteur["id_auteur"] == $en_cours) ? ' selected="selected"' : '';
		$options .= '<option value="' . $row_auteur["id_auteur"] . '"' . $selected . '>' . $row_auteur["nom"] . '</option>';
	}

	return $options;
}

// Affichage de la page des tickets classes par jalon
function tickets_classer_par_jalon($bidon) {
	$page = NULL;
	if (defined('_TICKETS_LISTE_JALONS') OR lire_config()) {
		$liste = explode(":", _TICKETS_LISTE_JALONS);
		$i = 0;
		foreach($liste as $_jalon) {
			$i += 1;
			$page .= recuperer_fond('prive/contenu/inc_liste_detaillee',
				array_merge($_GET, array('titre' => _T('tickets:champ_jalon').' '.$_jalon, 'statut' => 'ouvert', 'jalon' => $_jalon, 'bloc' => "_bloc$i")),
				array('ajax'=>true));
		}
	}
	return $page;
}

// Affichage des blocs de liste depliables et ajaxes
function tickets_debut_block_depliable($deplie,$id=""){
	include_spip('inc/layer');
	return debut_block_depliable($deplie,$id);
}
function tickets_fin_block() {
	include_spip('inc/layer');
	return fin_block();
}
function tickets_bouton_block_depliable($texte,$deplie,$page="",$ids=""){
	include_spip('inc/layer');
	if ($page)
		return bouton_block_depliable(afficher_plus(generer_url_ecrire($page)).$texte,$deplie,$ids);
	else
		return bouton_block_depliable($texte,$deplie,$ids);
	
}

// creation des fonction de selection de texte
// encore en truc a reprendre !
foreach (array('severite', 'type', 'statut') as $nom){
	eval("function tickets_texte_$nom(\$niveau) {
		\$type = tickets_liste_$nom();
		if (isset(\$type[\$niveau])) {
			return \$type[\$niveau];
		}
	}");
}

function tickets_icone_statut ($niveau) {
	$img = array(
		"redac" => "puce-blanche.gif",
		"ouvert" => "puce-orange.gif",
		"resolu" => "puce-verte.gif",
		"ferme" => "puce-poubelle.gif"
		);
	return $img[$niveau];
}

function tickets_icone_severite ($niveau) {
	$img = array(
		1 => "puce-rouge-breve.gif",
		2 => "puce-orange-breve.gif",
		3 => "puce-verte-breve.gif",
		4 => "puce-poubelle-breve.gif"
		);
	return $img[$niveau];
}

function tickets_liste_statut($connecte = true){
	$statuts = array(
		"redac" => _T("tickets:statut_redac"),
		"ouvert" => _T("tickets:statut_ouvert"),
		"resolu" => _T("tickets:statut_resolu"),
		"ferme" => _T("tickets:statut_ferme"),
	);
	if (!$connecte) {
		unset($statuts['redac']);
	}
	return $statuts;
}


function tickets_liste_type($id_ticket = null){
	$types = array(
		1 => _T("tickets:type_probleme"),
		2 => _T("tickets:type_amelioration"),
		3 => _T("tickets:type_tache"),
	);
	return $types;
}

function tickets_liste_severite($id_ticket = null){
	$severites = array(
		1 => _T("tickets:severite_bloquant"),
		2 => _T("tickets:severite_important"),
		3 => _T("tickets:severite_normal"),
		4 => _T("tickets:severite_peu_important"),
	);
	return $severites;
}

if(!function_exists('barre_typo')){
	function barre_typo(){
		return;
	}
}

/**
 * 
 * Fonction de génération d'url privée de tickets
 * 
 * @param int $id
 * @param string $args
 * @param string $ancre
 * @param string $statut
 * @param string $connect
 */
function generer_url_ecrire_ticket($id, $args='', $ancre='', $statut='', $connect='') {
	$a = "id_ticket=" . intval($id);
	if (!$statut) {
		$statut = sql_getfetsel('statut', 'spip_tickets', $a,'','','','',$connect);
	}
	$h = generer_url_ecrire('ticket_afficher', $a . ($args ? "&$args" : ''))
	. ($ancre ? "#$ancre" : '');
	return $h;
}
?>