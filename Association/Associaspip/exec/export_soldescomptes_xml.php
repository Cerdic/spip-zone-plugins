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

// Export du Compte de Resultat au format XML
// http://fr.wikipedia.org/wiki/Extensible_Markup_Language
// jeu de balisage propre a Associaspip ; pas de DTD ni de Schema
function exec_export_soldescomptes_xml() {
	if (!autoriser('associer', 'export_comptes')) {
		include_spip('inc/minipres');
		echo minipres();
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