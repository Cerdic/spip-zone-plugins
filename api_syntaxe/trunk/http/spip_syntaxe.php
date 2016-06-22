<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function http_spip_syntaxe_erreur_dist($code, $requete, $reponse) {
	$reponse->setStatusCode($code);
	
	return $reponse;
}

function http_spip_syntaxe_post_collection_dist($requete, $reponse) {-
	$collection = $requete->attributes->get('collection');
	
	if (in_array($collection, array('propre', 'typo', '_T', '_T_ou_typo'))) {
		include_spip('inc/texte');
		
		$contenu = $requete->getContent();
		$transforme = $collection($contenu);
		
		$reponse->setStatusCode(200);
		$reponse->setCharset('utf-8');
		$reponse->setContent($transforme);
	}
	
	return $reponse;
}
