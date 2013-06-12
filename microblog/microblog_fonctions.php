<?php
/**
 * Plugin spip|microblog
 * (c) Fil 2009-2010
 *
 * envoyer des micromessages depuis SPIP vers twitter ou laconica
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
function microblog_twitter_api($command,$type='get',$params=array(),$retour='',$tokens=null){
	include_spip('inc/microblog');
	if (!$connection = twitter_connect($tokens))
		return false;

	switch($type){
		case 'get':
			$content = $connection->get($command,$params);
			break;
		case 'post':
			$content = $connection->post($command,$params);
			break;
		case 'delete':
			$content = $connection->delete($command,$params);
			break;
		default:
			$content = $connection->get($command,$params);
	}

	switch($retour){
		case 'http_code':
			return $connection->http_code;
		case 'http_info':
			return $connection->http_info;
		case 'url':
			return $connection->url;
		default:
			if (!is_string($content) AND is_array($content)) {
				// recopie ?
				$contents = array();
				foreach($content as $key => $val){
					$contents[$key] = $val;	
				}
				return $contents;
			}
			else{
				return $content;
			}
			
	}
}

?>