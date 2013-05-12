<?php

// Ceci est un fichier langue de SPIP -- This is a SPIP language file

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// B
	// Pour la recherche par texte
	'bouton_generer_egal1' => 'GéNéRer',
	'bouton_generer_egal2' => 'G&Eacute;n&eacute;rer',
	'bouton_generer_egal3' => 'génÉrer',
	'bouton_generer_commence1' => 'Générer moi ça rapide !',
	'bouton_generer_commence2' => 'G&Eacute;n&eacute;rer moi ça rapide !',
	'bouton_generer_commence3' => 'génÉrer moi ça rapide !',
	'bouton_generer_contient1' => 'Alors Générer',
	'bouton_generer_contient2' => 'alors G&Eacute;n&eacute;rer moi ça rapide !',
	'bouton_generer_contient3' => 'ben va falloir génÉrer ça rapide !',

	// Pour la vérification "definition"
	'defini_xml_1' => 'item défini dans xml #1',
	'defini_paquet_1' => 'item défini dans paquet.xml #1',
	'defini_plugin_1' => 'item défini dans plugin.xml #1',
	'defini_yaml_1' => 'item défini dans yaml #1',

	'test' => 'TEST : Cet item de langue sert pour la recherche de raccourci et est égal à test.',
	'test_item_1_variable' => 'TEST : Cet item de langue est bien défini dans le fichier de langue, mais est utilisé sous forme "complexe" dans les fichiers du répertoire scanné.',
	'test_item_2_variable' => 'TEST : Cet item de langue est bien défini dans le fichier de langue, mais est utilisé sous forme "complexe" dans les fichiers du répertoire scanné.',
	'test_item_non_utilise_1' => 'TEST : Cet item de langue est bien défini dans le fichier de langue (), mais n\'est pas utilisé dans les fichiers du répertoire scanné ().',
	'test_item_non_utilise_2' => 'TEST : Cet item de langue est bien défini dans le fichier de langue (), mais n\'est pas utilisé dans les fichiers du répertoire scanné ().',

	'titre_charte' => 'titre de la charte', # correspond à une utilisation dans paquet.xml ou plugin.xml
	'titre_icone' => 'icone du titre', # correspond à un item obsolète (le bon étant titre_icones)

	'utilisation_html' => 'Item utilisé correctement dans du HTML', # utilisation <:module:item:> simple
	'utilisation_val_1' => 'Item utilisé dans une balise VAL simple', # utilisation #VAL{item}|_T

	// Z
	'z_test' => 'TEST : Cet item de langue sert pour la recherche de raccourci et contient test.',
);
?>