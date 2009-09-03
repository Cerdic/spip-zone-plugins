<?php

// Creation de la liste des options du select des champ jalon, version, projet ou composant
function tickets_select_champ_optionnel($champ='', $en_cours){
	$options = NULL;
	if ($champ ==  '')
		return $options;

	switch(strtolower($champ))
	{
		case 'jalon':
			if (defined('_TICKETS_LISTE_JALONS'))
				$define = _TICKETS_LISTE_JALONS;
			break;
		case 'version':
			if (defined('_TICKETS_LISTE_VERSIONS'))
				$define = _TICKETS_LISTE_VERSIONS;
			break;
		case 'projet':
			if (defined('_TICKETS_LISTE_PROJETS'))
				$define = _TICKETS_LISTE_PROJETS;
			break;
		case 'composant':
			if (defined('_TICKETS_LISTE_COMPOSANTS'))
				$define = _TICKETS_LISTE_COMPOSANTS;
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
	if (defined('_TICKETS_LISTE_JALONS')) {
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

// Affichage des boutons modifier ou retour
function tickets_bouton_modifier ($id_ticket, $logo, $align) {
	include_spip("inc/presentation");
	return icone_inline(_L('Modifier ce ticket'), generer_url_ecrire("ticket_editer","id_ticket=$id_ticket"), $logo, "edit.gif", $align);
}
function tickets_bouton_retour ($id_ticket, $logo, $align) {
	include_spip("inc/presentation");
	return icone_inline(_L('Retour'), generer_url_ecrire("ticket_afficher","id_ticket=$id_ticket"), $logo, "", $align);
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
function tickets_bouton_block_depliable($texte,$deplie,$ids=""){
	include_spip('inc/layer');
	return bouton_block_depliable($texte,$deplie,$ids);
}

// Interpretation des valeurs de certains champs de la table ticket
function tickets_texte_severite ($niveau) {
	if ($niveau == 1) return "bloquant";
	else if ($niveau == 2) return "important";
	else if ($niveau == 3) return "normal";
	else if ($niveau == 4) return "peu important";
}
function tickets_texte_type ($niveau) {
	if ($niveau == 1) return "probl&egrave;me";
	else if ($niveau == 2) return "am&eacute;lioration";
	else if ($niveau == 3) return "t&acirc;che";
}
function tickets_texte_statut ($niveau) {
	if ($niveau == "redac") return "en cours de r&eacute;daction";
	else if ($niveau == "ouvert") return "ouvert et discut&eacute;";
	else if ($niveau == "resolu") return "r&eacute;solu";
	else if ($niveau == "ferme") return "ferm&eacute;";
}
function tickets_icone_statut ($niveau) {
	if ($niveau == "redac") $img = "puce-blanche.gif";
	else if ($niveau == "ouvert") $img = "puce-orange.gif";
	else if ($niveau == "resolu") $img = "puce-verte.gif";
	else if ($niveau == "ferme")  $img = "puce-poubelle.gif";

	return $img;
}
function tickets_icone_severite ($niveau) {
	if ($niveau == 1) $img = "puce-rouge-breve.gif";
	else if ($niveau == 2) $img = "puce-orange-breve.gif";
	else if ($niveau == 3) $img = "puce-verte-breve.gif";
	else if ($niveau == 4)  $img = "puce-poubelle-breve.gif";

	return $img;
}


?>