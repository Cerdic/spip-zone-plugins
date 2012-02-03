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


if (!defined('_ECRIRE_INC_VERSION'))
	return;

include_spip('inc/presentation');
include_spip ('inc/navigation_modules');
include_spip('inc/association_comptabilite');

function exec_dons() {
	include_spip('inc/autoriser');
	if (!autoriser('associer', 'dons')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:titre_gestion_pour_association')) ;
		association_onglets(_T('asso:titre_onglet_dons'));
		echo debut_gauche('',true);
		echo debut_boite_info(true);
		echo association_date_du_jour();
		echo fin_boite_info(true);
		echo bloc_des_raccourcis(association_icone(_T('asso:ajouter_un_don'), generer_url_ecrire('edit_don'), 'ajout_don.png'));
		echo debut_droite('',true);
		debut_cadre_relief('', false, '', $titre = _T('asso:tous_les_dons'));
		// PAGINATION ET FILTRES
		echo '<table width="100%">';
		echo '<tr>';
		echo '<td>';
		$annee= intval(_request('annee'));
		if(empty($annee)){
			$annee = date('Y');
		}
		$query = sql_select("DATE_FORMAT(date_don, '%Y')  AS annee", 'spip_asso_dons', '', 'annee', 'annee');
		while ($data = sql_fetch($query)) {
		 	if ($data['annee']==$annee) {
				echo "\n<strong>".$data['annee'].'</strong>';
			} else {
				echo ' <a href="'. generer_url_ecrire('dons', '&annee='.$data['annee']) .'">'.$data['annee']."</a>\n";
			}
		}
		echo '</td></tr>';
		echo '</table>';
		//TABLEAU
		echo "<table border='0' cellpadding='2' cellspacing='0' width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
		echo "<tr style='background-color: #DBE1C5;'>\n";
		echo '<th>'. _T('asso:entete_id') .'</th>';
		echo '<th>'. _T('asso:entete_date') .'</th>';
		echo '<th>'. _T('asso:entete_nom') .'</th>';
		echo '<th>'. _T('asso:argent') .'</th>';
		echo '<th>'. _T('asso:colis') .'</th>';
		echo '<th>'. _T('asso:valeur') .'</th>';
		echo '<th>'. _T('asso:contrepartie') .'</th>';
		echo '<th colspan="2">' . _T('asso:action') .'</th>';
		echo '</tr>';
		$association_imputation = charger_fonction('association_imputation', 'inc');
		$critere = $association_imputation('pc_dons', 'C');
		if ($critere) $critere .= ' AND ';
		$query = sql_select('*', 'spip_asso_dons AS D LEFT JOIN spip_asso_comptes AS C ON C.id_journal=D.id_don', "$critere DATE_FORMAT(date_don, '%Y') = '$annee'", '',  "id_don" ) ;
		$exec_dons = generer_url_ecrire('dons');
		while ($data = sql_fetch($query)) {
			$id_don = $data['id_don'];
			echo "<tr id='don$id_don' style='background-color: #EEEEEE;'>";
			echo "<td class='arial11 border1'>$id_don</td>\n";
			echo '<td class="date">'. association_datefr($data['date_don']) .'</td>';
			echo '<td class="text">'. association_calculer_lien_nomid($data['bienfaiteur'],$data['id_adherent']) .'</td>';
			echo '<td class="decimal">'. association_prixfr($data['argent']) .'</td>';
			echo '<td class="text">'.$data['colis'].'</td>';
			echo ($data['vu']
				? ('<td class="text" colspan="2">&nbsp;</td>')
			    : ('<td class="decimal">'.association_prixfr($data['valeur']).'</td>'
				 . '<td class="text">'. propre($data['contrepartie']) .'</td>')
				);
			echo '<td  class="actions">'. association_bouton('supprimer_le_don', 'poubelle-12.gif', 'action_dons', "id=$id_don") .'</td>';
			echo '<td class="actions">' . association_bouton('mettre_a_jour_le_don', 'edit-12.gif', 'edit_don', "id=$id_don") .'</td>';;
			echo '</tr>';
		}
		echo '</table>';
		echo fin_cadre_relief();
		echo fin_page_association();
	}
}

?>