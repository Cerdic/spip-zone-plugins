<?php function bible_install($action){
	
	switch($action){
		case 'install':
			
			if (function_exists('ecrire_config')){
				
				ecrire_config('bible/numeros','oui');
				ecrire_config('bible/retour','oui');
				ecrire_config('bible/ref','oui');
				ecrire_config('bible/traduction_fr','jerusalem');
				ecrire_config('bible/traduction_en','kj');
				ecrire_config('bible/traduction_de','luther1545');
				ecrire_config('bible/traduction_es','dhh');
				ecrire_config('bible/traduction_it','cei');
				ecrire_config('bible/traduction_pl','bty');
				ecrire_config('bible/traduction_pt','ol');
				ecrire_config('bible/traduction_hu','hk');
				ecrire_config('bible/traduction_da','dbpd');
				ecrire_config('bible/traduction_nl','hb');
				ecrire_config('bible/traduction_no','dnb30');
				ecrire_config('bible/traduction_sv','lb_sv');
				ecrire_config('bible/traduction_fi','pr92');
				ecrire_config('bible/traduction_ru','вж');
				ecrire_config('bible/traduction_bg','bb');
            include_spip('inc/plugin');
            $liste=liste_plugin_actifs();
			if ($liste['SPIP_BONUX']){
			     bible_initialise_pp();
			}
				
				}
			break;
			
		case 'uninstall':
			
			if (function_exists('effacer_config')){
				effacer_config('bible/numeros');
				effacer_config('bible/retour');
				effacer_config('bible/ref');
				effacer_config('bible/traduction');
				effacer_config('bible/traduction_fr');
				effacer_config('bible/traduction_en');
				effacer_config('bible/traduction_de');
				effacer_config('bible/traduction_es');
				effacer_config('bible/traduction_it');
				effacer_config('bible/traduction_pl');
				effacer_config('bible/traduction_pt');
				effacer_config('bible/traduction_ht');
				effacer_config('bible/traduction_da');
				effacer_config('bible/traduction_nl');
				effacer_config('bible/traduction_no');
				effacer_config('bible/traduction_sv');
				effacer_config('bible/traduction_fi');
				effacer_config('bible/traduction_ru');
				effacer_config('bible/traduction_bg');
				effacer_config('bible_pp/trad_prop');
			}
			break;
			
	}
	return true;
}
function bible_initialise_pp(){
    $tableau = array_keys(bible_tableau('traduction'));
    ecrire_config('bible_pp/trad_prop',$tableau);
    ecrire_config('bible_pp/numeros','oui');
    ecrire_config('bible_pp/retour','oui');
    ecrire_config('bible_pp/ref','oui');
    ecrire_config('bible_pp/lang_pas_art','oui');
    ecrire_config('bible_pp/lang_morte','oui');
}
?>