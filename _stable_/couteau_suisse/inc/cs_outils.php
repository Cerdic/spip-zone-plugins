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

@define('_DIV_CS_INFOS', '<div id="cs_infos" class="cs_infos">');

// TODO : revoir tout ca avec compare_version() et la syntaxe de <necessite>
function cs_version_erreur($outil) {
	return (isset($outil['version-min']) && $GLOBALS['spip_version']<$outil['version-min'])
		|| (isset($outil['version-max']) && $GLOBALS['spip_version']>$outil['version-max']);
}

// initialise un outil, ses variables, et en renvoie la description compilee
function cs_initialisation_d_un_outil($outil_, $description_outil, $modif) {
	global $outils, $metas_outils;
	$outil = &$outils[$outil_];
	if (!isset($outil['categorie'])) $outil['categorie'] = 'divers';
	if (!isset($outil['nom'])) $outil['nom'] = _T('cout:'.$outil['id'].':nom');
	if (isset($outil['code:jq'])) $outil['jquery']='oui';
	$outil['actif'] = isset($metas_outils[$outil['id']])?$metas_outils[$outil['id']]['actif']:0;
	// Si Spip est trop ancien ou trop recent...
	if (cs_version_erreur($outil)) $outil['actif'] = 0;
	// au cas ou des variables sont presentes dans le code
	$outil['variables'] = array(); $outil['nb_variables'] = 0;
	// ces 2 lignes peuvent initialiser des variables dans $metas_vars ou $metas_vars_code
	if (isset($outil['code:options'])) $outil['code:options'] = cs_parse_code_php($outil['code:options']);
	if (isset($outil['code:fonctions'])) $outil['code:fonctions'] = cs_parse_code_php($outil['code:fonctions']);
	// cette ligne peut utiliser des variables dans $metas_vars ou $metas_vars_code
	return $description_outil($outil_, 'admin_couteau_suisse', $modif);
}

// renvoie (pour la nouvelle interface) la description d'un outil
function description_outil2($outil_id) {
	if(!strlen($outil_id)) return (_DIV_CS_INFOS . _T('cout:cliquezlesoutils') . '</div>');
	global $outils, $metas_vars, $metas_outils;
	include_spip('cout_utils');
	// remplir $outils (et aussi $cs_variables qu'on n'utilise pas ici);
	include_spip('config_outils');
cs_log(" -- exec_charger_description_outil_dist() - Appel de config_outils.php : nb_outils = ".count($outils));

	// charger les metas
	$metas_outils = isset($GLOBALS['meta']['tweaks_actifs'])?unserialize($GLOBALS['meta']['tweaks_actifs']):array();
	$metas_vars = isset($GLOBALS['meta']['tweaks_variables'])?unserialize($GLOBALS['meta']['tweaks_variables']):array();

cs_log(" -- appel de charger_fonction('description_outil', 'inc') et de description_outil($outil_id, $script) :");
	$description_outil = charger_fonction('description_outil', 'inc');
	$descrip = cs_initialisation_d_un_outil($outil_id, $description_outil, true);

	include_spip('inc/presentation');
	$s = debut_cadre_relief('', true);
	$outil = $outils[$outil_id]; unset($outils);
	$actif = $outil['actif'];
	$puce = $actif?'puce-verte.gif':'puce-rouge.gif';
	$titre_etat = _T('cout:'.($actif?'actif':'inactif'));
	$nb_var = intval($outil['nb_variables']);
	
	$s .= "<h3 class='titrem'><img src='"._DIR_IMG_PACK."$puce' name='puce_$id_input' width='9' height='9' style='border:0;' alt=\"$titre_etat\" title=\"$titre_etat\" />&nbsp;" . $outil['nom'] . '</h3>';
	$s .= '<div style="text-align:right; font-size:85%;">';
	if ($nb_var)
		$s .= '<a href="'.generer_url_ecrire(_request('source'),'cmd=reset&outil='.$outil_id).'" title="' . _T('cout:par_defaut') . '">' . _T('cout:par_defaut') . '</a>&nbsp;|&nbsp;';
	if (!$actif)
		$s .= '<a href="'.generer_url_ecrire(_request('source'),'cmd=hide&outil='.$outil_id).'" title="' . _T('cout:neplusafficher') . '">' . _T('cout:neplusafficher') . '</a>&nbsp;|&nbsp;';
	$act = $actif?'des':'';
	$s .= '<a href="'.generer_url_ecrire(_request('source'),'cmd=toggle&outil='.$outil_id).'" title="'._T("cout:{$act}activer_outil").'">'._T("cout:{$act}activer")."</a></div>";
	include_spip('inc/texte');
	$s .= propre($descrip);

	if (isset($outil['jquery']) && $outil['jquery']=='oui') $p .= '<p>' . _T(defined('_SPIP19100')?'cout:jquery1':'cout:jquery2') . '</p>';
	if (isset($outil['auteur']) && strlen($outil['auteur'])) $p .= '<p>' . _T('auteur') .' '. ($outil['auteur']) . '</p>';
	if (isset($outil['contrib']) && strlen($outil['contrib'])) $p .= '<p>' . _T('cout:contrib', array('id'=>$outil['contrib'])) . '</p>';
	$s .= propre($p);
	$s .= detail_outil($outil_id);

	return _DIV_CS_INFOS . $s . fin_cadre_relief(true) . '</div>';
}

// renvoie simplement deux liste des outils actifs/inactifs
function liste_outils() {
	global $outils;
	$id = 0;
	$metas_caches = isset($GLOBALS['meta']['tweaks_caches'])?unserialize($GLOBALS['meta']['tweaks_caches']):array();
	foreach($outils as $outil) $categ[_T('cout:'.$outil['categorie'])] = $outil['categorie']; ksort($categ);
	$result_actifs = $result_inactifs = '';
	foreach($categ as $c=>$i) {
		$s_actifs = $s_inactifs = array();
		foreach($outils as $outil) if ($outil['categorie']==$i) {
			$test = $outil['actif']?'s_actifs':'s_inactifs';
			$hide = cs_version_erreur($outil) || (!$outil['actif'] && isset($metas_caches[$outil['id']]['cache']));
			if (!$hide)
				${$test}[] .= _T('cout:'.$outil['id'].':nom') . '|' . $outil['index'] . '|' . $outil['id'];
		}
		foreach(array('s_actifs', 's_inactifs') as $temp) {
			sort(${$temp});
			$titre = " <span class='light cs_hidden'>(".count(${$temp}).")</span>";
			preg_match(',([0-9]+)\.?\s*(.*),', _T('cout:'.$c), $reg);
			$titre = "<div class='titrem categorie'>$reg[2]$titre</div>";
			$href = generer_url_ecrire(_request('exec'),"cmd=descrip&outil=");
			foreach(${$temp} as $j=>$v) {
				${$temp}[$j] = preg_replace(',^(.*)\|(.*)\|(.*)$,', '<a class="cs_href" id="href_$3" name="$3" href="'.$href.'$3">$1</a>', $v);
			}
			${$temp} = join("<br/>\n", ${$temp});
			if (strlen(${$temp})) ${'result'.$temp} .= $titre . "<div id='sous_liste_$id'>" . ${$temp} . '</div>';
			$id++;
		}
	}

	$fieldset = '<fieldset style="width:92%; margin:0; padding:0.6em;" class="cadre-trait-couleur liste_outils"><legend style="font-weight:bold; color:';
	return '<div id="cs_outils" class="cs_outils">'
	. '<div class="cs_liste cs_inactifs">' . $fieldset . 'red;">' . _T('cout:inactifs') . '</legend>'
	. $results_inactifs . '</fieldset></div>'
	. '<form id="csform" name="csform" method="post" action="'.generer_url_ecrire(_request('exec'),"cmd=toggle").'">'
	. '<input type="hidden" value="test" name="cs_selection" id="cs_selection"/>'
	. '<div class="cs_toggle"><div style="display:none;">'
	. '<a id="cs_toggle_a" title="'._T('cout:permuter').'" href="'.generer_url_ecrire(_request('exec'),"cmd=toggle").'">'
	. '<img alt="<->" src="'.find_in_path('img/permute.gif').'"/></a>'
	. '<p id="cs_toggle_p">(0)</p>'
	. '<a id="cs_reset_a" title="'._T('cout:resetselection').'" href="#">'
	. '<img alt="X" class="class_png" src="'.find_in_path('img/nosel.gif').'"/></a>'
	.	'</div></div></form>'
	. '<div class="cs_liste cs_actifs">' . $fieldset . '#22BB22;">' . _T('cout:actifs') . '</legend>'
	. $results_actifs . '</fieldset>'
	. '<div style="text-align: right;"><a id="cs_tous_a" title="'._T('cout:selectiontous').'" href="#">'._T('cout:tous').'</a></div>'
	. '</div></div>';
}

// renvoie les details techniques d'un outil
function detail_outil($outil_id) {
	global $outils;
	$outil = &$outils[$outil_id];
	$hr = '<hr style="margin:6pt 0 0 0;"/><div style="font-size:85%;">';
	if (cs_version_erreur($outil)) return $hr . _T('cout:erreur:version') . '</div>';
	$details = array();
	if ($erreur_version) $details[] = _T('cout:erreur:version');
	$a = array();
	foreach(array('options', 'fonctions', 'js', 'jq', 'css') as $in)
		if(isset($outil['code:'.$in])) $a[] = _T('cout:code_'.$in);
	if(count($a)) $details[] = _T('cout:detail_inline') . ' ' . join(', ', $a);
	$a = array();
	foreach(array('.php', '_options.php', '_fonctions.php', '.js', '.js.html', '.css', '.css.html') as $ext)
		if (find_in_path('outils/'.($temp=$outil_id.$ext))) $a[] = $temp;
	if(count($a)) $details[] = _T('cout:detail_fichiers') . ' ' . join(', ', $a);
	$serial = serialize(array_keys($outil));
	if (preg_match_all(',traitement:([A-Z_]+),', $serial, $regs, PREG_PATTERN_ORDER))
		$details[] =  _T('cout:detail_traitements') . ' #' . join(', #', array_unique($regs[1]));	
	if (preg_match_all(',pipeline:([a-z_]+),', serialize(array_keys($outil)), $regs, PREG_PATTERN_ORDER))
		$details[] = _T('cout:detail_pipelines') . ' ' . join(', ', array_unique($regs[1]));	
	if(count($details)) return $hr . join('<br />', $details) . '</div>';
	return '';
}
?>