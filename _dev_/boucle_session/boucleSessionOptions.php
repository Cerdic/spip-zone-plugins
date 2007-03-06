<?php

// mettre cette variable a true pour avoir un cache qui stocke un exemplaire
// par statut de d'internaute. Si on n'utilise les boucles session que comme
// critere, cela evite de mettre un cache a zero
$cacheParStatutAuteur= true;

// avec cette autre variable, il y aura en cache autant d'exemplaires que
// d'auteurs. donc, a eviter s'ils sont nombreux
$cacheParIdAuteur= false;

if($cacheParStatutAuteur) {
	$GLOBALS['marqueur'].= ":bouclesession ".
		( is_array($GLOBALS['auteur_session'])
		  ? $GLOBALS['auteur_session']['statut']
		  : 'anonymous');
}
if($cacheParIdAuteur) {
	$GLOBALS['marqueur'].= ":bouclesession ".
		( is_array($GLOBALS['auteur_session'])
		  ? $GLOBALS['auteur_session']['id_auteur']
		  : '--');
}

error_log($GLOBALS['marqueur']);
?>
