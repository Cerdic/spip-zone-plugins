<?php

// Creation de la liste des options du select des champ jalon, version, projet ou composant
function ticketskiss_select_champ_optionnel($champ='', $en_cours){
	$options = NULL;
	if ($champ ==  '')
		return $options;

	switch(strtolower($champ))
	{
		case 'jalon':
			if (defined('_TICKETSKISS_LISTE_JALONS'))
				$define = _TICKETSKISS_LISTE_JALONS;
			break;
		case 'version':
			if (defined('_TICKETSKISS_LISTE_VERSIONS'))
				$define = _TICKETSKISS_LISTE_VERSIONS;
			break;
		case 'projet':
			if (defined('_TICKETSKISS_LISTE_PROJETS'))
				$define = _TICKETSKISS_LISTE_PROJETS;
			break;
		case 'composant':
			if (defined('_TICKETSKISS_LISTE_COMPOSANTS'))
				$define = _TICKETSKISS_LISTE_COMPOSANTS;
			break;
		default:
			$define = '';
			break;
	}
	if ($define ==  '')
		return $options;

	$liste = explode(':', $define);
	foreach ($liste as $_item) {
		if ($_item != '') {
			$selected = ($_item == $en_cours) ? ' selected="selected"' : '';
			$options .= '<option value="' . $_item . '"' . $selected . '>' . $_item . '</option>';
		}
	}

	return $options;
}

// Creation de la liste des options du select d'assignation
function ticketskiss_select_assignation($en_cours){
	$options = NULL;

	include_spip('inc/ticketskiss_autoriser');
	$select = array('nom','id_auteur');
	$from = array('spip_auteurs AS t1');
	$autorises = definir_autorisations_ticketskiss('assigner');
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

// Affichage de la page des ticketskiss classes par jalon
function ticketskiss_classer_par_jalon($bidon) {
	$page = NULL;
	if (defined('_TICKETSKISS_LISTE_JALONS')) {
		$liste = explode(":", _TICKETSKISS_LISTE_JALONS);
		$i = 0;
		foreach($liste as $_jalon) {
			$i += 1;
			$page .= recuperer_fond('prive/contenu/inc_liste_detaillee',
				array_merge($_GET, array('titre' => _T('ticketskiss:champ_jalon').' '.$_jalon, 'statut' => 'ouvert', 'jalon' => $_jalon, 'bloc' => "_bloc$i")),
				array('ajax'=>true));
		}
	}
	return $page;
}

// Affichage des boutons modifier ou retour
function ticketskiss_bouton_modifier ($id_ticket, $logo, $align) {
	include_spip("inc/presentation");
	return icone_inline(_L('Modifier ce ticket'), generer_url_ecrire("ticket_editer","id_ticket=$id_ticket"), $logo, "edit.gif", $align);
}
function ticketskiss_bouton_retour ($id_ticket, $logo, $align) {
	include_spip("inc/presentation");
	return icone_inline(_L('Retour'), generer_url_ecrire("ticket_afficher","id_ticket=$id_ticket"), $logo, "", $align);
}

// Affichage des blocs de liste depliables et ajaxes
function ticketskiss_debut_block_depliable($deplie,$id=""){
	include_spip('inc/layer');
	return debut_block_depliable($deplie,$id);
}
function ticketskiss_fin_block() {
	include_spip('inc/layer');
	return fin_block();
}
function ticketskiss_bouton_block_depliable($texte,$deplie,$ids=""){
	include_spip('inc/layer');
	return bouton_block_depliable($texte,$deplie,$ids);
}

// Interpretation des valeurs de certains champs de la table ticket
function ticketskiss_texte_severite ($niveau) {
	$severite = array(
		1 => _T("ticketskiss:severite_bloquant"),
		2 => _T("ticketskiss:severite_important"),
		3 => _T("ticketskiss:severite_normal"),
		4 => _T("ticketskiss:severite_peu_important")
		);
	return $severite[$niveau];
}
function ticketskiss_texte_type ($niveau) {
	$type = array(
		1 => _T("ticketskiss:type_probleme"),
		2 => _T("ticketskiss:type_amelioration"),
		3 => _T("ticketskiss:type_tache")
		);
	return $type[$niveau];
}
function ticketskiss_texte_statut ($niveau) {
	$statut = array(
		"redac" => _T("ticketskiss:statut_redac"),
		"ouvert" => _T("ticketskiss:statut_ouvert"),
		"resolu" => _T("ticketskiss:statut_resolu"),
		"ferme" => _T("ticketskiss:statut_ferme")
		);
	return $statut[$niveau];
}
function ticketskiss_icone_statut ($niveau) {
	$icone_statut = array(
		"redac" => "puce-blanche.gif",
		"ouvert" => "puce-orange.gif",
		"resolu" => "puce-verte.gif",
		"ferme" => "puce-poubelle.gif"
		);
	return $icone_statut[$niveau];
}
function ticketskiss_icone_severite ($niveau) {
	$icone_severite = array(
		1 => "puce-rouge-breve.gif",
		2 => "puce-orange-breve.gif",
		3 => "puce-verte-breve.gif",
		4 => "puce-poubelle-breve.gif"
		);
	return $icone_severite[$niveau];
}


?>