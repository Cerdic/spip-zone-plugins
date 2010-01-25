<?php

// module inclu dans la description de l'outil en page de configuration

include_spip('inc/actions');

function spam_filtre_de_test($texte) {
	if (!strlen($texte)) return '';
	if (!isset($GLOBALS['meta']['cs_spam_mots'])) spam_installe();
	return preg_match($GLOBALS['meta']['cs_spam_mots'], $texte)?'ko':'ok';
}

function spam_filtre_de_test_ip($texte) {
	if (!strlen($texte)) return '';
	if (!isset($GLOBALS['meta']['cs_spam_ips'])) spam_installe();
	if(!preg_match_all(',\d+\.\d+\.\d+\.\d+,', $texte, $regs, PREG_PATTERN_ORDER)) return '';
	$res = array();
	foreach($regs[0] as $r)
		$res[] = _T('couteauprive:spam_ip', array('ip'=>$r)).' '._T(preg_match($GLOBALS['meta']['cs_spam_ips'], "$r")?'item_oui':'item_non');
	return join('<br />', $res);
}

function spam_action_rapide() {
	$msg = _request('ar_message');
	include_spip('public/assembler'); // pour recuperer_fond()
	$fd = recuperer_fond('fonds/test_spam', array(
		'ar_message' => $msg,
	));
	// syntaxe : ajax_action_auteur($action, $id, $script, $args='', $corps=false, $args_ajax='', $fct_ajax='')
	return ajax_action_auteur('action_rapide', 'test_spam', 'admin_couteau_suisse', "arg=spam|description_outil&modif=oui&cmd=descrip#cs_action_rapide", $fd)."\n";
}

// fonction {$outil}_{$arg}_action() appelee par action/action_rapide.php
function spam_test_spam_action() {
	// tester l'anti-spam
	// aucune action, le test est pris en charge par ?exec=action_rapide
	redirige_par_entete(parametre_url(urldecode(_request('redirect')), 'ar_message', _request('ar_message'), '&'));
}

?>