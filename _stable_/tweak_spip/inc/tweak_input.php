<?php
#-----------------------------------------------------#
#  Plugin  : Tweak SPIP - Licence : GPL               #
#  Auteur  : Patrice Vanneufville, 2006               #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net   #
#  Infos : http://www.spip-contrib.net/?article1554   #
#-----------------------------------------------------#
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/texte');
include_spip('inc/layer');
include_spip('inc/presentation');
include_spip('inc/message_select');


// affiche un petit input
function inc_tweak_input_dist($index, $variable, $valeur, $label, $actif, $url_self) {

	$len=0;
	if (preg_match(',^"(.*)"$,', trim($valeur), $matches2)) $valeur = str_replace('"','&quot;',$matches2[1]);
		else $len=strlen(strval($valeur));
	// HIDDENTWEAKVAR__ pour eviter d'avoir deux inputs du meme nom...
	$ok_input = "$label<input name='HIDDENTWEAKVAR__$variable' value='$valeur' type='text' size='$len' />"
	. "<input type='submit' class='fondo' value=\""._T('bouton_modifier')."\" />";
	$ok_valeur = $label.$valeur.(strlen($valeur)?'':'&nbsp;-');
	$ok_visible = $actif?str_replace("HIDDENTWEAKVAR__","",$ok_input):$ok_valeur;
	
	$res = "<input type='hidden' value='$variable' name='variable'>"
	. "<div id='tweak_$index-input' style='position:absolute; visibility:hidden;' >$ok_input</div>"
	. "<div id='tweak_$index-valeur' style='position:absolute; visibility:hidden;' >$ok_valeur</div>\n"
	. "<div id='tweak_$index-visible' >$ok_visible</div>";

	// syntaxe : ajax_action_auteur($action, $id, $script, $args='', $corps=false, $args_ajax='', $fct_ajax='')
	$res = ajax_action_auteur('tweak_input', $index, $url_self, "index=$index&variable=$variable&valeur=$valeur&actif=".intval($actif)."&label=".urlencode($label), $res);

tweak_log("inc_tweak_input_dist($index, $variable, $valeur, [label], $actif, $url_self)");
return "<div id='tweak_input-$index'>$res</div>";

}
?>