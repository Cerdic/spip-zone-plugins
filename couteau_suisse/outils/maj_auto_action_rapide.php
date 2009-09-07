<?php

// module inclu dans la description de l'outil en page de configuration

if (!defined("_ECRIRE_INC_VERSION")) return; // securiser
include_spip('inc/actions');

define('_MAJ_SVN_DEBUT', 'svn://zone.spip.org/spip-zone/');
define('_MAJ_LOG_DEBUT', 'http://zone.spip.org/trac/spip-zone/log/');
define('_MAJ_LOG_FIN', '?format=changelog');
define('_MAJ_ZIP', 'http://files.spip.org/spip-zone/');

function maj_auto_action_rapide() {
	include_spip('inc/plugin');
	$plugins = liste_plugin_files();
	$plugins_actifs = liste_chemin_plugin_actifs();
	$html_actifs = $html_inactifs = array();
	$checked = 0;
	foreach ($plugins as $p) /*if(preg_match(',^auto/,', $p))*/ {
		$actif = array_search($p, $plugins_actifs, true);
		$auto = preg_match(',^auto/,', $p);
		list($zip, $rev_local, $rev_rss, $maj_dispo) = plugin_get_infos_maj($p);
		$maj_lib = '';
		if($maj_dispo) {
			$maj_lib = _T('La r&eacute;vision @revision@ est disponible.', array('revision' => $rev_rss));
			$checked++; }
		elseif($rev_rss>0 && $rev_local)
			$maj_lib = _T('Ce plugin semble &agrave; jour.');
		elseif($auto) {
			$maj_lib = _L("La r&eacute;vision distante n'a pas pu &ecirc;tre trouv&eacute;e.");
			$checked++; }
		elseif($rev_local && $rev_rss<=0)
			$maj_lib = _L("La r&eacute;vision distante n'a pas pu &ecirc;tre trouv&eacute;e.").' '._L("Veuillez proc&eacute;der manuellement &agrave; la mise &agrave; jour de ce plugin.");
		$infos = plugin_get_infos($p);
		$nom = trim($infos['nom']) . '.' . ($maj_lib?"\n_ {{".$maj_lib.'}}':'') . '|'
			. ($rev_local?_L("R&eacute;v.&nbsp;@revision@", array('revision' => $rev_local)):'&nbsp;');
		$bouton = $auto			
//			?"<input type='checkbox' value='$p'".($maj_dispo?" checked='checked'":"").($rev_rss<=0?" disabled='disabled'":"")." name='plugins[]'/>"
			?"<input type='radio' value='$zip'".($checked==1?" checked='checked'":'')./*($rev_rss<=0?" disabled='disabled'":"").*/" name='url_zip_plugin'/>"
			:'&nbsp;';
		${$actif?'html_actifs':'html_inactifs'}[] = "|$bouton|$nom|";
	}

	$html1 = "\n<div style='padding:0.4em;' id='maj_auto_div'><fieldset><legend style='padding:0.4em;'>"
		. _L('Liste des plugins d&eacute;tect&eacute;s :').'</legend>'
		. propre(
			(count($html_actifs)? "\n|{{" . _L('Plugins actifs') . "}}|<|<|\n" . join("\n",$html_actifs) . "\n" : '')
			. (count($html_inactifs)? "\n|{{" . _L('Plugins inactifs') . "}}|<|<|\n" . join("\n",$html_inactifs) . "\n" : '')
		  )
		. "<div style='text-align: right;'><input class='fondo' type='submit' value=\""
		. attribut_html(_L('Mettre &agrave; jour le plugin s&eacute;lectionn&eacute;'))
		. '" /></div></fieldset></div>'
		. http_script("
jQuery(document).ready(function() {
	if(!jQuery('#maj_auto_div :radio:checked').length)
		jQuery('#maj_auto_div :radio:first')[0].checked = true;
});");
	$html2 = "\n<div class='cs_sobre'><input class='cs_sobre' type='submit' value=\"["
		. attribut_html(_L('Acualiser cette liste'))
		. ']" /></div>';

// premier formulaire non ajax, lancant directement charger_plugin
	return redirige_action_post('charger_plugin', '', 'admin_couteau_suisse', "cmd=descrip&outil=maj_auto#cs_infos", $html1)
// second formulaire ajax : lien d'actualisation forcee
		. ajax_action_auteur('action_rapide', 'maj_auto_forcer', 'admin_couteau_suisse', "arg=retour_normal&cmd=descrip&outil=maj_auto#cs_action_rapide", $html2);
}


function maj_auto_rev_distante_rss($url, $lastmodified = 0, $force = false) {
	$force |= in_array(_request('var_mode'), array('calcul', 'recalcul'));

	// pour la version distante, on regarde toutes les 2h00 (meme en cas d'echec)
	$maj_ = isset($GLOBALS['meta']['tweaks_maj_auto'])?unserialize($GLOBALS['meta']['tweaks_maj_auto']):array();
	if(!isset($maj_[$url])) $maj_[$url] = array(0, false);
	$maj = &$maj_[$url];
	// prendre le cache si svn.revision n'est pas modifie recemment, si les 4h ne sont pas ecoulee, et si on ne force pas
	if (!$force && $maj[1]!==false && ($lastmodified<$maj[0]) && (time()-$maj[0] < 2*3600)) $distant = $maj[1];
	else {
		include_spip('inc/distant');
		$distant = recuperer_page($url);
		$distant = $maj[1] = $distant?(preg_match(', \[(\d+)\],', $distant, $regs)?$regs[1]:'-2'):'-1';
		$maj[0] = time();
		ecrire_meta('tweaks_maj_auto', serialize($maj_));
		ecrire_metas();
	}
	return intval($distant);
}

function plugin_get_infos_maj($p, $force = false) {
	$zip = preg_match(',^auto/(.*)$,', $p, $regs)?$regs[1]:'';
	if(strlen($zip)) $zip = _MAJ_ZIP . $zip. '.zip';
	lire_fichier($svn_rev = _DIR_PLUGINS.$p.'/svn.revision', $svn);
	$lastmodified = @file_exists($svn_rev)?@filemtime($svn_rev):0;
	$origine = (strlen($svn) && preg_match(',<origine>(.+)</origine>,', $svn, $regs))
		?str_replace(_MAJ_SVN_DEBUT, _MAJ_LOG_DEBUT, $regs[1]) . _MAJ_LOG_FIN
		:'';
	$rev_local = (strlen($svn) && preg_match(',<revision>(.+)</revision>,', $svn, $regs))?intval($regs[1]):0;
	$rev_rss = maj_auto_rev_distante_rss($origine, $lastmodified, $force);
	return array(
		$zip,
		$rev_local, $rev_rss,
		$rev_rss>0 && $rev_local && $rev_rss>$rev_local);
}

?>