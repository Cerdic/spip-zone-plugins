<?php
#-----------------------------------------------------#
#  Plugin  : Couteau Suisse - Licence : GPL           #
#  Auteur  : Patrice Vanneufville, 2006               #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net   #
#  Infos : http://www.spip-contrib.net/?article2166   #
#-----------------------------------------------------#
if (!defined("_ECRIRE_INC_VERSION")) return;

define('_VAR_OUTIL', cs_code_echappement("<!--  VAR-OUTIL -->\n", 'OUTIL'));

// constantes utilisees dans la description des outils, sous forme @_CS_MACONSTANTE@

@define('_CS_EXEMPLE_COULEURS', '<br /><span style="font-weight:normal; font-size:85%;"><span style="background-color:black; color:white;">black/noir</span>, <span style="background-color:red;">red/rouge</span>, <span style="background-color:maroon;">maroon/marron</span>, <span style="background-color:green;">green/vert</span>, <span style="background-color:olive;">olive/vert&nbsp;olive</span>, <span style="background-color:navy; color:white;">navy/bleu&nbsp;marine</span>, <span style="background-color:purple;">purple/violet</span>, <span style="background-color:gray;">gray/gris</span>, <span style="background-color:silver;">silver/argent</span>, <span style="background-color:chartreuse;">chartreuse/vert&nbsp;clair</span>, <span style="background-color:blue;">blue/bleu</span>, <span style="background-color:fuchsia;">fuchsia/fuchia</span>, <span style="background-color:aqua;">aqua/bleu&nbsp;clair</span>, <span style="background-color:white;">white/blanc</span>, <span style="background-color:azure;">azure/bleu&nbsp;azur</span>, <span style="background-color:bisque;">bisque/beige</span>, <span style="background-color:brown;">brown/brun</span>, <span style="background-color:blueviolet;">blueviolet/bleu&nbsp;violet</span>, <span style="background-color:chocolate;">chocolate/brun&nbsp;clair</span>, <span style="background-color:cornsilk;">cornsilk/rose&nbsp;clair</span>, <span style="background-color:darkgreen;">darkgreen/vert&nbsp;fonce</span>, <span style="background-color:darkorange;">darkorange/orange&nbsp;fonce</span>, <span style="background-color:darkorchid;">darkorchid/mauve&nbsp;fonce</span>, <span style="background-color:deepskyblue;">deepskyblue/bleu&nbsp;ciel</span>, <span style="background-color:gold;">gold/or</span>, <span style="background-color:ivory;">ivory/ivoire</span>, <span style="background-color:orange;">orange/orange</span>, <span style="background-color:lavender;">lavender/lavande</span>, <span style="background-color:pink;">pink/rose</span>, <span style="background-color:plum;">plum/prune</span>, <span style="background-color:salmon;">salmon/saumon</span>, <span style="background-color:snow;">snow/neige</span>, <span style="background-color:turquoise;">turquoise/turquoise</span>, <span style="background-color:wheat;">wheat/jaune&nbsp;paille</span>, <span style="background-color:yellow;">yellow/jaune</span></span><span style="font-size:50%;"><br />&nbsp;</span>');
@define('_CS_EXEMPLE_COULEURS2', "\n-* <code>Lorem ipsum [rouge]dolor[/rouge] sit amet</code>\n-* <code>Lorem ipsum [red]dolor[/red] sit amet</code>.");
@define('_CS_EXEMPLE_COULEURS3', "\n-* <code>Lorem ipsum [fond rouge]dolor[/fond rouge] sit amet</code>\n-* <code>Lorem ipsum [bg red]dolor[/bg red] sit amet</code>.");
@define('_CS_ASTER', '<sup>(*)</sup>');
@define('_CS_DIR_TMP', cs_canonicalize(_DIR_RESTREINT_ABS._DIR_TMP));
@define('_CS_FORUM_NOM', _T('forum_votre_nom'));
@define('_CS_TRAVAUX_TITRE', '<i>'._T('info_travaux_titre').'</i>');
@define('_CS_NOM_SITE', '<i>'.$GLOBALS['meta']['nom_site'].'</i>');
@define('_CS_CHOIX', _T('couteauprive:votre_choix'));
@define('_CS_FILE_OPTIONS', str_replace('../','',(defined('_FILE_OPTION') && strlen(_FILE_OPTION))?_FILE_OPTION:
	(defined('_SPIP19100')?_DIR_RESTREINT.'mes_options.php':_DIR_RACINE._NOM_PERMANENTS_INACCESSIBLES._NOM_CONFIG.'.php')
));

@define('_CS_PLUGIN_JQUERY192', defined('_SPIP19300')?'':_T('couteauprive:detail_jquery3'));
include_spip('inc/autoriser');
if(defined('_SPIP19200')) {
	// Qui sont les webmestres et les administrateurs ?
	include_spip('inc/texte');
	function def_liste_adminsitrateurs() {
		$webmestres = array();
		$s = spip_query("SELECT * FROM spip_auteurs WHERE statut='0minirezo'");
		$fetch = function_exists('sql_fetch')?'sql_fetch':'spip_fetch_array'; // compatibilite SPIP 1.92
		while ($qui = $fetch($s)) {
			$nom = typo($qui['nom']." (id_auteur=$qui[id_auteur])");
			if (autoriser('webmestre','','',$qui)) $webmestres[$qui['id_auteur']] = $nom;
			else if (autoriser('configurer','plugins','',$qui)) $admins[$qui['id_auteur']] = $nom;
		}
		@define('_CS_LISTE_WEBMESTRES', join(', ', $webmestres));
		@define('_CS_LISTE_ADMINS', join(', ', $admins));
	}
	def_liste_adminsitrateurs();
}

// fin des constantes

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
		$ok_valeur = $label._T($cs_variable['check'])._T($valeur?'couteauprive:2pts_oui':'couteauprive:2pts_non');
	}
	// ... ou un textarea ... ou une case input
	else {
		$len = $cs_variable['format']==_format_NOMBRE?4:0;
		$width = $len?'':'style="width:100%;" ';
		$lignes = $cs_variable['format']==_format_NOMBRE?0:strval($cs_variable['lignes']);
//			else $len=strlen(strval($valeur));
		$ok_input = $label .
			( $lignes < 2
				// <html></html> empechera SPIP de modifier le contenu des <input> ou <textarea>
				?"<html><input name='HIDDENCSVAR__$variable' value=\""
					. htmlspecialchars($valeur) . "\" type='text' size='$len' $width/></html>"
				:"<html><textarea rows='$lignes' name='HIDDENCSVAR__$variable' $width>"
					. htmlspecialchars($valeur) . '</textarea></html>'
			) . _VAR_OUTIL;
		$ok_valeur = $label.'<html>'.(strlen($valeur)?nl2br(echapper_tags($valeur)):'&nbsp;'._T('couteauprive:variable_vide')).'</html>';
	}
	$ok_input_ .= $ok_input; $ok_valeur_ .= $ok_valeur;
}

function description_outil_input_callback($matches) {
	return '<fieldset><legend>'._T('couteauprive:label:'.$matches[3]).'</legend><div style="margin:0;">'.$matches[1].'</div></fieldset>';
}
function description_outil_const_callback($matches) {
	return defined($matches[1])?constant($matches[1]):'';
}
function description_outil_descrip_callback($matches) {
	return _T("couteauprive:$matches[1]:description$matches[2]");
}

// renvoie la description de $outil_ : toutes les %variables% ont ete remplacees par le code adequat
function inc_description_outil_dist($outil_, $url_self, $modif=false) {
	global $outils, $cs_variables, $metas_vars;
	$outil = &$outils[$outil_];
	$actif = $outil['actif'];
	$index = $outil['index'];
	// la description de base est a priori dans le fichier de langue
	$descrip = isset($outil['description'])?$outil['description']:_T('couteauprive:'.$outil['id'].':description');
	// reconstitution d'une description eventuellement morcelee
	// exemple : <:mon_outil:3:> est remplace par _T('couteauprive:mon_outil:description3')
	$descrip = preg_replace_callback(',<:([a-zA-Z_][a-zA-Z0-9_-]*):([0-9]*):>,', 'description_outil_descrip_callback', $descrip);
	// remplacement des zone input de format [[label->variable]]
	$descrip = preg_replace(',(\[\[([^][]*)->([^]]*)\]\]),msS', '<fieldset><legend>\\2</legend><div style="margin:0;">\\3</div></fieldset>', $descrip);
	// remplacement des zone input de format [[tata %variable% toto]] en utilisant _T('couteauprive:label:variable') comme label
	$descrip = preg_replace_callback(',\[\[(([^][]*)%([a-zA-Z_][a-zA-Z0-9_]*)%([^]]*))\]\],msS', 'description_outil_input_callback', $descrip);
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
		$bouton = "<input type='submit' class='fondo' value=\"".($c>1?_T('couteauprive:modifier_vars', array('nb'=>$c)):_T('bouton_modifier'))."\" />";
		if($c>1) $ok_input .= "<div style=\"margin-top: 0; text-align: right;\">$bouton</div>";
			else $ok_input = str_replace(_VAR_OUTIL, $bouton, $ok_input);
	} else
		$ok_input = $ok_valeur . '<div style="margin-top: 0; text-align: right;">'._T('couteauprive:validez_page').' <span class="fondo" style="cursor:pointer; padding:0.2em;" onclick="submit_general('.$index.')">'._T('bouton_valider').'</span></div>';
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
	$res = preg_replace(',(<br />)?</fieldset><fieldset><legend>-</legend>,', '<br />', $res);
	$res = str_replace('</label></div><div style="margin:0;"><label><input type="checkbox"', '</label>&nbsp;<label><input type="checkbox"', $res);
	// remplacement des puces
	$res = str_replace('@puce@', definir_puce(), $res);
	// remplacement des constantes
	$res = preg_replace_callback(',@(_CS_[a-zA-Z0-9_]+)@,', 'description_outil_const_callback', $res);
	// deuxieme reconstitution d'une description introduite par les constantes
//	$res = preg_replace_callback(',<:([a-zA-Z_][a-zA-Z0-9_-]*):([0-9]*):>,', 'description_outil_descrip_callback', $res);
	// remplacement des blocs avec style
	$res = str_replace(array('<q1>','</q1>','<q2>','</q2>','<q3>','</q3>'),
//		array('<div class="q1">', '</div>', '<div class="q2">', '</div>', '<div class="q3">', '</div>'), $res);
		array("<div style='margin:0 2em;'>", '</div>', '<div style="margin-left:2em;">', '</div>', '<div style="font-size:85%;">', '</div>'), $res);

	$modif=$modif?'<div style="font-weight:bold; color:green; margin:0.4em; text-align:center">&gt;&nbsp;'._T('couteauprive:vars_modifiees').'&nbsp;&lt;</div>':'';
	return cs_ajax_action_greffe("description_outil-$index", $res, $modif);
}
?>
