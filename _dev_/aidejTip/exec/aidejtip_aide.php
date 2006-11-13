<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_aidejtip_aide(){
	include_spip('inc/texte');
	$champ = _request('champ');
	switch ($champ) {
      case ("surtitre"): echo propre(_T('aidejtip:aide_surtitre')); break;
      case ("titre"): echo propre(_T('aidejtip:aide_titre'));   break;
      case ("soustitre"): echo propre(_T('aidejtip:aide_soustitre')); break;  
      case ("descriptif"): echo propre(_T('aidejtip:aide_descriptif')); break;
      case ("chapo"): echo propre(_T('aidejtip:aide_chapo')); break;
      case ("texte"): echo propre(_T('aidejtip:aide_texte')); break;
      default:  break;
   }
}

?>