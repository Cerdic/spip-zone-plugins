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
	include_spip('inc/presentation');
	include_spip ('inc/navigation_modules');

function exec_action_comptes(){
		
	include_spip('inc/autoriser');
	if (!autoriser('associer', 'comptes')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		
		$id_compte= intval(_request('id'));
		
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:titre_gestion_pour_association'));
		association_onglets();
		echo debut_gauche('', true);
		echo debut_boite_info(true);
		echo association_date_du_jour();	
		echo fin_boite_info(true);
		echo association_retour();
		echo debut_droite('', true);
			
		debut_cadre_relief(  "", false, "",  _T('asso:operations_comptables'));
		//SUPPRESSION PROVISOIRE OPERATION
		if ($id_compte) {
			
			echo '<p><strong>' . _T('asso:vous_vous_appretez_a_effacer_la_ligne_de_compte'). ' ' . $id_compte . '</strong></p>';

			$res = action_comptes_ligne("id_compte=$id_compte");
			$res .= '<p style="float:right;"><input type="submit" value="'._T('asso:bouton_confirmer').'" class="fondo"></p>';

			echo redirige_action_post('supprimer_comptes', $id_compte, 'comptes', '', $res);

		} else {
		
		//---------------------------- 
		//VALIDATION PROVISOIRE COMPTE		
			
			echo '<p>' . _T('asso:vous_vous_appretez_a_valider_les_operations') .  '</p>';

			$res = action_comptes_ligne(sql_in("id_compte", $_REQUEST['valide']));
			$res .= '<p>' . _T('asso:apres_confirmation_vous_ne_pourrez_plus_modifier_ces_operations') . '</p>';
			
			$res .= '<p style="float:right;"><input type="submit" value="'._T('asso:bouton_confirmer').'" class="fondo" /></p>';

			// count est du bruit de fond de secu
			echo redirige_action_post('valider_comptes', count($_REQUEST['valide']), 'comptes', "", $res);
		}
		fin_cadre_relief();  
		echo fin_page_association();
	}
}

function action_comptes_ligne($where)
{
	$res = '';
	$query = sql_select('*', 'spip_asso_comptes', $where);
	while($data = sql_fetch($query)) {
		$res .= "<tr>"
		. '<td><strong>'.association_datefr($data['date']).'</strong></td>'
		. '<td><strong>'.propre($data['justification']).'</strong></td>'
		. "<td><input type=checkbox name='definitif[]' value='".$data['id_compte']."' checked='checked' /></td></tr>\n";
	}
	return $res ? "<table>$res</table>" : '';
}
?>
