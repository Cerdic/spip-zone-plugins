<?php

// module inclu dans la description de l'outil en page de configuration

include_spip('inc/actions');

function spam_filtre_de_test($texte) {
	if (!strlen($texte)) return '';
	if (!isset($GLOBALS['meta']['cs_spam_mots'])) spam_installe();
	return preg_match($GLOBALS['meta']['cs_spam_mots'], $texte)?'ko':'ok';
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

?>