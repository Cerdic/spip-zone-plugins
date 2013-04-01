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

function exec_destination_comptable(){

	include_spip('inc/autoriser');
	if (!autoriser('associer', 'comptes')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {

		$url_destination = generer_url_ecrire('destination_comptable');
		$url_edit_destination=generer_url_ecrire('edit_destination');
		$url_action_destination=generer_url_ecrire('action_destination');

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:plan_comptable')) ;

		association_onglets();

		echo debut_gauche("",true);

		echo debut_boite_info(true);

		echo association_date_du_jour();
		echo propre(_T('asso:plan_info'));
		echo fin_boite_info(true);

		echo bloc_des_raccourcis(association_icone(_T('asso:destination_nav_ajouter'),  generer_url_ecrire('edit_destination'), 'EuroOff.gif',  'creer.gif'));

		echo debut_droite("",true);

		debut_cadre_relief(  _DIR_PLUGIN_ASSOCIATION_ICONES."EuroOff.gif", false, "",  '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . _T('asso:destination_comptable'));

		//Affichage de la table
		echo "<table border='0' cellpadding='2' cellspacing='0' width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
		echo "<tr style='background-color: #DBE1C5;'>\n";
		echo '<th>' . _T('asso:intitule') . "</th>\n";
		echo '<th colspan="2" style="text-align:center;">' . _T('asso:action') . "</th>\n";
		echo'  </tr>';
		$query = sql_select('*', 'spip_asso_destination', '', '', "intitule" );
		while ($data = sql_fetch($query)) {
			echo '<tr style="background-color: #EEEEEE;">';
			echo '<td class="arial11 border1">'.$data['intitule'].'</td>';
			echo '<td class="arial11 border1" style="text-align:center;"><a href="'.$url_action_destination.'&id='.$data['id_destination'].'"><img src="'._DIR_PLUGIN_ASSOCIATION_ICONES.'poubelle-12.gif" title="Supprimer"></a></td>';
			echo '<td class="arial11 border1" style="text-align:center;"><a href="'.$url_edit_destination.'&id='.$data['id_destination'].'"><img src="'._DIR_PLUGIN_ASSOCIATION_ICONES.'edit-12.gif" title="Modifier"></a></td>';
			echo'  </tr>';
		}
		echo'</table>';

		fin_cadre_relief();

		echo fin_gauche(), fin_page();
	}
}
?>
