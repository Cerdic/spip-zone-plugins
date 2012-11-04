<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 (v1) Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010--2011 (v2) Emmanuel Saint-James & Jeannot Lapin
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

include_spip('inc/association_comptabilite');

// Export du Compte de Resultat au format XML : balisage DocBooK
// http://fr.wikipedia.org/wiki/DocBook
function action_export_soldescomptes_dbk() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	if (!preg_match(',^(\d+)-(\w+)-(\w+)(-(\w+))?$,', $arg, $r)) {
		spip_log("action_export_soldescomptes incompris: " . $arg);
	} else {
		include_spip('inc/association_comptabilite');
		$dbk = new ExportComptes_TXT(_Request('var'));
		$balises = array();
		foreach (array('charges', 'produits', 'contributions_volontaires') as $key) {
			$balises[$key.'1'] = '<simplesect><title>'. ucfirst(_T("asso:$key")) .'</title>';
			$balises[$key.'0'] = '</simplesect>';
		}
		$balises['compteresultat1'] = '<?xml version="1.0" encoding="'.$GLOBALS['meta']['charset'].'"?>'."\n".'<!DOCTYPE article PUBLIC "-//OASIS//DTD DocBook XML V4.5//EN" "http://www.oasis-open.org/docbook/xml/4.5/docbookx.dtd">'."\n<article xmlns='http://docbook.org/ns/docbook'>";
		$balises['compteresultat0'] = '</article>';
		$balises['entete1'] = '<info>';
		$balises['entete0'] = '</info>';
		$balises['titre1'] = '<title>';
		$balises['titre0'] = '</title>';
		$balises['exercice1'] = '<subtitle>';
		$balises['exercice0'] = '</subtitle>';
		$balises['nom1'] = '<author>';
		$balises['nom0'] = '</author>';
		$balises['code1'] = '<row><entry>';
		$balises['intitule1'] = ' <entrytbl cols="2"><tbody><row><entry>';
		$balises['libelle1'] = $balises['montant1'] = '<entry>';
		$balises['code0'] = $balises['libelle0'] = $balises['intitule0'] = '</entry>';
		$balises['montant0'] = '</entry></row></tbody></entrytbl>';
		$balises['categorie1'] = '</row>';
		$balises['categorie0'] = '</row>';
		$balises['chapitre1'] = '<informaltable frame="all"><tgroup cols="3">';
		$balises['chapitre0'] = '</tgroup></informaltable>';
		$dbk->exportLignesMultiples(array($GLOBALS['association_metas']['classe_charges']=>'-1', $GLOBALS['association_metas']['classe_produits']=>'+1', $GLOBALS['association_metas']['classe_contributions_volontaires']=>0), $balises, array('<'=>'&lt;','>'=>'&gt;'), '', '');
		$dbk->leFichier('dbk');
	}
}

?>