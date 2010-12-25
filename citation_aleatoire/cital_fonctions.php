<?php

// cital_fonctions.php

	/*****************************************************
	Copyright (C) 2009 Christian PAULUS
	cpaulus@quesaco.org - http://www.quesaco.org/
	/*****************************************************
	
	This file is part of CitAl (Citation Aleatoire).
	
	CitAl is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	
	CitAl is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with CitAl; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	
	/*****************************************************
	
	Ce fichier est un des composants de CitAl. 
	
	CitAl est un programme libre, vous pouvez le redistribuer et/ou le modifier 
	selon les termes de la Licence Publique Generale GNU publie'e par 
	la Free Software Foundation (version 2 ou bien toute autre version ulterieure 
	choisie par vous).
	
	CitAl est distribue' car potentiellement utile, mais SANS AUCUNE GARANTIE,
	ni explicite ni implicite, y compris les garanties de commercialisation ou
	d'adaptation dans un but spe'cifique. Reportez-vous a' la Licence Publique Ge'ne'rale GNU 
	pour plus de details. 
	
	Vous devez avoir reçu une copie de la Licence Publique Generale GNU 
	en meme temps que ce programme ; si ce n'est pas le cas, ecrivez a' la  
	Free Software Foundation, Inc., 
	59 Temple Place, Suite 330, Boston, MA 02111-1307, Etats-Unis.
	
	*****************************************************/
	
// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

// la balise #CITATION

include_spip('inc/cital_api_globales');

function balise_CITATION ($p) {

	// options pour la balise
	$options = array(
		'sig' => _T('cital:cital')
		, 'paragrapher' => true
		, 'class' => false
		, 'style' => false
	);
	
	// si options transmises, style :
	// #CITATION{paragrapher=non,class=maclass,style=monstyle}
	if(isset($p->param) && $p->param) {
		
		$params = $p->param[0];
		
		foreach($params as $key => $value) {
			
			if(is_array($value) && isset($value[0]->texte)) {
			
				if($t = $value[0]->texte) {
				
					if(strpos($t, "=") === false) {
						$k = $t;
						$v = "oui";
					}
					else {
						list($k, $v) = explode('=', $t);
					}
					switch($k) {
						case 'paragrapher':
							$options[$k] = ($v == "oui");
							break;
						case 'class':
						case 'style':
							$options[$k] = trim($v);
					}
				}
			}
		}
	}
	
	$params = urlencode(serialize($options));

	$p->code = "calcul_CITATION('$params')";
	$p->statut = 'php';

	return($p);
}

function calcul_CITATION ($params) {

	global $spip_lang;

	$citations = cital_citations_charger($spip_lang);
	
	$params = urldecode($params);
	$params = unserialize($params);
	
	if(is_array($citations) && isset($citations['citations']['citation'])) {

		$nb = count($citations['citations']['citation']);
		//cital_log("$nb dans le fichier XML");
		
		// choisir une citation au hasard
		$alea = rand(0, ($nb - 1));
		
		$c = $citations['citations']['citation'][$alea];
		
		$auteur = trim($c['auteur']);
		$texte = trim($c['texte']);
		
		$auteur = ($auteur) ? " <span class='auteur'>$auteur</span>" : "";
		
		$p = ($params['paragrapher'] ? "p" : "span");
		$class = ($params['class'] ? " " . $params['class'] : "");
		$style = ($params['style'] ? "style='" . $params['style'] . "'" : "");
		
		$result = ($texte) ? "\n<!-- " . $params['sig'] . " -->\n" 
			. "<$p class='citation" . $class . "' $style>$texte$auteur</$p>\n" : "";
			
	}
	else {
		cital_log('Pas de citation disponible');
	}

	return($result);
}

