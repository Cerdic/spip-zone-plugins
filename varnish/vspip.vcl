# 
# Configuration de varnish (varnish-cache.org)
# optimisée pour SPIP
# 
# Varnish est un proxy inverse, installé sur le serveur Web,
# qui permet :
#
# - 1. Rapidité. Absorbe une demande importante sur une même URL
#      et y répond directement sans solliciter apache
# - 2. Tolérance aux pannes. Renvoie des pages depuis sa mémoire cache
#      même lorsque apache est planté
# - 3. Souplesse dans l'affectation de serveur(s) apache à différentes
#      applications (par ex. load balancing, changement de serveur, etc)
# - 4. Moindre consommation de CPU : économique et écologique
# 
# La configuration ci-dessous permet d'optimiser son fonctionnement
# avec SPIP ; elle s'applique sans problème à des serveurs
# hébergeant aussi d'autres types de scripts, qui peuvent utiliser
# les mêmes mécanismes de communication X-Varnish-Message et X-Varnish-Purge
#


## -- BACKEND PRINCIPAL --
##
## Tout d'abord, nous allons définir l'adresse du serveur apache hébergeant
## nos sites Web (ce que varnish appelle le backend).
##
## Dans notre cas, le serveur apache est accessible sur le
## port 8080 de l'interface 127.0.0.1 ; il répond aux mêmes requêtes que
## lorsqu'il est configuré sur le port 80 :
##     GET / HTTP/1.0
##     Host: nomdusite.tld
##
## Pour ce backend, nous définissons un temps de latence avant le premier octet
## de 300s ; ainsi, lorsqu'une page demande un temps de calcul très long (par
## exemple un POST d'un long article), on attend jusqu'à 5 minutes avant de
## déclarer forfait et d'envoyer une erreur 500.
## http://vincentfretin.ecreall.com/articles/varnish-guru-meditation-on-timeout
##
## De plus, varnish va lancer chaque seconde une requête de test sur une URL
## type ; si 2 requêtes sur les 3 dernières sont en faute, il déclarera le
## backend "malade" ("sick") et passera en mode "tolérance aux pannes",
## jusqu'à ce que le backend revienne en bonne santé ("healthy").
##
backend default {
	.host = "127.0.0.1";
	.port = "8080";
	.first_byte_timeout = 300s;
	.probe = {
		# Ici mettre un hit vers un petit fichier fixe sur le backend 
		#.url = "/";
		.request =
			"GET /prive/images/searching.gif HTTP/1.0"
			"Host: zzz.rezo.net"
			"Connection: close";
		.timeout = 34 ms; 
		.interval = 3s; 
		.window = 3;
		.threshold = 2;
	}
	.max_connections = 50;
}


## -- BACKEND DE TEST D'ERREUR 503 --
##
## Ce backend "guru" est toujours en panne ; il est destiné à provoquer
## une erreur 503, ce qui permet d'afficher délibérément le message d'erreur
## qu'on définira en fin de ce fichier de configuration
## => http://nomdusite.tld/I'm-a-guru
##
backend guru {
	.host = "127.0.0.1";
	.port = "8082"; # !!il faut choisir un numéro de port inutilisé
}



## -- RECV --
## Cette fonction est appelée à chaque connexion d'un client sur varnish.
##
## Elle normalise la requête :
## - en supprimant les cookies inutiles
## - en unifiant les différents types de Accept-Encoding
##
sub vcl_recv {

	## -- GRACE --
	##
	## La "grace" sert dans deux scénarios :
	##
	## - a. "Absorber" la connexion *simultanée* de plusieurs clients
	##      sur une même URL : le temps que le backend calcule et renvoie
	##      la nouvelle réponse, on s'autorise à servir une réponse en cache
	##      mais dont la date de péremption est dépassée de moins de 30s.
	## - b. "Panne" : si le backend est en panne, on s'autorise à servir
	##      des contenus mis en cache mais dont la date de péremption est
	##      dépassée (jusqu'à une heure).
	##
	if (req.backend.healthy) {
		set req.grace = 30s;
	} else {
		set req.grace = 1h;
	}


	## -- COOKIES --
	##
	## On nettoie ici les cookies qui n'impactent pas SPIP ;
	## essentiellement les cookies de tracking statistique, mais aussi
	## les cookies d'option traités côté client (vs. côté serveur).
	## Attention les cookies importants côté serveur (cookie de session admin, 
	## par exemple) ne doivent *pas* être nettoyés.
	##
	if (req.http.Cookie) {

		## __utm[a-z] = cookies google analytics
		## xtvrn = cookies xiti
		set req.http.Cookie = regsuball(req.http.Cookie, "(^|; ) *(__utm[a-z]|xtvrn)=[^;]+;? *", "\1");

		## _pk.* = cookies piwik
		##   => attention, sur l'URL de piwik, ne pas nettoyer les cookies piwik
		if (req.url !~ "/piwik\.php") {
			set req.http.Cookie = regsuball(req.http.Cookie, "(^|; ) *(_pk_[^=]+)=[^;]+;? *", "\1");
		}

		## dans une application particulière, le cookie "blink" est traité
		## côté client ; il ne nous intéresse pas, on le nettoie
		set req.http.Cookie = regsuball(req.http.Cookie, "(^|; ) *(blink|service_\w+)=[^;]+;? *", "\1");

		## si le cookie résultant est vide, le supprimer
		if (req.http.Cookie == "") {
			remove req.http.Cookie;
		}

		## si on est dans un repertoire statique ignorer totalement
		## les cookies (ici, les répertoires SPIP & Drupal + les images
		## css, scripts, etc.)
		## (le ?\d+ final est un éventuel timestamp)
		if (req.url ~ "^[^?]*\.(css|js|jpg|jpeg|gif|png|ico|txt|mp3|ttf)(\?\d+)?$"
		|| req.url ~ "^/(local|IMG|extensions|plugins|static)/") {
			remove req.http.Cookie;
		}

	}
	## fin de la section COOKIE


	## -- X-FORWARDED-FOR --
	##
	## Ajouter un entête X-Forwarded-For: IP
	## en le concaténant avec un éventuel entête déjà existant
	##
	if (req.http.x-forwarded-for) {
		set req.http.X-Forwarded-For = req.http.X-Forwarded-For + ", " + client.ip;
	} else {
		set req.http.X-Forwarded-For = client.ip;
	}

	## -- GZIP --
	## unifier les accept-encoding: accepte gzip ou pas ; on ne gere pas deflate
	## (notamment : FF annonce "gzip, deflate" et Safari "gzip,deflate" !)
	if (req.http.Accept-Encoding) {
		if (req.http.Accept-Encoding ~ "gzip") {
			set req.http.Accept-Encoding = "gzip";
		#} elsif (req.http.Accept-Encoding ~ "deflate") {
		#	set req.http.Accept-Encoding = "deflate";
		} else {
			remove req.http.Accept-Encoding;
		}
	}

	## -- RANGE --
	## Varnish ne doit pas gérer pas les requêtes de contenu partiel ;
	## on les passe directement au backend
	## cf. http://forum.developers.facebook.net/viewtopic.php?id=68440#p253346
	if (req.http.Range) {
		return(pipe);
	}

	## -- TEST 503 --
	## cf. ci-dessus le backend "guru" qui ne mène nulle part
	## se teste via le navigateur sur http://urldusite.tld/_-_-I-m-a-guru
	## permet d'afficher l'erreur définie en bas de ce fichier
	## le nom zen vient de l'erreur par défaut de Varnish : "guru meditation"
	if (req.url == "/_-_-I-m-a-guru") {
		set req.backend = guru;
	}


	## -- DIVERS --
	## certain robot fou demande host:127.0.0.1, on le jette immédiatement
	## note: si le client est local, on accepte (c'est pour munin)
	if (req.http.host == "127.0.0.1" && client.ip != "127.0.0.1" ) {
		error 500 "Unknown virtual host.";
	}
}


## -- FETCH --
## Cette fonction est appelée à chaque retour du backend vers varnish.
##

sub vcl_fetch {

	## -- REDIRECTION --
	## supprimer le port (:8080) envoyé par le backend lors d'une redirection
	## car on veut rediriger vers le port public :80
	if (beresp.http.Location) {
		set beresp.http.Location = regsub(beresp.http.Location, "^(\w+://[^/]+):\d+", "\1");
	}

	## -- TTL: DUREE DE CACHE --
	##
	## C'est la durée de vie de la page dans le cache ; au-delà de cette durée,
	## elle ne pourra être servie au client que dans le scénario de "grace".
	##
	## Différents cas :
	## -1 la ressource signale X-Varnish-TTL: 20s
	##    (c'est ce que fait le plugin pour SPIP)
	##    elle annonce à varnish que son script lui signalera plus tard,
	##    par un entete X-Varnish-Purge, quand le moment sera venu de
	##    rafraichir la page. Dans ce cas de figure on peut donc la mettre
	##    en cache pour la durée indiquée (si elle est > 0)
	## -2a la ressource est statique, on la cache un certain temps raisonnable
	##     (sauf si un autre entete indique qu'elle n'est pas cachable)
	## -2b la ressource est dynamique, on ne la cache pas

	## L'entête X-VARNISH-TTL permet au backend de définir le ttl du cache
	## code inspiré de: http://open.blogs.nytimes.com/2010/09/15/using-varnish-so-news-doesnt-break-your-server/
	## http://www.lovelysystems.com/configuring-varnish-to-use-custom-http-headers/
	##
	## (n'utilise pas X-SPIP-Cache car, sur SPIP standard, ça tuerait les stats)
	##
	if (beresp.http.X-VARNISH-TTL) {
		C{
			char *ttl;
			ttl = VRT_GetHdr(sp, HDR_BERESP, "\016X-VARNISH-TTL:");
			VRT_l_beresp_ttl(sp, atoi(ttl));
		}C
		remove beresp.http.X-VARNISH-TTL;
	}
	## sinon se baser sur la logique habituelle de varnish (Expires, etc) ;
	## en ajoutant une règle pour les fichiers dont on sait avec certitude
	## qu'ils sont statiques : les images etc
	else {
		## ne cacher que les css, js, jpg, gif, png, etc.
		## le (?\d+) est un éventuel timestamp
		## à noter : si apache est bien configuré, cette ligne est inutile
		if (req.url ~ "\.(css|js|jpg|jpeg|gif|png|ico|txt|mp3|ttf)(\?\d+)?$"
		|| req.url ~ "^/(local|IMG|extensions|plugins|static)/") {
			set beresp.ttl = 600s;
			set beresp.http.Cache-Control = "max-age=600";
			set beresp.http.Vary = "Accept-Encoding";
		}
		## ne pas cacher une ressource qui ne precise pas d'entete de cache
		else {
			if (
			(!beresp.http.Cache-Control && !beresp.http.Expires)
			|| beresp.http.Cache-Control ~ "no-cache"
			|| beresp.http.Cache-Control ~ "private" ) {
				set beresp.ttl = 0s;
			}
			#else {
			#	set beresp.ttl = 0s;
			#	remove beresp.http.Cache-Control;
			#}
		}
	}
	## ne pas conserver une ressource servie vieille aux robots
	if (beresp.http.X-Varnish-Stale) {
		set beresp.ttl = 0s;
		#remove beresp.http.X-Varnish-Stale;
	}


	## -- INVALIDATIONS --
	##

	## On a vu ci-dessus qu'une page pouvait entrer en cache si elle s'annonçait
	## via X-Varnish-Message-OK
	##
	## Si à l'inverse le backend veut invalider le cache, il suffit
	## qu'il envoie un entête X-Varnish-Purge
	##
	if (beresp.http.X-Varnish-Purge) {
		ban("req.http.host == " + req.http.host);
		remove beresp.http.X-Varnish-Purge;
		set beresp.ttl = 0s;
	}


	## -- VAR_MODE --
	## Cette section gère les invalidations via le bouton d'admin
	## si on demande un var_mode=recalcul on va par principe tout purger
	## pour ne pas subir de cache secondaire, par exemple dans local/
	## lorsqu'on modifie des CSS ou des images calculées
	## En revanche un var_mode=calcul est plus léger
	if (req.url ~ "[?&]var_mode=(recalcul|images)") {
		ban("req.http.host == " + req.http.host);
		set beresp.ttl = 0s;
	}
	## si on demande un var_mode=calcul on va purger uniquement la page
	## demandee, sans son var_mode
	elsif (req.url ~ "[?&]var_mode=calcul") {
		ban("req.http.host == " + req.http.host + " && req.url == " + regsuball(req.url,"[&?]var_mode=.*$", ""));
		set beresp.ttl = 0s;
	}

	## -- GRACE --
	## Si la réponse est cachable, on peut la conserver pour un maximum d'1h
	## au cas où on aurait une panne (maximum des "grace" définies dans RECV)
	## http://varnish-cache.org/trac/wiki/VCLExampleGrace
	set beresp.grace = 1h;


	## -- RANGE --
	## seuls les gros fichiers (sons, videos) sont susceptibles de valoir
	## un range ; on n'annonce donc le range que pour ceux-la, et on desactive
	## l'entete
	if (beresp.http.Accept-Ranges && beresp.http.content-type !~ "(image|audio|video)/") {
		remove beresp.http.Accept-Ranges;
	}

	## -- ETAG --
	## corrige un bug d'apache qui donne le même Etag aux représentations
	## gzip et non-gzip d'un même fichier...
	if (beresp.http.content-encoding == "gzip"
	&& beresp.http.etag) {
		set beresp.http.Etag = regsub(beresp.http.etag, ".$", "-gzip\0");
	}

	## -- ACTION=CRON --
	## inutile de solliciter la page action=cron de SPIP plus d'1 fois par 5s
	## (à noter : avec les stats en js cette action disparaît)
	if (req.url ~ "\?action=cron$") {
		set beresp.ttl = 5s;
	}

}


## -- HASH --
##
## Cette fonction établit le nom du cache en fonction des caractéristiques
## de la requête
sub vcl_hash {
	hash_data(req.url);
	if (req.http.host) {
		hash_data(req.http.host);
	} else {
		hash_data(server.ip);
	}
	return (hash);
}


## -- HIT --
##
## Cette fonction est appelée quand une page est trouvée dans le cache
## de Varnish ; on ajoute une logique pour que "force-reload", sur un
## navigateur correct, n'utilise pas le cache : il conduit jusqu'au backend
## et met à jour le cache
sub vcl_hit {
	# non cachable, on passe
	if (obj.ttl == 0s) {
		return (pass);
	}

	# force-refresh will update the cache
	# http://www.varnish-cache.org/trac/wiki/VCLExampleEnableForceRefresh
	if (req.http.Cache-Control ~ "no-cache") {
		# Ignore requests via proxy caches, IE users and badly behaved crawlers
		# like msnbot that send no-cache with every request.
		if (! (req.http.Via || req.http.User-Agent ~ "bot|MSIE")) {
			set obj.ttl = 0s;
			return (restart);
		}
	}

	# standard response: use the cached version
	return (deliver);
}

## -- DELIVER --
##
## Cette fonction est appelée à l'envoi final du fichier, sauf (pass)
##
## On met le champ Age dans X-Varnish-Age sinon ça fait râler redbot.org
## On supprime Accept-Ranges de tous les fichiers sauf les images/sons/vidéos
##
sub vcl_deliver {
	{
		set resp.http.X-Varnish-Age = resp.http.age;
		remove resp.http.age;
		if (resp.http.content-type !~ "(image|audio|video)/") {
			remove resp.http.accept-ranges;
		}

	}
}

## -- ERROR --
##
## Cette fonction est appelée en cas d'erreur, par exemple
## lorsque l'objet n'est pas en cache et que le backend est
## en panne (erreur 503).
##
## On peut la déclencher volontairement, pour la tester, via l'URL "guru"
##

sub vcl_error {
    set obj.http.Content-Type = "text/html; charset=utf-8";
    synthetic {"
<html><head>
<meta http-equiv="content-type" content="text/html; charset=utf-8">
<title>"} + obj.status + " " + obj.response + {"</title>
<style type="text/css"> 
body {
	background: #689ab3;
	color: #333;
	margin: 0;
	font-size:1.3em;
	font-family: georgia, serif;
}
 
div {
	margin: 30px 0 0 0;
	background: #ffffff;
	opacity: 0.6;
	width: 650px;
	padding: 20px 20px 23px 23px;
}
 
p {
	margin: 0.5em 0 0.5em 0;
}

p.en {
	margin: 0 0 0.5em 0;
}

p.fin {
	margin: 0.5em 0 0 0;
}
 
p.message {
	font-size: 1.5em;
	margin: 0 0 0.5em 0;
}	
 
h1 {
	font-size: 2em;
	margin: 0 0 0.5em 0;
	font-weight:normal;
	display:none;
}
 
 
</style> 
</head><body> 
 
<div> 
 
<p class="message">Le service est momentan&#233;ment indisponible.<br /> 
Veuillez r&#233;essayer un peu plus tard.
</p> 

<p class="en">The service is currently unavailable. Please try again later.
</p>
 
<p class="fin"><small>Erreur "} + obj.status + {" | XID: "} + req.xid + {"</small></p> 
 
</div> 


</body></html>
"};
    return (deliver);
}

