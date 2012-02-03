<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & Fran�ois de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


if (!defined('_ECRIRE_INC_VERSION'))
	return;

include_spip('inc/presentation');
include_spip ('inc/navigation_modules');

function exec_plan()
{
	include_spip('inc/autoriser');
	if (!autoriser('associer', 'comptes')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:plan_comptable')) ;
		association_onglets();
		echo debut_gauche('',true);
		echo debut_boite_info(true);
		echo propre(_T('asso:plan_info'));
		echo association_date_du_jour();
		echo fin_boite_info(true);
		$res = association_icone(_T('asso:plan_nav_ajouter'), generer_url_ecrire('edit_plan'), 'EuroOff.gif', 'creer.gif');
		$res .= association_icone(_T('asso:bouton_retour'), generer_url_ecrire('association'), 'retour-24.png');echo bloc_des_raccourcis($res);
		echo debut_droite('',true);
		debut_cadre_relief(  _DIR_PLUGIN_ASSOCIATION_ICONES.'EuroOff.gif', false, '',  '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . _T('asso:plan_comptable'));
		$classe = _request('classe');
		if (!$classe)
			$classe = '%';
		$active = _request('active');
		if ($active=='')
			$active = true; /* si on n'a pas de filtre active dans l'environnement, on affiche par defaut les comptes actifs */
		echo '<table width="100%">';
		echo '<tr>';
		echo '<td>';
		$query = sql_select('DISTINCT classe, active', 'spip_asso_plan', 'active='. sql_quote($active),'', 'classe');
		while ($data = sql_fetch($query)) {
			if ($data['classe']==$classe) {
				echo ' <strong>'.$data['classe'].' </strong>';
			} else {
				echo '<a href="'.generer_url_ecrire('plan', 'classe='.$data['classe']).'">'.$data['classe'].'</a> ';
			}
		}
		if ($classe=='%') {
			echo ' <strong>'._T('asso:plan_entete_tous').'</strong>';
		} else {
			echo ' <a href="'.generer_url_ecrire('plan').'">'._T('asso:plan_entete_tous').'</a>';
		}
		echo '</td>';
		echo '<td style="text-align:right;">';
		//Filtre active
		echo '<form method="post" action="'.generer_url_ecrire('plan').'"><div>';
		echo '<input type="hidden" name="classe" value="'.$classe.'" />';
		echo '<select name ="active" class="fondl" onchange="form.submit()">';
		echo '<option value="1" ';
		if ($active) {
			echo ' selected="selected"';
		}
		echo '> '._T('asso:plan_libelle_comptes_actifs').'</option>';
		echo '<option value="0" ';
		if (!$active) {
			echo ' selected="selected"';
		}
		echo '> '._T('asso:plan_libelle_comptes_desactives').'</option>';
		echo '</select>';
		echo '</div></form>';
		echo '</td>';
		echo '</tr></table>';
		//Affichage de la table
		echo "<table width='100%' class='asso_tablo' id='asso_tablo_plan'>\n";
		echo "<thead>\n<tr>";
		echo '<th>'. _T('asso:classe') .'</th>';
		echo '<th>'. _T('asso:code') .'</th>';
		echo '<th>'. _T('asso:intitule') .'</th>';
		echo '<th>'. _T('asso:solde_initial') .'</th>';
		echo '<th>'. _T('asso:date') .'</th>';
		echo '<th colspan="2">' . _T('asso:action') .'</th>';
		echo "</tr>\n</thead><tbody>";
		$query = sql_select('*', 'spip_asso_plan', 'classe LIKE '. sql_quote($classe) .' AND active=' . sql_quote($active), '', 'classe, code' );
		$classe = '';
		$i = 0;
        while ($data = sql_fetch($query)) {
			echo '<tr>';
			if ($classe!=$data['classe']) {
				if ($i!=0) {
					echo '<td colspan="8" style="border:0;"><hr style="color: #EEE;" /></td>';
					echo '<tr>';
				} else {
					$i++;
				}
				$classe = $data['classe'];
				echo '<td class="actions">'. $data['classe'] .'</td>';
			} else {
				echo '<td> </td>';
			}
			echo '<td class="text">'.$data['code'].'</td>';
			echo '<td class="text">'.$data['intitule'].'</td>';
			echo '<td class="decimal">'. association_prixfr($data['solde_anterieur']).'</td>';
			echo '<td class="date">'.association_datefr($data['date_anterieure']).'</td>';
			echo '<td class="actions"><a href="'.generer_url_ecrire('action_plan','id='.$data['id_plan']).'"><img src="'._DIR_PLUGIN_ASSOCIATION_ICONES.'poubelle-12.gif" title="Supprimer"></a></td>';
			echo '<td class="actions"><a href="'.generer_url_ecrire('edit_plan','id_plan='.$data['id_plan']).'"><img src="'._DIR_PLUGIN_ASSOCIATION_ICONES.'edit-12.gif" title="Modifier"></a></td>';
			echo "</tr>\n";
		}
		echo "</tbody>\n</table>\n";
		fin_cadre_relief();
		echo fin_page_association();
	}
}

?>