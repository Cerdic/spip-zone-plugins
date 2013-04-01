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


if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/presentation');
include_spip ('inc/navigation_modules');

function exec_plan_comptable(){

	include_spip('inc/autoriser');
	if (!autoriser('associer', 'comptes')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {

		$url_plan = generer_url_ecrire('plan_comptable');
		$url_edit_plan=generer_url_ecrire('edit_plan');
		$url_action_plan=generer_url_ecrire('action_plan');

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:plan_comptable')) ;

		association_onglets();

		echo debut_gauche("",true);

		echo debut_boite_info(true);

		echo association_date_du_jour();
		echo propre(_T('asso:plan_info'));
		echo fin_boite_info(true);

		echo bloc_des_raccourcis(association_icone(_T('asso:plan_nav_ajouter'),  generer_url_ecrire('edit_plan'), 'EuroOff.gif',  'creer.gif'));

		echo debut_droite("",true);

		debut_cadre_relief(  _DIR_PLUGIN_ASSOCIATION_ICONES."EuroOff.gif", false, "",  '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . _T('asso:plan_comptable'));

		$classe = _request('classe');
		if (!$classe) $classe = '%';
		$active = _request('active');
		if ($active=='') $active = true; /* si on n'a pas de filtre active dans l'environnement, on affiche par defaut les comptes actifs */

		echo '<table width="100%">';
		echo '<tr>';
		echo '<td>';

		$query = sql_select('DISTINCT classe, active', 'spip_asso_plan', "active=". sql_quote($active),'', "classe");

		while ($data = sql_fetch($query)) {
			if ($data['classe']==$class)	{echo ' <strong>'.$data['classe'].' </strong>';}
			else {echo '<a href="'.$url_plan.'&classe='.$data['classe'].'">'.$data['classe'].'</a> ';}
		}
		if ($classe == "%") { echo ' <strong>'._T('asso:plan_entete_tous').'</strong>'; }
		else { echo ' <a href="'.$url_plan.'">'._T('asso:plan_entete_tous').'</a>'; }
		echo '</td>';

		echo '<td style="text-align:right;">';

		//Filtre active
		echo '<form method="post" action="'.$url_plan.'"><div>';
		echo '<input type="hidden" name="classe" value="'.$classe.'" />';
		echo '<select name ="active" class="fondl" onchange="form.submit()">';
		echo '<option value="1" ';
		if ($active) {echo ' selected="selected"';}
		echo '> '._T('asso:plan_libelle_comptes_actifs').'</option>';
		echo '<option value="0" ';
		if (!$active) {echo ' selected="selected"';}
			echo '> '._T('asso:plan_libelle_comptes_desactives').'</option>';
		echo '</select>';
		echo '</div></form>';
		echo '</td>';
		echo '</tr></table>';

		//Affichage de la table
		echo "<table border='0' cellpadding='2' cellspacing='0' width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
		echo "<tr style='background-color: #DBE1C5;'>\n";
		echo '<th>' . _T('asso:classe') . "</th>\n";
		echo '<th>' . _T('asso:code') . "</th>\n";
		echo '<th>' . _T('asso:intitule') . "</th>\n";
		echo '<th>' . _T('asso:reference') . "</th>\n";
		echo '<th style="text-align:right;">' . _T('asso:solde_initial') . "</th>\n";
		echo '<th>' . _T('asso:date') . "</th>\n";
		echo '<th colspan="2" style="text-align:center;">' . _T('asso:action') . "</th>\n";
		echo'  </tr>';
		$query = sql_select('*', 'spip_asso_plan', "classe LIKE " . sql_quote($classe) ." AND active=" . sql_quote($active),'', "classe, code" );
		while ($data = sql_fetch($query)) {
			echo '<tr style="background-color: #EEEEEE;">';
			echo '<td class="arial11 border1" style="text-align:right;">'.$data['classe'].'</td>';
			echo '<td class="arial11 border1">'.$data['code'].'</td>';
			echo '<td class="arial11 border1">'.$data['intitule'].'</td>';
			echo '<td class="arial11 border1">'.$data['reference'].'</td>';
			echo '<td class="arial11 border1" style="text-align:right;">'.number_format($data['solde_anterieur'], 2, ',', ' ').' &euro;</td>';
			echo '<td class="arial11 border1">'.association_datefr($data['date_anterieure']).'</td>';
			echo '<td class="arial11 border1" style="text-align:center;"><a href="'.$url_action_plan.'&id='.$data['id_plan'].'"><img src="'._DIR_PLUGIN_ASSOCIATION_ICONES.'poubelle-12.gif" title="Supprimer"></a></td>';
			echo '<td class="arial11 border1" style="text-align:center;"><a href="'.$url_edit_plan.'&id_plan='.$data['id_plan'].'"><img src="'._DIR_PLUGIN_ASSOCIATION_ICONES.'edit-12.gif" title="Modifier"></a></td>';
			echo'  </tr>';
		}
		echo'</table>';

		fin_cadre_relief();

		echo fin_page_association();
	}
}
?>
