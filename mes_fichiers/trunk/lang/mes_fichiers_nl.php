<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/mes_fichiers?lang_cible=nl
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// B
	'bouton_configurer' => 'Mijn bestanden',
	'bouton_mes_fichiers' => 'Maak een backup van mijn bestanden',
	'bouton_sauver' => 'Backup',
	'bouton_tout_cocher' => 'Alles selecteren',
	'bouton_tout_decocher' => 'Selectie verwijderen',
	'bouton_voir' => 'Bekijken',
	'bulle_bouton_voir' => 'Bekijk de inhoud van het archief',

	// C
	'colonne_nom' => 'Naam',

	// E
	'erreur_aucun_fichier_sauver' => 'Er is geen bestand beschikbaar voor een backup',
	'erreur_repertoire_trop_grand' => 'Deze map overschrijdt de limiet van @taille_max@ MB. Er kan geen backup van worden gemaakt.',
	'explication_cfg_duree_sauvegarde' => 'Vermeld het aantal dagen dat een backup bewaard moet worden',
	'explication_cfg_frequence' => 'Vermeld de frequentie in dagen van een backup',
	'explication_cfg_notif_mail' => 'Vermeld emailadressen, gescheiden door een komma ",". Deze adressen worden aan die van de webmaster toegevoegd.',
	'explication_cfg_prefixe' => 'Vermeld het voorvoegsel voor ieder archief',
	'explication_cfg_taille_max_rep' => 'Vermeld de maximum grootte in MB van de mappen waarvan een backup moet worden gemaakt',

	// I
	'info_liste_a_sauver' => 'Backuplijst van bestanden en mappen:',
	'info_sauver_1' => 'Deze option maakt een archiefbestand waarin de personalisaties van de site worden bewaard, zoals de laatste dump van de database, de benoemde skeletmappen, de map met afbeeldingen...',
	'info_sauver_2' => 'Het archiefbestand is aangemaakt in <em>tmp/mes_fichiers/</em> en heet <em>@prefixe@_aaaammjj_hhmmss.zip</em>.',
	'info_sauver_3' => 'De automatische backup is geactiveerd (frequentie in dagen: @frequence@).',

	// L
	'label_cfg_duree_sauvegarde' => 'Bewaren van archieven',
	'label_cfg_frequence' => 'Frequentie van het archiveren',
	'label_cfg_nettoyage_journalier' => 'Activeer een dagelijkse opschoning van de archieven',
	'label_cfg_notif_active' => 'Activeer de berichtgeving',
	'label_cfg_notif_mail' => 'Te informeren emailadressen',
	'label_cfg_prefixe' => 'Voorvoegsel',
	'label_cfg_sauvegarde_reguliere' => 'Activeer een regelmatige backup',
	'label_cfg_taille_max_rep' => 'Maximale grootte van mappen',
	'legende_cfg_generale' => 'Algemene backup-parameters',
	'legende_cfg_notification' => 'Berichtgeving',
	'legende_cfg_sauvegarde_reguliere' => 'Automatische verwerking',

	// M
	'message_cleaner_sujet' => 'Opschonen van backups',
	'message_notif_cleaner_intro' => 'Het automatisch verwijderen van verouderde backups (ouder dan @duree@ dagen) is geslaagd. De volgende archieven werden verwijderd: ',
	'message_notif_sauver_intro' => 'Een nieuwe backup van je bestanden si beschikbaar. Ze werd uitgevoerd door @auteur@.',
	'message_rien_a_sauver' => 'Geen bestand of map voor een backup.',
	'message_rien_a_telecharger' => 'Geen enkele backup kan worden geladen.',
	'message_sauvegarde_nok' => 'Dout tijdens backup. Het archiefbestand kon niet worden gemaakt.',
	'message_sauvegarde_ok' => 'Het archiefbestand is met succes gemaakt.',
	'message_sauver_sujet' => 'Backup',
	'message_telechargement_nok' => 'Fout tijdens het downloaden.',
	'message_zip_auteur_indetermine' => 'Onbepaald',
	'message_zip_propriete_nok' => 'Er is geen eigenaar van dit archief.',
	'message_zip_sans_contenu' => 'Er is geen informatie beschikbaar over de inhoud van dit archief.',

	// R
	'resume_zip_auteur' => 'Gemaakt door',
	'resume_zip_compteur' => 'Bestanden / mappen in archief',
	'resume_zip_contenu' => 'Samenvatting inhoud',
	'resume_zip_statut' => 'Status',

	// T
	'titre_boite_sauver' => 'Een archief maken',
	'titre_boite_telecharger' => 'Lijst van de voor download beschikbare archieven',
	'titre_page_configurer' => 'Configuratie van plugin Mijn bestanden',
	'titre_page_mes_fichiers' => 'Backup van mijn gepersonaliseerde bestanden'
);

?>
