<?php
#-----------------------------------------------------#
#  Plugin  : Couteau Suisse - Licence : GPL           #
#  Auteur  : Patrice Vanneufville, 2007               #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net   #
#  Infos : http://www.spip-contrib.net/?article2166   #
#-----------------------------------------------------#
#  Fichier contenant les fonctions concernant la      #
#  description des outils.                            #
#-----------------------------------------------------#

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('cout_define');
cout_define('distant');

// TODO : revoir tout ca avec la syntaxe de <necessite>
function cs_version_erreur($outil) {
	return (isset($outil['version-min']) && version_compare($GLOBALS['spip_version_code'], $outil['version-min'], '<'))
		|| (isset($outil['version-max']) && version_compare($GLOBALS['spip_version_code'], $outil['version-max'], '>'));
}

// initialise un outil, ses variables, et en renvoie la description compilee
function cs_initialisation_d_un_outil($outil_, $description_outil, $modif) {
	global $outils, $metas_outils;
	$outil = &$outils[$outil_];
	if (!isset($outil['categorie'])) $outil['categorie'] = 'divers';
	if (!isset($outil['nom'])) $outil['nom'] = _T('couteauprive:'.$outil['id'].':nom');
	if (isset($outil['code:jq'])) $outil['jquery']='oui';
	$outil['actif'] = isset($metas_outils[$outil['id']])?$metas_outils[$outil['id']]['actif']:0;
	// Si Spip est trop ancien ou trop recent...
	if (cs_version_erreur($outil)) $outil['actif'] = 0;
	// au cas ou des variables sont presentes dans le code
	$outil['variables'] = array(); $outil['nb_variables'] = 0;
	// ces 2 lignes peuvent initialiser des variables dans $metas_vars ou $metas_vars_code
	if (isset($outil['code:spip_options'])) $outil['code:spip_options'] = cs_parse_code_php($outil['code:spip_options']);
	if (isset($outil['code:options'])) $outil['code:options'] = cs_parse_code_php($outil['code:options']);
	if (isset($outil['code:fonctions'])) $outil['code:fonctions'] = cs_parse_code_php($outil['code:fonctions']);
	// cette ligne peut utiliser des variables dans $metas_vars ou $metas_vars_code
	return $description_outil($outil_, 'admin_couteau_suisse', $modif);
}

// renvoie le configuration du pack actuel
function cs_description_pack() {
	if(!isset($GLOBALS['cs_pack_actuel'])) return '';
	return debut_cadre_relief('', true)
		. "<h3 class='titrem'><img src='"._DIR_IMG_PACK."puce-verte.gif' width='9' height='9' alt='-' />&nbsp;" . _T('couteauprive:pack_titre') . '</h3>'
		. propre(_T('couteauprive:pack_descrip') . "\n\n" . _T('couteauprive:contrib', array('url'=>'[->'._URL_CONTRIB.'2552]')))
		. '<br/><textarea rows=30 cols=200 style="width:520px; font-size:90%;">'.htmlentities($GLOBALS[cs_pack_actuel], ENT_QUOTES, $GLOBALS['meta']['charset']).'</textarea>'
		. fin_cadre_relief(true);
}

// renvoie (pour la nouvelle interface) la description d'un outil
function description_outil2($outil_id) {
	if(!strlen($outil_id)) return _T('couteauprive:outils_cliquez');
	global $outils, $metas_vars, $metas_outils;
	include_spip('cout_utils');
	// remplir $outils (et aussi $cs_variables qu'on n'utilise pas ici);
	include_spip('config_outils');
cs_log(" -- exec_charger_description_outil_dist() - Appel de config_outils.php : nb_outils = ".count($outils));

cs_log(" -- appel de charger_fonction('description_outil', 'inc') et de description_outil($outil_id) :");
	$description_outil = charger_fonction('description_outil', 'inc');
	$descrip = cs_initialisation_d_un_outil($outil_id, $description_outil, true);

	include_spip('inc/presentation');
	$s = '<div class="cs-cadre">';

	$outil = $outils[$outil_id]; unset($outils);
	$actif = $outil['actif'];
	$puce = $actif?'puce-verte.gif':'puce-rouge.gif';
	$titre_etat = _T('couteauprive:outil_'.($actif?'actif':'inactif'));
	$nb_var = intval($outil['nb_variables']);
	
	if(!strlen($outil['id']) || !cout_autoriser('outiller', $outil) || cs_version_erreur($outil))
		return $s . _T('info_acces_interdit') . '</div>';

	$s .= "<h3 class='titrem'><img src='"._DIR_IMG_PACK."$puce' name='puce_$id_input' width='9' height='9' alt=\"$titre_etat\" title=\"$titre_etat\" />&nbsp;" . $outil['nom'] . '</h3>';
	$s .= '<div class="cs_menu_outil">';
	if ($nb_var)
		$s .= '<a href="'.generer_url_ecrire(_request('source'),'cmd=reset&outil='.$outil_id).'" title="' . _T('couteauprive:par_defaut') . '">' . _T('couteauprive:par_defaut') . '</a>&nbsp;|&nbsp;';
	if (!$actif)
		$s .= '<a href="'.generer_url_ecrire(_request('source'),'cmd=hide&outil='.$outil_id).'" title="' . _T('couteauprive:outil_cacher') . '">' . _T('couteauprive:outil_cacher') . '</a>&nbsp;|&nbsp;';
	$act = $actif?'des':'';
	$s .= '<a href="'.generer_url_ecrire(_request('source'),'cmd=toggle&outil='.$outil_id).'" title="'._T("couteauprive:outil_{$act}activer_le").'">'._T("couteauprive:outil_{$act}activer")."</a></div>";
	if(strlen($temp = cs_action_rapide($outil_id))) $s .= "<div class='cs_action_rapide' id='cs_action_rapide'>$temp</div>";
	include_spip('inc/texte');
	$s .= propre($descrip);

	$serial = serialize(array_keys($outil));
	if (preg_match_all(',traitement:([A-Z_]+),', $serial, $regs, PREG_PATTERN_ORDER))
		$p .=  _T('couteauprive:detail_balise_etoilee', array('bal' => '#'.join('*, #', array_unique($regs[1])).'*'));	
	if (isset($outil['jquery']) && $outil['jquery']=='oui') $p .= '<p>' . _T(defined('_SPIP19100')?'couteauprive:detail_jquery1':'couteauprive:detail_jquery2') . '</p>';
	if (isset($outil['auteur']) && strlen($outil['auteur'])) $p .= '<p>' . _T('auteur') .' '. ($outil['auteur']) . '</p>';
	if (isset($outil['contrib']) && strlen($outil['contrib'])) $p .= '<p>' . _T('couteauprive:contrib', array('url'=>'[->'._URL_CONTRIB.$outil['contrib'].']')) . '</p>';
	$s .= propre($p);
	$s .= detail_outil($outil_id);

	return $s . '</div>';
}

// renvoie simplement deux liste des outils actifs/inactifs
function liste_outils() {
	global $outils;
	$id = 0;
	$metas_caches = isset($GLOBALS['meta']['tweaks_caches'])?unserialize($GLOBALS['meta']['tweaks_caches']):array();
	foreach($outils as $outil) $categ[_T('couteauprive:categ:'.$outil['categorie'])] = $outil['categorie']; ksort($categ);
	$result_actifs = $result_inactifs = '';
	foreach($categ as $c=>$i) {
		$s_actifs = $s_inactifs = array();
		foreach($outils as $outil) if ($outil['categorie']==$i) {
			$test = $outil['actif']?'s_actifs':'s_inactifs';
			$hide = cs_version_erreur($outil) || (!$outil['actif'] && isset($metas_caches[$outil['id']]['cache'])) || !cout_autoriser('outiller', $outil);
			if (!$hide)
				${$test}[] .= $outil['nom'] . '|' . $outil['index'] . '|' . $outil['id'];
		}
		foreach(array('s_actifs', 's_inactifs') as $temp) {
			sort(${$temp});
			$reset=_request('cmd')=='resetjs'?"\ncs_EffaceCookie('sous_liste_$id');":'';
			$titre = "<script type=\"text/javascript\"><!--$reset
document.write('<span class=\"light'+cs_Titre('sous_liste_$id')+'\">');
//--></script><noscript><span class='light cs_hidden'></noscript>" 
				." (".count(${$temp}).")</span>";
			preg_match(',([0-9]+)\.?\s*(.*),', _T('couteauprive:'.$c), $reg);
			$titre = "<div class='titrem categorie'>$reg[2]$titre</div>";
			$href = generer_url_ecrire(_request('exec'),"cmd=descrip&outil=");
			foreach(${$temp} as $j=>$v)
				${$temp}[$j] = preg_replace(',^(.*)\|(.*)\|(.*)$,', '<a class="cs_href" id="href_$3" name="$3" href="'.$href.'$3">$1</a>', $v);
			${$temp} = join("<br/>\n", ${$temp});
			if (strlen(${$temp})) ${'result'.$temp} .= $titre
				. "<script type=\"text/javascript\"><!--
document.write('<div id=\"sous_liste_$id\" class=\"'+cs_Categorie('sous_liste_$id')+'\">');
//--></script><noscript><div id=\"sous_liste_$id\"></noscript>" . ${$temp} . '</div>';
			$id++;
		}
	}

	$fieldset = '<fieldset style="width:92%; margin:0; padding:0.6em;" class="cadre-trait-couleur liste_outils"><legend style="font-weight:bold; color:';
	return '<div id="cs_outils" class="cs_outils">'
	. '<div class="cs_liste cs_inactifs">' . $fieldset . 'red;">' . _T('couteauprive:outils_inactifs') . '</legend>'
	. $results_inactifs . '</fieldset></div>'
	. '<form id="csform" name="csform" method="post" action="'.generer_url_ecrire(_request('exec'),"cmd=toggle").'">'
	. '<input type="hidden" value="test" name="cs_selection" id="cs_selection"/>'
	. '<div class="cs_toggle"><div style="display:none;">'
	. '<a id="cs_toggle_a" title="'._T('couteauprive:outils_permuter_gras1').'" href="'.generer_url_ecrire(_request('exec'),"cmd=toggle").'">'
	. '<img alt="<->" src="'.find_in_path('img/permute.gif').'"/></a>'
	. '<p id="cs_toggle_p">(0)</p>'
	. '<a id="cs_reset_a" title="'._T('couteauprive:outils_resetselection').'" href="#">'
	. '&nbsp;<img alt="X" class="class_png" src="'.find_in_path('img/nosel.gif').'"/>&nbsp;</a>'
	.	'</div></div></form>'
	. '<div class="cs_liste cs_actifs">' . $fieldset . '#22BB22;">' . _T('couteauprive:outils_actifs') . '</legend>'
	. $results_actifs . '</fieldset>'
	. '<div style="text-align: right;"><a id="cs_tous_a" title="'._T('couteauprive:outils_selectionactifs').'" href="#">'._T('couteauprive:outils_selectiontous').'</a></div>'
	. '</div></div>';
}

// renvoie les details techniques d'un outil
function detail_outil($outil_id) {
	global $outils;
	$outil = &$outils[$outil_id];
	$div = '<div class="cs_details_outil">';
	if (cs_version_erreur($outil)) return $div . _T('couteauprive:erreur:version') . '</div>';
	$details = array();
	if ($erreur_version) $details[] = _T('couteauprive:erreur:version');
	$a = array();
	foreach(array('spip_options', 'options', 'fonctions', 'js', 'jq', 'css') as $in)
		if(isset($outil['code:'.$in])) $a[] = _T('couteauprive:code_'.$in);
	if(count($a)) $details[] = _T('couteauprive:detail_inline') . ' ' . join(', ', $a);
	$a = array();
	foreach(array('.php', '_options.php', '_fonctions.php', '.js', '.js.html', '.css', '.css.html') as $ext)
		if (find_in_path('outils/'.($temp=$outil_id.$ext))) $a[] = $temp;
	if(count($a)) $details[] = _T('couteauprive:detail_fichiers') . ' ' . join(', ', $a);
	$serial = serialize(array_keys($outil));
	if (preg_match_all(',traitement:([A-Z_]+),', $serial, $regs, PREG_PATTERN_ORDER))
		$details[] =  _T('couteauprive:detail_traitements') . ' #' . join(', #', array_unique($regs[1]));	
	if (preg_match_all(',(pipeline|pipelinecode):([a-z_]+),', serialize(array_keys($outil)), $regs, PREG_PATTERN_ORDER))
		$details[] = _T('couteauprive:detail_pipelines') . ' ' . join(', ', array_unique($regs[2]));	
	if(count($details)) return $div . join('<br />', $details) . '</div>';
	return '';
}

// renvoie les boutons eventuels d'action rapide
function cs_action_rapide($outil_id) {
	include_spip('inc/texte');
	$f = "{$outil_id}_action_rapide";
	include_spip("outils/$f");
	if(!function_exists($f)) return '';
	$f = trim($f());
	if(strlen($f)) {
		$mem = $GLOBALS['toujours_paragrapher'];
		$GLOBALS['toujours_paragrapher'] = false;
		$info = '<strong>' . definir_puce() . '&nbsp;' . propre(_T('couteauprive:action_rapide')) . "</strong>";
		$GLOBALS['toujours_paragrapher'] = $mem;
		return "<div>$info</div><div>$f</div>";
	}
	return '';
}

?>