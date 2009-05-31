<?php

// inc/cital_api_globales.php

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
	
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/utils');
include_spip('inc/metas');
include_spip('inc/xml');

/*
 * Ecrire dans un fichier
 * @return 
 * @param $filename string
 * @param $data string
 */
function cital_file_write_contents ($filename, $data) {
	if ($f = @fopen($filename, 'w')) {
		$bytes = fwrite($f, $data);
		fclose($f);
	}
	return($bytes);
}

/*
 * Message dans le journal
 * @return 
 * @param $message string
 */
function cital_log ($message) {
	spip_log($message.$flag, _CITAL_PREFIX);
	return(true);
}

/*
 * Va chercher le numero de revision SVN dans le fichier svn.revision si present
 * @return string ou null si absent
 */
function cital_plugin_revision () {
	if(is_readable($f = _DIR_PLUGIN_CITAL."svn.revision")) {
		if($content = file_get_contents($f)) {
			if(preg_match("|<revision>(.*)</revision>|U", $content, $matches)) {
				return($matches[1]);
			}
		}
	}
	return($revision);
}

/*
 * D'après http://fr2.php.net/manual/fr/function.xml-parse.php#87920
 * @return array
 * @param $url string
 * @param $get_attributes Object[optional]
 * @param $priority Object[optional]
 */
function xml2array($url, $get_attributes = 1, $priority = 'tag') {

	$charset = $GLOBALS['meta']['charset'];
	$charset = (!$charset) ? "UTF-8" : strtoupper($charset);

    if (function_exists('xml_parser_create')) {
		$parser = xml_parser_create('');
		if ($contents = file_get_contents($url)) {
			xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, $charset);
			xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
			xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
			xml_parse_into_struct($parser, trim($contents), $xml_values);
			xml_parser_free($parser);
			if ($xml_values) {
				$xml_array = array ();
				$parents = array ();
				$opened_tags = array ();
				$arr = array ();
				$current = & $xml_array;
				$repeated_tag_index = array ();
				foreach ($xml_values as $data)
				{
					unset ($attributes, $value);
					extract($data);
					$result = array ();
					$attributes_data = array ();
					if (isset ($value))
					{
						if ($priority == 'tag')
							$result = $value;
						else
							$result['value'] = $value;
					}
					if (isset ($attributes) and $get_attributes)
					{
						foreach ($attributes as $attr => $val)
						{
							if ($priority == 'tag')
								$attributes_data[$attr] = $val;
							else
								$result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr'
						}
					}
					if ($type == "open")
					{
						$parent[$level -1] = & $current;
						if (!is_array($current) or (!in_array($tag, array_keys($current))))
						{
							$current[$tag] = $result;
							if ($attributes_data)
								$current[$tag . '_attr'] = $attributes_data;
							$repeated_tag_index[$tag . '_' . $level] = 1;
							$current = & $current[$tag];
						}
						else
						{
							if (isset ($current[$tag][0]))
							{
								$current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;
								$repeated_tag_index[$tag . '_' . $level]++;
							}
							else
							{
								$current[$tag] = array (
									$current[$tag],
									$result
								);
								$repeated_tag_index[$tag . '_' . $level] = 2;
								if (isset ($current[$tag . '_attr']))
								{
									$current[$tag]['0_attr'] = $current[$tag . '_attr'];
									unset ($current[$tag . '_attr']);
								}
							}
							$last_item_index = $repeated_tag_index[$tag . '_' . $level] - 1;
							$current = & $current[$tag][$last_item_index];
						}
					}
					elseif ($type == "complete")
					{
						if (!isset ($current[$tag]))
						{
							$current[$tag] = $result;
							$repeated_tag_index[$tag . '_' . $level] = 1;
							if ($priority == 'tag' and $attributes_data)
								$current[$tag . '_attr'] = $attributes_data;
						}
						else
						{
							if (isset ($current[$tag][0]) and is_array($current[$tag]))
							{
								$current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;
								if ($priority == 'tag' and $get_attributes and $attributes_data)
								{
									$current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
								}
								$repeated_tag_index[$tag . '_' . $level]++;
							}
							else
							{
								$current[$tag] = array (
									$current[$tag],
									$result
								);
								$repeated_tag_index[$tag . '_' . $level] = 1;
								if ($priority == 'tag' and $get_attributes)
								{
									if (isset ($current[$tag . '_attr']))
									{
										$current[$tag]['0_attr'] = $current[$tag . '_attr'];
										unset ($current[$tag . '_attr']);
									}
									if ($attributes_data)
									{
										$current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
									}
								}
								$repeated_tag_index[$tag . '_' . $level]++; //0 and 1 index is already taken
							}
						}
					}
					elseif ($type == 'close')
					{
						$current = & $parent[$level -1];
					}
				}
			} // end if (!$xml_values) 
		} // end if ($contents = file_get_contents($url))
	}
	else cital_log("pas de parser xml ?");
	
    return ($xml_array);
}

/*
 * Charger les citations pour la langue en cours
 * @return array
 * @param $lang string[optional]
 */
function cital_citations_charger ($lang = "") {
	
	$lang = "_" . (($lang) ? $lang : $GLOBALS['langue_site']);
	
	if(!function_exists('xml_parser_create')) {
		cital_log("Erreur: Analyseur syntaxique XML manquant");
    }
	else {
		if(
			($f = find_in_path(_DIR_CITATIONS . "citations" . $lang.".xml"))
			|| ($f = find_in_path(_DIR_CITATIONS . "citations_fr.xml"))
		) {
			cital_log("lecture de $f");
			$citations = xml2array($f);
		}
	}
	
	return($citations);
}

?>