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

function balise_PUBBAN_dyn($p) {
	$div = '';
	$border = ( _request('border') ) ? _request('border') : 0;
	if (is_numeric($p))
		$emplacement = pubban_recuperer_emplacement($p);
	else
		$emplacement = pubban_recuperer_emplacement_par_nom($p);
	
	if($emplacement['statut'] == '2actif') {
//		$div = "<center><div class=\"pubban pubban_".$emplacement['titre']."\"><iframe name='".$emplacement['titre']."' src='".generer_url_public(_PUBBAN_ADDS_DISPLAYER)."&empl=".$emplacement['titre']."' width=".$emplacement['width']." height=".$emplacement['height']." marginwidth=auto marginheight=0 hspace=0 vspace=0 frameborder=".$border." scrolling=no></iframe></div></center>";
		$div = "<center><div class=\"pubban pubban_".$emplacement['titre_id']."\"><iframe name='".$emplacement['titre_id']."' src='".generer_url_public(_PUBBAN_ADDS_DISPLAYER)."&empl=".$emplacement['titre_id']."' width=".$emplacement['width']." height=".$emplacement['height']." marginwidth=auto marginheight=0 hspace=0 vspace=0 frameborder=".$border." scrolling=no></iframe></div></center>";
	}
	echo $div;
}
?>