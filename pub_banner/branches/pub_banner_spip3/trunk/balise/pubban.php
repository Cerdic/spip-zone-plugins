<?php
/**
 * @name 		PUBBAN
 * @author 		Piero Wbmstr <@link piero.wbmstr@gmail.com>
 * @license		http://creativecommons.org/licenses/by-nc-sa/3.0/ Creative Commons BY-NC-SA
 * @version 	1.0 (06/2009)
 * @package		Pub Banner
 * @subpackage	Balises
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

function balise_PUBBAN_dist($p) {
	return calculer_balise_dynamique($p, PUBBAN, array());
}

function balise_PUBBAN_dyn($banner_id, $tout='non') {
	$div = '';
	$tout_montrer = ($tout=='oui' || $tout=='tout');
	$border = ( _request('border') ) ? _request('border') : 0;
	if (is_numeric($banner_id))
		$banniere = pubban_recuperer_banniere($banner_id);
	else
		$banniere = pubban_recuperer_banniere_par_nom($banner_id);
	
	if ($banniere && ($tout_montrer || $banniere['statut'] == '2actif')) {
		$url = generer_url_public(_PUBBAN_ADDS_DISPLAYER)."&empl=".$banniere['titre_id'];
		if ($tout_montrer)
			$url .= "&tout=oui";
		$div = "<center><div class=\"pubban banniere_".$banniere['titre_id']."\"><iframe name='".$banniere['titre_id']."' src=\"".$url."\" width=\"".$banniere['width']."\" height=\"".$banniere['height']."\" marginwidth=\"auto\" marginheight=\"0\" hspace=\"0\" vspace=\"0\" frameborder=\"".$border."\" scrolling=\"no\"></iframe></div></center>";
	}
	echo $div;
}
?>