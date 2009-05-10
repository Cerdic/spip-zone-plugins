<?php

// balise/lien_mon_diplome.php


// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Donner l'url pour appeler le fichier PDF.
 * N'apparaît que si le visiteur est identifié
 * @param array $p Contexte de la balise
 */
function balise_LIEN_MON_DIPLOME ($p)
{
	return (calculer_balise_dynamique($p, 'LIEN_MON_DIPLOME', array()));
}

function balise_LIEN_MON_DIPLOME_stat ($args, $filtres)
{
	// la balise ne gère pas de filtre
	// si filtre présent, les $args ne sont pas reçus
	return (array(rawurlencode(serialize($args))));
}

/**
 * Retourne le lien complet si id_auteur authentifié
 * return string
 */
function balise_LIEN_MON_DIPLOME_dyn ($args)
{
	global $plom_options;
	
	$connect_id_auteur = $GLOBALS['auteur_session']['id_auteur'];
	
	if ($connect_id_auteur)
	{
		$queries = unserialize(rawurldecode($args));
		
		$result = array();
		
		foreach($queries as $query)
		{
			if(strpos($query, "=")) {
				list($key, $val) = explode("=", $query);
				if(array_key_exists($key, $plom_options))
				{
					$val = trim($val, "\"'");
					$result[$key] = $val;
				}
			}
		}
		$queries = $result;

		$args = "id_auteur=" . $connect_id_auteur;

		$params = "";
		
		foreach($queries as $key=>$val) {
			$params .= "&amp;$key=$val";
		}
		
		$result = generer_url_public(_PLOM_MODELE_DEFAUT, $args) . $params;
				
		return ("<a href=\"$result\">" . _T('plom:obtenir_mon_diplome') . "</a>");
	}
	return ('');
}

?>