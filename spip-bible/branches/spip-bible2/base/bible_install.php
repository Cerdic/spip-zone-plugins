<?php 
function bible_install($action){

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
				bible_initialise_pp();
			
				
				}
			return;
			
		case 'uninstall':
			
			if (function_exists('effacer_config')){
				effacer_config('bible');
				effacer_config('bible_pp');
			}

			break;
		case 'test':
			//	spip_log('on passe icicfg','bible');
			if (function_exists('lire_config')){
				
				if (lire_config('bible')&lire_config('bible_pp')){
					
					return true;
				}
				else {
					return false;
				}
			}
			return true;
	}
		
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