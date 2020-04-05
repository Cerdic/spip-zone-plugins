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

function exec_suppr_don() {
	$r = association_controle_id('don', 'asso_dons', 'editer_dons');
	if ($r) {
		list($id_don, $don) = $r;
		exec_suppr_don_args($id_don, $don);
	}
}

function exec_suppr_don_args($id_don, $don) {
	include_spip ('association_modules');

	echo association_navigation_onglets('titre_onglet_dons', 'dons');
	// info
	$infos['entete_date'] = association_formater_date($don['date_don'], '');
	$infos['entete_nom'] = association_formater_idnom($don['id_auteur'], $don['nom'], 'membre');
	$infos['argent'] = association_formater_prix($don['argent'], 'donation cash');
	$infos['colis'] = ($don['valeur'] ? '('.association_formater_prix($don['valeur'], 'donation estimated').')<div class="n">' : '') .$don['colis'] .($don['valeur']?'</div>':'');
	$infos['contrepartie'] = $don['contrepartie'];
	$infos['entete_commentaire'] = $don['commentaire'];
	echo '<div class="hproduct">'. association_tablinfos_intro('', 'don', $id_don, $infos ) .'</div>';
	// datation et raccourcis
	echo association_navigation_raccourcis(array(
		array('tous_les_dons', 'grille-24.png', array('dons', "id=$id_don"), array('voir_dons', 'association') ),
	) );
	debut_cadre_association('dons-24.gif', 'action_sur_les_dons');
	echo association_form_suppression('don', $id_don);
	fin_page_association();
}

?>