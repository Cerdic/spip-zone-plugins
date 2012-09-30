<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 (v1) Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010--2011 (v2) Emmanuel Saint-James & Jeannot Lapin
 *
 *  @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

// Export du Compte de Resultat au format YAML
// http://fr.wikipedia.org/wiki/Yaml
function exec_export_compteresultats_yaml() {
	if (!autoriser('associer', 'export_comptes')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		include_spip('inc/association_comptabilite');
		$yaml = new ExportComptes_TXT();
		$balises = array();
		foreach (array('entete', 'titre', 'nom', 'exercice', 'charges', 'produits', 'contributions_volontaires', 'chapitre', 'code', 'libelle', 'categorie', 'intitule', 'montant') as $key) {
			$balises[$key.'1'] = ucfirst($key).': ';
			$balises[$key.'0'] = '';
		}
		$balises['compteresultat1'] = 'Encodage: '.$GLOBALS['meta']['charset']."\nCompteDeResultat: ";
		$balises['compteresultat0'] = '';
		$yaml->exportLignesMultiples($balises, array("\t"=>'\t',"\r"=>'\r',"\n"=>'\n','\\'=>'\\\\'), '', '');
		$yaml->leFichier('yaml');
	}
}

?>