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
	'background_image_erreur_ext' => 'Type de fichier incorrect !<div>Seules les images au format png, gif, jpg, jpeg son admises</div>',
	'background_image_erreur_nom' => 'Nom de fichier incorrect !<div>Seules les lettres, chiffres, tirets haut et bas sont admis dans les noms de fichier</div>',
	'background_image_explication' => '<strong>Url de l\'image.</strong> Cliquez sur "Voir les images diponibles" sous ce formulaire et choisissez votre image d\'arr&egrave;re-plan. Vous pourrez t&eacute;l&eacute;verser une image depuis votre PC',
	'background_position' => 'Position de l\'image d\'arri&#232;re plan',
	'background_position_erreur' => 'Valeur non admise !<div>Valeurs admises :<ul><li>(top ou bottom) (right ou left)</li><li>XX(% ou px ou em) YY(% ou px ou em)</li></ul></div>',
	'background_position_explication' => '<strong>Position de l\'image.</strong> Vous pouvez saisir les valeurs litt&eacute;rales, ou en pixels ou en pourcentage. <i>Exemple : </i> "10px 50%", "top left", "50% 50%" ',
	'background_repetition' => 'Rep&#233;tition de l\'image d\'arri&#232;re plan',
	'background_size' => 'Dimensions de l\'image d\'arri&#232;re plan',
	'background_size_explication' => '<strong>Taille de l\'image.</strong> Vous pouvez ici  spécifier la taille de l\'image dans l\'arrière plan. Voir les valeurs possibles sur <a href="http://www.alsacreations.com/tuto/lire/1390-arriere-plans-css3-background.html" title="Alsacréations">Alsacréations </a>',
	'barre_navigation' => 'Barre de navigation',
	'bouton_ajouter_image' => 'Téléverser ou supprimer une image',
	'bouton_supprimer' => 'Réinitialiser',

	//C
	'cfg_exemple' => 'Exemple',
	'cfg_exemple_explication' => 'Explication de cet exemple',
	'cfg_titre_parametrages' => 'Paramétrages',
	'choisir' => 'Choisir',
	'choisir_couleurs_base' => 'Choisir les couleurs de base',
	'choisir_police' => 'Choisir une police de caractères',
	'configurer_sdc' => 'Configurer le thème',
	'configuration_sdc' => 'Configuration du thème',
	'configuration_absente' => 'Aucune configuration n\'est enregistrée',
	'couleur' => 'Couleur @bloc@',
	'couleurs' => 'Couleurs @bloc@',
	'couleur_arriere_plan' => 'Couleur de l\'arri&egrave;re-plan',
	'couleur_base_1' => 'Couleur de base N°1',
	'couleur_base_2' => 'Couleur de base N°2',
	'couleur_base_3' => 'Couleur de base N°3',
	
	//E
	'entete' => 'Ent&#234;te',
	'explication_famille_de_police' => 'Les familles de police sont issues de <a href="https://www.google.com/fonts/" title="https://www.google.com/fonts/">Google Web Fonts</a>. Si vous s&eacute;l&eacute;ctionnez "Personnelle" dans la liste d&eacute;roulante, vous pourez alors choisir une autre police que celles propos&eacute;es',
	'explication_inverser_navbar' => 'Par défaut, l\'arrière-plan de la barre de barre de navigation utilise la couleur de base N°2, vous pouvez choisir d\'inverser en choisissant la couleur de base N°1',
	'explication_police_personnelle' => 'Choisissez une police sur le site <a href="https://www.google.com/fonts/" title="https://www.google.com/fonts/">Google Web Fonts</a>. Copiez le nom de la police choisie et collez le dans le champ ci-dessous',
    'exporter_configuration' => 'Exporter la configuration',

	//F
	'famille_de_police' => 'Famille de police de caract&#234;re',
		
	//H
	'height' => 'Hauteur',

	//I
	'image_arriere_plan' => 'Image de l\'arri&egrave;re-plan',
	'image_disponible' => 'image disponible',
	'images_disponibles' => 'images disponibles',
	'info_rechercher'=>'Que cherchez-vous ?',
    'importer_configuration' => 'Importer une configuration',

	//L
	'label_inverser_navbar' => 'Inverser la couleur de l\'arrière-plan de la barre de navigation',
	'label_couleur_liens' => 'Couleur des liens',
	'label_couleur_liens_hover' => 'Inverser la couleur des liens au survol',
	'label_file' => 'Téléverser une image',
	'largeur_logo' => 'Largeur du logo',
	'largeur_page' => 'Largeur de la page',
	'largeur_background' => 'Largeur de l\'arrière plan',
	'largeur_background_explication' => 'Vous pouvez appliquer l\'arrière plan sur la largeur de l\'entête ou sur toute la largeur de l\'écran',
	'layers' => 'Base',
	'logo_site' => 'Logo du site',
	'' => '',

	//M
	'masquer_background_couleur_degrade' => 'Masquer le d&eacute;grad&eacute; de couleur de l\'arri&egrave;re-plan',
	'masquer_images' => 'Masquer les images',
	'masquer_logo' => 'Masquer le logo du site',
	'masquer_configuration' => 'Masquer la configuration',
	'modifier' => 'Modifier',
	'msg_image_supprimee' => 'Image supprimée',

	//N
	'navbar_inverse_erreur' => 'La configuration de la couleur d\'arrière plan de la barre de navigation n\'a pas été enregistrée.',
	'navbar_inverse_ok' => 'La configuration de la couleur d\'arrière plan de la barre de navigation a été enregistrée.',
	'no_shadow' => 'Masquer l\'ombre du titre',
	'nom_page-mentions' => 'Mentions légales',
	
	//P
	'page' => 'Page',
	'page_defaut' => 'Page par défaut',
	'page-mentions_explications' => 'Le plugin Eva Mentions doit être activé',
	'parametres_image_arriere_plan' => 'Param&eacute;tres de l\'image d\'arri&egrave;re-plan',
	'parametres_suplementaires' => 'Paramètres suppl&eacute;mentaires',
	'params_background' => 'Paramètres de l\'arrière-plan @bloc@',
	'params_couleurs_enregistres' => 'La configuration des couleurs a été enregistrée.',
	'params_couleurs_supprimes' => 'La configuration des couleurs a été réinitialisée.',
	'params_typography_enregistres' => 'La police de caractères @bloc@ à été enregistrée.',
	'params_typography_supprimes' => 'La configuration de la police de caratères @bloc@ a été réinitialisée.',
	'police' => 'Police de caractères @bloc@',
	'police_personnelle' => 'Police personnelle',
	'position_horizontale_explication' => 'Position par rapport au bord @dg@ de l\'ent&#234;te',
	'position_horizontale' => 'Position horizontale',
	'position_x' => 'Position horizontale',
	'position_left' => 'Position horizontale',
	'position_left_explication' => 'Position par rapport au bord gauche de l\'ent&#234;te',
	'position_right_explication' => 'Position par rapport au bord droit de l\'ent&#234;te',
	'position_top' => 'Position verticale',
	'position_top_explication' => 'Position par rapport au bord sup&eacute;rieur de l\'ent&#234;te',
	'position_verticale' => 'Position verticale',
	'position_logo' => 'Position du logo du site',
	'position_logo_acad' => 'Position du logo académique',

	// R
	'repetition_horizontale_verticale' => 'R&#233;p&#233;tition horizontale et verticale',
	'repetition_horizontale' => 'R&#233;p&#233;tition horizontale',
	'repetition_non' => 'Pas de r&#233;p&#233;tition',
	'repetition_verticale' => 'R&#233;p&#233;tition verticale',

	// S
	'sdc_titre' => 'Spipr-Dane Config',

	// T
	'taille_coins_arrondis' => 'Taille des coins arrondis',
	'taille_coins_arrondis_explication' => 'Saisissez une ou des valeurs num&eacute;riques en pixels. Exemple : 15px.<br/> Vous pouvez aussi saisir une valeur pour chaque coin . Exemple : 15px 0 15px 0. <a href="https://www.w3schools.com/cssref/css3_pr_border-radius.asp" title="Voir la documentation">Voir la doc</a>',
	'taille_et_position' => 'Taille et position',
	'taille_marge' => 'Taille de la marge',
	'taille_marge_explication' => 'Saisissez la valeur num&eacute en pixels. Exemple : 20px',
	'taille_ombre' => 'Taille de l\'ombre',
	'taille_ombre_explication' => 'Les valeurs de l\ombre sont dans l\'ordre : d&eacute;calage horizontal (valeur num&eacute;rique), d&eacute;calage vertical (valeur num&eacute;rique), flou (en px, pt, em ou  %), couleur. <a href="https://www.w3schools.com/cssref/css3_pr_box-shadow.asp" title="Voir la documentation">Voir la doc</a> ',
	'taille_de_police' => 'Taille de la police',
	'taille_de_police_explication' => 'Valeur en em. Voir la documentation sur <a href="https://www.alsacreations.com/article/lire/563-gerer-la-taille-du-texte-avec-les-em.html">Alsacréations</a>',
	'televerser_une_image' => 'T&eacute;l&eacute;verser une image',
	'texte_titre_shadow' => 'Ombre du titre',
	'titre_site' => 'Titre du site',
	'titre_page_configurer_sdc' => 'Configuration du thème public',

	// V
	'voir_configuration' => 'Afficher la configuration',
	'voir_les_images_disponibles' => 'Voir les images disponibles',
	
	// W
	'width' => 'Largeur',

);
