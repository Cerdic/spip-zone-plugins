<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 (v1) Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010--2011 (v2) Emmanuel Saint-James & Jeannot Lapin
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

function exec_suppr_vente() {
	$r = association_controle_id('vente', 'asso_ventes', 'editer_ventes');
	if ($r) {
		list($id_vente, $vente) = $r;
		exec_suppr_vente_args($id_vente, $vente);
	}
}

function exec_suppr_vente_args($id_vente, $vente) {
	include_spip ('association_modules');
	echo association_navigation_onglets('titre_onglet_ventes', 'ventes');
	// info
	$infos['ventes_entete_date_vente'] = association_formater_date($vente['date_vente'],'dtstart');
	$infos['ventes_entete_date_envoi'] = association_formater_date($vente['date_envoi'],'dtend');
	$infos['entete_intitule'] = '<span class="n">'. (test_plugin_actif('CATALOGUE') && (is_numeric($vente['article'])) ? ( association_formater_idnom($vente['article'], array('spip_articles', 'titre', 'id_article'), 'article') . association_formater_idnom($vente['code'], array('spip_cat_variantes', 'titre', 'id_cat_variante'), '') ) : $vente['article'] ) .'</span>';
//		$infos['entete_code'] = association_formater_code($vente['code'], 'x-spip_asso_ventes');
	$infos['entete_nom'] = association_formater_idnom($vente['id_auteur'], $vente['nom'], 'membre');
	$infos['entete_quantite'] = association_formater_nombre($vente['quantite'], 2, 'quantity');
	$infos['entete_montant'] = association_formater_prix($vente['prix_unitaire'], 'purchase cost offer');
	$infos['entete_commentaire'] = $vente['commentaire'];
	echo '<div class="hproduct">'. association_totauxinfos_intro('', 'vente', $id_vente, $infos ) .'</div>';
	// datation et raccourcis
	echo association_navigation_raccourcis(array(
		array('titre_onglet_ventes', 'grille-24.png', array('ventes', "id=$id_vente"), array('voir_ventes', 'association') ),
	) );
	debut_cadre_association('ventes.gif', 'action_sur_les_ventes_associatives');
	echo association_form_suppression('vente', $id_vente);
	fin_page_association();
}

?>