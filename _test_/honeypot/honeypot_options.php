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
		$sep = ' ';
	}
	$p->code='honeypot_random('.$arg.','.$sep.')';
  return $p;
  }

/*generation d'une phrase aleatoire a partir de la table index de SPIP.*/
function honeypot_random($arg=5,$sep=' ') {
  $arg = intval($arg);
  if($arg <= 0) $arg = 5;
  srand(time());
  $limit =  (rand()%$arg)+1;
  $rez = spip_abstract_select(array('dico'), #SELECT
					   array('spip_index_dico'), #FROM
					   array("dico REGEXP '^[a-zA-Z]+$'"), #WHERE
					   array(), #GROUPBY 
					   array("RAND()"), #ORDERBY
							 "0,$limit" #LIMIT
					   );
  $texte = '';
  while($row = spip_abstract_fetch($rez)) {
	$texte.= $row['dico'].$sep;
  }
  if(count($texte) <= 0) $texte = 'paper copy and fax';
  return $texte;
}

?>
