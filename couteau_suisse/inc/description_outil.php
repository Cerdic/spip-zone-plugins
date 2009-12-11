<?php
#-----------------------------------------------------#
#  Plugin  : Couteau Suisse - Licence : GPL           #
#  Auteur  : Patrice Vanneufville, 2006               #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net   #
#  Infos : http://www.spip-contrib.net/?article2166   #
#-----------------------------------------------------#
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('cout_define');
cout_define('description_outils');

include_spip('inc/actions');
include_spip('inc/texte');
include_spip('inc/layer');
include_spip('inc/presentation');
include_spip('inc/message_select');

function cs_ajax_outil_greffe($idom, $corps)	{
	// en fait, ajax uniquement si une modif est demandee...
	return _request('modif')=='oui'
		?'<div class="cs_modif_ok">&gt;&nbsp;'._T('couteauprive:vars_modifiees')."&nbsp;&lt;</div>$corps"
		:"\n<div id='$idom'>$corps\n</div>\n";
}

// retourne le code html qu'il faut pour fabriquer le formulaire de l'outil proprietaire
function description_outil_une_variable($index, $outil, $variable, $label) {
	global $cs_variables, $metas_vars;
	$actif = $outil['actif'];
	// la valeur de la variable n'est stockee dans les metas qu'au premier post
	if (isset($metas_vars[$variable])) $valeur = $metas_vars[$variable];
		else $valeur = cs_get_defaut($variable);
	$valeur = cs_retire_guillemets($valeur);
//cs_log(" -- description_outil_une_variable($index) - Traite %$variable%");
	$cs_variable = &$cs_variables[$variable];
	// autorisations de variables
	include_spip('inc/autoriser');
	$cs_variable['disabled'] = $disab 
		= autoriser('configurer', 'variable', 0, NULL, array('nom'=>$cs_variable['nom'], 'outil'=>$outil))?'':' disabled="disabled"';
	// si la variable necessite des boutons radio
	if (is_array($radios = &$cs_variable['radio'])) {
		if(!$actif) {
			$code = _T($radios[$valeur]);
			return "<input type=\"hidden\" name=\"$variable\" class=\"cs_hidden_checkbox\" value=\"$code\" />"
				. $label . (strlen($valeur)?ucfirst($code):'&nbsp;-');
		}
		$res = "$label <ul>";
		$i = 0; $nb = isset($cs_variable['radio/ligne'])?intval($cs_variable['radio/ligne']):0;
		foreach($radios as $code=>$traduc) {
			$br = (($nb>0) && ( ++$i % $nb == 0))?'</ul><ul>':''; 
			$res .=
				"<li><label><input id=\"label_{$variable}_$code\" class=\"cs_input_checkbox\" type=\"radio\""
				.($valeur==$code?' checked="checked"':'')." value=\"$code\" name=\"$variable\"$disab />"
				.($valeur==$code?'<b>':'')._T($traduc).($valeur==$code?'</b>':'')
				."</label></li>$br";
		}
		return $res.'</ul>'._VAR_OUTIL;
	}
	// ... ou une case a cocher
	if (isset($cs_variable['check'])) {
		if(!$actif)
			return $label._T($cs_variable['check'])._T($valeur?'couteauprive:2pts_oui':'couteauprive:2pts_non');
		return $label.'<label><input type="checkbox" '.($valeur?' checked="checked"':'')." value=\"1\" name=\"$variable\" $disab/>"
			.($valeur?'<b>':'')._T($cs_variable['check']).($valeur?'</b>':'').'</label>'
			. _VAR_OUTIL;
	}
	// ... ou un textarea ... ou une case input
	if(!$actif)
		return $label.'<html>'.(strlen($valeur)?nl2br(echapper_tags($valeur)):'&nbsp;'._T('couteauprive:variable_vide')).'</html>';
	$len = $cs_variable['format']==_format_NOMBRE?4:0;
	$width = $len?'':'style="width:100%;" ';
	$lignes = !isset($cs_variable['lignes']) || ($cs_variable['format']==_format_NOMBRE)?0:strval($cs_variable['lignes']);
	return $label .
		( $lignes < 2
			// <html></html> empechera SPIP de modifier le contenu des <input> ou <textarea>
			?"<html><input name='$variable' value=\""
				. htmlspecialchars($valeur) . "\" type='text' size='$len' $width $disab/></html>"
			:"<html><textarea rows='$lignes' name='$variable' $width$disab>"
				. htmlspecialchars($valeur) . '</textarea></html>'
		) . _VAR_OUTIL;
}

// callback sur les labels de zones input ; format : [[label->qq chose]]
// regexpr : ,\[\[([^][]*)->([^]]*)\]\],msS
function description_outil_input1_callback($matches) {
	global $cs_input_variable;
	// pas de label : [[->qq chose]]
	if(!strlen($matches[1])) return "<fieldset><div>$matches[2]</div></fieldset>";
	// retour a la ligne : [[-->qq chose]]
	if($matches[1]=='-') return "<fieldset> <div>$matches[2]</div></fieldset>";
	// format complet : [[label->qq chose]]
	return "<fieldset><legend>$matches[1]</legend><div>$matches[2]</div></fieldset>";
}

// callback sur les label de zones input en utilisant _T('couteauprive:label:variable') ; format [[qq chose %variable% qq chose]]
// regexpr : ,\[\[((.*?)%([a-zA-Z_][a-zA-Z0-9_]*)%(.*?))\]\],msS
function description_outil_input2_callback($matches) {
	global $cs_input_variable;
	$cs_input_variable[] = $matches[3];
	return '<fieldset><legend>'._T('couteauprive:label:'.$matches[3]).'</legend><div>'.$matches[1].'</div></fieldset>';
}

function description_outil_liens_callback($matches) {
	$nom = _T("couteauprive:$matches[1]:nom");
	return '<a href="'.generer_url_ecrire('admin_couteau_suisse', 'cmd=descrip&outil='.$matches[1])
		."\" id=\"href_$matches[1]\" onclick=\"javascript:return cs_href_click(this);\">$nom</a>";
}

function cs_input_variable_callback($matches) {
	$a = " valeur_{$matches[1]}_";
	$tmp = str_replace('/',$a, $matches[3]);
	return "<div class='groupe_{$matches[1]} $a$tmp'>";
}

// renvoie la description de $outil_ : toutes les %variables% ont ete remplacees par le code adequat
function inc_description_outil_dist($outil_, $url_self, $modif=false) {
	global $outils, $cs_variables, $metas_vars;
	$outil = &$outils[$outil_];
	$actif = $outil['actif'];
	$index = $outil['index'];
	cout_define($outil_);
	// la description de base est a priori dans le fichier de langue
	$descrip = isset($outil['description'])?$outil['description']:_T('couteauprive:'.$outil['id'].':description');
	// reconstitution d'une description eventuellement morcelee
	// exemple : <:mon_outil:3:> est remplace par _T('couteauprive:mon_outil:description3')
	$descrip = preg_replace_callback(',<:([a-zA-Z_][a-zA-Z0-9_-]*):([0-9]*):>,', 
		create_function('$matches','return _T("couteauprive:$matches[1]:description$matches[2]");'), $descrip);
	global $cs_input_variable;
	$cs_input_variable = array();
	// remplacement des zones input de format [[label->qq chose]]
	$descrip = preg_replace_callback(',\[\[([^][]*)->([^]]*)\]\],msS', 'description_outil_input1_callback' , $descrip);
	// remplacement des zones input de format [[qq chose %variable% qq chose]] en utilisant _T('couteauprive:label:variable') comme label
	$descrip = preg_replace_callback(',\[\[((.*?)%([a-zA-Z_][a-zA-Z0-9_]*)%(.*?))\]\],msS', 'description_outil_input2_callback', $descrip);

	// recherche des blocs <variable></variable> eventuels associes pour du masquage/demasquage
	foreach($cs_input_variable as $v) {
		$descrip = str_replace("</$v>", '</div>', preg_replace_callback(",<($v)\s+valeur=(['\"])(.*?)\\2\s*>,", 'cs_input_variable_callback', $descrip));
	}
	unset($cs_input_variable);
	// remplacement des variables de format : %variable%
	$t = preg_split(',%([a-zA-Z_][a-zA-Z0-9_]*)%,', $descrip, -1, PREG_SPLIT_DELIM_CAPTURE);

//cs_log("inc_description_outil_dist() - Parse la description de '$outil_'");
	$res = '';
	$nb_disabled = $nb_variables = 0; $variables = array();
	for($i=0;$i<count($t);$i+=2) if (isset($t[$i+1]) && strlen($var=trim($t[$i+1]))) {
		// si la variable est presente on fabrique le input
		if (isset($cs_variables[$var])) {
			$res .= description_outil_une_variable(
				$index + (++$nb_variables),
				$outil, $var,
				$t[$i]);
			$variables[] = $var;
			if($cs_variables[$var]['disabled']) ++$nb_disabled;
		} else {
			// probleme a regler dans config_outils.php !
			$temp = $t[$i]."[$var?]"; $res .= $temp;
		}
	} else 
		$res .= $t[$i];
	$outil['variables'] = $variables;
	$outil['nb_variables'] = $nb_variables;
	$outil['nb_disabled'] = $nb_disabled;

	// si ce n'est qu'une simple initialisation, on sort
	if(!$modif) return;

	// bouton 'Modifier' : en dessous du texte s'il y a plusieurs variables, a la place de _VAR_OUTIL s'il n'y en a qu'une.
	// attention : on ne peut pas modifier les variables si l'outil est inactif
	if ($actif) {
		$bouton = "<input type='submit' class='fondo' style='margin-left:1em;' value=\"".($nb_variables>1?_T('couteauprive:modifier_vars_0'):_T('bouton_modifier'))."\" />";
		if($nb_variables>1) $res .= "<div class=\"cs_bouton\">$bouton</div>";
			else $res = str_replace(_VAR_OUTIL, $bouton, $res);
	}
	$res = "\n<div id='cs_inner_outil-$index' >" . str_replace(array('<ul></ul>',_VAR_OUTIL),'',$res) . '</div>';
	// si des variables sont trouvees ?
	if($nb_variables) {
		$variables = urlencode(serialize($variables));
		// syntaxe : ajax_action_auteur($action, $id, $script, $args='', $corps=false, $args_ajax='', $fct_ajax='')
		$res = ajax_action_auteur('description_outil', $index, $url_self, "modif=oui&cmd=descrip&outil={$outil['id']}", 
			"\n<input type='hidden' value='$variables' name='variables' /><input type='hidden' value='$outil_' name='outil' />"	. $res);
	}
//cs_log(" FIN : inc_description_outil_dist({$outil['id']}) - {$outil['nb_variables']} variables(s) trouvee(s)");
	// remplacement des labels en doublon
	$res = preg_replace_callback(',<:label:([a-zA-Z_][a-zA-Z0-9_-]*):>,', 
		create_function('$matches','return _T("couteauprive:label:$matches[1]");'), $res);
	// remplacement des blocs avec style. ex : <q2>bla bla</q2>
	$res = preg_replace(',</q(\d)>,','</div>', preg_replace(',<q(\d)>,','<div class="q$1">', $res));
	// remplacement des inputs successifs sans label : [[%var1%]][[->%var2%]] ou [[%var1%]][[-->%var2%]]
	$res = preg_replace(',(<br />)?</fieldset><fieldset>( ?<div>),', '$2', $res);
	// remplacement des puces
	$res = str_replace('@puce@', definir_puce(), $res);
	// remplacement des constantes de forme @_CS_XXXX@
	$res = preg_replace_callback(',@(_CS_[a-zA-Z0-9_]+)@,', 
		create_function('$matches','return defined($matches[1])?constant($matches[1]):"";'), $res);
	// remplacement des liens sur un outil
	$res = preg_replace_callback(',\[\.->([a-zA-Z_][a-zA-Z0-9_-]*)\],', 'description_outil_liens_callback', $res);

	return cs_ajax_outil_greffe("description_outil-$index", $res);
}
?>