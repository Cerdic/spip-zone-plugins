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
	$comptes = _request('comptes');

	$pc_cotisations = _request('pc_cotisations');
	$pc_dons = _request('pc_dons');
	$pc_ventes = _request('pc_ventes');
	$pc_frais_envoi = _request('pc_frais_envoi');
	$pc_prets = _request('pc_prets');
	$pc_activites = _request('pc_activites');


	// si la gestion comptable est activee, on valide le plan comptable
	$ref_attribuee = array();
	$classe_attribuee = array();
	if ($comptes) {
		include_spip('inc/association_comptabilite');
		if (!association_valider_plan_comptable()) {	
			$erreurs['comptes'] = _T('asso:erreur_configurer_association_plan_comptable_non_valide');
			return $erreurs;
		}

		// on verifie qu'il n'a pas deux fois la meme reference comptable en incluant celle des cotisations ou qu'on n'a pas attribue aux cotisations ou modules de gestion une reference comptable de la classe des comptes financiers
		$classe_financier = _request('classe_banques');
		$classe_attribuee[$classe_financier]='classe_banques';
		$ref_attribuee[$pc_cotisations]='pc_cotisations';
	
		// le premier caractere du code de la reference comptable est sa classe 
		if ($pc_cotisations[0] == $classe_financier) $erreurs['pc_cotisations'] = _T('asso:erreur_configurer_association_reference_financier');

		// on verifie que les classes sont uniques
		$classe_charge = _request('classe_charges');
		if(array_key_exists($classe_charge, $classe_attribuee)) {
			$erreurs['classe_charges'] = _T('asso:erreur_configurer_association_classe_identique');
			$erreurs[$ref_attribuee[$classe_charge]] = _T('asso:erreur_configurer_association_classe_identique');
		}
		$classe_attribuee[$classe_charge]='classe_charges';

		$classe_produit = _request('classe_produits');
		if(array_key_exists($classe_produit, $classe_attribuee)) {
			$erreurs['classe_produits'] = _T('asso:erreur_configurer_association_classe_identique');
			$erreurs[$ref_attribuee[$classe_produit]] = _T('asso:erreur_configurer_association_classe_identique');
		}
		$classe_attribuee[$classe_produit]='classe_produits';

		$classe_contribution_volontaire = _request('classe_contributions_volontaires');
		if(array_key_exists($classe_contribution_volontaire, $classe_attribuee)) {
			$erreurs['classe_contributions_volontaires'] = _T('asso:erreur_configurer_association_classe_identique');
			$erreurs[$ref_attribuee[$classe_contribution_volontaire]] = _T('asso:erreur_configurer_association_classe_identique');
		}
		$classe_attribuee[$classe_contribution_volontaire]='classe_contributions_volontaires';
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
		return array(); 
	}

	/* on a des erreurs, pour conserver l'etat des checkbox vides, il faut faire un set_request en mettant une valeur differente de 'on' sinon le retour de verif mange les eventuelles modifs */
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

/* reprise en grande partie du code de la fonction traiter de configurer_metas */
function formulaires_configurer_association_traiter_dist($form) {
	include_spip('formulaires/configurer_metas');
	/* code directement copie depuis formulaires_configurer_metas_traiter_dist */
	$infos = formulaires_configurer_metas_infos($form);
	if (!is_array($infos)) return $infos;
	$vars = formulaires_configurer_metas_recense($infos['path'], PREG_PATTERN_ORDER);
	$meta = $infos['meta'];
	/* fin du code directement copie depuis formulaires_configurer_metas_traiter_dist */

	$metas_list = array_flip(array_unique($vars[2])); /* on recupere tous les noms des metas comme cles d'un tableau */
	
	/* on ajoute toutes les metas utilisateurs: presentes avec le prefixe meta_utilisateur_ dans la table spip_association_metas */
	$query = sql_select('nom', 'spip_association_metas', "nom LIKE 'meta_utilisateur_%'");
	while ($row = sql_fetch($query)) {
		$metas_list[$row['nom']]=0;
	}

	/* ignorer les changements fait dans un module non active */
	$dons = _request('dons');
	$ventes = _request('ventes');
	$prets = _request('prets');
	$activites = _request('activites');
	$comptes = _request('comptes');

	if (!$comptes) {
		unset($metas_list['pc_cotisations']);
		unset($metas_list['dc_cotisations']);
		unset($metas_list['destinations']);
	}

	if (!$dons) {
		unset($metas_list['pc_dons']);
		unset($metas_list['dc_dons']);
	}
	
	if (!$ventes) {
		unset($metas_list['pc_ventes']);
		unset($metas_list['pc_frais_envoi']);
		unset($metas_list['dc_ventes']);
	}

	if (!$prets) {
		unset($metas_list['pc_prets']);
	}

	if (!$activites) {
		unset($metas_list['pc_activites']);
	}

	/* A-t-on modifie les metas pc_XXX si oui il faut faire suivre dans la table des comptes la modif, sinon on perd toutes les operations deja enregistrees */
	$pc_cotisations = _request('pc_cotisations');
	$pc_dons = _request('pc_dons');
	$pc_ventes = _request('pc_ventes');
	$pc_frais_envoi = _request('pc_frais_envoi');
	$pc_prets = _request('pc_prets');
	$pc_activites = _request('pc_activites');

	/* condition pour modifier dans la table des comptes: module actif(peut-etre aussi juste active par cet envoi) ET meta pre existente ET meta modifiee */ 
	if ($comptes && $GLOBALS['association_metas']['pc_cotisations'] && ($pc_cotisations != $GLOBALS['association_metas']['pc_cotisations'])) {
		sql_updateq('spip_asso_comptes', array('imputation' => $pc_cotisations), "imputation=".$GLOBALS['association_metas']['pc_cotisations']);
	}

	if ($dons && $GLOBALS['association_metas']['pc_dons'] && ($pc_dons != $GLOBALS['association_metas']['pc_dons'])) {
		sql_updateq('spip_asso_comptes', array('imputation' => $pc_dons), "imputation=".$GLOBALS['association_metas']['pc_dons']);
	}

	if ($ventes && $GLOBALS['association_metas']['pc_ventes'] && ($pc_ventes != $GLOBALS['association_metas']['pc_ventes'])) {
		sql_updateq('spip_asso_comptes', array('imputation' => $pc_ventes), "imputation=".$GLOBALS['association_metas']['pc_ventes']);
	}

	if ($ventes && 
		$GLOBALS['association_metas']['pc_frais_envoi'] &&
		($pc_frais_envoi != $GLOBALS['association_metas']['pc_frais_envoi']) &&
		($GLOBALS['association_metas']['pc_frais_envoi'] != $GLOBALS['association_metas']['pc_ventes']) &&
		$pc_ventes != $pc_frais_envoi) { /* pour celui la on controle aussi que le pc_vente et pc_frais_envoi etaient differents avant et apres la modif */
			/* si ils etaient egaux, on ne peux pas faire migrer les frais d'envoi vu qu'ils etaient inseres dans la meme operation comptable */
			/* si ils sont maintenant egaux mais ne l'etaient pas avant, toutes les ventes vont apparaitre en double: la vente elle meme et les frais d'envoi. */
			sql_updateq('spip_asso_comptes', array('imputation' => $pc_frais_envoi), "imputation=".$GLOBALS['association_metas']['pc_frais_envoi']);
	}

	if ($prets && $GLOBALS['association_metas']['pc_prets'] && ($pc_prets != $GLOBALS['association_metas']['pc_prets'])) {
		sql_updateq('spip_asso_comptes', array('imputation' => $pc_prets), "imputation=".$GLOBALS['association_metas']['pc_prets']);
	}

	if ($activites && $GLOBALS['association_metas']['pc_activites'] && ($pc_ != $GLOBALS['association_metas']['pc_activites'])) {
		sql_updateq('spip_asso_comptes', array('imputation' => $pc_activites), "imputation=".$GLOBALS['association_metas']['pc_activites']);
	}

	/* code repris sur formulaires_configurer_metas_traiter_dist */
	foreach (array_keys($metas_list) as $k) {
			$v = _request($k);
			ecrire_meta($k, is_array($v) ? serialize($v) : $v, 'oui', $meta);
	}
	return !isset($infos['prefix']) ? array()
		: array('redirect' => generer_url_ecrire($infos['prefix']));
}
?>
