<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/wp_import/trunk/lang/
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'articles_explication' => 'Importer les articles',
	'articles_label' => 'Articles ? ',
	'auteurs_explication' => 'Importer les auteurs ',
	'auteurs_label' => 'Auteurs ? ',

	// B
	'bouton_importer' => 'Importer',

	// D
	'document_xml_explication' => 'Nom du fichier xml exporté depuis Wordpress à placer dans le repertoire tmp de votre site SPIP',
	'document_xml_label' => 'fichier xml',
	'documents_explication' => 'Importer les documents. Si tu as accès à WORDPRESS/wp-content/uploads copie le répertoire dans SPIP/tmp/wordpress/uploads',
	'documents_label' => 'Documents',

	// E
	'etape1_texte' => 'À partir de l’interface d’administration de votre site Wordpress, il vous faut exporter l’ensemble des données au format XML. Pour cela, connectez vous avec votre compte administrateur à l’interface d’administration et dirigez vous vers la page Exporter grace au menu principal (outils > Exporter). Sur cette page, choissisez les options et cliquez sur le bouton "Télécharger le fichier d’export". Ensuite, enregistrer le fichier d’export sur votre ordinateur.',
	'etape1_titre' => 'Export du contenu du site',
	'etape2_texte' => 'Copier l’ensemble le répertoire "wp-content/uploads" de votre wordpress dans le répertoire tmp/wordpress de votre site.Cette étape est facultative si votre serveur possède un accès à internet et que le nombre de vos documents à importer est réduit.',
	'etape2_titre' => 'Copie des images dans tmp/wordpress',
	'etape3_texte' => ' ',
	'etape3_titre' => 'Lancer l’importation',

	// I
	'id_parent_explication' => 'Le Numéro de la rubrique qui recevra les articles créés ',
	'id_parent_label' => 'N° rubrique',

	// M
	'menage_explication' => 'Avant d’importer faire un ménage par le vide : (supprimer les rubriques, documents, articles, mot-clés et auteurs). ATTENTION Ne pas cocher en prod ;) ',
	'menage_label' => 'Ménage',
	'migration' => 'Migration depuis Wordpress',
	'migration2' => 'Migration de Wordpress vers SPIP',
	'mode_d_emploi' => 'Mode d’emploi',
	'motcle_explication' => 'Importer les mots-clés',
	'motcle_label' => 'Mots-clés',

	// R
	'rubriques_explication' => 'Importer les rubriques ',
	'rubriques_label' => 'Rubriques ?'
);
