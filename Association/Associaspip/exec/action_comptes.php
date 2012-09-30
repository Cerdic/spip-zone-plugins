<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 (v1) Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010--2011 (v2) Emmanuel Saint-James & Jeannot Lapin
 *
 *  @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

function exec_action_comptes()
{
	if (!autoriser('associer', 'comptes')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		include_spip('inc/navigation_modules');
		$id_compte = association_passeparam_id('compte');
		onglets_association('titre_onglet_comptes', 'comptes');
		// info
		echo _T('asso:confirmation');
		// datation et raccourcis
		raccourcis_association('');
		debut_cadre_association('finances-32.jpg', 'operations_comptables');
		if ($id_compte) { // SUPPRESSION PROVISOIRE OPERATION
			echo '<p><strong>' . _T('asso:vous_vous_appretez_a_effacer_la_ligne_de_compte'). " $id_compte</strong></p>";
			$res = action_comptes_ligne("id_compte=$id_compte");
			$res .= '<p class="boutons"><input type="submit" value="'._T('asso:bouton_confirmer').'" /></p>';
			echo redirige_action_post('supprimer_comptes', $id_compte, 'comptes', '', $res);

		} else { // VALIDATION PROVISOIRE COMPTE
			echo '<p>'. _T('asso:vous_vous_appretez_a_valider_les_operations') .'</p>';
			$res = action_comptes_ligne(sql_in("id_compte", association_recuperer_liste('valide', true) ) );
			$res .= '<p>'. _T('asso:apres_confirmation_vous_ne_pourrez_plus_modifier_ces_operations') .'</p>';
			$res .= '<p class="boutons"><input type="submit" value="'._T('asso:bouton_confirmer').'" /></p>';
			// count est du bruit de fond de secu
			echo redirige_action_post('valider_comptes', count($_REQUEST['valide']), 'comptes', '', $res);
		}
		fin_page_association();
	}
}

function action_comptes_ligne($where)
{
	$res = '';
	$query = sql_select('*', 'spip_asso_comptes', $where);
	while($data = sql_fetch($query)) {
		$res .= "<tr>"
		. '<td><strong>'. association_formater_date($data['date']).'</strong></td>'
		. '<td><strong>'.propre($data['justification']).'</strong></td>'
		. "<td><input type=checkbox name='definitif[]' value='".$data['id_compte']."' checked='checked' /></td></tr>\n";
	}
	return $res ? "<table>$res</table>" : '';
}

?>