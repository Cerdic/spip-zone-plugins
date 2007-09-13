<?php

/* Balise #TOTAL_CLICS
   Auteur chryjs (c) 2007
   Basé sur http://www.spip-contrib.net/Compter-les-clics-sur-les-liens
   Plugin pour spip 1.9
   Licence GNU/GPL
*/

//
// Génération du lien allant vers le compteur de liens
//
function compteur_clic_site($id_syndic) {
	// Config
	$activer = 1; // Mettre à 0 pour désactiver le compteur (et renvoyer directement vers l'URL)
	if ($activer) {
		return "./spip.php?page=clic&amp;id_syndic=".$id_syndic;
	}
	else {
		$r = spip_query_db("SELECT url_site FROM spip_syndic WHERE id_syndic='$id_syndic' LIMIT 1");
		$o = spip_fetch_array($r);
		return $o['url_site'];
	}
}

function compteur_clic_site_article($id_syndic_article) {
	// Config
	$activer = 1; // Mettre à 0 pour désactiver le compteur (et renvoyer directement vers l'URL)
	if ($activer) {
		return "./spip.php?page=clic&amp;id_syndic_article=".$id_syndic_article;
	}
	else {
		$r = spip_query_db("SELECT url FROM spip_syndic_articles WHERE id_syndic_article='$id_syndic_article' LIMIT 1");
		$o = spip_fetch_array($r);
		return $o['url'];
	}
}

?>
