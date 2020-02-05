<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans https://git.spip.net/spip-contrib-extensions/wp_import.git
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'articles_explication' => 'Les articles sont en général : les Posts (articles de blog, des fois utilisé comment actus), les Pages, et enfin les Topics c.a.d. les sujets de forum. Pour ce dernier cas, si le script trouve des Replys, ils seront automatiquement rattachés à l’article correspondant',
	'articles_label' => 'Importer les articles ? ',
	'auteurs_explication' => 'Importer les auteurs ',
	'auteurs_label' => 'Importer les auteurs ?',

	// B
	'bouton_importer' => 'Importer',
	'bouton_vider' => 'Vider',

	// D
	'document_xml_explication' => 'Nom du fichier xml exporté depuis Wordpress à placer dans le repertoire tmp de votre site SPIP',
	'document_xml_label' => 'Fichier xml',
	'documents_explication' => 'Si tu as accès à WORDPRESS/wp-content/uploads copie le répertoire dans SPIP/tmp/wordpress/uploads',
	'documents_label' => 'Importer les documents',

	// E
	'etape1_texte' => 'À partir de l’interface d’administration de votre site Wordpress, il vous faut exporter l’ensemble des données au format XML. Pour cela, connectez vous avec votre compte administrateur à l’interface d’administration et dirigez vous vers la page Exporter grace au menu principal (outils > Exporter). Sur cette page, choissisez les options et cliquez sur le bouton "Télécharger le fichier d’export". Ensuite, enregistrer le fichier d’export sur votre ordinateur.',
	'etape1_titre' => 'Export du contenu du site',
	'etape2_texte' => 'Copier l’ensemble le répertoire "wp-content/uploads" de votre wordpress dans le répertoire tmp/wordpress de votre site.Cette étape est facultative si votre serveur possède un accès à internet et que le nombre de vos documents à importer est réduit.',
	'etape2_titre' => 'Copie des images dans tmp/wordpress',
	'etape3_texte' => ' ',
	'etape3_titre' => 'Lancer l’importation',

	// F
	'forum_explication' => 'Importer les forums',
	'forum_label' => 'Importer les forums',

	// I
	'id_parent_explication' => 'Le Numéro de la rubrique qui recevra les articles créés ',
	'id_parent_label' => 'N° rubrique',

	// M
	'menage_explication' => 'Remettre à zéro la base de données : vider les tables auteurs, rubriques, articles, documents, forum et mot-clés. ATTENTION Ne pas cocher en prod ;) ',
	'menage_label' => 'Ménage',
	'migration' => 'Migration depuis Wordpress',
	'migration2' => 'Migration de Wordpress vers SPIP',
	'mode_d_emploi' => 'Mode d’emploi',
	'motcle_explication' => 'Ce sont les tags WP.',
	'motcle_label' => 'Importer les mots-clés',

	// R
	'rubriques_explication' => 'Attention, la logique [category WP = rubrique SPIP] n’est pas toujours vraie. C’est fonction de comment sont utilisées les category dans WP. A utiliser donc en connaissance de cause.',
	'rubriques_label' => 'Importer les rubriques ?'
);
