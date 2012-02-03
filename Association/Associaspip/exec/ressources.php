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
include_spip('inc/navigation_modules');

function exec_ressources()
{
	include_spip('inc/autoriser');
	if (!autoriser('associer', 'ressources')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:ressources_titre_liste_ressources')) ;
		association_onglets(_T('asso:titre_onglet_prets'));
		echo debut_gauche('',true);
		echo debut_boite_info(true);
		echo '<p>'._T('asso:ressources_info').'</p>';
		echo '<img src="'._DIR_PLUGIN_ASSOCIATION_ICONES.'puce-verte.gif" alt="', _T('asso:Libre'), '" /> ', _T('asso:Libre'), '<br />';
		echo '<img src="'._DIR_PLUGIN_ASSOCIATION_ICONES.'puce-orange.gif" alt="', _T('asso:En_suspend'), '" /> ', _T('asso:En_suspend'), '<br />';
		echo '<img src="'._DIR_PLUGIN_ASSOCIATION_ICONES.'puce-rouge.gif" alt="', _T('asso:reserve'), '" /> ', _T('asso:reserve'), '<br />';
		echo '<img src="'._DIR_PLUGIN_ASSOCIATION_ICONES.'puce-poubelle.gif" alt="', _T('asso:supprime'), '" /> ', _T('asso:supprime');
		echo fin_boite_info(true);
		echo bloc_des_raccourcis(association_icone(_T('asso:ressources_nav_ajouter'),  generer_url_ecrire('edit_ressource'), 'ajout_don.png'));
		echo debut_droite('',true);
		echo debut_cadre_relief('', false, '', $titre = _T('asso:ressources_titre_liste_ressources'));
		echo "<table width='100%' class='asso_tablo' id='asso_tablo_ressources'>\n";
		echo "<thead>\n<tr>";
		echo '<th>'._T('asso:entete_id').'</th>';
		echo '<th>&nbsp;</th>';
		echo '<th>'._T('asso:entete_intitule').'</th>';
		echo '<th>'._T('asso:ressources_entete_code').'</th>';
		echo '<th>'._T('asso:entete_montant').'</th>';
		echo '<th colspan="3" class="actions">'._T('asso:entete_action').'</th>';
		echo "</tr>\n</thead><tbody>";
		$query = sql_select('*', 'spip_asso_ressources', '','',  'id_ressource') ;
		while ($data = sql_fetch($query)) {
			echo '<tr>';
			echo '<td class="integer">'.$data['id_ressource'].'</td>';
			switch($data['statut']){
				case 'ok':
					$puce = 'verte'; break;
				case 'reserve':
					$puce = 'rouge'; break;
				case 'suspendu':
					$puce = 'orange'; break;
				case 'sorti':
					$puce = 'poubelle'; break;
			}
			echo '<td class="actions">'.association_bouton('','puce-'.$puce.'.gif','').'</td>';
			echo '<td class="text">'.$data['intitule'].'</td>';
			echo '<td class="text">'.$data['code'].'</td>';
			echo '<td class="decimal">'.association_prixfr($data['pu']).'</td>';
			echo '<td class="actions">', association_bouton('prets_nav_gerer', 'voir-12.png', 'prets', 'id='.$data['id_ressource']), '</td>';
			echo '<td class="actions">', association_bouton('ressources_nav_supprimer', 'poubelle-12.gif', 'action_ressources', 'id='.$data['id_ressource']), '</td>';
			echo '<td class="arial11 border1" style="text-align:center;">', association_bouton('ressources_nav_editer', 'edit-12.gif', 'edit_ressource', 'id='.$data['id_ressource']), '</td>';
			echo "</tr>\n";
		}
		echo "</tbody>\n</table>\n";
		fin_cadre_relief();
		echo fin_page_association();
	}
}

?>