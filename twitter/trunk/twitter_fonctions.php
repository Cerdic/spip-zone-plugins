<?php
/*
 * Plugin spip|twitter
 * (c) 2009-2013
 *
 * envoyer et lire des messages de Twitter
 * distribue sous licence GNU/LGPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


function generer_url_microblog($id, $entite='article', $args='', $ancre='', $public=true, $type=null){
	include_spip('inc/filtres_mini');
	$config = unserialize($GLOBALS['meta']['microblog']);

	if (!$public
	 OR $entite!=='article'
	 OR !$config['short_url'])
		return url_absolue(generer_url_entite($id, $entite, $args, $ancre, $public, $type));
	else
		return $GLOBALS['meta']['adresse_site'].'/'.$id;
}


/**
 * Fonction d'utilisation simple de l'API twitter oAuth
 *
 * @param $command string : la commande à passer
 * @param $type string : le type de commande (get/post/delete)
 * @param $params array : les paramètres dans un array de la commande
 * @param $retour string : le retour souhaité par défaut cela renverra la chaine
 * ou l'array retourné par la commande. Sinon on peut utiliser les valeurs http_code,http_info,url
 * @param array $tokens
 * @return bool|string|array
 */
if (!function_exists("microblog_twitter_api")){
function microblog_twitter_api($command,$type='get',$params=array(),$retour='',$tokens=null){
	$options = $tokens;
	if ($retour)
		$options['return_type'] = $retour;
	include_spip("inc/twitter");
	return twitter_api_call($command, $type, $params, $options);
}
}


/**
 * Pour utiliser |twitter_api_call dans un squelette
 * @use twitter_api_call
 *
 * @param string $command
 * @param string $type
 * @param array $params
 * @param array $options
 * @return array|bool|string
 */
function filtre_twitter_api_call_dist($command,$type='get',$params=array(),$options=null){
	include_spip("inc/twitter");
	return twitter_api_call($command, $type, $params, $options);
}

?>