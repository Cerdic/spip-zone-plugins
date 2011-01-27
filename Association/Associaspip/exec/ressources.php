<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & François de Montlivault (V1)       *
 *  Copyright (c) 2010 Emmanuel Saint-James                     (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


if (!defined("_ECRIRE_INC_VERSION")) return;
	
include_spip('inc/presentation');
include_spip('inc/navigation_modules');
	
function exec_ressources(){
		
	include_spip('inc/autoriser');
	if (!autoriser('associer', 'ressources')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		
		$url_ressources = generer_url_ecrire('ressources');
		$url_ajout_ressource=generer_url_ecrire('edit_ressource');
		
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:ressources_titre_liste_ressources')) ;
		
		association_onglets();
		
		echo debut_gauche("",true);
		
		echo debut_boite_info(true);
		echo '<p>'._T('asso:ressources_info').'</p>';
		echo '<img src="'._DIR_PLUGIN_ASSOCIATION_ICONES.'puce-verte.gif" alt=" " /> Libre<br />';
		echo '<img src="'._DIR_PLUGIN_ASSOCIATION_ICONES.'puce-orange.gif" alt=" " /> En suspend<br />';
		echo '<img src="'._DIR_PLUGIN_ASSOCIATION_ICONES.'puce-rouge.gif" alt=" " />', _T('asso:reserve'), '<br />';
		echo '<img src="'._DIR_PLUGIN_ASSOCIATION_ICONES.'puce-poubelle.gif" alt=" " />', _T('asso:supprime');
		echo fin_boite_info(true);
		
		echo bloc_des_raccourcis(association_icone(_T('asso:ressources_nav_ajouter'),  $url_ajout_ressource, 'ajout_don.png'));

		echo debut_droite("",true);
		echo debut_cadre_relief(  "", false, "", $titre = _T('asso:ressources_titre_liste_ressources'));
		
		echo "<table border='0' cellpadding='2' cellspacing='0' width='100%' class='arial2' style='border: 1px solid #aaaaaa;'>\n";
		echo "<tr style='background-color: #DBE1C5;'>\n";
		echo '<td>&nbsp;</td>';
		echo '<th>'._T('asso:ressources_entete_intitule').'</th>';
		echo '<th>'._T('asso:ressources_entete_code').'</th>';
		echo '<th>'._T('asso:ressources_entete_montant').'</th>';
		echo '<th colspan="4" style="text-align:center;">'._T('asso:entete_action').'</th>';
		echo "</tr>\n";
		$query = sql_select('*', 'spip_asso_ressources', '','',  "id_ressource" ) ;
		while ($data = sql_fetch($query)) {
			echo '<tr style="background-color: #EEEEEE;">';		
			echo "<td class='arial11 border1'>\n";
			switch($data['statut']){
				case "ok": $puce= "verte"; break;
				case "reserve": $puce= "rouge"; break;
				case "suspendu": $puce="orange"; break;
				case "sorti": $puce="poubelle"; break;	   
			}
			echo '<img src="'._DIR_PLUGIN_ASSOCIATION_ICONES.'puce-'.$puce.'.gif" alt=" " /></td>';
			echo "<td class='arial11 border1'>\n".$data['intitule'].'</td>';
			echo "<td class='arial11 border1'>\n".$data['code'].'</td>';
			echo '<td class="arial11 border1" style="text-align:center;">'.number_format($data['pu'], 2, ',', ' ')."</td>\n";
			
			echo '<td class="arial11 border1"></td>';
			echo '<td class="arial11 border1">', association_bouton(_T('asso:prets_nav_gerer'), 'voir-12.png', 'prets', 'id='.$data['id_ressource']), "</td>\n";

			echo '<td class="arial11 border1" style="text-align:center;">', association_bouton(_T('asso:ressources_nav_supprimer'), 'poubelle-12.gif', 'action_ressources', 'id='.$data['id_ressource']), "</td>\n";
			echo '<td class="arial11 border1" style="text-align:center;">', association_bouton(_T('asso:ressources_nav_editer'), 'edit-12.gif', 'edit_ressource', 'id='.$data['id_ressource']), "</td>\n";
			echo'  </tr>';
		}     
		echo'</table>';
		
		fin_cadre_relief();  
		echo fin_page_association();
	}
}
?>
