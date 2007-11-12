<?php

function mutualiser_gerer_img(){
	// IMG
	if (!defined('_URL_IMG'))
		define('_URL_IMG', _DIR_RACINE . _NOM_PERMANENTS_ACCESSIBLES);
	// local	
	if (!defined('_URL_VAR'))
		define('_URL_VAR', _DIR_RACINE . _NOM_TEMPORAIRES_ACCESSIBLES);
	
	// Creer les .htaccess ?	
	$ok = true;
	if (   (!file_exists(_URL_IMG . _ACCESS_FILE_NAME))
		OR (!file_exists(_URL_VAR . _ACCESS_FILE_NAME))){
		$ok = mutualiser_creer_redirection_img();	
	}
	
	// Ajouter le pipeline ?
	if ($ok)
		$GLOBALS['spip_pipeline']['affichage_final'] .= '|mutualisation_url_img_courtes';
}




/* 
 * Creer les rewrite rules
 * 
 * Dans /IMG, on arrive avec (http://naya/IMG/jpg/photo.jpg) :
 *  %{REQUEST_URI} = '/IMG/jpg/photo.jpg'
 *  %{REDIRECT_URL} = ''
 *  %{DOCUMENT_ROOT} = '/home/marcimat/www/spip/'
 *  %{HTTP_HOST} = 'naya'
 *
 * Si rubrique differente (http://naya/mon_site/IMG/jpg/photo.jpg) :
 *  rien ne change
 * 
 * Si host different (http://cfg.naya/IMG/jpg/photo.jpg) :
 *  %{HTTP_HOST} = 'cfg.naya'
 * 
 * On n'a pas d'autre choix que d'utiliser http_host pour
 * differencier les sites 
 * (du coup, une mutualisation de repertoire
 * ne pourra pas utiliser l'option url_img_courtes)
 * 
 */
function mutualiser_creer_redirection_img(){
	$contenu  = "RewriteEngine On\n"
		 	. "RewriteBase /\n"
		 	. "RewriteRule .* " 
		 	. $GLOBALS['mutualisation_dir'] 
		 	. "/%{HTTP_HOST}%{REQUEST_URI} [QSA,L]";
	
	include_spip('inc/flock');
	return 
		    ecrire_fichier (_URL_IMG . _ACCESS_FILE_NAME, $contenu)
		AND ecrire_fichier (_URL_VAR . _ACCESS_FILE_NAME, $contenu);
	
}

/*
 * 
 * Transformer les liens
 * sites/nom_site/(IMG|local).* en (IMG|local).*
 * 
 */
function mutualisation_url_img_courtes($flux){

	return str_replace(
			array(_DIR_VAR, _DIR_IMG), 
			array(_URL_VAR, _URL_IMG), 
			$flux);
}

?>
