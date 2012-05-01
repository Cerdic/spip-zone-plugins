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

include_spip('exec/compte_resultat'); // c'est pour la definition de classe ExportCompteResultats

// Export du Compte de Resultat au format XML : balisage DocBooK
// http://fr.wikipedia.org/wiki/DocBook
function exec_export_compteresultats_dbk() {
	if (!autoriser('associer', 'export_comptes')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		$dbk = new ExportCompteResultats(_request('var'));
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
		$dbk->exportLignesMultiples($balises, array('<'=>'&lt;','>'=>'&gt;'), '', '');
		$dbk->leFichier('dbk');
	}
}

?>