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

include_spip('inc/actions');
include_spip('inc/editer');

function formulaires_editer_asso_compte_charger_dist($id_compte=0) {
    $contexte = formulaires_editer_objet_charger('asso_comptes', $id_compte, '', '',  generer_url_ecrire('comptes'), '');
    if (!$id_compte) { // si c'est une nouvelle operation
	$contexte['date'] = date('Y-m-d'); // on charge la date d'aujourd'hui
	$contexte['type_operation'] = $GLOBALS['association_metas']['classe_charges']; // on fixe par defaut une depense
    } else { // si on edite une operation
	// ToDo : dans une version suivante il faut mettre cette information dans le champ "type_op" du type enum dans lequel il faut ajouter 'financier' (pour un virement interne) et 'contribution_volontaire' en plus de 'credit', 'debit' et 'multi'
	$contexte['type_operation'] = substr($contexte['imputation'],0,1); // le type d'operation est fonction du compte (de l'imputation dans le cas present) : c'est le 1er caractere du code (la classe)
    }
    association_chargeparam_destinations('', $contexte);
    include_spip('inc/association_comptabilite');
    // Recuperation du plan comptable sous forme de tableau javascript
    // correspondant aux classes utilisees.
    // Ces tableaux sont ensuite utilises pour initialiser
    // le selecteur d'imputation (js)

    $code = '';
    foreach ($GLOBALS['association_metas'] as $key => $val) {
	// ne prendre dans les metas que les classes
	if (substr($key, 0, 6)==="classe") {
		$code .= "var classe$val = new Array();\n";
		// code virement interne
		$interne = $GLOBALS['association_metas']['pc_intravirements'];
		foreach (association_liste_plan_comptable($val,1) as $k => $v) {
			if ($k != $înterne) {
				$code .= "classe$val" . "['$k'] = '". addslashes($v) ."';\n";
			}
		}
	}
    }
    if ($code)
	echo http_script($code);

    // paufiner la presentation des valeurs
    $contexte['depense'] = association_formater_nombre($contexte['depense']);
    $contexte['recette'] = association_formater_nombre($contexte['recette']);

    return $contexte;
}

function formulaires_editer_asso_compte_verifier_dist($id_compte=0) {
    $erreurs = array();

    $recette = association_recuperer_montant('recette');
    $depense = association_recuperer_montant('depense');
    if (($recette<0) || ($depense<0) || ($recette>0 && $depense>0) || ($recette==0 && $depense==0)) { // on verifie que l'on a bien soit depense soit recette different de 0 et qu'aucun n'est negatif
	$erreurs['montant'] = _T('asso:erreur_recette_depense');
    }
    $code = _request('imputation');
    if (!array_key_exists('montant', $erreurs)) { // on verifie que ce type d'operation est bien permis sur ce compte
	$type_op = sql_getfetsel('type_op', 'spip_asso_plan', 'code='.sql_quote($code));
	if ((($type_op=='credit') && ($depense>0)) || (($type_op=='debit') && ($recette>0))) {
	    $erreurs['imputation'] = _T('asso:erreur_operation_non_permise_sur_ce_compte');
	}
    }
    if ($erreur = association_verifier_destinations($recette+$depense, FALSE) )
	$erreurs['destinations'] = $erreur;
    if ($erreur = association_verifier_date('date') )
	$erreurs['date'] = $erreur;

    if (count($erreurs)) {
	$erreurs['message_erreur'] = _T('asso:erreur_titre');
    }
    return $erreurs;
}

function formulaires_editer_asso_compte_traiter($id_compte=0) {
    return formulaires_editer_objet_traiter('asso_comptes', $id_compte, '', '',  generer_url_ecrire('comptes'), '');
}

?>