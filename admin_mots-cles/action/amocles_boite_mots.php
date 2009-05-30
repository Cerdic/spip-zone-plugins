<?php

	// action/amocles_boite_mots.php

	// $LastChangedRevision$
	// $LastChangedBy$
	// $LastChangedDate$

	/*****************************************************
	Copyright (C) 2007-2008 Christian PAULUS
	cpaulus@quesaco.org - http://www.quesaco.org/
	/*****************************************************
	
	This file is part of Amocles.
	
	Amocles is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	
	Amocles is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with Amocles; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	
	/*****************************************************
	
	Ce fichier est un des composants de Amocles. 
	
	Amocles est un programme libre, vous pouvez le redistribuer et/ou le modifier 
	selon les termes de la Licence Publique Generale GNU publiee par 
	la Free Software Foundation (version 2 ou bien toute autre version ulterieure 
	choisie par vous).
	
	Amocles est distribue car potentiellement utile, mais SANS AUCUNE GARANTIE,
	ni explicite ni implicite, y compris les garanties de commercialisation ou
	d'adaptation dans un but specifique. Reportez-vous a la Licence Publique Generale GNU 
	pour plus de details. 
	
	Vous devez avoir recu une copie de la Licence Publique Generale GNU 
	en meme temps que ce programme ; si ce n'est pas le cas, ecrivez a la  
	Free Software Foundation, Inc., 
	59 Temple Place, Suite 330, Boston, MA 02111-1307, etats-Unis.
	
	*****************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('base/abstract_sql');
include_spip('inc/amocles_api_globales');
include_spip('inc/amocles_api_prive');


function action_amocles_boite_mots_dist () {
	
	$args = _request('arg');
	
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	if (preg_match("/^(\d+),(\d+)$/", $args, $r)) 
	{
		$connect_id_auteur = $r[1];
		$id_article = intval($r[2]);
		
		$ids = amocles_get_preference ('admins_groupes_mots_ids');
		
		$champs_array = unserialize(_AMOCLES_POIDS_MOTS_CHAMPS);
		$champs_sql = implode(",", array_keys($champs_array));
		
		if (
			in_array($connect_id_auteur, $ids)
			&& $row = spip_fetch_array(spip_query("SELECT $champs_sql,lang FROM spip_articles WHERE id_article=$id_article LIMIT 1"))
		) 
		{
			$stop_words = amocles_get_preference ('stop_words');
			if(!$stop_words = $stop_words[$row['lang']]) {
				$stop_words = array();
			}
			$les_mots = array();
			
			$typos = unserialize(_AMOCLES_POIDS_MOTS_TEXTE);
			
			foreach($champs_array as $key => $poids) 
			{
				foreach($typos as $pattern => $poids_typo)
				{
					preg_match_all("|".$pattern."|U", $row[$key], $out, PREG_SET_ORDER);
				}
				if ($out) 
				{
					foreach($out as $mot) 
					{
						$les_mots = amocles_ajouter_mot($les_mots, $mot[1], $poids * $poids_typo, $stop_words);
					}
				}
				/* texte brut (ce qu'il en reste) */
				$les_mots = amocles_ajouter_mot($les_mots, $row[$key], $poids * $poids_typo, $stop_words);
			}
		}
		/* nom du domaine donne un peu plus de poids */
		$les_mots = amocles_ajouter_mot($les_mots, $GLOBALS['meta']['nom_site'], 10, $stop_words);
		
		$result = "";

		foreach($les_mots as $mot => $val)
		{
			$result .= ""
				. "<tr>\n"
				. " <td>" . $val['mot'] . "</td>\n"
				. " <td>" . $val['occurence'] . "</td>\n"
				. " <td>" . $val['poids'] . "</td>\n"
				. "</tr>\n"
				;
		}
		
		$result = ""
			. "<table id='amocles_table_mots' class='amocles_mots_liste verdana2' charset='".$GLOBALS['meta']['charset']."'>\n"
			. "<caption>"._T(_AMOCLES_LANG."mots_detectes")."</caption>"
			. "<tr><th>"._T(_AMOCLES_LANG."mot")."</th><th>"._T(_AMOCLES_LANG."occurence")."</th><th>"._T(_AMOCLES_LANG."poids")."</th></tr>"
			. $result
			. "</table>"
			;
		
		echo($result);
	} 
	else {
		amocles_log("action_amocles_boite_mots: $arg pas compris");
	}
}

function amocles_ajouter_mot ($les_mots, $string, $poids, $stop_words)
{
	$string = amocles_nettoyer_typo($string);
	foreach(explode(" ", $string) as $mot) 
	{
		$key = strtolower(translitteration($mot));
		$mot = utf8_encode(strtolower(utf8_decode($mot)));
		
		if (
			(strlen($key) >= _AMOCLES_KEYWORDS_MINSIZE)
			&& !in_array($mot, $stop_words)
			)
		{
			if (!isset($les_mots[$key])) 
			{
				$les_mots[$key] = array(
					'mot' => $mot
					, 'occurence' => 0
					, 'poids' => 0
				);
			}
			$les_mots[$key]['occurence']++;
			$les_mots[$key]['poids'] += $poids;
		}
	}
	return ($les_mots);
}

function amocles_nettoyer_typo ($string)
{
	$patterns = array("/[{}.;:,!?()]/", "/[[:space:]]+/");
	$replacements = array(" ", " ");
	$string = preg_replace($patterns, $replacements, $string);
	return ($string);
}

?>