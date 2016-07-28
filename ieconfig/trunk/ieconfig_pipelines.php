<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function ieconfig_ieconfig($table) {
	return $table;
}

// On déclare ici la config du core
function ieconfig_ieconfig_metas($table) {
	// Articles
	$table['articles']['titre'] = _T('icone_articles');
	$table['articles']['icone'] = 'article-16.png';
	$table['articles']['metas_brutes'] = 'articles_surtitre,articles_soustitre,articles_descriptif,articles_chapeau,articles_texte,articles_ps,articles_redac,post_dates,articles_urlref,articles_redirection';
	// Rubriques
	$table['rubriques']['titre'] = _T('icone_rubriques');
	$table['rubriques']['icone'] = 'rubrique-16.png';
	$table['rubriques']['metas_brutes'] = 'rubriques_descriptif,rubriques_texte';
	// Logos
	$table['logos']['titre'] = _T('info_logos');
	$table['logos']['icone'] = 'image-16.png';
	$table['logos']['metas_brutes'] = 'activer_logos,activer_logos_survol';
	// Flux
	$table['flux']['titre'] = _T('ical_titre_rss');
	$table['flux']['icone'] = 'rss-16.png';
	$table['flux']['metas_brutes'] = 'syndication_integrale';
	// Langue
	$table['langue']['titre'] = _T('info_langue_principale');
	$table['langue']['icone'] = 'langue-16.png';
	$table['langue']['metas_brutes'] = 'langue_site';
	// Multilinguisme
	$table['multilinguisme']['titre'] = _T('info_multilinguisme');
	$table['multilinguisme']['icone'] = 'traduction-16.png';
	$table['multilinguisme']['metas_brutes'] = 'multi_secteurs,multi_objets,gerer_trad_objets,langues_multilingue';
	// Rédacteurs
	$table['redacteurs']['titre'] = _T('info_inscription_automatique');
	$table['redacteurs']['icone'] = 'auteur-1comite-16.png';
	$table['redacteurs']['metas_brutes'] = 'accepter_inscriptions';
	// Visiteurs
	$table['visiteurs']['titre'] = _T('info_visiteurs');
	$table['visiteurs']['icone'] = 'auteur-6forum-16.png';
	$table['visiteurs']['metas_brutes'] = 'accepter_visiteurs';
	// Annonces
	$table['annonces']['titre'] = _T('info_envoi_email_automatique');
	$table['annonces']['icone'] = 'annonce-16.png';
	$table['annonces']['metas_brutes'] = 'suivi_edito,adresse_suivi,adresse_suivi_inscription,quoi_de_neuf,adresse_neuf,jours_neuf,email_envoi';
	// Réducteur
	$table['reducteur']['titre'] = _T('info_generation_miniatures_images');
	$table['reducteur']['icone'] = 'image-16.png';
	$table['reducteur']['metas_brutes'] = 'creer_preview,taille_preview'; // on se limite volontairement aux vignettes, le process dépendant de chaque install
	// Avertisseur
	$table['avertisseur']['titre'] = _T('info_travail_colaboratif');
	$table['avertisseur']['icone'] = 'article-16.png';
	$table['avertisseur']['metas_brutes'] = 'articles_modif';
	// Prévisualiseur
	$table['previsualiseur']['titre'] = _T('previsualisation');
	$table['previsualiseur']['icone'] = 'preview-16.png';
	$table['previsualiseur']['metas_brutes'] = 'preview';
	// Moderniseur
	$table['moderniseur']['titre'] = _T('info_compatibilite_html');
	$table['moderniseur']['icone'] = 'compat-16.png';
	$table['moderniseur']['metas_brutes'] = 'version_html_max';

	return $table;
}
