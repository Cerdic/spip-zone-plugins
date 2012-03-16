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

function action_supprimer_prets()
{
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	if (!preg_match('/^(\d+)\D(\d+)/', $arg, $r))
		spip_log("action_supprimer_prets: $arg incompris",'associaspip');
	else {
		list($id_pret,$id_ressource) = $r;
		include_spip ('inc/association_comptabilite');
		association_supprimer_operation_comptable2($id_pret, $GLOBALS['association_metas']['pc_prets']);
		sql_delete('spip_asso_prets', "id_pret=$id_pret");
		sql_updateq('spip_asso_ressources',
			array('statut'=>'ok',
		), "statut='reserve' AND id_ressource=$id_ressource" );
		sql_updateq('spip_asso_ressources',
			array('statut'=>'statut+1',
		), "statut>=0 AND id_ressource=$id_ressource" );
		sql_updateq('spip_asso_ressources',
			array('statut'=>'statut-1',
		), "statut<0 AND id_ressource=$id_ressource" );
	}
}

?>