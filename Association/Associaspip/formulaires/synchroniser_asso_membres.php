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

if (!defined('_ECRIRE_INC_VERSION'))
	return;

include_spip('inc/actions');
include_spip('inc/editer');
include_spip('inc/autoriser');

function formulaires_synchroniser_asso_membres_charger_dist()
{

	// rien a charger, c'est un formulaire basique
	$contexte['_action'] = array('synchroniser_asso_membres',''); // pour passer securiser action

	return $contexte;
}

function formulaires_synchroniser_asso_membres_verifier_dist()
{

	$erreurs = array();
	// pas de verification non plus

	return $erreurs;
}

function formulaires_synchroniser_asso_membres_traiter_dist() {
	$res = array();
	$synchro = charger_fonction('synchroniser_asso_membres','action');
	$nb_insertion = $synchro(); // la fonction action retourne le nombre d'insertion realisees
	if ($nb_insertion>1) {
		$nb_insertion .= _T('asso:membres_ajoutes');
	} else {
		$nb_insertion .= _T('asso:membre_ajoute');
	}
	$res['message_ok'] = $nb_insertion;

	return $res;
}

?>