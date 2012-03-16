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

function exec_destination()
{
	if (!autoriser('associer', 'comptes')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		association_onglets(_T('asso:plan_comptable'));
		// notice
		echo propre(_T('asso:destination_info')); //!\ il en faut une specifique pour cette partie
		// datation
		echo association_date_du_jour();
		echo fin_boite_info(true);
		$res = association_icone('destination_nav_ajouter',  generer_url_ecrire('edit_destination'), 'EuroOff.gif',  'creer.gif');
		$res .= association_icone('bouton_retour', generer_url_ecrire('association'), 'retour-24.png');
		echo bloc_des_raccourcis($res);
		debut_cadre_association('EuroOff.gif', 'destination_comptable');
		//Affichage de la table
		echo "<table width='100%' class='asso_tablo' id='asso_tablo_destinations'>\n";
		echo "<thead>\n<tr>";
		echo '<th>'. _T('asso:entete_id') .'</th>';
		echo '<th>'. _T('asso:entete_intitule') .'</th>';
		echo '<th>'. _T('asso:entete_utilise') .'</th>';
		echo '<th colspan="2" class="actions">'. _T('asso:entete_actions') .'</th>';
		echo "</tr>\n</thead><tbody>";
		$query = sql_select('*', 'spip_asso_destination', '', '', 'intitule');
		while ($data = sql_fetch($query)) {
			echo '<tr>';
			echo '<td class="integer">'.$data['id_destination'].'</td>';
			echo '<td class="text">'.$data['intitule'].'</td>';
			echo '<td class="integer">'. _T('asso:nombre_fois', array('nombre'=>sql_countsel('spip_asso_destination_op','id_destination='.$data['id_destination']))).'</td>';
			echo association_bouton_supprimer('destination', $data['id_destination']);
			echo association_bouton_modifier('destination', $data['id_destination']);
			echo "</tr>\n";
		}
		echo "</tbody>\n</table>\n";
		fin_page_association();
	}
}

?>