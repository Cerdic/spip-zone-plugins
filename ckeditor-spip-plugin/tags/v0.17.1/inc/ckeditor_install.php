<?php

include_spip('inc/config') ;

function ckeditor_install($action,$prefix,$version_cible){
	switch($action) {
		case 'test':
			$ckeditor = lire_config('ckeditor',false) ;
			return is_array($ckeditor) ;
		case 'install':
			$formulaires = preg_files(_DIR_RACINE, "ckeditor-spip-plugin/formulaires/.*.php") ;
			foreach($formulaires as $formulaire) {
				spip_log("formulaire : $formulaire", "ckeditor");
				if (preg_match("~formulaires/(ck[a-z]).php$~",$formulaire, $m)) {
					include_spip("formulaires/".$m[1]);
					$fonction = "formulaires_".$m[1]."_charger_dist" ;
					$valeurs = $fonction() ;
					$vals=array();
					foreach($valeurs as $entree => $valeur) {
						if (! is_null($valeur) && ! ($valeur === '')) {
								ecrire_config("ckeditor/".$entree, $valeur) ;
								$vals[]="ckeditor/$entree";
						}
					}
				}
			}
			return true ;
		default:
			spip_log("installation[action:$action non prise en charge]", "ckeditor");
	}
}
?>
