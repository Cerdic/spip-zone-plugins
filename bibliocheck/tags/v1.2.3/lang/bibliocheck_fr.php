<?php

// Ceci est un fichier langue de SPIP -- This is a SPIP language file
// Langue: fr

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(
// A
	'ajouter_commentaire' => 'Ajouter un commentaire',
	'ajouter_references' => 'Ajouter des références',
	'ajouter_reference' => 'Ajouter une référence',
	'ajouter_reference2' => 'Ajouter une référence pour @auteur@',
	'aucun_composant_defini' => 'Aucun composant n\'est défini dans la configuration du plugin Tickets.',

// B
	'bibliocheck' => 'Biblio Check',

// C
	'champ_description_correction' => 'Description de la correction',
	'champ_lien' => 'Lien',
	'configurer_bibliocheck' => 'Configurer Biblio Check',
	'connexion_requise' => 'Vous devez vous identifier pour accéder à cette page.',
	'correction_enregistree' => 'Votre demande de correction a bien été enregistrée.',
	'corriger' => 'Indiquer une correction',
	'corriger_reference' => 'Corriger la référence @id@',
	'corriger_reference2' => 'Corriger la référence @id@ pour @auteur@',

// D
	'demande_ajout_enregistree' => 'Votre demande d\'ajout a bien été enregistrée.',
	'droits_insuffisants' => 'Nous n\'avez pas les droits requis pour accéder à cette page.',

// E
	'envoyer_email' => 'Envoyez un courriel à <a href="mailto:@email@">@email@</a>.',
	'erreur_conference' => 'La conférence n\'est pas renseignée.',
	'erreur_editeurs_ouvrage' => 'Les auteurs/éditeurs de l\'ouvrage ne sont pas renseignés.',
	'erreur_maison_edition' => 'La maison d\'édition n\'est pas renseignée.',
	'erreur_nb_pages' => 'Le nombre de pages n\'est pas renseigné.',
	'erreur_nom_ouvrage' => 'Le nom de l\'ouvrage n\'est pas renseigné.',
	'erreur_pages_debut_fin' => 'Les pages de début et de fin ne sont pas renseignées.',
	'erreur_pages_debut_fin2' => 'Les pages de début et de fin ne sont pas renseignés (normal seulement s\'il s\'agit d\'une revue exclusivement en ligne).',
	'erreur_revue' => 'Le nom de la revue n\'est pas renseigné.',
	'erreur_saisie_nouvelle_reference' => 'Vous devez décrire la référence à ajouter et/ou en saisir les détails.',
	'erreur_titre' => 'Le titre n\'est pas renseigné.',
	'erreur_type_rapport' => 'Le type de rapport n\'est pas précisé.',
	'explication_ajouter_reference' => 'Décrivez aussi précisément que possible la référence à ajouter. Le cas échéant, n\'hésitez pas à préciser l\'ISBN de l\'ouvrage ou le DOI de l\'article.',
	'explication_ajouter_reference_docs' => 'Si possible, joignez un export de la référence dans un format bibliographique standard (RIS, BibTeX, Zotero RDF...). Vous pouvez également saisir les détails dans le formulaire ci-après.',
	'explication_ajouter_reference_une_a_une' => 'Si vous avez plusieurs références à ajouter, merci de les signaler une par une (sauf si vous joignez un export EndNote, Zotero ou équivalent).',
	'explication_composant' => 'Faut-il attribuer un composant particulier aux tickets créés par Biblio Check ? La liste des composants possibles est paramétrable sur la <a href="@ull_tickets">page de configuration du plugin Tickets</a>.',
	'explication_description_correction' => 'Décrivez aussi précisément que possible la correction à apporter à cette référence (coquilles, informations manquantes...). Le cas échéant, n\'hésitez pas à préciser l\'ISBN de l\'ouvrage ou le DOI de l\'article. N\'hésitez pas à joindre un fichier (par exemple un export BibTeX, Zotero ou EndNote).',
	'explication_docs_joints' => 'Pour permettre à vos utilisateurs de joindre un document à leurs ajouts/corrections de référence (par exemple, un export BibTeK EndNote ou Zotero), pensez à <a href="@url_tickets@">configurer le plugin tickets</a> de manière adéquate. Nous vous recommandons les extensions de fichier suivantes : <em> txt, ris, bib, rdf, xml, doc, docx, odt, ppt, pptx, odp, pdf, html, jpg, png, gif</em>.<br />Vous pouvez également modifier la <a href="@url_forum@">configuration des forum</a> pour autoriser l\'ajout de documents aux commentaires.',
	'explication_fichier' => 'Formats acceptés : @formats@.',
	'explication_lien' => 'Indiquez un lien vers la publication ou vers sa description (site de la revue par exemple).',

// F
	'fichier_joint' => 'Fichier joint :',

// I
	'import_pb' => 'La référence n\'a pas pu être importée (veuillez vérifier la connexion avec Zotero).', 
	'import_pb_400' => 'Import impossible : un ou plusieurs champs sont incorrects. Veuillez importer cette référence manuellement.',
	'import_pb_409' => 'Import impossible : la librairie est verrouillée.',
	'import_pb_412' => 'Import impossible : cette référence a déjà été importée dans Zotero.',
	'import_ok' => 'La référence a été correctement importée.',
	'importer_dans_zotero' => 'Importer dans Zotero',

// L
	'label_config_autorisations' => 'Qui peut vérifier les références ?',
	'label_config_composant' => 'Composant des tickets',
	'label_config_docs_joints' => 'Documents joints',
	'label_config_email' => 'Courriel de contact (support)',
	'label_explication_personnalisee_ajouter_reference' => 'Explication personnalisée pour le formulaire "Proposer une nouvelle référence"',
	'lien' => 'Lien :',

// P
	'probleme_question' => 'Un problème, Une question ?',
	'proposer_nouvelle_reference' => 'Proposer une nouvelle référence',
	'proposition' => 'Proposition :',

// R
	'reference_en_base' => 'Réference actuellement en base de données',
	
// S
	'syntaxe_spip_autorisee' => 'Vous pouvez utiliser la syntaxe SPIP dans ce champs.',

// T
	'type_biblio' => 'Biblio',
	'type_biblio_long' => 'Références bibliographiques à corriger/ajouter',

// V
	'verifier_biblio' => 'Vérifier la bibliographie',	
);
