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

// Export du Compte de Resultat au format JSON
// http://fr.wikipedia.org/wiki/Json
function exec_export_compteresultats_json() {
	if (!autoriser('associer', 'export_compteresultats')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		$json = new ExportCompteResultats(_request('var'));
		$balises = array();
		foreach (array('entete', 'titre', 'nom', 'exercice', 'charges', 'produits', 'contributions_volontaires', 'chapitre', 'code', 'libelle', 'categorie', 'intitule', 'montant') as $key) {
			$balises[$key.'1'] = '{ "'.ucfirst($key).'": ';
			$balises[$key.'0'] = '}';
		}
		$balises['compteresultat1'] = '{ "CompteDeResultat": ';
		$balises['compteresultat0'] = '}';
		$json->exportLignesMultiples($balises, array('&'=>'&amp;','"'=>'&quot;','<'=>'&lt;','>'=>'&gt;'), '"', '"');
		$json->leFichier('json');
	}
}

?>