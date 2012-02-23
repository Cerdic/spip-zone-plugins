<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & François de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)      *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


if (!defined('_ECRIRE_INC_VERSION'))
	return;

function action_supprimer_dons()
{
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$id_don = $securiser_action();

	$critere = $association_imputation('pc_dons');
	if ($critere)
		$critere .= ' AND ';
	$id_compte = sql_getfetsel('id_compte', 'spip_asso_comptes', "$critere id_journal=$id_don");
	association_supprimer_operation_comptable($id_compte);

	$critere = $association_imputation('pc_colis');
	if ($critere)
		$critere .= ' AND ';
	$id_compte = sql_getfetsel('id_compte', 'spip_asso_comptes', "$critere id_journal=$id_don");
	association_supprimer_operation_comptable($id_compte);

	sql_delete('spip_asso_dons', "id_don=$id_don");
}

?>
