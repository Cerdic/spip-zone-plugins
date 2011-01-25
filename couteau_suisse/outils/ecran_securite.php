<?php

function ecran_securite_pre_description_outil($flux) {
	if($flux['outil']!="ecran_securite") return $flux;
$f = _DIR_RACINE._NOM_PERMANENTS_INACCESSIBLES."ecran_securite.php";
echo "<br>0. ", _DIR_SITE,
	"<br>1. ", $f, file_exists($f)?" ok":" ko", 
	"<br>2. ", $f=realpath($f), file_exists($f)?" ok":" ko", 
	"<br>A. ", $f=realpath(dirname(cs_spip_file_options(1))), file_exists($f)?" ok":" ko", 
	"<br>B. ", $f=realpath(dirname(cs_spip_file_options(2))), file_exists($f)?" ok":" ko", 
	"<br>C. ", $f=realpath(dirname(cs_spip_file_options(3))), file_exists($f)?" ok":" ko", 
	"<br>D. ", $f=realpath(dirname(cs_spip_file_options(4))), file_exists($f)?" ok":" ko", 
	"<br>5. ", $f=__FILE__, file_exists($f)?" ok":" ko";
echo $flux['non']?" => non!":"=> oui!";

	// ecran de securite dans config/
	$f = dirname(cs_spip_file_options(3))."/ecran_securite.php";
	// conflit/doublon potentiel ?
	// $flux['non'] est vrai si le Couteau Suisse ne lance aucun fichier par lui-meme
	$conf = @file_exists($f) || (defined("_ECRAN_SECURITE") && $flux['non'])
		?"<hr/>\n@puce@ <span style=\"color: red;\">"._T("couteauprive:ecran_conflit".($flux['non']?"2":""), array("file"=>_NOM_PERMANENTS_INACCESSIBLES."ecran_securite.php"))."</span>"
		:"";
	if(defined('_ECRAN_SECURITE')) {
		$vers = _ECRAN_SECURITE;
		// recherche de la version du fichier distant
		include_spip("outils/maj_auto_action_rapide");
		$maj = maj_auto_rev_distante("http://zone.spip.org/trac/spip-zone/browser/_core_/securite/ecran_securite.php?format=txt",false,",(\d+\.\d+(\.\d+)?),",0,true);
		if($maj{0} != "-") 
			$tmp = "\n".(_ECRAN_SECURITE!=$maj?"- "._T("couteauprive:ecran_maj_ko", array("n"=>"<span style=\"color: red;\">$maj</span>")):_T("couteauprive:ecran_maj_ok"));
	} else $vers=_T("couteauprive:ecran_ko");
	// options SPIP en amont ? (mieux !)
	if(!defined("_CS_SPIP_OPTIONS_OK"))
		$tmp .= "\n- "._T("couteauprive:detail_spip_options2");
	$flux['texte'] = str_replace(array("@_ECRAN_SECURITE@","@_ECRAN_CONFLIT@","@_ECRAN_SUITE@"), array($vers,$conf,$tmp), $flux['texte']);
	return $flux;
}

# TODO : eviter l'insertion et recopier le fichier dans config/mes_options.php pour SPIP>=2.1
function ecran_securite_fichier_distant($flux) {
	// fichier global de config (y compris la mutu)
	$f = dirname(cs_spip_file_options(4));
	// fichier local de config
	$f = dirname(cs_spip_file_options(3));
	return $flux;
}

?>