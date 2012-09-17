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

include_spip ('inc/navigation_modules');

function exec_suppr_vente()
{
	if (!autoriser('associer', 'ventes')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		$id_vente = intval(_request('id'));
		onglets_association('titre_onglet_ventes', 'ventes');
		// info
		$vente = sql_fetsel('*', 'spip_asso_ventes', "id_vente=$id_vente");
		$infos['ventes_entete_date_vente'] = association_formater_date($vente['date_vente'],'dtstart');
		$infos['ventes_entete_date_envoi'] = association_formater_date($vente['date_envoi'],'dtend');
		$infos['entete_intitule'] = '<span class="n">'. (test_plugin_actif('CATALOGUE') && (is_numeric($vente['article'])) ? ( association_formater_idnom($vente['article'], array('spip_articles', 'titre', 'id_article'), 'article') . association_formater_idnom($vente['code'], array('spip_cat_variantes', 'titre', 'id_cat_variante'), '') ) : $vente['article'] ) .'</span>';
//		$infos['entete_code'] = association_formater_code($vente['code'], 'x-spip_asso_ventes');
		$infos['entete_nom'] = association_formater_idnom($vente['id_acheteur'], $vente['acheteur'], 'membre');
		$infos['entete_quantite'] = association_formater_nombre($vente['quantite'], 2, 'quantity');
		$infos['entete_montant'] = association_formater_prix($vente['prix_vente'], 'purchase cost offer');
		$infos['entete_commentaire'] = $vente['commentaire'];
		echo '<div class="hproduct">'. association_totauxinfos_intro('', 'vente', $id_vente, $infos ) .'</div>';
		// datation et raccourcis
		raccourcis_association('');
		debut_cadre_association('ventes.gif', 'action_sur_les_ventes_associatives');
		echo association_bloc_suppression('vente', $id_vente);
		fin_page_association();
	}
}

?>