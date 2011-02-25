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


if (!defined("_ECRIRE_INC_VERSION")) return;

function action_modifier_dons() {
		
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$id_don=$securiser_action();

	$journal= _request('journal');
	$date_don = _request('date_don');
	$bienfaiteur = _request('bienfaiteur');
	$id_adherent = _request('id_adherent');
	if ($argent_req =  _request('argent')) {
		$argent = floatval(preg_replace("/,/",".",$argent_req));
	}
	else $argent = 0;
	$colis = _request('colis');
	if ($valeur_req =  _request('valeur')) {
		$valeur = floatval(preg_replace("/,/",".",$valeur_req));
	}
	else $valeur = 0;
	$contrepartie = _request('contrepartie');
	$commentaire = _request('commentaire');
	$id_compte = _request('id_compte');

	include_spip('base/association');

	// on modifie l'operation comptable associe au don
	association_modifier_operation_comptable($date_don, $argent, 0, "[->don$id_don] [->membre$id_adherent]", $GLOBALS['association_metas']['pc_dons'], $journal, '', $id_compte);

	sql_updateq('spip_asso_dons', array(
			'date_don' => $date_don,
			'bienfaiteur' => $bienfaiteur,
			'id_adherent' => $id_adherent,
			'argent' => $argent,
			'colis' => $colis,
			'valeur' => $valeur,
			'contrepartie' => $contrepartie,
			'commentaire' => $commentaire),
		    "id_don=$id_don");
}
?>
