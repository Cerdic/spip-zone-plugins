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

include_spip ('inc/navigation_modules');

function exec_plan_comptable()
{
	if (!autoriser('associer', 'comptes')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		onglets_association('plan_comptable', 'association');
		// notice
		echo propre(_T('asso:plan_info'));
		// datation et raccourcis
		raccourcis_association('association', array(
			'plan_nav_ajouter' => array('euro-39.gif', 'edit_plan'),
		));
		debut_cadre_association('euro-39.gif',  'plan_comptable');
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
				echo '<a href="'.generer_url_ecrire('plan_comptable', 'classe='.$data['classe']).'">'.$data['classe'].'</a> ';
			}
		}
		if ($classe=='%') {
			echo ' <strong>'._T('asso:plan_entete_tous').'</strong>';
		} else {
			echo ' <a href="'.generer_url_ecrire('plan_comptable').'">'._T('asso:plan_entete_tous').'</a>';
		}
		echo '</td>';
		echo '<td style="text-align:right;">';
		//Filtre active
		echo '<form method="post" action="'.generer_url_ecrire('plan_comptable').'"><div>';
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
		echo '<th>'. _T('asso:entete_code') .'</th>';
		echo '<th>'. _T('asso:entete_intitule') .'</th>';
		echo '<th>'. _T('asso:solde_initial') .'</th>';
		echo '<th>'. _T('asso:entete_date') .'</th>';
		echo '<th colspan="2" class="actions">' . _T('asso:entete_actions') .'</th>';
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
				echo '<td class="integer">'. $data['classe'] .'</td>';
			} else {
				echo '<td> </td>';
			}
			echo '<td class="text">'.$data['code'].'</td>';
			echo '<td class="text">'.$data['intitule'].'</td>';
			echo '<td class="decimal">'. association_formater_prix($data['solde_anterieur']) .'</td>';
			echo '<td class="date">'. association_formater_date($data['date_anterieure'], 'dtstart') .'</td>';
			echo association_bouton_suppr('plan', $data['id_plan']);
			echo association_bouton_edit('plan', $data['id_plan']);
			echo "</tr>\n";
		}
		echo "</tbody>\n</table>\n";
		fin_page_association();
	}
}

?>