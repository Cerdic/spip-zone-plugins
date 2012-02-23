<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & François de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


if (!defined('_ECRIRE_INC_VERSION'))
	return;

function action_ajouter_activites()
{
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$securiser_action();
	$n = sql_insertq('spip_asso_activites', array(
		'date' => _request('date'),
		'id_evenement' => intval(_request('id_evenement')),
		'nom' => _request('nom'),
		'id_adherent' => intval(_request('id_menbre')),
		'membres' => _request('membres'),
		'non_membres' => _request('non_membres'),
		'inscrits' => intval(_request('inscrits')),
		'montant' => association_recupere_montant(_request('montant')),
		'commentaire' => _request('commentaire'),
	));
	spip_log("insertion activite numero: $n",'associaspip');
}

?>