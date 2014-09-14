<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(
	// C
	'configurer_explication' => 'Consultez le <a href="http://users.tpg.com.au/j_birch/plugins/superfish/" target="_blank">site du plugin jQuery SuperFish</a> pour une démonstration de ses possibilités.',
	'configurer_titre' => 'Configurer jQuery Superfish',

	// E
	'erreur_generique' => 'Il y a des erreurs dans les champs ci-dessous, veuillez vérifier vos saisies',
	'erreur_min_max' => 'La valeur max doit être supérieure à la valeur min',
	'explication_animation'=>'Un objet equivalent au premier paramètre de la méthode jQuery .animate() permettant d\'animer les sous-menus. Par exemple <strong>opacity:\'show\',height:\'show\'</strong>, équivaut à une animation fade-in et slide-down',
	'explication_classe'=>'Classe de l\'élément ul sur lequel portera le menu',
	'explication_delai'=>'Le délai en millisecondes entre la sortie du pointeur du sous-menu et la fermeture de celui-ci',
	'explication_menu'=>'Cliquez sur oui pour activer et paramétrer un menu @type@ sur une arborescence ul/li',
	'explication_supersubs' => 'Le plugin optionnel Supersubs (actuellement en version bêta) permet d\'avoir des largeurs variables pour les sous-menu. La largeur fixe donnée dans la feuille de style CSS est annulée et tous les éléments d\'un sous-menu sont modifiés de façon à ce que leur largeur commune soit celle de l\'élément le plus long. Utiliser ceci par ex. pour faire en sorte que tous les items d\'un sous-menu tiennent en une ligne (ayant donc la même hauteur).',
	'explication_supersubs_extrawidth' => 'Marge supplémentaire appliquée aux calculs permettant de gérer au mieux les retours à la ligne',
	'explication_supersubs_maxwidth' => 'Largeur maximale d\'un item (en em)',
	'explication_supersubs_minwidth' => 'Largeur minimale d\'un item (en em)',

	// L
	'label_animation' => 'Animation',
	'label_classe' => 'Classe',
	'label_delai' => 'Délai',
	'label_menu' => 'Gérer un menu @type@',
	'label_supersubs' => 'Activer le plugin Supersubs',
	'label_supersubs_extrawidth' => 'Largeur extra',
	'label_supersubs_maxwidth' => 'Largeur maxi',
	'label_supersubs_minwidth' => 'Largeur mini',
	'legend_menu' => 'Menu @type@' ,
	'legend_supersubs' => 'Plugin Supersubs',

	// T
	'texte_tester' => 'Si la configuration est enregistrée, vous pouvez <a href="@lien@">tester ce type de menu</a>' ,
	'titre_menu' => 'jQuery Superfish',
);
?>