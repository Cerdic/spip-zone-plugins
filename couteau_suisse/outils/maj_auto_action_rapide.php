<?php

// module inclu dans la description de l'outil en page de configuration

if (!defined("_ECRIRE_INC_VERSION")) return; // securiser
include_spip('inc/actions');
include_spip('inc/distant');

define('_MAJ_SVN_DEBUT', 'svn://zone.spip.org/spip-zone/');
define('_MAJ_SVN_TRAC', 'svn://trac.rezo.net/spip-zone/'); // ancienne URL
define('_MAJ_LOG_DEBUT', 'http://zone.spip.org/trac/spip-zone/log/');
define('_MAJ_LOG_FIN', '?format=changelog');
define('_MAJ_ZIP', 'http://files.spip.org/spip-zone/');

function maj_auto_action_rapide() {
	include_spip('inc/plugin');
	$plugins = liste_plugin_files();
	$plugins_actifs = liste_chemin_plugin_actifs();
	$html_actifs = $html_inactifs = array();
	foreach ($plugins as $p) /*if(preg_match(',^auto/,', $p))*/ {
		$actif = array_search($p, $plugins_actifs, true);
		$auto = preg_match(',^auto/,', $p);
		$infos = plugin_get_infos_maj($p);
		$maj_lib = $checked = '';
		if($infos['maj_dispo']) { 
			$maj_lib = _T('couteau:maj_rev_ok', 
				array('revision' => $infos['rev_rss'], 'url'=>$infos['url_origine'], 'zip'=>$infos['zip_trac']));
			$checked = " class='maj_checked'"; }
		elseif($infos['rev_rss']>0 && $infos['rev_local'])
			$maj_lib = _T('couteau:maj'.($infos['svn']?'_svn':'_ok'),
				array('zip'=>$infos['zip_trac'], 'url'=>$infos['url_origine']));
		elseif($auto) {
			$maj_lib = _T('couteau:maj_rev_ko', array('url'=>$infos['url_origine']));
			$checked = " class='maj_checked'"; }
		elseif($infos['rev_local'] && $infos['rev_rss']<=0)
			$maj_lib = _T('couteau:maj_rev_ko', array('url'=>$infos['url_origine']));
		// eventuels liens morts
		$maj_lib = preg_replace(',\[([^[]+)->\],', '$1', $maj_lib);
		$nom = preg_replace(",[\n\r]+,",' ',$infos['nom']). '&nbsp;(v' .$infos['version'] . ')' . ($maj_lib?"\n_ {{".$maj_lib.'}}':'');
		$rev = $infos['rev_local']?_T('couteau:maj_rev', array('revision' => $infos['rev_local'])):'';
		if(strlen($infos['commit'])) $rev .= (strlen($rev)?'<br/>':'') . cs_date_court($infos['commit']);
		if($infos['svn']) $rev .= '<br/>SVN';		
		if(!strlen($rev)) $rev = '&nbsp;';
		$zip_log = (strlen($infos['zip_log']) && $infos['zip_log']!=$infos['zip_trac'])
			?"<label><input type='radio' value='$infos[zip_log]'$checked name='url_zip_plugin'/>[->$infos[zip_log]]</label>":'';
		$bouton = $auto			
			?"<input type='radio' value='$infos[zip_trac]'$checked name='url_zip_plugin'/>"
			:'&nbsp;';
		if(strlen($zip_log)) {
			$nom .= "\n_ "._T('couteau:maj_verif') . "\n_ $zip_log\n_ {$bouton}[->$infos[zip_trac]]<label>";
			$bouton = '&nbsp;';
		}
		${$actif?'html_actifs':'html_inactifs'}[] = "|$bouton|$nom|$rev|";
	}

	$html1 = "\n<div style='padding:0.4em;' id='maj_auto_div'><fieldset><legend style='padding:0.4em;'>"
		. _T('couteau:maj_liste').'</legend>'
		. propre(
			(count($html_actifs)? "\n|{{" . _T('couteau:plug_actifs') . "}}|<|<|\n" . join("\n",$html_actifs) . "\n" : '')
			. (count($html_inactifs)? "\n|{{" . _T('couteau:plug_inactifs') . "}}|<|<|\n" . join("\n",$html_inactifs) . "\n" : '')
		  )
		. "<div style='text-align: right;'><input class='fondo' type='submit' value=\""
		. attribut_html(_T('couteau:maj_maj'))
		. '" /><p><i>'._T('couteau:maj_verif2').'</i></p></div></fieldset></div>'
		. http_script("
jQuery(document).ready(function() {
	var ch = jQuery('#maj_auto_div .maj_checked');
	if(ch.length) ch[0].checked = true;
	if(!jQuery('#maj_auto_div :radio:checked').length)
		jQuery('#maj_auto_div :radio:first')[0].checked = true;
});");
	$html2 = "\n<div class='cs_sobre'><input class='cs_sobre' type='submit' value=\"["
		. attribut_html(_T('couteau:maj_actu'))	. ']" /></div>';

// premier formulaire non ajax, lancant directement charger_plugin
	return redirige_action_post('charger_plugin', '', 'admin_couteau_suisse', "cmd=descrip&outil=maj_auto#cs_infos", $html1)
// second formulaire ajax : lien d'actualisation forcee
		. ajax_action_auteur('action_rapide', 'maj_auto_forcer', 'admin_couteau_suisse', "arg=maj_auto|description_outil&cmd=descrip#cs_action_rapide", $html2);
}

// renvoie le pattern present dans la page distante
// si le pattern est NULL, renvoie un simple 'is_file_exists'
function maj_auto_rev_distante($url, $pattern=NULL, $lastmodified = 0, $force = false) {
	$force |= in_array(_request('var_mode'), array('calcul', 'recalcul'));

	// pour la version distante, on regarde toutes les 24h00 (meme en cas d'echec)
	$maj_ = isset($GLOBALS['meta']['tweaks_maj_auto'])?unserialize($GLOBALS['meta']['tweaks_maj_auto']):array();
	if(!isset($maj_[$url_=md5($url)])) $maj_[$url_] = array(0, false);
	$maj = &$maj_[$url_];
	// prendre le cache si svn.revision n'est pas modifie recemment, si les 24h ne sont pas ecoulee, et si on ne force pas
	if (!$force && $maj[1]!==false && ($lastmodified<$maj[0]) && (time()-$maj[0] < 24*3600)) $distant = $maj[1];
	else {
		$distant = $maj[1] = ($pattern!==NULL)
			?(($distant = recuperer_page($url))
				?(preg_match($pattern, $distant, $regs)?$regs[1]:'-2')
				:'-1')
			:strlen(recuperer_page($url, false, true, 0));
		$maj[0] = time();
		ecrire_meta('tweaks_maj_auto', serialize($maj_));
		ecrire_metas();
	}
	return intval($distant);
}

function plugin_get_infos_maj($p, $force = false) {
	$get_infos = defined('_SPIP20100')?charger_fonction('get_infos','plugins'):'plugin_get_infos';
	$infos = $get_infos($p);
	// fichier svn.revision
	$ok = lire_fichier($svn_rev = _DIR_PLUGINS.$p.'/svn.revision', $svn);
	$lastmodified = @file_exists($svn_rev)?@filemtime($svn_rev):0;
	if($ok && preg_match(',<origine>(.+)</origine>,', $svn, $regs)) {
		$url_origine = str_replace(_MAJ_SVN_DEBUT, _MAJ_LOG_DEBUT, $regs[1]);
		// prise en compte du recent demenagement de la Zone...
		$url_origine = preg_replace(',/_plugins_/_(?:stable|dev|test)_/,','/_plugins_/', $url_origine);
	} else $url_origine = '';
	$infos['commit'] = ($ok && preg_match(',<commit>(.+)</commit>,', $svn, $regs))?$regs[1]:'';
	$rev_local = (strlen($svn) && preg_match(',<revision>(.+)</revision>,', $svn, $regs))
		?intval($regs[1]):version_svn_courante(_DIR_PLUGINS.$p);
	if($infos['svn'] = $rev_local<0) { 
		// fichier SVN
		if (lire_fichier(_DIR_PLUGINS.$p.'/.svn/entries', $svn) 
				&& preg_match(',(?:'.preg_quote(_MAJ_SVN_TRAC).'|'.preg_quote(_MAJ_SVN_DEBUT).')[^\n\r]+,ms', $svn, $regs)) {
			$url_origine = str_replace(array(_MAJ_SVN_TRAC,_MAJ_SVN_DEBUT), _MAJ_LOG_DEBUT, $regs[0]);
			// prise en compte du recent demenagement de la Zone...
			$url_origine = preg_replace(',/_plugins_/_(?:stable|dev|test)_/,','/_plugins_/', $url_origine);
		}
		//$infos['zip_trac'] = 'SVN';
	}
	$infos['url_origine'] = strlen($url_origine)?$url_origine._MAJ_LOG_FIN:'';
	$infos['rev_local'] = abs($rev_local);
	$infos['rev_rss'] = maj_auto_rev_distante($infos['url_origine'], ', \[(\d+)\],', $lastmodified, $force);
	$infos['maj_dispo'] = $infos['rev_rss']>0 && $infos['rev_local']>0 && $infos['rev_rss']>$infos['rev_local'];
	// fichiers zip
	$infos['zip_log'] = $infos['zip_trac'] = '';
	$p2 = preg_match(',^auto/(.*)$,', $p, $regs)?$regs[1]:'';
	if(strlen($p2)) {
		// supposition du nom d'archive sur files.spip.org
		if(maj_auto_rev_distante($f = _MAJ_ZIP.$p2.'.zip')) $infos['zip_trac'] = $f;
		// nom de l'archive recemment installee par chargeur
		if(lire_fichier(sous_repertoire(_DIR_CACHE, 'chargeur').$p2.'/install.log', $log)
				&& preg_match(',[\n\r]source: *([^\n\r]+),msi', $log, $regs)
				&& maj_auto_rev_distante($regs[1]))
			$infos['zip_log'] = $regs[1];
		// au final on prend le bon
		if(!$infos['zip_trac']) $infos['zip_trac'] = $infos['zip_log'];
	}
	return $infos;
}

// fonction {$outil}_{$arg}_action() appelee par action/action_rapide.php
function maj_auto_maj_auto_forcer_action() {
	// forcer la lecture des revisions distantes de plugins
	ecrire_meta('tweaks_maj_auto', serialize(array()));
	ecrire_metas();
}

?>