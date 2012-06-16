<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.org/tradlang_module/getid3?lang_cible=sk
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// B
	'bouton_appliquer_cover_defaut' => 'Appliquer la pochette par défaut à tous les documents sonores sans vignette', # NEW

	// E
	'erreur_formats_ecriture_impossible' => 'Zápis značiek do týchto formátov sa nedá vykonať:',
	'erreur_logiciels_indisponibles' => 'Momentálne nemôžete zapisovať značky do všetkých dostupných formátov.  Niektoré programy nie sú totiž k dispozícii.',
	'erreur_necessite' => 'vyžaduje si @soft@',
	'explication_cover_defaut' => 'À la mise en ligne de fichiers sonores, une vignette par défaut (URL à mettre ci-dessous) est associée au fichier son. Si l\'option de réécriture des tags à la modification du logoest activée, la pochette des tags id3 sera également mise à jour.', # NEW

	// F
	'formulaire_modifier_id3' => 'Upraviť metadata súboru:',

	// I
	'info_album' => 'Album:',
	'info_artist' => 'Artiste :', # NEW
	'info_audiosamplerate' => 'Sample rate :', # NEW
	'info_bitrate' => 'Bitová rýchlosť:',
	'info_bitrate_mode' => 'Režim:',
	'info_bits' => 'Rozlíšenie (v bitoch):',
	'info_channel_mode' => 'Režim (kanál):',
	'info_channels' => 'Počet kanálov:',
	'info_codec' => 'Kodek:',
	'info_comment' => 'Komentár:',
	'info_comments' => 'Komentáre:',
	'info_commercial_information' => 'Informations commerciales :', # NEW
	'info_copyright' => 'Autorské práva:',
	'info_copyright_message' => 'Message de copyright :', # NEW
	'info_duree' => 'Dĺžka:',
	'info_duree_secondes' => 'Dĺžka (v sekundách):',
	'info_encoded_by' => 'Encodé par :', # NEW
	'info_encoding_time' => 'Date d\'encodage :', # NEW
	'info_erreurs' => 'Chyby',
	'info_extension' => 'Extension :', # NEW
	'info_format' => 'Formát:',
	'info_gauche_numero_document' => 'Súbor číslo',
	'info_genre' => 'Žáner:',
	'info_lossless' => 'Žiadna strata kompresie',
	'info_mime' => 'Typ mime:',
	'info_nom_fichier' => 'Názov súboru:',
	'info_original_filename' => 'Pôvodný názov',
	'info_original_release_time' => 'Dátum vytvorenia originálu:',
	'info_sample_rate' => 'Sample rate :', # NEW
	'info_source' => 'Zdroj:',
	'info_title' => 'Názov:',
	'info_totaltracks' => 'Celkový počet stôp:',
	'info_track' => 'Stopa:',
	'info_track_number' => 'Číslo stopy:',
	'info_url_artist' => 'Url de l\'artiste :', # NEW
	'info_url_file' => 'Url du fichier :', # NEW
	'info_url_payment' => 'Url de paiement :', # NEW
	'info_url_publisher' => 'Url du site de publication :', # NEW
	'info_url_source' => 'Url de la source :', # NEW
	'info_url_station' => 'Url de station (?) :', # NEW
	'info_utilisation_aucune' => 'Aucune utilisation de ce document', # NEW
	'info_utilisation_plusieurs' => '@nb@ utilisations', # NEW
	'info_utilisation_unique' => 'Une utilisation', # NEW
	'info_year' => 'Rok',
	'install_ajout_champs_documents' => 'GetID3 : Ajout des champs sur spip_documents', # NEW
	'install_mise_a_jour_base' => 'Mise à jour de la base de getid3 en @version@', # NEW

	// L
	'label_album' => 'Album',
	'label_artist' => 'Artiste', # NEW
	'label_comment' => 'Komentár',
	'label_cover' => 'Pochette', # NEW
	'label_cover_defaut' => 'Utiliser une pochette par défaut', # NEW
	'label_genre' => 'Žáner',
	'label_reecriture_tags' => 'Réécrire les tags des fichiers à la modification', # NEW
	'label_reecriture_tags_descriptif' => 'de la description du document', # NEW
	'label_reecriture_tags_logo' => 'du logo du document', # NEW
	'label_reecriture_tags_titre' => 'du titre du document', # NEW
	'label_title' => 'Názov',
	'label_verifier_logiciels' => 'Znova skontrolovať softvér',
	'label_year' => 'Rok',
	'legende_ecriture_tags' => 'Zápis značiek',
	'lien_modifier_id3' => 'Upraviť audio značky',
	'lien_recuperer_infos' => 'Zistiť informácie o súbore',

	// M
	'message_cover_defaut_modifiee' => 'Súbor bol upravený',
	'message_cover_defaut_modifiees' => 'bolo upravených @nb@ súborov',
	'message_erreur_document_distant_ecriture' => 'Ce document est «distant» et ne peut donc pas être modifié.', # NEW
	'message_extension_invalide_ecriture' => 'Le format de ce fichier n\'est pas pris en charge.', # NEW
	'message_fichier_maj' => 'Súbor bol aktualizovaný.',
	'message_infos_document_distant' => 'Ce document est distant. Aucune information ne peut en être récupérée.', # NEW
	'message_texte_binaire_manquant' => 'Un logiciel nécessaire n\'est pas disponible sur votre serveur :', # NEW
	'message_texte_binaires_informer' => 'Prosím, kontaktujte svojho administrátora.',
	'message_texte_binaires_manquant' => 'Plusieurs logiciels nécessaires ne sont pas disponibles sur votre serveur :', # NEW
	'message_titre_binaire_manquant' => 'Chýba softvér',
	'message_titre_binaires_manquant' => 'Ďalší softvér, ktorý chýba',
	'message_validation_appliquer_cover' => 'Cette action est définitive. Il n\'est pas possible de revenir en arrière par la suite.', # NEW
	'message_valider_cover_defaut' => 'Validez le formulaire pour associer la pochette par défaut', # NEW

	// S
	'son_bitrate_cbr' => 'Bitrate constant', # NEW
	'son_bitrate_vbr' => 'Bitrate variable', # NEW

	// T
	'titre_getid3' => 'GetID3',
	'titre_infos_techniques' => 'Technické údaje',

	// V
	'verifier_formulaire' => 'Prosím, skontrolujte, či ste vyplnili formulár.'
);

?>
