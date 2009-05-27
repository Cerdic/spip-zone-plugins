<?php

// module inclu dans la description de la "Configuration Actuelle" en page de configuration
// ici, un bouton : "sauver la configuration actuelle"

include_spip('inc/actions');

// Compatibilite SPIP < 2.0
if(!defined('_SPIP19300')) {
	function redirige_action_post($action, $arg, $ret, $gra, $corps, $att='') {
		$r = _DIR_RESTREINT_ABS . generer_url_ecrire($ret, $gra, true, true);
		return generer_action_auteur($action, $arg, $r, $corps, $att . " method='post'");
	}
}

function pack_action_rapide() {
	include_spip('inc/texte'); // pour attribut_html()
	switch($n = count($GLOBALS['cs_installer'])) {
		case 0 : $info = _T('couteauprive:pack_nb_zero'); break;
		case 1 : $info = _T('couteauprive:pack_nb_un'); break;
		default : $info = _T('couteauprive:pack_nb_plrs', array('nb' => $n));
	}
	// pour la constante _CS_FILE_OPTIONS
	cout_define('cs_comportement');
	// appel direct, sans ajax, histoire de mettre a jour le menu :
	return redirige_action_post('action_rapide', 'sauve_pack', 'admin_couteau_suisse', "cmd=pack#cs_infos",
			"\n<div style='padding:0.4em;'><p>$info</p><p>"._T('couteauprive:pack_sauver_descrip', array('file' => _CS_FILE_OPTIONS))
			."</p><div style='text-align: center;'><input class='fondo' type='submit' value=\""
			.attribut_html(_T('couteauprive:pack_sauver')) . "\" /></div></div>"); 
}

// clic "Sauver la configuration actuelle"
function action_rapide_sauve_pack() {
	$titre0 = $titre = _T('couteauprive:pack_actuel', array('date'=>cs_date())); $n=0;
	if(isset($GLOBALS['cs_installer'][$titre]))
		while(isset($GLOBALS['cs_installer']["$titre (".++$n.')']));
	if($n) $titre = "$titre ($n)";
	include_spip(_DIR_CS_TMP.'config');
	$pack = "\n# Le Couteau Suisse : pack de configuration du ".date("d M Y, H:i:s")."\n\$GLOBALS['cs_installer']['$titre'] = " . var_export($GLOBALS['cs_installer'][$titre0], true) . ";\n";
	$fo = strlen(_FILE_OPTIONS)? _FILE_OPTIONS:false;
	$t='';
	if ($fo) {
		if (lire_fichier($fo, $t) && strlen($t)) {
			$t = preg_replace(',\?'.'>\s*$,m', $pack.'?'.'>', $t, 1);
			if(ecrire_fichier($fo, $t)) return;
			else cs_log("ERREUR : l'ecriture du fichier $fo a echoue !");
		} else cs_log(" -- fichier $fo illisible. Inclusion non permise");
		if(strlen($t)) return;
	}
	// creation
	$fo = _DIR_RACINE._NOM_PERMANENTS_INACCESSIBLES._NOM_CONFIG.'.php';
	$ok = ecrire_fichier($fo, '<?'."php\n".$pack."\n?".'>');
cs_log(" -- fichier $fo absent ".($ok?'mais cree avec l\'inclusion':' et impossible a creer'));
}

?>