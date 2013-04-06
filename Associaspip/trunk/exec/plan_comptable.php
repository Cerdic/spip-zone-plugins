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

function exec_plan_comptable() {
	if (!autoriser('gerer_compta', 'association')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		include_spip ('association_modules');
		echo association_navigation_onglets('plan_comptable', 'association');
		// notice
		echo propre(_T('asso:plan_info'));
		// datation et raccourcis
		echo association_navigation_raccourcis(array(
			array('association_infos_contacts', 'assoc_qui.png', array('association'), array('voir_profil', 'association') ),
			array('plan_nav_ajouter', 'plan_compte.png', array('edit_plan'), array('gerer_compta', 'association') ),
			array('destination_comptable', 'euro-39.gif', array('destination_comptable'), $GLOBALS['association_metas']['destinations'] ? array('gerer_compta', 'association') : FALSE ),
			array('exercices_budgetaires_titre', 'calculatrice.gif', array('exercice_comptable'), $GLOBALS['association_metas']['exercices'] ? array('gerer_compta', 'association') : FALSE ),
		) );
		debut_cadre_association('plan_compte.png',  'plan_comptable');
		$classe = _request('classe');
		if (!$classe) // si on n'a pas de classe selectionnee...
			$classe = '%'; // ...on les prend toutes
		$active = _request('active');
		if ($active=='') // si on n'a pas de filtre active dans l'environnement...
			$active = 1; // ...on affiche par defaut les comptes actifs
		$lettre = _request('lettre'); // c'est si on fait passer la classe comptable par le filtre lettre
		// FILTRES
		$filtre_classes = '<div id="asso_classe">';
		$query = sql_select('DISTINCT classe, active', 'spip_asso_plan', 'active='. sql_quote($active),'', 'classe');
		while ($data = sql_fetch($query)) {
			if ($data['classe']==$classe) {
				$filtre_classes .= ' <strong>'.$data['classe'].' </strong>';
			} else {
				$filtre_classes .= '<a href="'.generer_url_ecrire('plan_comptable', 'classe='.$data['classe']).'">'.$data['classe'].'</a> ';
			}
		}
		if ($classe=='%') {
			$filtre_classes .= ' <strong>'._T('asso:plan_entete_tous').'</strong>';
		} else {
			$filtre_classes .= ' <a href="'.generer_url_ecrire('plan_comptable').'">'._T('asso:plan_entete_tous').'</a>';
		}
		$filtre_classes .= "</div>\n";
		$filtre_activation = "<select name='active' onchange='form.submit()'>\n";
		$filtre_activation .= '<option value="1" ';
		if ($active) {
			$filtre_activation .= ' selected="selected"';
		}
		$filtre_activation .= '> '. _T('asso:plan_libelle_comptes_actifs') ."</option>\n";
		$filtre_activation .= '<option value="0" ';
		if (!$active) {
			$filtre_activation .= ' selected="selected"';
		}
		$filtre_activation .= '> '. _T('asso:plan_libelle_comptes_desactives') ."</option>\n";
		$filtre_activation .= "</select>\n";
		echo association_bloc_filtres(array(
//			'lettre' => array($lettre, 'asso_plan', 'classe', generer_url_ecrire('plan_comptable', "active=$active") ),
		), 'plan_comptable', array(
			'classe' => $filtre_classes,
			'active' => $filtre_activation,
		));
		//Affichage de la table
		echo "<table width='100%' class='asso_tablo' id='liste_asso_plan'>\n";
		echo '<tr class="row_first">';
		echo "\n<th scope='col'>". _T('asso:classe') .'</th>';
		echo "\n<th scope='col'>". _T('asso:entete_code') .'</th>';
		echo "\n<th scope='col'>". _T('asso:entete_intitule') .'</th>';
		echo "\n<th scope='col'>". _T('asso:solde_initial') .'</th>';
		echo "\n<th scope='col'>". _T('asso:entete_date') .'</th>';
		echo '<th colspan="2" class="actions">' . _T('asso:entete_actions') .'</th>';
		echo "</tr>\n";
		$query = sql_select('*', 'spip_asso_plan', 'classe LIKE '. sql_quote($classe) .' AND active=' . sql_quote($active), '', 'classe, code' );
		$classe = '';
		$i = 0;
        while ($data = sql_fetch($query)) {
			echo '<tr>';
			if ($classe!=$data['classe']) {
				if ($i!=0) {
					echo '<th colspan="8" style="border:0;"><hr class="spip" /></th>';
					echo "</tr>\n<tr>";
				} else {
					$i++;
				}
				$classe = $data['classe'];
				echo '<td class="integer">'. $data['classe'] ."</td>\n";
			} else {
				echo '<td> </td>';
			}
			echo '<td class="text">'.$data['code']."</td>\n";
			echo '<td class="text">'.$data['intitule']."</td>\n";
			echo '<td class="decimal">'. association_formater_prix($data['solde_anterieur']) ."</td>\n";
			echo '<td class="date">'. association_formater_date($data['date_anterieure'], 'dtstart') ."</td>\n";
			echo association_bouton_suppr('plan', $data['id_plan']);
			echo "\n";
			echo association_bouton_edit('plan', $data['id_plan']);
			echo "</tr>\n";
		}
		echo "</table>\n";
		fin_page_association();
	}
}

?>