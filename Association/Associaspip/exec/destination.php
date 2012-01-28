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


if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/presentation');
include_spip ('inc/navigation_modules');

function exec_destination(){

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

		$res = association_icone(_T('asso:destination_nav_ajouter'),  generer_url_ecrire('edit_destination'), 'EuroOff.gif',  'creer.gif');
		$res.= association_icone(_T('asso:bouton_retour'), generer_url_ecrire('association'), 'retour-24.png');
		echo bloc_des_raccourcis($res);
		echo debut_droite('',true);

		debut_cadre_relief(  _DIR_PLUGIN_ASSOCIATION_ICONES.'EuroOff.gif', false, '',  '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . _T('asso:destination_comptable'));

		//Affichage de la table
		echo "<table border='0' cellpadding='2' cellspacing='0' width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
		echo "<tr style='background-color: #DBE1C5;'>\n";
		echo '<th>' . _T('asso:intitule') . '</th>';
		echo '<th>' . _T('asso:utilise').'</th>';
		echo '<th colspan="2" style="text-align:center;">' . _T('asso:action') . '</th>';
		echo'  </tr>';
		$query = sql_select('*', 'spip_asso_destination', '', '', 'intitule');
		while ($data = sql_fetch($query)) {
			$id_destination = $data['id_destination'];
			$utilise = sql_countsel('spip_asso_destination_op','id_destination='.$id_destination);
			echo '<tr style="background-color: #EEEEEE;">';
			echo '<td class="arial11 border1">'.$data['intitule'].'</td>';
			echo '<td class="arial11 border1">'.$utilise.' fois</td>';
			echo '<td class="arial11 border1" style="text-align:center;"><a href="'.generer_url_ecrire('action_destination').'&id='.$id_destination.'"><img src="'._DIR_PLUGIN_ASSOCIATION_ICONES.'poubelle-12.gif" title="Supprimer"></a></td>';
			echo '<td class="arial11 border1" style="text-align:center;"><a href="'.generer_url_ecrire('edit_destination').'&id='.$id_destination.'"><img src="'._DIR_PLUGIN_ASSOCIATION_ICONES.'edit-12.gif" title="Modifier"></a></td>';
			echo'  </tr>';
		}
		echo'</table>';

		fin_cadre_relief();
		echo fin_gauche(), fin_page();
	}
}

?>