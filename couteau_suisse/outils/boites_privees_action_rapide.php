<?php

/*
module mon_outil_action_rapide.php inclu :
 - dans la description de l'outil en page de configuration
 - apres l'appel de ?exec=action_rapide&arg=boites_privees|argument
*/

// Fonction appelee par exec/action_rapide : ?exec=action_rapide&arg=type_urls|URL_objet (pipe obligatoire)
// Renvoie un formulaire en partie privee
function boites_privees_URL_objet_exec() {
cs_log("INIT : exec_action_rapide_dist() - Preparation du retour par Ajax (donnees transmises par GET)");
	$script = _request('script');
cs_log(" -- fonction = $fct - script = $script - arg = $arg");
	cs_minipres(!preg_match('/^\w+$/', $script));
	$res = function_exists($fct = 'action_rapide_'._request('fct'))?$fct():'';
cs_log(" FIN : exec_description_outil_dist() - Appel maintenant de ajax_retour() pour afficher le formulaire de la boite privee");	
	ajax_retour($res);
}

// Fonction qui centralise : 
//	- 1er affichage : action_rapide_tri_auteurs($id_article)
//	- appel exec : action_rapide_tri_auteurs()
// 	- appel action : action_rapide_tri_auteurs($id_article, $id_auteur, $monter)
function action_rapide_tri_auteurs($id_article=0, $id_auteur=0, $monter=true) {
spip_log("action_rapide_tri_auteurs : $id_article, $id_auteur, $monter");
	// si appel action, l'auteur est non nul...
	 if($id_auteur) {
		$s = sql_select('id_auteur', 'spip_auteurs_articles', "id_article=$id_article");
		$i=0; $j=0;
		while ($a = sql_fetch($s)) {
			if($a['id_auteur']==$id_auteur) { $i = $a['id_auteur']; break; }
			$j = $a['id_auteur'];
		}
		if(!$monter && $i && ($a = sql_fetch($s))) $j = $a['id_auteur'];
		spip_log("action_rapide_tri_auteurs, article $id_article : echange entre l'auteur $i et l'auteur $j");
		if($i && $j) {
			sql_update("spip_auteurs_articles", array('id_auteur'=>-99), "id_article=$id_article AND id_auteur=$i");
			sql_update("spip_auteurs_articles", array('id_auteur'=>$i), "id_article=$id_article AND id_auteur=$j");
			sql_update("spip_auteurs_articles", array('id_auteur'=>$j), "id_article=$id_article AND id_auteur=-99");
		}
		// action terminee, pret pour la redirection exec !
		return;
	 }
	$id = $id_article?$id_article:_request('id_article');
	include_spip('public/assembler'); // pour recuperer_fond(), SPIP < 2.0
	$texte = trim(recuperer_fond('fonds/tri_auteurs', array('id_article'=>$id)));
	// syntaxe : ajax_action_auteur($action, $id, $script, $args='', $corps=false, $args_ajax='', $fct_ajax='')
	if(strlen($texte))
		// un clic sur 'monter' ou 'descendre' va permettre une redirection vers
		// les fonctions : boites_privees_URL_objet_exec(), puis action_rapide_tri_auteurs()
		$texte = ajax_action_auteur('action_rapide', 'tri_auteurs', 'articles', "arg=boites_privees|URL_objet&fct=tri_auteurs&id_article=$id#bp_tri_auteurs_corps", $texte);
	// si appel exec, l'id article est nul...
	if(!$id_article) return $texte;
	// ici, 1er affichage !
	if(!strlen($texte)) return '';
	// SPIP < 2.0
	if(!defined('_SPIP19300')) return debut_cadre_relief(find_in_path('img/couteau-24.gif'), true)
		. cs_div_configuration()
		. "<div class='verdana1' style='text-align: left;'>"
		. block_parfois_visible('bp_ta', '<b>'._T('couteau:tri_auteurs').'</b>', "<div id='bp_tri_auteurs_corps'>$texte</div>", 'text-align: center;')
		. "</div>"
		. fin_cadre_relief(true);
	// SPIP >= 2.0
	return cadre_depliable(find_in_path('img/couteau-24.gif'),
		cs_div_configuration().'<b>'._T('couteau:tri_auteurs').'</b>',
		false,	// true = deplie
		"<div id='bp_tri_auteurs_corps'>$texte</div>",
		'bp_tri_auteurs');
}

