<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & François de Montlivault (V1)       *
 *  Copyright (c) 2010 Emmanuel Saint-James                     (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

function formulaires_configurer_association_verifier_dist() {
	$erreurs = array();
	$erreur = false;

	$ref_attribuee = array();
	// on verifie qu'il n'a pas deux fois la meme reference comptable en incluant celle des cotisations
	$ref_attribuee[_request('pc_cotisations')]=0;
	if (_request('dons') == 'on') {
		$ref_dons = _request('pc_dons');
		if (!array_key_exists($ref_dons,$ref_attribuee)) {
			$ref_attribuee[$ref_dons]=0;
		}
		else $erreur = true;
	}

	if (_request('ventes') == 'on') {
		$ref_ventes = _request('pc_ventes');
		if (!array_key_exists($ref_ventes,$ref_attribuee)) {
			$ref_attribuee[$ref_ventes]=0;
		}
		else $erreur = true;
	}

	if (_request('prets') == 'on') {
		$ref_prets = _request('pc_prets');
		if (!array_key_exists($ref_prets,$ref_attribuee)) {
			$ref_attribuee[$ref_prets]=0;
		}
		else $erreur = true;
	}	 

	if (_request('activites') == 'on') {
		$ref_activites = _request('pc_activites');
		if (!array_key_exists($ref_activites,$ref_attribuee)) {
			$ref_attribuee[$ref_activites]=0;
		}
		else $erreur = true;
	}	 

	if ($erreur) $erreurs['message_erreur'] = _T('asso:erreur_configurer_association_titre').'<br/>'._T('asso:erreur_configurer_association_reference_multiple');
	return $erreurs;
}
?>
