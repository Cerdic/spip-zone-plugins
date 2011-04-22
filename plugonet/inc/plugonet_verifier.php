<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2011                                                *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/plugin');

function inc_plugonet_verifier($files, $forcer_paquetxml=false, $simuler=false) {
	// Chargement des fonctions de validation XML et d'extraction des informations contenues 
	// dans la balise plugin
	$valider_xml = charger_fonction('valider', 'xml');
	$informer_xml = charger_fonction('infos_plugin', 'plugins', true);
	$informer_xml = ($informer_xml)	? $informer_xml : charger_fonction('get_infos', 'plugins');

	$erreurs = array();
	foreach ($files  as $nom)  {
		if (lire_fichier($nom, $contenu)) {
			$erreurs[$nom]['erreur_lecture_pluginxml'] = false;
			// Validation formelle du fichier plugin.xml (uniquement des avertissements)
			$resultats = $valider_xml($contenu, false, false, 'plugin.dtd');
			$erreurs[$nom]['notice_validation_pluginxml'] = is_array($resultats) ? $resultats[1] : $resultats->err; //2.1 ou 2.2

			// Recherche de toutes les balises plugin contenues dans le fichier plugin.xml et 
			// extraction de leurs infos
			$regexp = '#<plugin[^>]*>(.*)</plugin>#Uims';
			if ($nb_balises = preg_match_all($regexp, $contenu, $matches)) {
				$plugins = array();
				// Pour chacune des occurences de la balise on extrait les infos
				$erreurs[$nom]['erreur_information_pluginxml'] = false;
				foreach ($matches[0] as $_balise_plugin) {
					// Extraction des informations du plugin suivant le standard SPIP
					// -- si une balise est illisible on sort de la boucle et on retourne 
					//    l'erreur sans plus de traitement
					if (!$infos = $informer_xml($_balise_plugin)) {
						$erreurs[$nom]['erreur_information_pluginxml'] = true;
						break;
					}
					$plugins[] = $infos;
				}
			}
			else
				$erreurs[$nom]['erreur_information_pluginxml'] = true;
		}
		else
			$erreurs[$nom]['erreur_lecture_pluginxml'] = true;
	}

	return array($erreurs);
}

?>
