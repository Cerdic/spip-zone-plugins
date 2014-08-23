<?php
/**
 * Plugin Aspirateur pour Spip 3.0
 * Licence GPL 3
 *
 * (c) 2014 Anne-lise Martenot
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/* traitement SPIP */
/**
 *
 * Prend en argument le chemin d'un fichier 
 * et retourne un chemin en SPIP document en appliquant l'adresse local du SPIP utilisé
 *
 * Exemple lien_spip_document(http://www.exemple.fr/upload/autre_repertoire/UN_Fichier.pdf)
 * retournera http://localhost/site_recup/IMG/pdf/un_fichier.pdf
 *
 * @param $chemin_fichier
 *
 * @return string
 *	le chemin SPIP
 *
**/
function lien_spip_document($chemin_fichier){
		$nom_du_fichier=basename($chemin_fichier);
		//passage en minuscules
		$nom_du_fichier=strtolower(translitteration($nom_du_fichier));
		$nom_du_fichier=preg_replace('#%20#Umis','_',$nom_du_fichier);
		$nom_du_fichier=preg_replace('#_-_#Umis','_',$nom_du_fichier);
		$path_parts = pathinfo($chemin_fichier);
		$chemin_fichier_site_prod=$GLOBALS['meta']['adresse_site'];
		$ext = $path_parts ? $path_parts['extension'] : substr($chemin_fichier, strrpos($chemin_fichier, '.') + 1);
		return $chemin_fichier_site_prod."/IMG/$ext/".$nom_du_fichier;;
}
