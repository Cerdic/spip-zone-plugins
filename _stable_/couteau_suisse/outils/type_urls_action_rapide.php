<?

// module inclu dans la description de l'outil en page de configuration

include_spip('inc/actions');

function type_urls_action_rapide() {
//cs_log($_POST, '==== type_urls_action_rapide :'); cs_log($_GET);
	include_spip('public/assembler'); // pour recuperer_fond()
	$fd = recuperer_fond(defined('_SPIP19300')?'fonds/type_urls':'fonds/type_urls_191', array(
		'type_urls' => $GLOBALS['type_urls'],
		'ar_num_objet' => _request('ar_num_objet'),
		'ar_type_objet' => _request('ar_type_objet'),
	));
	// au cas ou il y aurait plusieurs actions, on fabrique plusieurs <form>
	$fd = explode('@@CS_FORM@@', $fd);
	$res = "";
	$arg = defined('_SPIP19300')?'edit_urls2_':'edit_urls_';
	foreach($fd as $i=>$f) {
		// syntaxe : ajax_action_auteur($action, $id, $script, $args='', $corps=false, $args_ajax='', $fct_ajax='')
		$res .= ajax_action_auteur('action_rapide', $arg.$i, 'admin_couteau_suisse', "arg={$arg}{$i}&modif=oui&cmd=descrip&outil=type_urls#cs_action_rapide", $f, '', 'function() { jQuery(\'#ar_chercher\', this).click();}')."\n";
	}
	return $res;
}

?>