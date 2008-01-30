<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// http://doc.spip.org/@action_charger_plugin_dist
function action_jqueryp_charger_lib_dist() {
	global $spip_lang_left;

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	include_spip('inc/minipres');

	// droits : il faut avoir le droit de choisir les plugins,
	// mais aussi d'en ajouter -- a voir
	include_spip('inc/autoriser');
	if (!autoriser('configurer', 'plugins')) {
		echo minipres();
		exit;
	}
	
	// on a tous les droits, on realise les operations demandees
	global $jquery_plugins;
	$j = $jquery_plugins[$nom = _request("id_jquery_plugin")];
	
	// chemin du retour :
	$retour = str_replace('exec=','',_request('retour'));
	
	if (empty($j)){
		echo minipres(
			_T("jqueryp:donnees_plugin_introuvables"),
			generer_form_ecrire($retour, bouton_suivant())
		);
		exit;
	} 
	
	// nous avons une liste de fichiers a recuperer
	$ret = array(); $noerr = true;
	foreach ($j['install'] as $nom=>$adresse){
		$noerr &= $ok = jqueryp_telecharge_librairie($adresse, $nom, $j['dir']);
		$ret[$j['dir'] . '/' . $nom] = $ok;
	}
	
	// affichage du resultat et bouton de retour
	include_spip('exec/install'); // pour bouton_suivant()

	$texte  = "<h3>" . _T("jqueryp:telechargement_librairie", array('nom'=>_T("jqueryp:$nom"))) . "</h3>\n";
	$texte .= "<p>" . _T("jqueryp:fichiers_installes") . "</p>\n";
	$texte .= "<ul>\n";
	foreach ($ret as $dest=>$ok){
		$texte .= "<li>$dest : <b>". ($ok?_T('jqueryp:ok'):_T('jqueryp:erreur')) . "</b></li>\n";
	}
	$texte .= "</ul>\n";
	
	
	$texte = "<div style='text-align:$spip_lang_left;'>$texte</div>\n";

	echo minipres(_T('jqueryp:installation_librairies'),
			 generer_form_ecrire(str_replace('exec=','',_request('retour')),
				$texte . bouton_suivant())
	);
	exit;
}


// telecharge une librairie :
// source : adresse du fichier a recuperer (format texte)
// dest : nom de la copie locae du fichier 
// dir : repertoire dans lequel on stocke ce fichier (il sera cree dans _DIR_LIB)
function jqueryp_telecharge_librairie($source, $dest, $dir){
	include_spip('inc/distant');
	include_spip('inc/flock');
	
	// dir racine car dans prive !!!
	$_dir = _DIR_RACINE . _DIR_LIB . $dir;
	
	// creer le repertoire si absent
	if (!is_dir($_dir)
		AND !sous_repertoire($_dir)){
		spip_log("Echec installation : Impossible de creer le repertoire $dir dans " . _DIR_LIB, 'jquery_plugins');
		return false;
	// recuperer le fichier
	} else {
		if (!($c = recuperer_page($source))){
			spip_log("- Echec installation : Impossible de rapatrier $source dans $_dir$dest", 'jquery_plugins');
			return false;
		} else {
			if (!ecrire_fichier($_dir . '/' . $dest, $c)) {
				spip_log("Echec installation : Impossible d'ecrire le fichier $dest dans $dir", 'jquery_plugins');
				return false;
			} else {
				spip_log("+ Installation : Copie de $adresse dans $dir/$dest", 'jquery_plugins');
			}
		} 
	}
	
	return true;
}		

