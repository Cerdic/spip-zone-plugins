<?php

#
# Au chargement soit il y a une URL demandŽe (bookmarklet) et on cherche
# alors la news, si elle existe, qui correspond ˆ cette URL
#
# Soit il n'y a rien et c'est le formulaire de crŽation de news
# ˆ partir de l'url demandŽe
#

function formulaires_stream_charger() {
	if ($url = _request('url')
	AND $url = substr($url, 0, 255) # cf. ecrire/genie/syndic.php
	AND $article = sql_fetsel(
		'*',
		'spip_syndic_articles',
		'url='.sql_quote($url))
	) {
		// $article est ok
	}
	else {
		// recuperer un article vide ou prepa nous appartenant
		// et sinon, le creer
		sql_updateq('spip_syndic_articles', array('statut' => 'prepa'),
			'statut='.sql_quote('dispo').' AND titre="" AND url="" AND descriptif=""');

		if ($article = sql_fetsel(
		'id_syndic_article',
		'spip_syndic_articles',
		'statut='.sql_quote('prepa')
		.' AND lesauteurs='.sql_quote(session_get('nom')))
		) {
			// ok !
			$article['date'] = date('Y-m-d H:i:s');
			if ($url)
				$article['url'] = $url;
			if ($titre = _request('titre'))
				$article['titre'] = $titre;
			sql_updateq('spip_syndic_articles', $article, 'id_syndic_article='.sql_quote($article['id_syndic_article']));
		}
		else {
			$titre = _request('titre');

			$id_syndic_article = sql_insertq('spip_syndic_articles',
				$article = array(
				'lesauteurs' => session_get('nom'),
				'url' => $url,
				'titre' => $titre,
				'id_syndic' => 0, # non publie tant que pas valide
				'descriptif' => '',
				'statut' => 'prepa',
				'date' => date('Y-m-d H:i:s'),
				)
			);
			if (!$id_syndic_article)
				return array('erreur' => 'Erreur SQL');
			else
				$article['id_syndic_article'] = $id_syndic_article;
		}
	}

	return $article;
}
