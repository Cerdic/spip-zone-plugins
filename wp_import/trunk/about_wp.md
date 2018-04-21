Description du fichier XML issu d'un dump depuis un WP 4.x

Méta Data du site
------------------
generator -> version de Wordpress utilisé
image -> logo du site ?
title -> Titre du site
link -> URL du site
description -> description du site
pubDate -> 
language -> langue du site
wp:wxr_version -> ?
wp:base_site_url -> URL du site (comme link ?)
wp:base_blog_url -> URL du site (comme link ?)


Les contenus
-------------
wp:author -> Auteurs
wp:category -> Rubriques
wp:tag -> mots-clés ?
wp:term ->

item -> le "fourre-tout" de Wordpress. La distinction entre item se fait au niveau de wp:post_type (et c'est tout ?)
item | wp:post_type 
 	[post, page] -> article (en fait post = article et page pourrait etre un article du plugin "Page")
 	[topic] -> article contenant le sujet du forum 
 	[reply] -> forum (un commentaire) lié à l'article contenant le sujet du forum (le wp:post_parent contiendra l'id de l'article)
 	[Document] : attachement
 	