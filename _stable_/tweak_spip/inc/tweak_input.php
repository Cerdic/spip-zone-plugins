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
	if (preg_match(',^"(.*)"$,', trim($valeur), $matches2)) $valeur = str_replace('\"','"',$matches2[1]);
		else $len=strlen(strval($valeur));
	
	// est-ce des boutons radio ? forme : choixX(choixY=traductionY|choixX=traductionX|etc)
	if (tweak_is_radio($valeur, $matches3)) {
		$choix=$matches3[1]; $liste = $matches3[2]; $lesoptions = explode('|', $liste);
		$ok_input = $label; $traducs = array();
		foreach($lesoptions as $option) {
			list($code, $traduc) = explode('=', $option, 2);
			$traducs[$code] = _T($traduc);
			$ok_input .= 
"<input id=\"label_{$variable}_$code\" type=\"radio\"".($choix==$code?' checked="checked"':'')." value=\"$code($liste)\" name=\"HIDDENTWEAKVAR__$variable\"/>
<label for=\"label_{$variable}_$code\">".($choix==$code?'<b>':'').$traducs[$code].($choix==$code?'</b>':'').'</label> ';
		}
		$ok_valeur = $label.(strlen($choix)?ucfirst(_T($traducs[$choix])):'&nbsp;-');
	} 
	// eh non, donc juste une case input
	else {
		$ok_input = "$label<input name='HIDDENTWEAKVAR__$variable' value=\"".htmlspecialchars($valeur)."\" type='text' size='$len' />";
		$ok_valeur = $label.(strlen($valeur)?"$valeur":'&nbsp;-');
	}

	// on ne peut pas modifier les variables si le tweak est inactif
	if ($actif) $ok_input .= "<input type='submit' class='fondo' value=\""._T('bouton_modifier')."\" />";
		else $ok_input = $ok_valeur.' ('._T('tweak:validez_page').')';
	// HIDDENTWEAKVAR__ pour eviter d'avoir deux inputs du meme nom...
	$ok_visible = $actif?str_replace("HIDDENTWEAKVAR__","",$ok_input):$ok_valeur;

	$res = "\n<input type='hidden' value='$variable' name='variable'>"
		. "\n<div id='tweak_$index-input' style='position:absolute; visibility:hidden;' ><p>$ok_input</p></div>"
		. "\n<div id='tweak_$index-valeur' style='position:absolute; visibility:hidden;' ><p>$ok_valeur</p></div>\n"
		. "\n<div id='tweak_$index-visible' ><p>$ok_visible</p></div>";

	// syntaxe : ajax_action_auteur($action, $id, $script, $args='', $corps=false, $args_ajax='', $fct_ajax='')
	$res = ajax_action_auteur('tweak_input', $index, $url_self, "index=$index&variable=$variable&valeur=$valeur&actif=".intval($actif)."&label=".urlencode($label), $res);

tweak_log("inc_tweak_input_dist($index, $variable, $valeur, [label], $actif, $url_self)");
return $res;

}
?>