<?php
/*
function openid_formulaire_charger($flux){
	if ($flux['args']['form'] == 'editer_auteur'){
	}
	return $flux;
}

function openid_formulaire_verifier($flux){
	if ($flux['args']['form'] == 'editer_auteur'){
#		if (!is_openid(_request('openid')) {
#			$flux['data']['openid'] = _T('openid:erreur_openid');
#		}
	}
	return $flux;
}
*/

function openid_editer_contenu_objet($flux){
	if ($flux['args']['type']=='auteur') {
		include_spip('public/assembler');
		$openid = recuperer_fond('formulaires/inc-openid', $flux['args']);
		$flux['data'] = preg_replace('%(<li class="editer_email(.*?)</li>)%is', '$1'."\n".$openid, $flux['data']);
	}
	return $flux;
}




/*
// determine si un login est de type openid (une url avec http ou https)
function is_openid($login){
	// Detection s'il s'agit d'un URL à traiter comme un openID
	// RFC3986 Regular expression for matching URIs
	#if (preg_match('_^(?:([^:/?#]+):)?(?://([^/?#]*))?([^?#]*)(?:\?([^#]*))?(?:#(.*))?$_', $login, $uri_parts)
	#	AND ($uri_parts[1] == "http" OR $uri_parts[1] == "https")) {
	
	// s'il y a un point, c'est potentiellement un login openid
	// ca permet d'eliminer un bon nombre de pseudos tout en 
	// autorisant les connexions openid sans avoir besoin de renseigner le http://
	if (strpos($login, '.')!==false) {
		return true;
	} else {
		return false;
	}
}
*/
?>
