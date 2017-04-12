<?php
#---------------------------------------------------#
#  Plugin  : jQuery 1.2.6 pour SPIP 1.92x           #
#  Auteur  : Patrice Vanneufville, 2008             #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net #
#  Licence : GPL                                    #
#---------------------------------------------------#

$GLOBALS['spip_pipeline']['insert_head'] = str_replace('|f_jQuery', '|f_jQuery_192', $GLOBALS['spip_pipeline']['insert_head']);
$GLOBALS['spip_pipeline']['jquery_plugins'] = '';

// Inserer jQuery
// et au passage verifier qu'on ne doublonne pas #INSERT_HEAD
// http://code.spip.net/@f_jQuery
function f_jQuery_192($texte) {
	static $doublon=0;
	if ($doublon++) {
		include_spip('public/debug');
		$texte = affiche_erreurs_page(array(
			array("#INSERT_HEAD",_T('double_occurrence')))
		) . $texte;
	} else {
		$x = "\n";
		$choix_version = function_exists('lire_config')?lire_config('jquery_192/choix_version','_126'):'_126';
		foreach (pipeline('jquery_plugins',
		array(
			// clefs obligees sans quoi "in_array('data', array_keys($val))" retourne true !!
			'jquery_'=>"javascript/jquery$choix_version.js",
			'jquery.form_'=>"javascript/jquery.form$choix_version.js",
			'ajaxCallback_'=>"javascript/ajaxCallback$choix_version.js"
		)) as $script)
			if ($script = find_in_path($script))
				$x .= "<script src=\"$script\" type=\"text/javascript\"></script>\n";
		$texte = $x.$texte;
	}
	return $texte;
}

?>