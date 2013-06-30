<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/noizetier?lang_cible=de
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'apercu' => 'Vorschau',

	// B
	'bloc_sans_noisette' => 'Ce bloc ne contient pas de noisette.', # NEW

	// C
	'choisir_noisette' => 'Wählen sie die Nuss, welche sie hinzufügen wollen:',
	'compositions_non_installe' => 'Das Plugin <b>Kompositionen:</b> ist nicht installiert. Das Plugin Nussbaum funktioniert auch ohne, jedoch können sie ihre Kompositionen direkt im Nussbaum-Plugin deklarieren, wenn es aktiv ist.', # MODIF

	// D
	'description_bloc_contenu' => 'Haupt-Inhalt jeder Seite.',
	'description_bloc_extra' => 'Kontextbezogene Zusatzinformationen für jede Seite.',
	'description_bloc_navigation' => 'Eigene Navigationsinformationen für jede Seite.',
	'description_bloctexte' => 'Der Titel ist optional. Sie können SPIP-Tags im Text verwenden.',

	// E
	'editer_composition' => 'Diese Komposition ändern',
	'editer_composition_heritages' => 'Vererbungen einstellen',
	'editer_configurer_page' => 'Code-Nüsse für diese Seite konfigurieren',
	'editer_exporter_configuration' => 'Konfiguration exportieren',
	'editer_importer_configuration' => 'Konfiguration importieren',
	'editer_noizetier_explication' => 'Konfigurieren sie hier die Code-Nüsse für die Seiten ihrer Website.', # MODIF
	'editer_noizetier_titre' => 'Nussbaum', # MODIF
	'editer_nouvelle_page' => 'Créer une nouvelle page / composition', # NEW
	'erreur_aucune_noisette_selectionnee' => 'Vous devez sélectionner une noisette !', # NEW
	'erreur_doit_choisir_noisette' => 'Sie müssen eine Code-Nuss auswählen.',
	'erreur_mise_a_jour' => 'Beim Aktualisieren der Datenbank ist ein Fehler aufgetreten.',
	'explication_glisser_deposer' => 'Vous pouvez ajouter une noisette ou les réordonner par simple glisser/déposer.', # NEW
	'explication_heritages_composition' => 'Hier können sie die Kompositionen festlegen, die den Objekten des Zweigs vererbt weden.',
	'explication_noizetier_css' => 'Sie können der Code-Nus zusätzliche CSS-Lassen zuordnen.',
	'explication_raccourcis_typo' => 'Sie können SPIP-Tags verwenden.',

	// F
	'formulaire_ajouter_noisette' => 'Code-Nuss hinzufügen',
	'formulaire_composition' => 'Bezeichner der Komposition',
	'formulaire_composition_explication' => 'Geben sie ein einziges Schlagwort an (in Kleinschreibung, ohne Leerzeichen, ohne Minuszeichen (-) und ohne Akzente oder Umlaute) um diese Komposition zu identifizieren.<br />z.B.: <i>meinekomposition</i>.', # MODIF
	'formulaire_composition_mise_a_jour' => 'Komposition aktualisiert',
	'formulaire_configurer_bloc' => 'Block konfigurieren:',
	'formulaire_configurer_page' => 'Seite konfigurieren:',
	'formulaire_deplacer_bas' => 'Nach unten',
	'formulaire_deplacer_haut' => 'Nach oben',
	'formulaire_description' => 'Beschreibung',
	'formulaire_description_explication' => 'Sie können die gewohnten SPIP-Tags verwenden, insbesondere  &lt;multi&gt;.',
	'formulaire_erreur_format_identifiant' => 'Der Bezeichner darf nur kleingeschriebene Zeichen ohne Akzente und Umlaute, Ziffern und den Unterstrich (_) enthalten.',
	'formulaire_icon' => 'Icon',
	'formulaire_icon_explication' => 'Sie können den relativen Pfad zu einem Icon angeben (z.B.: <i>images/objekt-liste-inhalte.png</i>). Eine Liste der Icons in Standardverzeichnissen finden sie<a href="../spip.php?page=icones_preview">auf dieser Seite</a>.', # MODIF
	'formulaire_identifiant_deja_pris' => 'Dieser Bezeichner wird bereits verwndet!',
	'formulaire_import_compos' => 'Kompositionen des Nussbaums importieren',
	'formulaire_import_fusion' => 'Mit der aktuellen Konfiguration zusammenführen',
	'formulaire_import_remplacer' => 'Aktuelle Konfiguration ersetzen',
	'formulaire_liste_compos_config' => 'Diese Konfigurationsdatei definiert die Kompositionen des folgenden Nussbaums:',
	'formulaire_liste_pages_config' => 'Diese Konfigurationsdatei definiert Code-Nüsse auf folgenden Seiten:',
	'formulaire_modifier_composition' => 'Diese Komposition ändern:',
	'formulaire_modifier_composition_heritages' => 'Vererbungen dieser Komposition ändern:', # MODIF
	'formulaire_modifier_noisette' => 'Diese Code-Nuss ändern',
	'formulaire_modifier_page' => 'Modifier cette page', # NEW
	'formulaire_noisette_sans_parametre' => 'Diese Code-Nuss bietet keine Parameter.',
	'formulaire_nom' => 'Titre',
	'formulaire_nom_explication' => 'Sie können des SPIP-Tag &lt;multi&gt; verwenden.',
	'formulaire_nouvelle_composition' => 'Neue Komposition',
	'formulaire_obligatoire' => 'Pflichtfeld',
	'formulaire_supprimer_noisette' => 'Diese Code-Nuss löschen',
	'formulaire_supprimer_noisettes_page' => 'Code-Nüsse dieser Seite löschen',
	'formulaire_supprimer_page' => 'Supprimer cette page', # NEW
	'formulaire_type' => 'Kompositionstyp', # MODIF
	'formulaire_type_explication' => 'Geben sie an, für welches Objekt bzw. welchen Seitentyp diese Komposition gestaltet ist.', # MODIF
	'formulaire_type_import' => 'Importtyp',
	'formulaire_type_import_explication' => 'Sie können diese Konfigurationsdatei mit ihrer aktuellen Konfiguration zusammenführen (die Code-Nüsse werden den bereits vorhandenen hinzugefügt) oder sie ersetzen.',

	// I
	'icone_introuvable' => 'Icône introuvable !', # NEW
	'ieconfig_ne_pas_importer' => 'Nicht importieren',
	'ieconfig_noizetier_export_explication' => 'Exporiert die Konfiguration der Code-Nüsse und die Kompsitionen des Nussbaums.',
	'ieconfig_noizetier_export_option' => 'In den Export einbeziehen?',
	'ieconfig_non_installe' => 'Das Plugin <b>Import/Export von Konfigurationen</b> ist nicht installiert.  Das Plugin Nussbaum funktioniert auch ohne, jedoch können sie die Konfiguration der Code-Nüsse im Nussbaum im- und exportieren, wenn es aktiviert ist.', # MODIF
	'ieconfig_probleme_import_config' => 'Beim Import der Konfiguration des Nussbaums ist ein Problem aufgetreten.',
	'info_composition' => 'KOMPOSITION :',
	'info_page' => 'SEITE:',
	'installation_tables' => 'Tabellen des Plugins Nussbaum installiert.<br />',
	'item_titre_perso' => 'individueller Titel',

	// L
	'label_afficher_titre_noisette' => 'Einen Titel für die Code-Nuss anzeigen?',
	'label_niveau_titre' => 'Ebene des Titels:',
	'label_noizetier_css' => 'CSS-Klassen:',
	'label_texte' => 'Text:',
	'label_texte_introductif' => 'Texte introductif (optionnel) :', # NEW
	'label_titre' => 'Titel:',
	'label_titre_noisette' => 'Titel der Code-Nuss:',
	'label_titre_noisette_perso' => 'Individueller Titel:',
	'liste_icones' => 'Icon-Liste',
	'liste_pages' => 'Liste des pages', # NEW

	// M
	'masquer' => 'Masquer', # NEW
	'mode_noisettes' => 'Éditer les noisettes', # NEW
	'modif_en_cours' => 'Modifications en cours', # NEW
	'modifier_dans_prive' => 'Modifier dans l\'espace privé', # NEW

	// N
	'ne_pas_definir_d_heritage' => 'Keine Vererbung festlegen',
	'noisette_numero' => 'noisette numéro :', # NEW
	'noisettes_composition' => 'Code-Nüsse der Komposition <i>@composition@</i> :',
	'noisettes_disponibles' => 'Noisettes disponibles', # NEW
	'noisettes_page' => 'Code-Nüsse der Seite <i>@type@</i> :',
	'noisettes_toutes_pages' => 'Code-Nüsse für alle Seiten:',
	'noizetier' => 'noiZetier', # NEW
	'nom_bloc_contenu' => 'Inhalt',
	'nom_bloc_extra' => 'Extra',
	'nom_bloc_navigation' => 'Navigation',
	'nom_bloctexte' => 'Block mit Freitext',
	'non' => 'Non',
	'notice_enregistrer_rang' => 'Klicken sie auf Speichern um die Anordnung der Code-Nüsse zu sichern.',

	// O
	'operation_annulee' => 'Opération annulée.', # NEW
	'oui' => 'Ja',

	// P
	'page' => 'Seite',
	'page_autonome' => 'Page autonome', # NEW
	'probleme_droits' => 'Vous n\'avez pas les droits nécessaires pour effectuer cette modification.', # NEW

	// Q
	'quitter_mode_noisettes' => 'Quitter l\'édition des noisettes', # NEW

	// R
	'retour' => 'Retour', # NEW

	// S
	'suggestions' => 'Suggestions', # NEW

	// W
	'warning_noisette_plus_disponible' => 'ACHTUNG: Diese Code-Nuss ist nicht merh verfügbar.',
	'warning_noisette_plus_disponible_details' => 'Das Skelett (<i>@squelette@</i>) dieser Code-Nuss ist nicht mehr verfügbar. Es handelt sich möglicherweise um eine Code-Nuss, die ein Plugin benötigt, dass sie deaktiviert oder deinstalliert haben.'
);

?>
