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
	
	// est-ce des boutons radio ?
	if (preg_match(',([0-9]*)\(('.'[A-Za-z_:-]+\|[A-Za-z_:|-]+'.')\),', $valeur, $matches3)) {
		$liste = $matches3[2]; $langues = explode('|', $liste); $choix=intval($matches3[1]);
		$ok_input = $valeur.'-'. $label;
		foreach($langues as $i => $l) $ok_input .= 
"<input id=\"label_$variable\" type=\"radio\"".($choix==$i?' checked="checked"':'')." value=\"$i($liste)\" name=\"HIDDENTWEAKVAR__$variable\"/>
<label for=\"label_$variable\">".($choix==$i?'<b>':'')._T($l).($choix==$i?'</b>':'').'</label>';
		$ok_valeur = $label.(strlen($matches3[1])?_T($langues[$choix]):'&nbsp;-');
/*
<input id="label_1" type="radio" value="non" name="activer_breves"/>
<label for="label_1">Ne pas utiliser les brèves</label>
*/	
	} 
	// eh non, donc juste une case input
	else {
		$ok_input = "$label<input name='HIDDENTWEAKVAR__$variable' value='$valeur' type='text' size='$len' />";
		$ok_valeur = $label.(strlen($valeur)?$valeur:'&nbsp;-');
	}

	$ok_input .= "<input type='submit' class='fondo' value=\""._T('bouton_modifier')."\" />";
	// HIDDENTWEAKVAR__ pour eviter d'avoir deux inputs du meme nom...
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