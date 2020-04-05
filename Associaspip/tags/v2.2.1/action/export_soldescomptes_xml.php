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

// Export du Compte de Resultat au format XML
// http://fr.wikipedia.org/wiki/Extensible_Markup_Language
// jeu de balisage propre a Associaspip ; pas de DTD ni de Schema
function action_export_soldescomptes_xml() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	if (!preg_match(',^(\d+)-(\w+)-(\w+)(-(\w+))?$,', $arg, $r)) {
		spip_log("action_export_soldescomptes incompris: " . $arg);
	} else {
		include_spip('inc/association_comptabilite');
		$xml = new ExportComptes_TXT();
		$balises = array();
		foreach (array('entete', 'titre', 'nom', 'exercice', 'charges', 'produits', 'contributions_volontaires', 'chapitre', 'code', 'libelle', 'categorie', 'intitule', 'montant') as $key) {
			$balises[$key.'1'] = '<'.ucfirst($key).'>';
			$balises[$key.'0'] = '</'.ucfirst($key).'>';
		}
		$balises['compteresultat1'] = '<?xml version="1.0" encoding="'.$GLOBALS['meta']['charset'].'"?>'."\n<CompteDeResultat>";
		$balises['compteresultat0'] = '</CompteDeResultat>';
		$xml->exportLignesMultiples(array($GLOBALS['association_metas']['classe_charges']=>'-1', $GLOBALS['association_metas']['classe_produits']=>'+1', $GLOBALS['association_metas']['classe_contributions_volontaires']=>0), $balises, array('<'=>'&lt;','>'=>'&gt;'), '', '');
		$xml->leFichier('xml');
	}
}

?>