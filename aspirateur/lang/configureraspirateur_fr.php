<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(
	
	//E
	
	'explications_aspirateur'=>'Configurez puis lancez l\'aspiration. Vous pouvez aussi entrer en \'Page référente\' une page HTML listant des liens.',
	'explication_contenu_inclure_tag_attribut'=>'Exemple : <code>#content</code>',	
	'explication_contenu_exclure_tag_attribut'=>'Exemple avec séparateur | : <code>.sociable|.navigation</code>',
	'explication_motif_chemin_pages_exclure'=>'Exemple : <code>recommander.asp|faq.asp</code>',
	
	'explication_motif_chemin_documents_exclure'=>'Exemple : <code>design/</code>',
	'explication_motif_chemin_documents'=>'Exemple : <code>upload/</code>',

	'explication_motif_debut_contenu_regex'=>'Exemple avec : <code>'.htmlentities("<!-- debut contenu -->").'</code>',
	'explication_motif_fin_contenu_regex'=>'Exemple avec : <code>'.htmlentities("<!-- fin du contenu -->").'</code>',
	
	
	// L
		
	'label_nom_site_aspirer' => 'Nom du site',
	'label_url_site_aspirer' => 'Adresse du site',
	
	'label_descriptif_site' => 'Descriptif du site',
	'label_suivre_liens_1'=>'Suivre les liens de cette page.',
	'label_page_referente' => 'Page référente',
	
	'legend_motifs_aspirateur'=>'Motifs d\'extraction',
	'legend_type_traitements'=>'Type de traitements',
	
	'legend_motifs_exclusion' =>'Motifs d\'exclusion',
	
	'label_activer_spip_1'=>'Réécrire le chemin des documents pour SPIP',
	
	'label_contenu_inclure_tag_attribut'=>'Aspirer le contenu d\'un tag HTML ayant la class ou l\'id suivante (xpath)',
	'label_contenu_exclure_tag_attribut'=>'Exclure le contenu d\'un tag HTML ayant la class ou l\'id suivante (xpath)',
	'label_motif_debut_contenu_regex'=>'Sinon motif pour le debut du contenu (Regex)',	
	'label_motif_fin_contenu_regex'=>'Sinon motif pour la fin du contenu (Regex)',
	'label_motif_chemin_documents'=>'Motif nécessaire dans le chemin des documents (Regex)',
	'label_motif_chemin_documents_exclure'=>'Motif d\'exclusion dans le chemin des documents (Regex)',
	'label_motif_chemin_pages_exclure'=>'Motif d\'exclusion dans le chemin des pages',
	'label_nettoyer_contenu_1'=>'Nettoyer le HTML',
	'label_forcer_utf8_1'=>'Forcer l\'UTF8',
		
	'label_nombre_de_pages' => 'Nombre de pages',
	
	// T
	'titre_configuration'=>'Configurer l\'aspirateur de site',
	'titre_page_configurer_aspirateur' =>'Configurer l\'aspirateur',
		


);

?>
