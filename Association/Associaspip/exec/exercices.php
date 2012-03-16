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

include_spip ('inc/navigation_modules');

function exec_exercices()
{
	if (!autoriser('associer', 'comptes')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		association_onglets(_T('asso:exercices_budgetaires_titre'));
		// notice
		echo '';
		// quelques stats sur les categories
		echo totauxinfos_stats('tous', 'exercices', array('entete_duree'=>"DATEDIFF(week,debut,fin)", 'mois'=>"MONTH(debut)") );
		// datation
		echo association_date_du_jour();
		echo fin_boite_info(true);
		$res = association_icone('ajouter_un_exercice',  generer_url_ecrire('edit_exercice'), 'calculatrice.gif');
		$res .= association_icone('bouton_retour', generer_url_ecrire('association'), 'retour-24.png');
		echo bloc_des_raccourcis($res);
		debut_cadre_association('calculatrice.gif', 'tous_les_exercices');
		echo "<table width='100%' class='asso_tablo' id='asso_tablo_exercices'>\n";
		echo "<thead>\n<tr>";
		echo '<th>'. _T('asso:entete_id') .'</th>';
		echo '<th>'. _T('asso:entete_intitule') .'</th>';
		echo '<th>'. _T('asso:exercice_entete_debut') .'</th>';
		echo '<th>'. _T('asso:exercice_entete_fin') .'</th>';
		echo '<th>'. _T('asso:entete_commentaire') .'</th>';
		echo '<th colspan="2" class="actions">'. _T('asso:entete_actions') .'</th>';
		echo "</tr>\n</thead><tbody>";
		$query = sql_select('*', 'spip_asso_exercices', '', 'intitule DESC') ;
		while ($data = sql_fetch($query)) {
			echo '<tr>';
			echo '<td class="integer">'.$data['id_exercice'].'</td>';
			echo '<td class="text">'.$data['intitule'].'</td>';
			echo '<td class="date">'. association_datefr($data['debut'],'dtstart') .'</td>';
			echo '<td class="date">'. association_datefr($data['fin'],'dtend') .'</td>';
			echo '<td class="text">'. propre($data['commentaire']) .'</td>';
			echo association_bouton_supprimer('exercice', 'id='.$data['id_exercice']);
			echo association_bouton_modifier('exercice', 'id='.$data['id_exercice']);
			echo "</tr>\n";
		}
		echo "</tbody>\n</table>\n";
		fin_page_association();
	}
}

?>