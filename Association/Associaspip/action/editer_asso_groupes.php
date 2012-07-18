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

function action_editer_asso_groupes_dist()
{
    $securiser_action = charger_fonction('securiser_action', 'inc');
    $id_groupe = $securiser_action();
    $erreur = '';
    $champs = array(
	'nom' => _request('nom'),
	'commentaires' => _request('commentaire'),
	'affichage' => intval(_request('affichage')),
    );
    include_spip('base/association');
    if ($id_groupe) { // modification
	sql_updateq('spip_asso_groupes', $champs, "id_groupe=$id_groupe");
    } else { // ajout
	$id_groupe = sql_insertq('spip_asso_groupes', $champs);
	if (!$id_groupe)
	    $erreur = _T('asso:erreur_sgbdr');
    }

    return array($id_groupe, $erreur);
}

?>