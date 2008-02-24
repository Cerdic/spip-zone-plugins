<?php
#-----------------------------------------------------#
#  Plugin  : Couteau Suisse - Licence : GPL           #
#  Auteur  : Patrice Vanneufville, 2006               #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net   #
#  Infos : http://www.spip-contrib.net/?article2166   #
#-----------------------------------------------------#
if (!defined("_ECRIRE_INC_VERSION")) return;

define('_VAR_OUTIL', cs_code_echappement("<!--  VAR-OUTIL -->\n", 'OUTIL'));

include_spip('inc/actions');
include_spip('inc/texte');
include_spip('inc/layer');
include_spip('inc/presentation');
include_spip('inc/message_select');

// retourne le code html qu'il faut pour fabriquer le formulaire de l'outil proprietaire
function description_outil_une_variable($index, $outil, $variable, $label, &$ok_input_, &$ok_valeur_) {
	global $cs_variables, $metas_vars;
	$actif = $outil['actif'];
	// la valeur de la variable n'est stockee dans les metas qu'au premier post
	if (isset($metas_vars[$variable])) $valeur = $metas_vars[$variable];
		else $valeur = cs_get_defaut($variable);
	$valeur = cs_retire_guillemets($valeur);
//cs_log(" -- description_outil_une_variable($index) - Traite %$variable%");
	$cs_variable = &$cs_variables[$variable];

	// si la variable necessite des boutons radio
	if (is_array($radios = &$cs_variable['radio'])) {
		$ok_input = $label;
		$i = 0; $nb = intval($cs_variable['radio/ligne']);
		foreach($radios as $code=>$traduc) {
			$br = (($nb>0) && ( ++$i % $nb == 0))?'<br />':' ';
			$ok_input .=
				"<label><input id=\"label_{$variable}_$code\" type=\"radio\""
				.($valeur==$code?' checked="checked"':'')." value=\"$code\" name=\"HIDDENCSVAR__$variable\"/>"
				.($valeur==$code?'<b>':'')._T($traduc).($valeur==$code?'</b>':'')
				."</label>$br";
		}
		$ok_input .= _VAR_OUTIL;
		$ok_valeur = $label.(strlen($valeur)?ucfirst(_T($radios[$valeur])):'&nbsp;-');
	}
	// ... ou une case a cocher
	elseif (isset($cs_variable['check'])) {
		$ok_input = $label;
		$ok_input .= '<label><input type="checkbox" '.($valeur?' checked="checked"':'')." value=\"1\" name=\"HIDDENCSVAR__$variable\"/>"
			.($valeur?'<b>':'')._T($cs_variable['check']).($valeur?'</b>':'').'</label>';

		$ok_input .= _VAR_OUTIL;
		$ok_valeur = $label._T($cs_variable['check'])._T($valeur?'desc:2pts_oui':'desc:2pts_non');
	}
	// ... ou un textarea ... ou une case input
	else {
		$len = $cs_variable['format']=='nombre'?4:0;
		$width = $len?'':'style="width:100%;" ';
		$lignes = $cs_variable['format']=='nombre'?0:strval($cs_variable['lignes']);
//			else $len=strlen(strval($valeur));
		$ok_input = $label .
			( $lignes < 2
				// <html></html> empechera SPIP de modifier le contenu des <input> ou <textarea>
				?"<html><input name='HIDDENCSVAR__$variable' value=\""
					. htmlspecialchars($valeur) . "\" type='text' size='$len' $width/></html>"
				:"<html><textarea rows='$lignes' name='HIDDENCSVAR__$variable' $width>"
					. htmlspecialchars($valeur) . '</textarea></html>'
			) . _VAR_OUTIL;
		$ok_valeur = $label.'<html>'.(strlen($valeur)?nl2br(echapper_tags($valeur)):'&nbsp;'._T('desc:variable_vide')).'</html>';
	}
	$ok_input_ .= $ok_input; $ok_valeur_ .= $ok_valeur;
}

// renvoie la description de $outil_ : toutes les %variables% ont ete remplacees par le code adequat
function inc_description_outil_dist($outil_, $url_self, $modif=false) {
	global $outils, $cs_variables, $metas_vars;
	$outil = &$outils[$outil_];
	$actif = $outil['actif'];
	$index = $outil['index'];
	// la description de base est a priori dans le fichier de langue
	$descrip = isset($outil['description'])?$outil['description']:_T('desc:'.$outil['id'].':description');
	// remplacement des puces
	$descrip = str_replace('#PUCE', definir_puce(), $descrip);
	// remplacement des zone input de format [[label->varable]]
	$descrip = preg_replace(',(\[\[([^][]*)->([^]]*)\]\]),msS', '<fieldset><legend>\\2</legend><div style="margin:0;">\\3</div></fieldset>', $descrip);
	// remplacement des variables de format : %variable%
	$t = preg_split(',%([a-zA-Z_][a-zA-Z0-9_]*)%,', $descrip, -1, PREG_SPLIT_DELIM_CAPTURE);

//cs_log("inc_description_outil_dist() - Parse la description de '$outil_'");
	$ok_input = $ok_valeur = $ok_visible = '';
	$outil['nb_variables'] = 0; $variables = array();
	for($i=0;$i<count($t);$i+=2) if (isset($t[$i+1]) && strlen($var=trim($t[$i+1]))) {
		// si la variable est presente on fabrique le input
		if (isset($cs_variables[$var])) {
			description_outil_une_variable(
				$index + (++$outil['nb_variables']),
				$outil, $var,
				$t[$i],
				$ok_input, $ok_valeur);
			$variables[] = $var;
		} else {
			// probleme a regler dans config_outils.php !
			$temp = $t[$i]."[$var?]"; $ok_input .= $temp; $ok_valeur .= $temp;
		}
	} else { $ok_input .= $t[$i]; $ok_valeur .= $t[$i]; }
	$outil['variables'] = $variables;
	$c = $outil['nb_variables'];

	// bouton 'Modifier' : en dessous du texte s'il y a plusieurs variables, a la place de _VAR_OUTIL s'il n'y en a qu'une.
	// attention : on ne peut pas modifier les variables si l'outil est inactif
	if ($actif) {
		$bouton = "<input type='submit' class='fondo' value=\"".($c>1?_T('desc:modifier_vars', array('nb'=>$c)):_T('bouton_modifier'))."\" />";
		if($c>1) $ok_input .= "<div style=\"margin-top: 0; text-align: right;\">$bouton</div>";
			else $ok_input = str_replace(_VAR_OUTIL, $bouton, $ok_input);
	} else
		$ok_input = $ok_valeur . '<div style="margin-top: 0; text-align: right;">'._T('desc:validez_page').' <span class="fondo" style="cursor:pointer; padding:0.2em;" onclick="submit_general('.$index.')">'._T('bouton_valider').'</span></div>';
	// nettoyage...
	$ok_input = str_replace(_VAR_OUTIL, '', $ok_input);
	// HIDDENCSVAR__ pour eviter d'avoir deux inputs du meme nom...
	$ok_visible .= $actif?str_replace("HIDDENCSVAR__", "", $ok_input):$ok_valeur;
	$variables = urlencode(serialize($variables));
	$res = "\n<div id='tweak$index-visible' >$ok_visible</div>";
	if($c) {
		$res = "\n<input type='hidden' value='$variables' name='variables'><input type='hidden' value='$outil_' name='outil'>"
			. "\n<div id='tweak$index-input' style='position:absolute; visibility:hidden;' >$ok_input</div>"
			. "\n<div id='tweak$index-valeur' style='position:absolute; visibility:hidden;' >$ok_valeur</div>\n"
			. $res;
		// syntaxe : ajax_action_auteur($action, $id, $script, $args='', $corps=false, $args_ajax='', $fct_ajax='')
		$res = ajax_action_auteur('description_outil', $index, $url_self, "modif=oui&cmd=descrip&outil={$outil['id']}", "$res");
	}
//cs_log(" FIN : inc_description_outil_dist({$outil['id']}) - {$outil['nb_variables']} variables(s) trouvee(s)");
	$res = preg_replace(',(<br />)?</fieldset><fieldset><legend></legend>,', '', $res);
	$res = str_replace('</label></div><div style="margin:0;"><label><input type="checkbox"', '</label>&nbsp;<label><input type="checkbox"', $res);
	
	$modif=$modif?'<div style="font-weight:bold; color:green; margin:0.4em; text-align:center">&gt;&nbsp;'._T('desc:vars_modifiees').'&nbsp;&lt;</div>':'';
	return cs_ajax_action_greffe("description_outil-$index", $res, $modif);
}
?>
