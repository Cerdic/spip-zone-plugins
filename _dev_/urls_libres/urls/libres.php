<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2007                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *  as original founders of spip                                           *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return; // securiser

if (!function_exists('generer_url_article')) { // si la place n'est pas prise
/*

- Comment utiliser ce jeu d'URLs ?

Recopiez le fichier "htaccess.txt" du repertoire de base du site SPIP sous
le sous le nom ".htaccess" (attention a ne pas ecraser d'autres reglages
que vous pourriez avoir mis dans ce fichier) ; si votre site est en
"sous-repertoire", vous devrez aussi editer la ligne "RewriteBase" ce fichier.
Les URLs definies seront alors redirigees vers les fichiers de SPIP.

Definissez ensuite dans ecrire/mes_options.php :
	< ?php $type_urls = 'libres'; ? >
SPIP calculera alors ses liens sous la forme "Mon-titre-d-article".

Variante 'libres2' :
	< ?php $type_urls = 'libres2'; ? >
ajoutera '.html' aux adresses generees : "Mon-titre-d-article.html"

Variante 'qs' (experimentale) : ce systeme fonctionne en "Query-String",
c'est-a-dire sans utilisation de .htaccess ; les adresses sont de la forme
"/?Mon-titre-d-article"
	< ?php $type_urls = 'qlibres'; ? >

*/
	// pour compatibilite
	define ('_terminaison_urls_propres', '');
	define ('_debut_urls_propres', './?');

	// constantes non incluses dans l'url libre stockee
	define ('_terminaison_urls_libres', _terminaison_urls_propres);
	if (($pos = strpos(_debut_urls_propres, '?')) !== false) {
		define ('_debut_urls_libres', substr(_debut_urls_propres, $pos + 1));
		define ('_qs_urls_libres', substr(_debut_urls_propres, 0, $pos + 1));
	} else {
		define ('_debut_urls_libres', _debut_urls_propres);
		define ('_qs_urls_libres', '');
	}

	include_spip('urls/urls_libres_recuperer');
	include_spip('urls/urls_libres_generer');
}
?>
