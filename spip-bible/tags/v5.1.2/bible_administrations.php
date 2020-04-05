<?php 

if (!defined("_ECRIRE_INC_VERSION")) return;

function bible_upgrade($nom_meta_base_version,$version_cible) {
  $maj = array();
  $maj['create'] = array(array('bible_conf'));	
  $maj["0.1.1"]  = array(array("bible_maj_0_1_1"));
  $maj["0.2.0"]  = array(array("bible_maj_0_2_0"));
  $maj["0.2.1"]  = array(array("bible_maj_0_2_1"));
  $maj["0.2.2"]  = array(array("bible_maj_0_2_2"));
  $maj["0.3.0"]  = array(array("bible_maj_0_3_0"));
  include_spip('base/upgrade');
  maj_plugin($nom_meta_base_version, $version_cible, $maj);
}
function bible_vider_tables($nom_meta_base_version) {
	effacer_config('bible');
	effacer_config('bible_pp');
	effacer_meta($nom_meta_base_version);
}
function bible_maj_0_1_1(){
	ecrire_config('bible/alias_na',"na28");
	$trad_prop = array_flip(lire_config("bible_pp/trad_prop"));
	unset($trad_prop["na27"]);
	ecrire_config("bible_pp/trad_prop",array_flip($trad_prop));
	}
function bible_maj_0_2_0(){
	ecrire_config("bible_pp/forme_livre","oui");
	}
function bible_maj_0_2_1(){
	ecrire_config('bible/forme_livre','abbr');
	}
function bible_maj_0_2_2(){
	ecrire_config('bible/url','oui');
	ecrire_config('bible_pp/url','oui');
	}
function bible_maj_0_3_0(){
	ecrire_config('bible/nommer_trad','oui');
	ecrire_config('bible_pp/nommer_trad','oui');
	}
function bible_conf(){
	include_spip('inc/config');
				ecrire_config('bible/numeros','oui');
				ecrire_config('bible/retour','oui');
				ecrire_config('bible/ref','oui');
				ecrire_config('bible/nommer_trad','oui');
				ecrire_config('bible/url','oui');
				ecrire_config('bible/forme_livre','abbr');
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
				ecrire_config('bible/alias_na','na27');
				bible_initialise_pp();		
}
function bible_initialise_pp(){
    $tableau = array_keys(bible_tableau('traduction'));
    ecrire_config('bible_pp/trad_prop',$tableau);
    ecrire_config('bible_pp/numeros','oui');
    ecrire_config('bible_pp/retour','oui');
    ecrire_config('bible_pp/ref','oui');
    ecrire_config('bible_pp/nommer_trad','oui');
    ecrire_config('bible_pp/url','oui');
    ecrire_config('bible_pp/lang_pas_art','oui');
    ecrire_config('bible_pp/lang_morte','oui');
    ecrire_config("bible_pp/forme_livre","oui");
}
?>
