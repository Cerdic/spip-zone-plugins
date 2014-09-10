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

    "document_xml_explication" => "Nom du fichier xml exporté depuis Wordpress à placer dans le repertoire tmp de votre site SPIP" ,
    "document_xml_label" => "fichier xml" ,

    "id_rubrique_label" => "N° rubrique",
    "id_rubrique_explication" => "Le Numéro de la rubrique qui recevra les articles créés ",

    "auteurs_label" => "Auteurs ? ",
    "auteurs_explication" => "Importer les auteurs ",

    "rubriques_label" => "Rubriques ?",
    "rubriques_explication" => "Importer les rubriques ",

    "articles_label" => "Articles ? ",
    "articles_explication" => "Importer les articles",

    "_label" => "",
    "_explication" => "",


    "menage_label" => "Ménage",
    "menage_explication" => "Avant d'importer faire un ménage par le vide : (supprimer les rubriques, documents, articles et auteurs)",


    'migration' => "Migration depuis Wordpress",
	'migration2' => "Migration de Wordpress vers SPIP",
	'mode_d_emploi' => "Mode d'emploi",
	
);