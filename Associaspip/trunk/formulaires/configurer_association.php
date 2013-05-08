<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010 Emmanuel Saint-James
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

function formulaires_configurer_association_charger_dist() {
	return $GLOBALS['association_metas'];
}

function formulaires_configurer_association_verifier_dist() {
	$erreurs = array();
	$erreurs['message_erreur'] = _T('asso:erreur_titre'); // on insere directement un titre de message d'erreurs, si on n'a que lui a la fin on renvoie un tableau vide

	if ( !strlen(trim(_request('nom'))) ) { // nom de l'association ne doit pas etre vide
		$erreurs['nom'] = _T('asso:erreur_configurer_association_nom_association_vide');
	}

	$comptes = _request('comptes');
	if ($comptes) { // si la gestion comptable est activee
		include_spip('inc/association_comptabilite');
		if (!association_valider_plan_comptable()) { // on (re)valide le plan comptable
			$erreurs['comptes'] = _T('asso:erreur_configurer_association_plan_comptable_non_valide');
			return $erreurs;
		}
		$classe_attribuee = array();
		foreach( array('banques', 'charges','produits','contributions_volontaires') as $index=>$exigee) { // on verifie que les classes obligatoires sont distincts
			$classe_testee = _request("classe_$exigee");
			if (array_key_exists($classe_testee, $classe_attribuee)) { // classe dupliquee
				$erreurs[$classe_testee] = _T('asso:erreur_configurer_association_classe_identique');
				$erreurs[$ref_attribuee[$classe_testee]] = _T('asso:erreur_configurer_association_classe_identique');
			}
			$classe_attribuee[$classe_testee] = $classe_testee;
		}
		$classe_financier = $classe_attribuee['classe_banques'];
	}
	$ref_attribuee = array();
	foreach (array(
		'comptes' => array('cotisations'), // pour inclure les cotisations
		'activites' => array('activites'),
		'dons' => array('dons'),
		'prets' => array('cautions','prets','ressources'),
		'ventes' => array('frais_envoi','ventes'),
	) as $module=>$refs) {
		if (_request($module)) { // module actif
			if (!$comptes) { // compta non activee :-S
				$erreurs[$module] = _T('asso:erreur_configurer_association_gestion_comptable_non_activee');
			} else {
				foreach ($refs as $champ) {
					$val = _request("pc_$champ");
					if (!array_key_exists($val,$ref_attribuee)) { // reference unique
						if ($val[0]==$classe_financier) // le 1er caractere de la reference comptable est sa classe : elle ne doit pas etre financiere
							$erreurs["pc_$champ"] = _T('asso:erreur_configurer_association_reference_financier');
					} elseif($val==_request('pc_ventes') && $champ=='frais_envoi') { // exception : vente et frais_envoi peuvent etre associes a la meme reference comptable meme si c'est deconseille d'un point de vue comptable
					} elseif($val=='' && $champ=='cautions') { // exception : la reference peut ne pas etre attribuee si on ne veut pas utiliser de systeme de cautionnement
					} else { // references multiples...
						$erreurs[$module] = _T('asso:erreur_configurer_association_reference_multiple');
						$erreurs[$ref_attribuee[$val]] = _T('asso:erreur_configurer_association_reference_multiple');
					}
					$ref_attribuee[$val] = "pc_$champ";
				}
			}
		}
	}

	if (count($erreurs)==1) { // si on n'a qu'un entree dans la table des erreurs, c'est le titre qu'on a mis au debut, on n'a pas d'erreur, on renvoie un tableau vide
		return array();
	}
	foreach (array(
		'activites', 'comptes', 'dons', 'prets', 'ventes', // compta (destinations, exercices)
		'civilite', 'id_asso', 'prenom', // fichemembres
	) as $checkbox) { // on a des erreurs, pour conserver l'etat des checkbox vides, il faut faire un set_request en mettant une valeur differente de 'on' sinon le retour de verif mange les eventuelles modifs
		if (!_request($checkbox))
			set_request($checkbox, 'off');
	}

	return $erreurs;
}

function formulaires_configurer_association_traiter_dist() {
	get_infos = charger_fonction('get_infos','plugins');
	$infos = $get_infos('association');
	include_spip('formulaires/configurer_metas');
	$vars = formulaires_configurer_metas_recense($infos['path'], PREG_PATTERN_ORDER);

	foreach ( array(
		'activites' => array('dc_activites', 'pc_activites'),
		'comptes' => array('destinations','dc_cotisations', 'pc_cotisations',),
		'dons' => array('dc_colis','pc_colis','pc_dons','dc_dons'), //no dc_colis
		'prets' => array('dc_cautions','pc_cautions','dc_prets','pc_prets','dc_ressources','pc_ressources',), //no dc_cautions
		'ventes' => array('dc_frais_envoi','pc_frais_envoi','dc_ventes','pc_ventes'), //no dc_frais_envoi
	) as $module=>$metas) { // ignorer les changements fait dans un module non active
		if (!_request($module)) { // module desactive...
			foreach ($metas as $meta_nom) { // ...ignorer les changements faits
				unset($metas_list[$meta_nom]);
			}
		}
	}
	foreach ( array('cotisations', 'dons', 'colis', 'ressources', 'cautions', 'prets', 'activites',) as $pc ) { // A-t-on modifie les metas pc_XXX si oui il faut faire suivre dans la table des comptes la modif, sinon on perd toutes les operations deja enregistrees
		$nrc = _request("pc_$pc"); // Nouvelle Reference
		if ($comptes && $GLOBALS['association_metas']["pc_$pc"] && ($nrc!=$GLOBALS['association_metas']["pc_$pc"])) { // condition pour modifier dans la table des comptes : module actif (peut-etre aussi juste active par cet envoi) ET meta pre-existente ET meta modifiee
			sql_updateq('spip_asso_comptes', array('imputation' => $nrc), 'imputation='.$GLOBALS['association_metas']["pc_$pc"]);
		}
	}
	$pc_frais_envoi = _request('pc_frais_envoi');
	if (_request('ventes') &&
		$GLOBALS['association_metas']['pc_frais_envoi'] &&
		($pc_frais_envoi!=$GLOBALS['association_metas']['pc_frais_envoi']) &&
		($GLOBALS['association_metas']['pc_frais_envoi']!=$GLOBALS['association_metas']['pc_ventes']) &&
		_request('pc_ventes')!=$pc_frais_envoi) { // pour celle-la on controle aussi que le pc_vente et pc_frais_envoi etaient differents avant et apres la modif
			// - si ils etaient egaux, on ne peux pas faire migrer les frais d'envoi vu qu'ils etaient inseres dans la meme operation comptable
			// - si ils sont maintenant egaux mais ne l'etaient pas avant, toutes les ventes vont apparaitre en double: la vente elle meme et les frais d'envoi.
			sql_updateq('spip_asso_comptes', array('imputation' => $pc_frais_envoi), 'imputation='.$GLOBALS['association_metas']['pc_frais_envoi']);
	}

	foreach (array_keys($vars[2]) as $k) { // enregistrer chaque meta recense
		$v = _request($k);
		ecrire_meta($k, is_array($v) ? serialize($v) : $v, 'oui', $infos['meta']);
	}
	return !isset($infos['prefix'])
		? array()
		: array('redirect' => generer_url_ecrire($infos['prefix']));
}

?>