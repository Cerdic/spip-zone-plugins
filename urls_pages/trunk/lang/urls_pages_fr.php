<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// C
	'champ_page_label' => 'Page',
	'champ_page_tri' => 'Page',
	'champ_page_explication' => 'Indiquez le nom de fichier du squelette correspondant à la page, sans l\'extension .html et en respectant la casse : unePage.html &rarr; unePage.
	Les objets éditoriaux et leurs déclinaisons sont proscris !',
	'champ_page_explication_fond' => 'Squelette de la page : @fond@',
	'champ_fond_label' => 'Fond',
	'champ_statut' => 'Statut',
	'champ_url_label' => 'URL',
	'champ_url_explication' => 'N\'indiquez que la partie après la racine du site : @racine@',
	'champ_dossier' => 'Dossier',
	'champ_action' => 'Action',

	// E
	'explication_dossier' => 'Dossier contenant les squelettes',
	'explication_generale' => 'Les « pages » sont des squelettes ne correspondant à aucun objet éditorial.

	Gérez ici leurs URLs, elles sont prises en compte par la balise <tt>#URL_PAGE</tt>.

	L\'onglet « fonds des pages » contient une liste des pages sans URL personnalisée, à titre indicatif.
	Si une page n\'y figure pas, vous pouvez tout de même lui en attribuer une en saisissant la page manuellement.',
	'erreur_page_mauvais_format' => 'Le nom de la page est incorrect. Il doit s\'agir du nom de fichier d\'un squelette, sans l\'extension .html',
	'erreur_url_doublon' => 'Cette URL est déjà utilisée',
	'erreur_fond_doublon' => 'Cette page est déjà enregistrée dans la base',
	'erreur_fond_doublon_url' => 'Cette page est déjà enregistrée dans la base avec l\'URL « @url@ »',
	'erreur_fond_prive' => 'Il s\'agit d\'une page de l\'espace privé',
	'erreur_fond_code_http' => 'Cette page correspond à une erreur HTTP',
	'erreur_fond_technique' => 'Il s\'agit d\'une page technique',
	'erreur_fond_pseudo_fichier' => 'Il s\'agit de la page d\'un pseudo-fichier',
	'erreur_fond_absent' => 'Aucun squelette pour cette page !',
	'erreur_fond_absent_page' => 'Aucun squelette trouvé pour la page « @page@ » !',
	'erreur_fond_objet_editorial' => 'Il s\'agit d\'une page d\'un objet éditorial',

	// I
	'info_0_url_page' => 'Aucune URL de page',
	'info_1_url_page' => '1 URL de page',
	'info_nb_urls_pages' => '@nb@ URLs de pages',
	'info_0_fond_page' => 'Aucun fond de page',
	'info_1_fond_page' => '1 fond de page',
	'info_nb_fonds_pages' => '@nb@ fonds de pages',
	'info_fonds_pages' => 'Fonds de pages',
	'info_0_fond' => 'Aucun squelette n\'a été trouvé',
	'icone_ajouter_url_page' => 'Ajouter l\'URL d\'une page',
	'icone_attribuer_url_page' => 'Attribuer une URL',
	'icone_supprimer_url_page' => 'Supprimer l\'URL de cette page',
	'icone_editer_url_page' => 'Éditer l\'URL de cette page',
	'icone_choisir_page' => 'Choisir une page',
	'icone_supprimer' => 'Supprimer',
	'icone_editer' => 'Éditer',
	'icone_choisir' => 'Choisir',
	'icone_retour' => 'Retour',

	// M
	'message_url_update_ok' => 'L\'URL a été mise à jour',
	'message_url_insert_ok' => 'L\'URL a été enregistrée',
	'menu_onglet_pages' => 'URLs des pages',
	'menu_onglet_fonds' => 'Fonds des pages',
	'menu_urls_pages' => 'URLs des pages',
	'menu_urls_objets' => 'URLs des objets éditoriaux',

	// T
	'titre_pages_detectees' => 'Pages détectées',
	'titre_urls_pages' => 'URLs des pages',
	'titre_editer_url_page' => 'Éditer l\'URL d\'une page',
	'titre_creer_url_page' => 'Créer l\'URL d\'une page',
	'titre_nouvelle_url' => 'Nouvelle URL',

);

?>
