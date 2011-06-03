<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & Francois de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

function formulaires_configurer_association_verifier_dist() {
	$erreurs = array();
	$erreurs['message_erreur'] = _T('asso:erreur_configurer_association_titre'); /* on insere directement un titre de message d'erreurs, si on n'a que lui a la fin on renvoie un tableau vide */


	$dons = _request('dons');
	$ventes = _request('ventes');
	$prets = _request('prets');
	$activites = _request('activites');
	$pc_cotisations = _request('pc_cotisations');
	$pc_dons = _request('pc_dons');
	$pc_ventes = _request('pc_ventes');
	$pc_frais_envoi = _request('pc_frais_envoi');
	$pc_prets = _request('pc_prets');
	$pc_activites = _request('pc_activites');
	$comptes = _request('comptes');

	// si la gestion comptable est activee, on valide le plan comptable
	if ($comptes) {
		include_spip('inc/association_comptabilite');
		if (!association_valider_plan_comptable()) {	
			$erreurs['comptes'] = _T('asso:erreur_configurer_association_plan_comptable_non_valide');
			return $erreurs;
		}

		// on verifie qu'il n'a pas deux fois la meme reference comptable en incluant celle des cotisations ou qu'on n'a pas attribue aux cotisations ou modules de gestion une reference comptable de la classe des comptes financiers
		$ref_attribuee = array();
		$classe_financier = _request('classe_banques');
		$ref_attribuee[$pc_cotisations]='pc_cotisations';
	
		// le premier caractere du code de la reference comptable est sa classe 
		if ($pc_cotisations[0] == $classe_financier) $erreurs['pc_cotisations'] = _T('asso:erreur_configurer_association_reference_financier');
	}

	if ($dons == 'on') {
		if (!$comptes) { $erreurs['dons'] = _T('asso:erreur_configurer_association_gestion_comptable_non_activee');}
		else {
			if (!array_key_exists($pc_dons,$ref_attribuee)) {
				// le premier caractere du code de la reference comptable est sa classe 
				if ($pc_dons[0] == $classe_financier) $erreurs['dons'] = _T('asso:erreur_configurer_association_reference_financier');
			}
			else {
				$erreurs['dons'] = _T('asso:erreur_configurer_association_reference_multiple');
				$erreurs[$ref_attribuee[$pc_dons]] = _T('asso:erreur_configurer_association_reference_multiple');
			}
			$ref_attribuee[$pc_dons]='dons';
		}
	}

	if ($ventes == 'on') {
		if (!$comptes) { $erreurs['ventes'] = _T('asso:erreur_configurer_association_gestion_comptable_non_activee'); }
		else {
			if (!array_key_exists($pc_ventes,$ref_attribuee)) {
				// le premier caractere du code de la reference comptable est sa classe 
				if ($pc_ventes[0] == $classe_financier) $erreurs['ventes'] = _T('asso:erreur_configurer_association_reference_financier');
			}
			else {
				$erreurs['ventes'] = _T('asso:erreur_configurer_association_reference_multiple');
				$erreurs[$ref_attribuee[$pc_ventes]] = _T('asso:erreur_configurer_association_reference_multiple');
			}
			$ref_attribuee[$pc_ventes]='ventes';

			if ($pc_ventes != $pc_frais_envoi) {
				/* vente et frais_envoi peuvent etre associes a la meme reference comptable meme si c'est deconseille d'un point de vue comptable */
				if (!array_key_exists($pc_frais_envoi,$ref_attribuee)) {
					// le premier caractere du code de la reference comptable est sa classe 
					if ($pc_frais_envoi[0] == $classe_financier) $erreurs['frais_envoi'] = _T('asso:erreur_configurer_association_reference_financier');
				}
				else {
					$erreurs['frais_envoi'] = _T('asso:erreur_configurer_association_reference_multiple');
					$erreurs[$ref_attribuee[$pc_frais_envoi]] = _T('asso:erreur_configurer_association_reference_multiple');
				}
				$ref_attribuee[$pc_frais_envoi]='frais_envoi';
			}
		}
	}

	if ($prets == 'on') {
		if (!$comptes) { $erreurs['prets'] = _T('asso:erreur_configurer_association_gestion_comptable_non_activee'); }
		else {
			if (!array_key_exists($pc_prets,$ref_attribuee)) {
				// le premier caractere du code de la reference comptable est sa classe 
				if ($pc_prets[0] == $classe_financier) $erreurs['prets'] = _T('asso:erreur_configurer_association_reference_financier');
			}
			else {
				$erreurs['prets'] = _T('asso:erreur_configurer_association_reference_multiple');
				$erreurs[$ref_attribuee[$pc_prets]] = _T('asso:erreur_configurer_association_reference_multiple');
			}
			$ref_attribuee[$pc_prets]='prets';
		}
	}	 

	if ($activites == 'on') {
		if (!$comptes) { $erreurs['activites'] = _T('asso:erreur_configurer_association_gestion_comptable_non_activee'); }
		else {
			if (!array_key_exists($pc_activites,$ref_attribuee)) {
				// le premier caractere du code de la reference comptable est sa classe 
				if ($pc_activites[0] == $classe_financier) $erreurs['activites'] = _T('asso:erreur_configurer_association_reference_financier');
			}
			else {
				$erreurs['activites'] = _T('asso:erreur_configurer_association_reference_multiple');
				$erreurs[$ref_attribuee[$pc_activites]] = _T('asso:erreur_configurer_association_reference_multiple');
			}
			$ref_attribuee[$pc_activites]='activites';
		}
	}

	if (count($erreurs)==1) { /* si on n'a qu'un entree dans la table des erreurs, c'est le titre qu'on a mis au debut, on n'a pas d'erreur, on renvoie un tableau vide */
		/* vilain hack pour conserver la fonction traiter des metas : on fait potentiellement ici des modifs dans la table */
		/* A-t-on modifie les metas pc_XXX si oui il faut faire suivre dans la table des comptes la modif, sinon on perd toutes les operations deja enregistrees */
		if ($pc_cotisations != $GLOBALS['association_metas']['pc_cotisations']) {
			$erreurs['pc_cotisations']="PC COTI: ".$pc_cotisations." GLOB: ".$GLOBALS['association_metas']['pc_cotisations']." lignes a modifier :".print_r(sql_allfetsel("id_compte, imputation", 'spip_asso_comptes', "imputation=".$GLOBALS['association_metas']['pc_cotisations']),true);
			//return $erreurs;
			sql_updateq('spip_asso_comptes', array('imputation' => $pc_cotisations), "imputation=".$GLOBALS['association_metas']['pc_cotisations']);
		}
		return array(); 
	}

	/* on a des erreurs, pour conserver l'etat des checkbox vides, il faut faire un set_request en mettant une valeur differente de on sinon le retour de verif mange les eventuelles modifs */
	if (!$comptes) set_request('comptes', 'off');
	if (!$dons) set_request('dons', 'off');
	if (!$ventes) set_request ('ventes', 'off');
	if (!$prets) set_request ('prets', 'off');
	if (!$activites) set_request ('activites', 'off');

	if (!_request('$destinations')) set_request ('destinations', 'off');
	if (!_request('civilite')) set_request ('civilite', 'off');
	if (!_request('prenom')) set_request ('prenom', 'off');
	if (!_request('id_asso')) set_request ('id_asso', 'off');

	return $erreurs;
}
?>
