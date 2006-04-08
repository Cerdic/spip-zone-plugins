<?php

//    Fichier créé pour SPIP avec un bout de code emprunté à celui ci.
//    Distribué sans garantie sous licence GPL./
//    Copyright (C) 2006  Pierre ANDREWS
//
//    This program is free software; you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation; either version 2 of the License, or any later version.
//
//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with this program; if not, write to the Free Software
//    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA


define('_DIR_PLUGIN_COLORATION_CODE',(_DIR_PLUGINS.end(explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__)))))));

function coloration_code_color($code, $language='php') {
  
  include_once(_DIR_PLUGIN_COLORATION_CODE.'/geshi/geshi.php');
  //
  // Create a GeSHi object
  //
  $geshi =& new GeSHi($code, $language);
  $geshi->set_header_type(GESHI_HEADER_DIV);
  $geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS);

  //
  // And echo the result!
  //
  return $geshi->parse_code();

}

function coloration_code_echappe($texte) {
  $rempl ='';

  if (preg_match_all(
		 ',<code[[:space:]]+class="(.*)"[[:space:]]*>(.*)</code>,Uims',
		 $texte, $matches, PREG_SET_ORDER))
	foreach ($matches as $regs) {
	  $code = echappe_retour($regs[2]);
	  $rempl = coloration_code_color($code,$regs[1]);
	  $texte = str_replace($regs[0],echappe_html("<html>$rempl</html>"),$texte);
	}
  return $texte;
}

?>
