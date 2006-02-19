<?php

define('_DIR_PLUGIN_COLORATION_CODE',(_DIR_PLUGINS . basename(dirname(__FILE__))));

function coloration_code_color($code, $language='php') {
  
  include_once(_DIR_PLUGINS_COLORATION_CODE.'/geshi/geshi.php');
  //
  // Create a GeSHi object
  //
  $geshi =& new GeSHi($source, $language);
  
  //
  // And echo the result!
  //
  return $geshi->parse_code();

}

function coloration_code_echappe($texte) {

  $rempl ='';
  if (preg_match_all(
					 ',<(ccode)\\(\(.*)\)>(.*)</\1>,Uims',
					 $letexte, $matches, PREG_SET_ORDER))
	foreach ($matches as $regs) {
	  $rempl = coloration_code_color($reg[3],$reg[2]);
	  $texte = str_replace($regs[0],code_echappement($rempl,"COLCODE"),$texte);
	  return $texte;

	}
}

function coloration_code_echappe_retour($texte) {
  return echappe_retour($texte,"COLCODE");
}


?>
