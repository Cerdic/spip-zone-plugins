<?php
/**
  Plugin SPIPr-Dane-Config
  Fichier sdc_fr.php
  (c) 2019 Dominique Lepaisant
  Distribue sous licence GPL3
*/

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(


	//A
	'aucune_image_disponible' => 'Aucune image disponible',
	'ajouter_image' => 'Ajouter une image',
	'appliquer_sur_les_pages' => 'Appliquer sur les pages',
	
	//B
	'background_afficher' => 'Afficher une image d\'arri&#232;re-plan',
	'background_couleur' => 'Couleur  du fond',
	'background_fixer' => 'Fixer l\'image d\'arri&#232;re plan',
	'background_image' => 'Url de l\'image  d\'arri&#232;re plan',
	'background_image_erreur' => 'Vous devez saisir le nom du ficier image',
	'background_image_erreur_ext' => 'Type de fichier incorrect !<div>Seules les images au format png, gif, jpg, jpeg son admises</div>',
	'background_image_erreur_nom' => 'Nom de fichier incorrect !<div>Seules les lettres, chiffres, tirets haut et bas sont admis dans les noms de fichier</div>',
	'background_image_explication' => '<strong>Url de l\'image.</strong> Cliquez sur "Voir les images diponibles" sous ce formulaire et choisissez votre image d\'arr&#232;re-plan. Vous pourrez t&#233;l&#233;verser une image depuis votre PC',
	'background_position' => 'Position de l\'image d\'arri&#232;re plan',
	'background_position_erreur' => 'Valeur non admise !<div>Valeurs admises :<ul><li>(top ou bottom) (right ou left)</li><li>XX(% ou px ou em) YY(% ou px ou em)</li></ul></div>',
	'background_position_explication' => '<strong>Position de l\'image.</strong> Vous pouvez saisir les valeurs litt&#233;rales, ou en pixels ou en pourcentage. <i>Exemple : </i> "10px 50%", "top left", "50% 50%" ',
	'background_repetition' => 'R&#233;p&#233;tition de l\'image d\'arri&#232;re plan',
	'background_size' => 'Dimensions de l\'image d\'arri&#232;re plan',
	'background_size_explication' => '<strong>Taille de l\'image.</strong> Vous pouvez ici  sp&#233;cifier la taille de l\'image dans l\'arri&#232;re plan. Voir les valeurs possibles sur <a href="http://www.alsacreations.com/tuto/lire/1390-arriere-plans-css3-background.html" title="Alsacr&#233;ations">Alsacr&#233;ations </a>',
	'barre_navigation' => 'Barre de navigation',
	'bouton_ajouter_image' => 'T&#233;l&#233;verser ou supprimer une image',
	'bouton_supprimer' => 'R&#233;initialiser',

	//C
	'cfg_exemple' => 'Exemple',
	'cfg_exemple_explication' => 'Explication de cet exemple',
	'cfg_titre_parametrages' => 'Param&#233;trages',
	'choisir' => 'Choisir',
	'choisir_couleurs_base' => 'Choisir les couleurs de base',
	'choisir_police' => 'Choisir une police de caract&#232;res',
	'configurer_sdc' => 'Configurer le th&#232;me',
	'configuration_sdc' => 'Configuration du th&#232;me',
	'configuration_absente' => 'Aucune configuration n\'est enregistr&#233;e',
	'couleur' => 'Couleur @bloc@',
	'couleurs' => 'Couleurs @bloc@',
	'couleur_arriere_plan' => 'Couleur de l\'arri&#232;re-plan',
	'couleur_base_1' => 'Couleur de base N°1',
	'couleur_base_2' => 'Couleur de base N°2',
	'couleur_base_3' => 'Couleur de base N°3',
	
	//E
	'entete' => 'Ent&#234;te',
	'erreur_creer_dir_css' => '&#201;chec lors de la cr&#233;ation du r&#233;pertoire @dir@/squelette/css',
	'erreur_creer_dir_images' => '&#201;chec lors de la cr&#233;ation du r&#233;pertoire @dir@/squelette/images',
 	'erreur_ecriture_champ' => '&#201;chec de l\'enregistrement du champ @champ@.',
 	'erreur_enregistrement_couleur' => '&#201;chec de l\'enregistrement de la couleur @couleur@.',
 	'erreur_enregistrement_couleur_barnav' => '&#201;chec de l\'enregistrement des couleurs de la barre de navigation.',
 	'erreur_format' => 'Format interdit !',
	'erreur_nombre_entier' => 'Vous devez saisir un nombre entier',
	'explication_famille_de_police' => 'Les familles de police sont issues de <a href="https://www.google.com/fonts/" title="https://www.google.com/fonts/">Google Web Fonts</a>. Si vous s&#233;l&#233;ctionnez "Personnelle" dans la liste d&#233;roulante, vous pourez alors choisir une autre police que celles propos&#233;es',
	'explication_inverser_navbar' => 'Par d&#233;faut, l\'arri&#232;re-plan de la barre de barre de navigation utilise la couleur de base N°2, vous pouvez choisir d\'inverser en choisissant la couleur de base N°1',
	'explication_navbar_responsive' => 'Sur petits &#233;crans, le bouton "Menu" est affich&#233; sous l\'ent&#234;te. Vous pouvez choisir d\'afficher ce bouton en haut de l\'&#233;cran.',
	'explication_police_personnelle' => 'Choisissez une police sur le site <a href="https://www.google.com/fonts/" title="https://www.google.com/fonts/">Google Web Fonts</a>. Copiez le nom de la police choisie et collez le dans le champ ci-dessous',
    'exporter_configuration' => 'Exporter la configuration',

	//F
	'famille_de_police' => 'Famille de police de caract&#233;re',
		
	//H
	'height' => 'Hauteur',

	//I
	'ieconfig_non_installe' => '<strong>Plugin Importeur/Exporteur de configurations :</strong> ce plugin n\'est pas install&#233; sur votre site. Il n\'est pas n&#233;cessaire au fonctionnement de SPIPr-Dane-Config. Cependant, s\'il est activ&#233;, vous pourrez exporter et importer des configurations de th&#232;me et ainsi sauvegarder votre configuration avant toute modification.',
	'image_arriere_plan' => 'Image de l\'arri&#232;re-plan',
	'image_disponible' => 'image disponible',
	'images_disponibles' => 'images disponibles',
	'info_rechercher'=>'Que cherchez-vous ?',
    'importer_configuration' => 'Importer une configuration',

	//L
	'label_inverser_navbar' => 'Inverser la couleur de l\'arri&#232;re-plan de la barre de navigation',
	'label_couleur_liens' => 'Couleur des liens',
	'label_couleur_liens_hover' => 'Inverser la couleur des liens au survol',
	'label_file' => 'T&#233;l&#233;verser une image',
	'label_navbar_responsive' => 'Afficher le bouton "Menu" en haut de l\'&#233;cran',
	'largeur_logo' => 'Largeur du logo',
	'largeur_page' => 'Largeur de la page',
	'largeur_background' => 'Largeur de l\'arri&#232;re plan',
	'largeur_background_explication' => 'Vous pouvez appliquer l\'arri&#232;re plan sur la largeur de l\'ent&#234;te ou sur toute la largeur de l\'&#233;cran',
	'layers' => 'Base',
	'logo_site' => 'Logo du site',
	'' => '',

	//M
	'masquer_background_couleur_degrade' => 'Masquer le d&#233;grad&#233; de couleur de l\'arri&#232;re-plan',
	'masquer_images' => 'Masquer les images',
	'masquer_logo' => 'Masquer le logo du site',
	'masquer_configuration' => 'Masquer la configuration',
	'modifier' => 'Modifier',
	'msg_image_supprimee' => 'Image supprim&#233;e',
    // Metas
    'metas_a_droite' => '&#192; droite',
    'metas_background-attachment' => 'D&#233;filement de l\'image d\'arri&#232;re plan',
    'metas_background-color' => 'Couleur d\'arri&#232;re plan',
    'metas_background-image' => 'Image d\'arri&#232;re plan',
    'metas_background-position' => 'Position de l\'image d\'arri&#232;re plan',
    'metas_background-repeat' => 'R&#233;p&#233;tition de l\'image d\'arri&#232;re plan',
    'metas_background-size' => 'Taille de l\'image d\'arri&#232;re plan',
    'metas_black' => 'Noir',
    'metas_body' => 'Page',
    'metas_color' => 'Couleur',
    'metas_color1' => 'Couleur N°1',
    'metas_color2' => 'Couleur N°2',
    'metas_color3' => 'Couleur N°3',
    'metas_couleur_liens' => 'Couleur des liens',
    'metas_defaut' => 'Base',
    'metas_font-family' => 'Police',
    'metas_font-size' => 'Taille de police',
    'metas_header' => 'Ent&#234;te',
    'metas_inverser_navbar' => 'Inverser la couleur',
    'metas_largeur_background' => 'Largeur de l\'arri&#232;re plan',
    'metas_largeur_logo' => 'Largeur du logo',
    'metas_layer' => 'Mod&#232;le',
    'metas_navbar' => 'Barre de navigation',
    'metas_no-repeat' => 'Pas de r&#233;p&#233;tition',
    'metas_on' => 'Oui',
    'metas_position_logo_acad' => 'Position du logo ac-caen',
    'metas_repeat' => 'R&#233;p&#233;tition horizontale et verticale',
    'metas_repeat-x' => 'R&#233;p&#233;tition horizontale',
    'metas_repeat-y' => 'R&#233;p&#233;tition verticale',
    'metas_screen' => '&#201;cran',
    'metas_scroll' => 'D&#233;filement',
    'metas_title' => 'Titre',
    'metas_white' => 'Blanc',


	//N
	'navbar_inverse_erreur' => 'La configuration de la couleur d\'arri&#232;re plan de la barre de navigation n\'a pas &#233;t&#233; enregistr&#233;e.',
	'navbar_inverse_ok' => 'La configuration de la couleur d\'arri&#232;re plan de la barre de navigation a &#233;t&#233; enregistr&#233;e.',
	'no_shadow' => 'Masquer l\'ombre du titre',
	'nom_page-mentions' => 'Mentions l&#233;gales',
	
	//P
	'page' => 'Page',
	'page_defaut' => 'Page par d&#233;faut',
	'page-mentions_explications' => 'Le plugin Eva Mentions doit &#234;tre activ&#233;',
	'parametres_image_arriere_plan' => 'Param&#233;tres de l\'image d\'arri&#232;re-plan',
	'parametres_suplementaires' => 'Param&#232;tres suppl&#233;mentaires',
	'params_background' => 'Param&#232;tres de l\'arri&#232;re-plan @bloc@',
	'params_background_enregistres' => 'La configuration de l\'arri&#232;re-plan @bloc@ a &#233;t&#233; enregistr&#233;e',
	'params_background_non_enregistres' => 'La configuration de l\'arri&#232;re-plan @bloc@ n\'a pas &#233;t&#233; enregistr&#233;e',
	'params_background_supprimes' => 'La configuration de l\'arri&#232;re-plan @bloc@ a &#233;t&#233; r&#233;initialis&#233;e',
	'params_barnav_enregistres' => 'La configuration des couleurs de la barre de navigation a &#233;t&#233; enregistr&#233;e.',
	'params_barnav_non_enregistres' => 'La configuration des couleurs de la barre de navigation n\'a pas &#233;t&#233; enregistr&#233;e.',
	'params_barnav_supprimes' => 'La configuration des couleurs de la barre de navigation a &#233;t&#233; r&#233;initialis&#233;e.',
	'params_couleurs_enregistres' => 'La configuration des couleurs a &#233;t&#233; enregistr&#233;e.',
	'params_couleurs_supprimes' => 'La configuration des couleurs a &#233;t&#233; r&#233;initialis&#233;e.',
	'params_logos_enregistres' => 'La configuration des logos a &#233;t&#233; enregistr&#233;e.',
	'params_logos_supprimes' => 'La configuration des logos a &#233;t&#233; r&#233;initialis&#233;e.',
	'params_typography_enregistres' => 'La police de caract&#232;res @bloc@ a &#233;t&#233; enregistr&#233;e.',
	'params_typography_supprimes' => 'La configuration de la police de carat&#232;res @bloc@ a &#233;t&#233; r&#233;initialis&#233;e.',
	'police' => 'Police de caract&#232;res @bloc@',
	'police_personnelle' => 'Police personnelle',
	'position_horizontale_explication' => 'Position par rapport au bord @dg@ de l\'ent&#234;te',
	'position_horizontale' => 'Position horizontale',
	'position_x' => 'Position horizontale',
	'position_left' => 'Position horizontale',
	'position_left_explication' => 'Position par rapport au bord gauche de l\'ent&#234;te',
	'position_right_explication' => 'Position par rapport au bord droit de l\'ent&#234;te',
	'position_top' => 'Position verticale',
	'position_top_explication' => 'Position par rapport au bord sup&#233;rieur de l\'ent&#234;te',
	'position_verticale' => 'Position verticale',
	'position_logo' => 'Position du logo du site',
	'position_logo_acad' => 'Position du logo acad&#233;mique',

	// R
	'repetition_horizontale_verticale' => 'R&#233;p&#233;tition horizontale et verticale',
	'repetition_horizontale' => 'R&#233;p&#233;tition horizontale',
	'repetition_non' => 'Pas de r&#233;p&#233;tition',
	'repetition_verticale' => 'R&#233;p&#233;tition verticale',

	// S
	'sdc_titre' => 'SPIPr-Dane Config',

	// T
	'taille_coins_arrondis' => 'Taille des coins arrondis',
	'taille_coins_arrondis_explication' => 'Saisissez une ou des valeurs num&#233;riques en pixels. Exemple : 15px.<br/> Vous pouvez aussi saisir une valeur pour chaque coin . Exemple : 15px 0 15px 0. <a href="https://www.w3schools.com/cssref/css3_pr_border-radius.asp" title="Voir la documentation">Voir la doc</a>',
	'taille_et_position' => 'Taille et position',
	'taille_marge' => 'Taille de la marge',
	'taille_marge_explication' => 'Saisissez la valeur num&eacute en pixels. Exemple : 20px',
	'taille_ombre' => 'Taille de l\'ombre',
	'taille_ombre_explication' => 'Les valeurs de l\ombre sont dans l\'ordre : d&#233;calage horizontal (valeur num&#233;rique), d&#233;calage vertical (valeur num&#233;rique), flou (en px, pt, em ou  %), couleur. <a href="https://www.w3schools.com/cssref/css3_pr_box-shadow.asp" title="Voir la documentation">Voir la doc</a> ',
	'taille_de_police' => 'Taille de la police',
	'taille_de_police_explication' => 'Valeur en em. Voir la documentation sur <a href="https://www.alsacreations.com/article/lire/563-gerer-la-taille-du-texte-avec-les-em.html">Alsacr&#233;ations</a>',
	'televerser_une_image' => 'T&#233;l&#233;verser une image',
	'texte_titre_shadow' => 'Ombre du titre',
	'titre_site' => 'Titre du site',
	'titre_page_configurer_sdc' => 'Configuration du th&#232;me public',

	// V
	'voir_configuration' => 'Afficher la configuration',
	'voir_les_images_disponibles' => 'Voir les images disponibles',
	
	// W
	'width' => 'Largeur',

);
