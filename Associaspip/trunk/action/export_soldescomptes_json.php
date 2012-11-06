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

// Export du Compte de Resultat au format JSON
// http://fr.wikipedia.org/wiki/Json
function action_export_soldescomptes_json() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();

	if (!preg_match(',^(\d+)-(\w+)-(\w+)(-(\w+))?$,', $arg, $r)) {
		spip_log("action_export_soldescomptes incompris: " . $arg);
	} else {
		include_spip('inc/association_comptabilite');
		$json = new ExportComptes_TXT();
		$balises = array();
		foreach (array('entete', 'titre', 'nom', 'exercice', 'charges', 'produits', 'contributions_volontaires', 'chapitre', 'code', 'libelle', 'categorie', 'intitule', 'montant') as $key) {
			$balises[$key.'1'] = '{ "'.ucfirst($key).'": ';
			$balises[$key.'0'] = '}';
		}
		$balises['compteresultat1'] = '{ "CompteDeResultat": ';
		$balises['compteresultat0'] = '}';
		$json->exportLignesMultiples(array($GLOBALS['association_metas']['classe_charges']=>'-1', $GLOBALS['association_metas']['classe_produits']=>'+1', $GLOBALS['association_metas']['classe_contributions_volontaires']=>0), $balises, array('&'=>'&amp;','"'=>'&quot;','<'=>'&lt;','>'=>'&gt;'), '"', '"');
		$json->leFichier('json');
	}
}

?>