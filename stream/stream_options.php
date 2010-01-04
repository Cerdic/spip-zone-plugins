<?php

# interdire de resyndiquer un article deja syndique
# afin de ne pas l'ecraser si on l'a modifie avec les crayons
define('_SYNDICATION_CORRECTION', false);

#define('_SYNDICATION_URL_UNIQUE', true);
define('_SYNDICATION_DEREFERENCER_URL', true); # dereferencer feedburner
define('_PERIODE_SYNDICATION', 10); // 10 min
define('_PERIODE_SYNDICATION_SUSPENDUE', 60); // 1h


# transformer les tags en recherche dans le stream
function stream_tags($tags, $rels='tag,directory,external') {
	$mots = array();

	// les mots en balise <a rel="tag,directory"> (donc pas les fichiers joints)
	foreach(extraire_balises($tags,'a') as $mot) {
		$tags = str_replace($mot, '', $tags);
		$rel = extraire_attribut($mot, 'rel');
		if (strstr(",$rels,", ",$rel,"))
			$mots[] = supprimer_tags($mot);
	}

	// les mots restants
	foreach (preg_split('/\s*,\s+|\s+/S', $tags) as $mot)
		$mots[] = supprimer_tags($mot);

	$tags = array();
	foreach(array_unique(array_filter($mots)) as $mot) {
		$url = generer_url_public('stream', 'recherche='.urlencode($mot));
		$tags[] = '<a href="'.$url.'" rel="tag nofollow">'.$mot."</a>";
	}

	return join(', ',$tags);
}
