<?php
/**
 * Déclaration de la barre d'outil de sommaire de Bouquinerie
 *
 * @plugin Porte Plume pour SPIP
 * @license GPL
 * @package SPIP\PortePlume\BarreOutils
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Définition de la barre 'sommaire' pour markitup
 *
 * @return Barre_outils La barre d'outil
 */
function barre_outils_sommaire() {
	// on modifie simplement la barre d'edition
	$edition = charger_fonction('edition', 'barre_outils');
	$barre = $edition();
	$barre->nameSpace = 'sommaire';
	$barre->cacherTout();
	$barre->afficher(array(
		'header1',
		'bold',
		'italic',
		'liste_ul',
		'liste_ol',
		'desindenter',
		'indenter',
	));

	return $barre;
}
