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

function exec_suppr_pret() {
	$r = association_controle_id('pret', 'asso_prets', 'editer_prets');
	if ($r) {
		list($id_pret, $pret) = $r;
		exec_suppr_pret_args($id_pret, $pret);
	}
}

function exec_suppr_pret_args($id_pret, $pret) {
	include_spip ('association_modules');
	echo association_navigation_onglets('titre_onglet_prets', 'ressources');
	$ressource = sql_fetsel('*', 'spip_asso_ressources', 'id_ressource='.$pret['id_ressource'] ) ;
	$infos['entete_article'] = $ressource['intitule'];
	$infos['entete_nom'] = association_formater_idnom($pret['id_auteur'], array(), 'membre');
	$infos['prets_entete_date_sortie'] = association_formater_date($pret['date_sortie'],'dtstart');
	$infos['prets_entete_date_retour'] = association_formater_date($pret['date_retour'],'dtend');
	$infos['entete_montant'] = association_formater_prix($pret['prix_unitaire']*$pret['duree'], 'fees');
	echo association_totauxinfos_intro('', 'pret', $id_pret, $infos );
	// datation et raccourcis
		echo association_navigation_raccourcis(array(
			'prets_titre_liste_reservations' => array('grille-24.png', array('prets', "id=$pret[id_ressource]"), array('voir_prets', 'association') ),
		) );
	debut_cadre_association('pret-24.gif', 'prets_titre_suppression_prets');
	echo association_bloc_suppression('pret', "$id_pret-$pret[id_ressource]");
	fin_page_association();
}

?>