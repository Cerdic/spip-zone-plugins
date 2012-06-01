<?php

/* * *************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & François de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)      *
 *  Ecrit par Marcel BOLLA en 01/2012                                      *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
  \************************************************************************** */

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