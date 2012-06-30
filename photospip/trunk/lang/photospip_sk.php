<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.org/tradlang_module/photospip?lang_cible=sk
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// B
	'bouton_creer_vignette' => 'Vytvoriť miniatúru z tohto dokumentu',
	'bouton_editer_image' => 'Upraviť obrázok',
	'bouton_editer_vignette' => 'Upraviť miniatúru',
	'bouton_modifier_document' => 'Upraviť informácie o dokumente',
	'bouton_revenir_version' => 'Vrátiť sa k tejto verzii',
	'bouton_supprimer_previsu' => 'Vrátiť sa k verzii bez ukážky',
	'bouton_supprimer_version' => 'Vymazať túto verziu',
	'bouton_supprimer_vignette' => 'Vymazať túto miniatúru',
	'bouton_supprimer_vignette_document' => 'Vymazať miniatúru tohto dokumentu',
	'bouton_tester' => 'Ukážka',
	'bouton_valider_continuer' => 'Potvrdiť a pokračovať',
	'bouton_valider_fermer' => 'Potvrdiť a vrátiť sa na predošlú stránku',

	// D
	'date_doc' => 'Dátum publikovania online: ',
	'date_modif_doc' => 'Dátum poslednej zmeny: ',
	'donnees_exif' => 'Dáta EXIF',

	// E
	'erreur_auth_modifier' => 'Na úpravu tohto dokumentu nemáte dostatočné práva.',
	'erreur_choisir_version' => 'Vyberte si verziu',
	'erreur_doc_numero' => 'Musíte uviesť existujúci dokument.',
	'erreur_form_filtre' => 'Prosím, uveďte filter, ktorý sa má použiť.',
	'erreur_form_filtre_sstest' => 'Le filtre que vous avez essayé ne permet pas d\'être testé. Vous ne pouvez que l\'appliquer.', # NEW
	'erreur_form_filtre_valeur_obligatoire' => 'Musíte si vybrať hodnotu.',
	'erreur_form_type_resultat' => 'Musíte si vybrať typ výsledku',
	'erreur_image_process' => 'Le site n\'utilise pas GD2 pour gérer les images, veuillez l\'utiliser pour leur traitement.', # NEW
	'erreur_nb_versions_atteint' => 'Počet rôznych verzií obrázka dosiahol svoj limit (@nb@). Odteraz môžete úpravy iba skúšať, ale nemôžete ich používať.',
	'erreur_previsu' => 'Ak ste s výsledkom spokojný, môžete ho potvrdiť tlačidlom vo formulári, ak nie, môžete vyskúšať ďalšie filtre.',
	'erreur_selectionner_au_moins_une_valeur' => 'Musíte si vybrať aspoň jednu hodnotu',
	'erreur_valeur_numerique' => 'Tento filter si ako parameter vyžaduje číselnú hodnotu',
	'erreur_valeurs_numeriques' => 'Tento filter si vyžaduje číselné hodnoty',
	'explication_image_flip_horizontal' => 'Appliquer un effet de « miroir » selon un axe horizontal (bas<->haut). Aucun réglage nécessaire.', # NEW
	'explication_image_flip_vertical' => 'Appliquer un effet de « miroir » selon un axe vertical (gauche<->droite). Aucun réglage nécessaire.', # NEW
	'explication_image_flou' => 'Le filtre image_flou rend l\'image... floue. On peut lui passer en paramètre un nombre compris entre 1 et 11, définissant l’intensité du flou (de 1 à 11 pixels de flou).', # NEW
	'explication_image_gamma' => 'Le filtre Gamma change la luminosité d\'une image.<br />Il rend une image plus claire ou plus foncée.<br />Son paramètre est compris entre -254 et 254. Les valeurs supérieures à zéro rendent l\'image plus claire (254 rend toute l\'image entièrement blanche); les valeurs négatives rendent l\'image plus foncée (-254 rend l\'image complètement noire).', # NEW
	'explication_image_nb' => 'Zmeniť obrázok na čiernobiely',
	'explication_image_niveau_de_gris_auto' => 'Correction automatique des niveaux de l\'image.<br />(Ne nécessite pas de paramètres).', # NEW
	'explication_image_passe_partout' => 'Ce filtre réduira la taille de l\'image au minimum la faisant entrer dans un cadre des largeurs et hauteurs fournies.', # NEW
	'explication_image_recadre' => 'Skosí obrázok podľa výberu používateľa.',
	'explication_image_reduire' => 'Ce filtre réduira la taille de l\'image proportionnellement en fonction de la hauteur et de la largeur fournie.', # NEW
	'explication_image_rotation' => 'Fait tourner l’image d’un angle égal au paramètre passé. Les valeurs positives sont dans le sens des aiguilles d’une montre et inversement.<br />Attention : ce filtre modifie les dimensions de l’image.', # NEW
	'explication_image_saturation_desaturation' => 'Ce filtre va saturer ou désaturer les couleurs d’une image.<br />La luminosité de l’image et le contraste sont inchangés<br />Dans le premier cas, la couleur est « affadie » ; l’effet, utilisé subtilement, donnera des tonalités de photographie vieillie...<br />Dans l’autre cas, à l’inverse, le même filtre pourra «doper» les couleurs.', # NEW
	'explication_image_sincity' => 'Ce filtre donne un aspect "Sin City" (Ne nécessite aucun réglage).<br />Il exécute une désaturation contrastée et une accentuation du rouge.', # NEW
	'explication_resultats' => 'Pri potvrdení zmeny obrázkov sú možné tri typy výsledkov.',
	'explication_resultats_defaut' => 'Predvolená hodnota vybraná pri nahraní formulára.',
	'explication_tourner' => 'Použite otočenie o 90, 180 alebo 270 stupňov<br />Ak sa tento filter nedá otestovať, nedá sa použiť.',

	// I
	'id_document' => 'P. č. súboru na stránke: ',
	'image_taille_actuelle' => 'Aktuálna veľkosť obrázka:',
	'info_modifier_image' => 'Upraviť obrázok',
	'info_modifier_vignette' => 'Upraviť miniatúru dokumentu #@id_document@',
	'info_nb_versions' => '@nb@ verzií',
	'info_nb_versions_une' => 'Jedna verzia',

	// L
	'label_angle_rotation' => 'Otočiť o uhol:',
	'label_choisir_filtres' => 'Vyberte si filter, ktorý sa má použiť',
	'label_compression_rendu' => 'Kvalita kompresie (v %, predvolené 85):',
	'label_couleur_sepia' => 'Farba:',
	'label_hauteur_previsu' => 'Maximálna výška ukážky v px (predvolené 450): ',
	'label_image_aplatir' => 'Vyhladiť obrázok',
	'label_image_flip_horizontal' => 'Flip Horizontal de l\'image', # NEW
	'label_image_flip_vertical' => 'Flip Vertical de l\'image', # NEW
	'label_image_flou' => 'Filter Rozmazanie',
	'label_image_gamma' => 'Filter gama',
	'label_image_nb' => 'Čiernobiely filter',
	'label_image_niveau_de_gris_auto' => 'Automatické úrovne',
	'label_image_passe_partout' => 'Zmenšiť obrázok (pasparta)',
	'label_image_recadre' => 'Skosiť obrázok',
	'label_image_reduire' => 'Zmenšiť obrázok',
	'label_image_rotation' => 'Manuálne otočenie obrázka',
	'label_image_saturation_desaturation' => 'Filtre [Dé-]saturation', # NEW
	'label_image_sepia' => 'Filter Sépia',
	'label_image_sincity' => 'Filter Sin City',
	'label_largeur_previsu' => 'Maximálna šírka ukážky v px (predvolené 450): ',
	'label_limiter_version' => 'Počet možných verzií obmedziť na:',
	'label_modif_creer_nouvelle_image' => 'Nový súbor bude vytvorený z pôvodného obrázka',
	'label_modif_creer_version_image' => 'L\'image originale sera enregistrée en tant que version du nouveau document qui la remplacera dans le site', # NEW
	'label_modif_remplacer_image' => 'Pôvodný obrázok bude jednoducho nahradený',
	'label_modif_vignette_creer_version_image' => 'La vignette originale sera enregistrée en tant que version de la nouvelle vignette qui la remplacera dans le site', # NEW
	'label_modif_vignette_remplacer_image' => 'Pôvodná miniatúra bude jednoducho nahradená',
	'label_niveau_flou' => 'Niveau de flou :', # NEW
	'label_niveau_gamma' => 'Úroveň gama:',
	'label_niveau_saturation_desaturation' => 'Úroveň nasýtenia:',
	'label_ratio' => 'Ratio de la sélection :', # NEW
	'label_ratio_libre' => 'Libre', # NEW
	'label_recadre_height' => 'Výška výberu (v px):',
	'label_recadre_width' => 'Šírka výberu (v px):',
	'label_recadre_x1_y1' => 'Position (coin supérieur gauche)', # NEW
	'label_recadre_x2_y2' => 'Position (coin inférieur droit)', # NEW
	'label_reduire_height' => 'Výška (v px):',
	'label_reduire_width' => 'Šírka (v px):',
	'label_resultats' => 'Výber možných výsledkov podľa používateľa',
	'label_resultats_defaut' => 'Vybratá predvolená hodnota',
	'label_tourner' => 'Rotation paramétrée', # NEW
	'label_tourner_180' => 'Un demi tour', # NEW
	'label_tourner_270' => 'Un quart de tour vers la gauche', # NEW
	'label_tourner_90' => 'Un quart de tour vers la droite', # NEW
	'label_type_modification' => 'Aký bude výsledok?',
	'label_type_retour' => 'Čo treba urobiť po použití filtra?',
	'label_type_retour_continuer' => 'Pokračovať v úprave obrázka',
	'label_type_retour_retour' => 'Zatvoriť úpravy',
	'legend_configuration' => 'Nastavenia zásuvného modulu',
	'legend_configuration_publique' => 'Nastavenie verejne prístupnej zóny',
	'legend_configuration_resultats' => 'Nastavenie výsledkov',
	'legend_filtres_a_disposition' => 'Filtre k dispozícii',
	'legende_filtres_de_couleur' => 'Farebné filtre',
	'legende_filtres_format' => 'Upraviť formát',
	'lien_editer_image' => 'Upraviť tento obrázok',
	'lien_editer_vignette' => 'Upraviť miniatúru',

	// M
	'message_image_taille_actuelle' => 'Aktuálna veľkosť obrázka: @largeur@ x @hauteur@px.',
	'message_limite_versions' => 'Počet predchádzajúcich verzií je obmedzený na @limite@.',
	'message_nouvelle_image_creee' => 'Váš nový obrázok bol vytvorený #@id_document@',
	'message_ok_version_retour' => 'Vrátili ste sa na verziu #@version@',
	'message_ok_version_supprimee' => 'Verzia #@version@ bola vymazaná',
	'message_pas_de_versions' => 'Tento dokument nemá verziu.',
	'message_vignette_installe_succes' => 'Miniatúra bola úspešne nahraná',

	// P
	'photospip' => 'PhotoSPIP',

	// T
	'taille_fichier' => 'Veľkosť súboru: ',
	'title_version' => 'Verzia #@version@',
	'titre_informations_images' => 'Informácie o súbore',
	'titre_page_image_edit' => 'Úprava obrázka',
	'type_original' => 'Typ súboru: '
);

?>
