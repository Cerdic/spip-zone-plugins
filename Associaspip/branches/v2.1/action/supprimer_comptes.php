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


if (!defined("_ECRIRE_INC_VERSION")) return;

function action_supprimer_comptes() {
		
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$id_compte = $securiser_action();
	sql_delete('spip_asso_comptes', 'id_compte=' . $id_compte);
	/* on efface de la table destination_op toutes les entrees correspondant a cette operation */
	sql_delete('spip_asso_destination_op', 'id_compte=' . $id_compte);
}
?>
