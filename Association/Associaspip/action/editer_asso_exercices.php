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

function action_editer_asso_exercices_dist()
{
    $securiser_action = charger_fonction('securiser_action', 'inc');
    $id_exercice = $securiser_action();
    $erreur = '';
    $champs = array(
	'intitule' => _request('intitule'),
	'commentaire' => _request('commentaire'),
	'debut' => association_recupere_date(_request('debut')),
	'fin' => association_recupere_date(_request('fin')),
    );
    include_spip('base/association');
    if ($id_exercice) { // modification
	sql_updateq('spip_asso_exercices', $champs, "id_exercice=$id_exercice");
    } else { // ajout
	$id_exercice = sql_insertq('spip_asso_exercices', $champs);
	if (!$id_exercice)
	    $erreur = _T('asso:erreur_sgbdr');
    }

    return array($id_exercice, $erreur);
}

?>