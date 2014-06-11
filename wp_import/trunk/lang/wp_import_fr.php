<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	'bouton_importer' => "Importer",
	
	'etape1_titre' => "Export du contenu du site",
	'etape1_texte' => "&Agrave; partir de l'interface d'administration de votre site Wordpress, il vous faut exporter l'ensemble des données au format XML. Pour cela, connectez vous avec votre compte administrateur à l'interface d'administration et dirigez vous vers la page Exporter grace au menu principal (outils > Exporter). Sur cette page, choissisez les options et cliquez sur le bouton \"Télécharger le fichier d'export\". Ensuite, enregistrer le fichier d'export sur votre ordinateur.",
	'etape2_titre' => "Copie des images dans tmp/wordpress",
	'etape2_texte' => "Copier l'ensemble le répertoire \"wp-content/uploads\" de votre wordpress dans le répertoire tmp/wordpress de votre site.Cette étape est facultative si votre serveur possède un accès à internet et que le nombre de vos documents à importer est réduit.",
	'etape3_titre' => "Lancer l'importation",
	'etape3_texte' => " ",
	
	'migration' => "Migration depuis Wordpress",
	'migration2' => "Migration de Wordpress vers SPIP",
	'mode_d_emploi' => "Mode d'emploi",
	
);