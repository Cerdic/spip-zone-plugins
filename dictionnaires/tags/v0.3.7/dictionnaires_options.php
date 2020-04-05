<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function generer_url_ecrire_dictionnaire($id, $args='', $ancre='', $statut='', $connect=''){
	$a = "id_dictionnaire=" . intval($id);
	$h = (!$statut OR $connect)
	? generer_url_entite_absolue($id, 'dictionnaire', $args, $ancre, $connect)
	: (generer_url_ecrire('dictionnaire', $a . ($args ? "&$args" : ''))
		. ($ancre ? "#$ancre" : ''));
	return $h;
}

function generer_url_ecrire_definition($id, $args='', $ancre='', $statut='', $connect=''){
	$a = "id_definition=" . intval($id);
	if (!$statut) {
		$statut = sql_getfetsel('statut', 'spip_definitions', $a,'','','','',$connect);
	}
	$h = ($statut == 'publie' OR $connect)
	? generer_url_entite_absolue($id, 'definition', $args, $ancre, $connect)
	: (generer_url_ecrire('definition', $a . ($args ? "&$args" : ''))
		. ($ancre ? "#$ancre" : ''));
	return $h;
}

// Pour les puces de statut rapide
function puce_statut_definition_dist($id, $statut, $id_dictionnaire, $type='definition', $ajax=false){
	global $lang_objet;
	
	static $coord = array(
		'publie' => 1,
		'prop' => 0,
		'poubelle' => 2
	);

	$lang_dir = lang_dir($lang_objet);
	$ajax_node = " id='imgstatut$type$id'";
	$inser_puce = puce_statut($statut, " width='9' height='9' style='margin: 1px;'$ajax_node");

	if (!autoriser('instituer', 'definition', $id)
		or !_ACTIVER_PUCE_RAPIDE
	)
		return $inser_puce;

	$titles = array(
		"orange" => _T('texte_statut_propose_evaluation'),
		"verte" => _T('texte_statut_publie'),
		"poubelle" => _T('texte_statut_poubelle')
	);
	
	$clip = 1 + (11 * $coord[$statut]);

	if ($ajax){
		return 	"<span class='puce_article_fixe'>"
		. $inser_puce
		. "</span>"
		. "<span class='puce_article_popup' id='statutdecal$type$id' style='width:33px; margin-left: -$clip"."px;'>"
		  . afficher_script_statut($id, $type, -1, 'puce-orange.gif', 'prop', $titles['orange'])
		  . afficher_script_statut($id, $type, -12, 'puce-verte.gif', 'publie', $titles['verte'])
		  . afficher_script_statut($id, $type, -23, 'puce-poubelle.gif', 'poubelle', $titles['poubelle'])
		  . "</span>";
	}

	$nom = "puce_statut_";

	if ((! _SPIP_AJAX) AND $type != 'definition') 
	  $over ='';
	else {
		$action = generer_url_ecrire('puce_statut_definitions',"",true);
		$action = "if (!this.puce_loaded) { this.puce_loaded = true; prepare_selec_statut('$nom', '$type', '$id', '$action'); }";
		$over = "\nonmouseover=\"$action\"";
	}

	return 	"<span class='puce_article' id='$nom$type$id' dir='$lang_dir'$over>"
	. $inser_puce
	. '</span>';
}

?>
