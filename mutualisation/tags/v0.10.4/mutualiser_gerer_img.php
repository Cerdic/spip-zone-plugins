<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/*
 * Creer les rewrite rules
 *
 * Dans /IMG, on arrive avec (http://naya/IMG/jpg/photo.jpg) :
 *  %{HTTP_HOST} = 'naya'
 *  %{SCRIPT_NAME} = '/chemin/http/jusqu/a/spip/spip.php'
 *  %{REQUEST_URI} = '/IMG/jpg/photo.jpg'
 *
 */
// sur le repertoire $dir (sites/truc/local/)
function mutualisation_verifier_htaccess($url, $dir) {
	// lire le .htaccess existant
	lire_fichier($url._ACCESS_FILE_NAME, $htaccess);
	$source = $htaccess;

	// verifier notre bloc init
	$bloc = '####
## ce fichier .htaccess est gere par le plugin *mutualisation*
##
## ne le modifiez pas : en cas de besoin editez ce plugin,
## puis effacez ce fichier, il sera recree
##

RewriteEngine On
RewriteBase /
';

	if (strpos($htaccess, $bloc) === false)
		$htaccess = $bloc;

	$host = $_SERVER['HTTP_HOST'];
	$racine = dirname($_SERVER['SCRIPT_NAME']); // profondeur_url();
	if ($racine == '/') $racine='';
	$site = basename(dirname($dir));
	$bloc = "

#### 'http://$host$racine/' = 'sites/$site/'
RewriteCond %{HTTP_HOST}%{REQUEST_URI} ^".preg_quote("$host$racine/",',')."
RewriteRule .* $racine/$dir\$0 [L]
";

	if (strpos($htaccess, $bloc) === false)
		$htaccess .= $bloc;


	return ($htaccess === $source
		OR ecrire_fichier($url._ACCESS_FILE_NAME, $htaccess)
		);
}


/*
 * 
 * Transformer les liens
 * sites/nom_site/(IMG|local).* en (IMG|local).*
 * 
 */
function mutualisation_traiter_url_img_courtes($flux) {
	// IMG
	if (!defined('_URL_IMG'))
		define('_URL_IMG', _DIR_RACINE . _NOM_PERMANENTS_ACCESSIBLES);
	// local
	if (!defined('_URL_VAR'))
		define('_URL_VAR', _DIR_RACINE . _NOM_TEMPORAIRES_ACCESSIBLES);

	if (mutualisation_verifier_htaccess(_URL_VAR, _DIR_VAR)
	AND mutualisation_verifier_htaccess(_URL_IMG, _DIR_IMG)) {
		return str_replace(
			array(_DIR_VAR, _DIR_IMG),
			array(_URL_VAR, _URL_IMG),
		$flux);
	} else
		return $flux;
}

?>
