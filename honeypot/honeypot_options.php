<?php
/*
*   Plugin HoneyPot
*   Copyright (C) 2007 Pierre Andrews
*
*   This program is free software: you can redistribute it and/or modify
*   it under the terms of the GNU General Public License as published by
*   the Free Software Foundation, either version 3 of the License, or
*   (at your option) any later version.
*
*   This program is distributed in the hope that it will be useful,
*   but WITHOUT ANY WARRANTY; without even the implied warranty of
*   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*   GNU General Public License for more details.
*
*   You should have received a copy of the GNU General Public License
*   along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

/*Cette balise genere une phrase avec des mots aleatoires*/
function balise_HONEYPOT_RANDOM($p) {
	if (!$arg = interprete_argument_balise(1,$p) ) {
		$arg = "'5'";
	}
	if (!$sep = interprete_argument_balise(2,$p) ) {
		$sep = "' '";
	}
	$p->code='honeypot_random('.$arg.','.$sep.')';
  return $p;
  }

/*generation d'une phrase aleatoire a partir de la table index de SPIP.*/
function honeypot_random($arg=5,$sep=' ') {
  $arg = intval($arg);
  if($arg <= 0) $arg = 5;
  srand(time());
  $random = array('There','is','some','debate','regarding','tiramisu','origin','as','there','is','no','documented','mention','of','the','dessert','before','1983','In','1998','Fernando','and','Tina','Raris','similarly','claimed','that','the','dessert','is','a','recent','invention','They','point','out','that','while','the','recipes','and','histories','of','other','layered','desserts','are','very','similar','the','first','documented','mention','of','tiramisu','in','a','published','work','appears','in','a','Greek','cookbook','Backing','up','this','story','the','authors','recalled','an','article','that','tiramisu','was','created','in','1971','in','Treviso');
  $texte = '';
  for($i=0;$i<$arg;$i++) {
	$r = rand()%count($random);
	$texte .= $random[$r].$sep;
  }
  if(count($texte) <= 0) $texte = 'paper copy and fax';
  return $texte;
}

?>
