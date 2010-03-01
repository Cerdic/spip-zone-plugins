<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

$groupe_imc = array();
$liste_imc = array();

$cities_html="";
$value="";
$current_key="";
$current_cat="";
$dict_i=0;

/*
 * récupére et lit le contenu du fichier cities.xml
 * Place ces infos dans les tables spip-listeimc-imc et spip-listeimc-groupe
 * Mettre en place une gestion des erreurs
 */
function generer_cities_html() {

	global $groupe_imc, $liste_imc;

	include_spip('inc/distant');
	
	if (!$cities_xml = recuperer_page("http://www.indymedia.org/cities.xml"))
	{
		$erreur = "Impossible de récuperer cities.xml : le fichier n'existe pas";
		return;
	}
                             
	/*if (!$cities_array = spip_xml_parse($cities_xml))
	{
		$erreur = "Impossible de traiter le contenu de cities.xml.";
		return;
	}*/

	// le parseur de spip ne fonctionne pas correctement
	// du coup ecriture d'un parseur perso
                            
	//global $cities_html;
                                         
	$xml_parse = xml_parser_create("UTF-8");
	xml_set_element_handler($xml_parse,"cities_debut_element","cities_fin_element");
	xml_set_character_data_handler($xml_parse,"cities_element");

	if (!xml_parse($xml_parse,$cities_xml,false))
	{
		die(sprintf("erreur XML : %s à  la ligne %d",
		xml_error_string(xml_get_error_code($xml_parser)),
		xml_get_current_line_number($xml_parser)));
	}

	xml_parser_free($xml_parse);
        
	// il faut maintenant alimenter les tables avec les infos du fichier
	foreach($groupe_imc as $id_groupe => $groupe)
	{
		sql_insertq(
			'spip_listeimc_groupe',
			array(
				'id_groupe' => $id_groupe,
				'libelle' => $groupe
			)
		);
	}
	
	//print_r($liste_imc);
	foreach($liste_imc as $id_imc => $imc)
	{
		
		sql_insertq(
			'spip_listeimc_imc',
			array(
				'id_imc' => $id_imc,
				'id_groupe' => $imc['id_groupe'],
				'libelle' => $imc['libelle'],
				'url' => $imc['url']
			)
		);
	}
	return;                                                                                                                      
}




function cities_debut_element($parser,$name,$attrs)
{
	global $groupe_imc, $liste_imc;
	global $id_groupe, $id_imc;

	global $value;
	global $dict_i;
	global $current_key, $current_cat;
        
	switch ($name) {
		case "PLIST": // debut du document
			//." version du fichier : ".$attrs['VERSION']."-->\n";
			$id_groupe  = 0;
			$id_imc = 1;
			break;

		case "DICT": // ensemble de valeur
			$dict_i++;
			break;

		case "KEY" : // cle de la valeur
			$current_key="";
			break;

		case "STRING" : // url de l'imc
			break;

		case "ARRAY": // debut de tableau (contenant des ensembles de valeur)
			break;
	}                                    
}

function cities_fin_element($parser,$name)
{
	global $groupe_imc, $liste_imc;
	global $id_groupe, $id_imc;

	global $dict_i;
	global $value; 
	global $current_key, $current_cat;
          
	switch($name) {
		case "PLIST": // fin du document
			//$cities_html .= "<!-- FIN CITIES.HTML -->\n";
			break;

		case "DICT": // fin d'ensemble de valeur
			$dict_i--;
			break;

		case "KEY" : // fin cle de valeur
			$value = trim($value);
			if ($dict_i == 1)	// indique dans quelle catégorie on ce trouve
			{
				/*
					null : la liste des imcs
					process :  la liste des sites process
					projects : la liste des sites projets
					regions : la liste des sites regions
					topics : la liste des sites topics
				*/
				$current_cat = $value;
				if ($current_cat != 'NULL')
				{
					$groupe_imc[++$id_groupe] = $value;
				}
				
				
			}
			else if ($dict_i == 2)  // une catégorie, faut alimenter la table groupe
			{
				if (strcmp("NULL",$value)!=0 && $current_cat == 'NULL')
				{
					$groupe_imc[++$id_groupe] = $value;
				}
			}
			else if ($dict_i == 3) // un imc
			{ 
				$current_key = $value;
			}

        		$value="";
		        break;

		case "STRING" : // fin valeur

			$value = trim($value);
			if ($current_key != 'www.indymedia.org')
			{
				$liste_imc[$id_imc++] = array(
					'libelle' => $current_key,
					'url' => $value,
					'id_groupe' => $id_groupe
				);
			}

			$value="";
			break;

	}                                                                                                        
}  
                                                                                                                                                        
function cities_element($parser,$data) 
{
	global $value;
	$value .= $data;
}
                                                                                                                                                                                                                                                                             
?>