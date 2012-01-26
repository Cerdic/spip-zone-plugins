<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// On déclare ici la config du core
function ieconfig_ieconfig_metas($table){
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
	
	
	
	
	
	
	
	
	
	return $table;
}

?>