<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & François de Montlivault (V1)       *
 *  Copyright (c) 2010 Emmanuel Saint-James & Jeannot Lapin     (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


if (!defined("_ECRIRE_INC_VERSION")) return;

function action_valider_comptes() {
		
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$securiser_action();
	include_spip('base/association');
	$where = sql_in('id_compte', $_POST["definitif"]);
	sql_updateq('spip_asso_comptes', array('vu' => 1), $where);
}
?>
