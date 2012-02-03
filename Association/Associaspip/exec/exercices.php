<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & Fran�ois de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)      *
 *  ajouté en 11/2011 par Marcel BOLLA ...                                 *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


if (!defined('_ECRIRE_INC_VERSION'))
	return;

include_spip('inc/presentation');
include_spip ('inc/navigation_modules');

function exec_exercices(){
	include_spip('inc/autoriser');
	if (!autoriser('associer', 'comptes')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:exercices_budgetaires_titre')) ;
		association_onglets();
		echo debut_gauche('',true);
		echo debut_boite_info(true);
		echo association_date_du_jour();
		echo fin_boite_info(true);
		$res = association_icone(_T('asso:ajouter_un_exercice'),  generer_url_ecrire('edit_exercice'), 'calculatrice.gif');
		$res .= association_icone(_T('asso:bouton_retour'), generer_url_ecrire('association'), 'retour-24.png');
		echo bloc_des_raccourcis($res);
		echo debut_droite('',true);
		echo debut_cadre_relief(  _DIR_PLUGIN_ASSOCIATION_ICONES.'calculatrice.gif', false, '', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' ._T('asso:tous_les_exercices'));
		echo "<table width='100%' class='asso_tablo' id='asso_tablo_exercices'>\n";
		echo "<thead>\n<tr>";
		echo '<th>'. _T('asso:id') .'</th>';
		echo '<th>'. _T('asso:exercice_intitule') .'</th>';
		echo '<th>'. _T('asso:exercice_commentaire') .'</th>';
		echo '<th>'. _T('asso:exercice_debut') .'</th>';
		echo '<th>'. _T('asso:exercice_fin') .'</th>';
		echo '<th colspan="2" class="actions">'. _T('asso:action') .'</th>';
		echo "</tr>\n</thead><tbody>";
		$query = sql_select('*', 'spip_asso_exercices', '', 'intitule DESC') ;
		while ($data = sql_fetch($query)) {
			echo '<tr>';
			echo '<td class="integer">'.$data['id_exercice'].'</td>';
			echo '<td class="text">'.$data['intitule'].'</td>';
			echo '<td class="text">'. propre($data['commentaire']) .'</td>';
			echo '<td class="date">'. association_datefr($data['debut'],'dtstart') .'</td>';
			echo '<td class="date">'. association_datefr($data['fin'],'dtend') .'</td>';
			echo '<td class="actions">' . association_bouton('bouton_supprimer', 'poubelle-12.gif', 'action_exercice','id='.$data['id_exercice']). '</td>';
			echo '<td class="actions">' . association_bouton('bouton_modifier', 'edit-12.gif', 'edit_exercice','id='.$data['id_exercice']). '</td>';
			echo "</tr>\n";
		}
		echo "</tbody>\n</table>\n";
		echo fin_cadre_relief(true);
		echo fin_page_association();
	}
}

?>