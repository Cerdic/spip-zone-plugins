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

function exec_categories(){
	include_spip('inc/autoriser');
	if (!autoriser('associer', 'comptes')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:categories_de_cotisations')) ;
		association_onglets();
		echo debut_gauche('',true);
		echo debut_boite_info(true);
		// quelles infos/stats ?
		echo association_date_du_jour();
		echo fin_boite_info(true);
		$res = association_icone(_T('asso:ajouter_une_categorie_de_cotisation'),  generer_url_ecrire('edit_categorie'), 'calculatrice.gif');
		$res.= association_icone(_T('asso:bouton_retour'), generer_url_ecrire('association'), 'retour-24.png');
		echo bloc_des_raccourcis($res);
		echo debut_droite('',true);
		echo debut_cadre_relief(  _DIR_PLUGIN_ASSOCIATION_ICONES.'calculatrice.gif', false, '', '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' ._T('asso:toutes_categories_de_cotisations'));
		echo "<table width='100%' class='asso_tablo' id='asso_tablo_categories'>\n";
		echo "<thead>\n<tr>";
		echo '<th>'. _T('asso:entete_id') .'</th>';
		echo '<th>'. _T('asso:categorie_entete_valeur') .'</th>';
		echo '<th>'. _T('asso:categorie_entete_libelle') .'</th>';
		echo '<th>'. _T('asso:entete_duree') .'</th>';
		echo '<th>'. _T('asso:entete_montant') .'</th>';
		echo '<th>'. _T('asso:entete_commentaire') .'</th>';
		echo '<th colspan="2" class="actions">' . _T('asso:action') .'</th>';
		echo "</tr>\n</thead><tbody>";
		$query = sql_select('*', 'spip_asso_categories', '', 'id_categorie') ;
		while ($data = sql_fetch($query)) {
			echo '<tr>';
			echo '<td class="integer">'.$data['id_categorie'].'</td>';
			echo '<td class="text">'.$data['valeur'].'</td>';
			echo '<td class="text">'.$data['libelle'].'</td>';
			echo '<td class="decimal">'. association_dureefr($data['duree'],'m') .'</td>';
			echo '<td class="decimal">'. association_prixfr($data['cotisation']) .'</td>';
			echo '<td class="text">'. propre($data['commentaires']) .'</td>';
			echo '<td class="actions">' . association_bouton('bouton_supprimer', 'poubelle-12.gif', echo fin_cadre_relief();'action_categorie','id='.$data['id_categorie']). '</td>';
			echo '<td class="actions">' . association_bouton('bouton_modifier', 'edit-12.gif', 'edit_categorie','id='.$data['id_categorie']). '</td>';
			echo "</tr>\n";
		}
		echo "</tbody>\n</table>\n";
		echo fin_cadre_relief(true);
		echo fin_page_association();
	}
}

?>