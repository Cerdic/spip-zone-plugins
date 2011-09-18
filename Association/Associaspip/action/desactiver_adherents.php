<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & François de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)      *
 *  Ecrit par Marcel BOLLA en 09/2011                                      *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


if (!defined("_ECRIRE_INC_VERSION")) return;

function action_desactiver_adherents() {		
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$securiser_action();
	$statut_courant = $_POST['statut_courant'];
	$where = sql_in('id_auteur', $_POST["drop_des"]);
	if($statut_courant==='sorti') {
		sql_updateq('spip_asso_membres', array("statut_interne" => 'prospect'), $where);
	}
	else {
		sql_updateq('spip_asso_membres', array("statut_interne" => 'sorti'), $where);
	}
}

?>
