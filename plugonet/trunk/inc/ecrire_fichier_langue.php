<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

if (!defined('_PLUGONET_SIGNATURE'))
	define('_PLUGONET_SIGNATURE', "// Ceci est un fichier langue de SPIP -- This is a SPIP language file");

if (!defined('_PLUGONET_TAG_NOUVEAU'))
	define('_PLUGONET_TAG_NOUVEAU', '# NEW');
if (!defined('_PLUGONET_TAG_MODIFIE'))
	define('_PLUGONET_TAG_MODIFIE', '# MODIF');


/**
 * Ecriture d'un fichier de langue à partir de la liste de ces couples (item, traduction)
 * et de son bandeau d'information
 * Cette fonction est aussi utilisée par PlugOnet
 *
 * @param $dir
 * @param $langue
 * @param $module
 * @param $items
 * @param string $producteur
 * @return bool|string
 */
function inc_ecrire_fichier_langue($dir, $langue, $module, $items, $producteur='') {
	$nom_fichier = $dir . $module . "_" . $langue   . '.php';
	$contenu = produire_fichier_langue($langue, $module, $items, $producteur);

	return ecrire_fichier($nom_fichier, $contenu) ? $nom_fichier : false;
}


/**
 * Produit un fichier de langue a partir d'un tableau (index => trad)
 * Si la traduction n'est pas une chaine mais un tableau, on inclut un commentaire
 *
 * @param $langue
 * @param $module
 * @param $items
 * @param string $producteur
 * @return string
 */
function produire_fichier_langue($langue, $module, $items, $producteur='') {
	ksort($items);
	$initiale = '';
	$contenu = array();
	foreach($items as $_item => $_traduction) {
		if ($initiale != strtoupper($_item[0])) {
			$initiale = strtoupper($_item[0]);
			$contenu[]= "\n\t// $initiale";
		}
		if (!is_string($_traduction)) {
			$t = str_replace("'", '\\\'', $_traduction[1]);
			if ($_traduction[2] == 'inutile')
				$contenu[]= "/*\t" . $_traduction[0] ."\n\t'$_item' => '$t',*/";
			else {
				$com = !$_traduction[0] ? '' : ("/*\t". $_traduction[0] ." */\n");
				$contenu[]= "$com\t'$_item' => '$t',";
			}
		}
		else {
			$t = str_replace("'", '\\\'', $_traduction);
			$t = str_replace('\\\\n', "' . \"\\n\" .'", $t);
			$t = str_replace(_PLUGONET_TAG_NOUVEAU, '', $t, $c);
			$contenu[]= "\t'$_item' => '$t'," . ($c>0 ? ' ' . _PLUGONET_TAG_NOUVEAU : '');
		}
	}
		$producteur = "\n" . _PLUGONET_SIGNATURE . "\n\n" . $producteur;

	return '<'. "?php\n" .
$producteur . '
// Module: ' . $module . '
// Langue: ' . $langue . '
// Date: ' . date('d-m-Y H:i:s') . '
// Items: ' . count($items) . '

if (!defined(\'_ECRIRE_INC_VERSION\')) return;

$GLOBALS[$GLOBALS[\'idx_lang\']] = array(
' .
	  join("\n", $contenu)  .
	  "\n);\n?".'>';
}
?>
