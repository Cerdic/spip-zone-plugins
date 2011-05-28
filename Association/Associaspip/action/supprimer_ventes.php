<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & Fran�ois de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)      *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


if (!defined("_ECRIRE_INC_VERSION")) return;

function action_supprimer_ventes() {
		
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$securiser_action();

	$w = sql_in('id_vente', $_REQUEST['drop']);
	sql_delete('spip_asso_ventes', $w);

	// on recupere les id_compte correspondant aux ventes dans la table des comptes
	$w = sql_in('id_journal', $_REQUEST['drop']);
	$association_imputation = charger_fonction('association_imputation', 'inc');
	$critere = $association_imputation('pc_ventes');
	if ($critere) $critere .= ' AND ';
	$where = sql_in_select("id_compte", "id_compte", "spip_asso_comptes", $critere . $w);
	sql_delete('spip_asso_destination_op', $where);
	sql_delete('spip_asso_comptes', $critere . $w);
	/* si ventes et frais d'envoi ne sont pas associes a la meme reference, on repete l'operation pour les operation associes aux frais d'envoi */
	if ($GLOBALS['association_metas']['pc_ventes']!=$GLOBALS['association_metas']['pc_frais_envoi']) {
		$critere = $association_imputation('pc_frais_envoi');
		if ($critere) $w .= " AND $critere";
		$where = sql_in_select("id_compte", "id_compte", "spip_asso_comptes", $w);
		sql_delete('spip_asso_destination_op', $where);
		sql_delete('spip_asso_comptes', $w);
	}	
}
?>
