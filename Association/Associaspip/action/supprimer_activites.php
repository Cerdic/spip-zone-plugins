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

function action_supprimer_activites()
{
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$id_activite = $securiser_action();
	include_spip ('inc/association_comptabilite');
	association_supprimer_operation_comptable2($id_activite,$GLOBALS['association_metas']['pc_activites']);
	sql_delete('spip_asso_activites', "id_activite=$id_activite");
}

?>