<?php 
if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/presentation');
function exec_ldaplus_dist() {
	if (!autoriser('')){
		include_spip('inc/minipres');
		echo minipres(_T('ldaplus:titre_page'),_T('ldaplus:minipres_corps'));
		exit;
	}
	$commencer_page = charger_fonction('commencer_page','inc');
	echo $commencer_page(_T('ldaplus:titre_page'));
	echo gros_titre(_T('ldaplus:titre_page'),'',false);

	echo debut_gauche("",true);
	echo debut_boite_info(true);
	liste_actions();
	echo fin_boite_info(true);
	echo debut_droite('',true);
	
	switch(_request('page')) {
		case "gerer_infos":
			echo debut_cadre_relief($icone,true,'',_T('ldaplus:liste_act1'));
			echo recuperer_fond('fonds/gerer_infos', array('titre'=>_T('ldaplus:liste_actions_act1')));
		break;
		
		case "gerer_memberof" :
			echo debut_cadre_relief($icone,true,'',_T('ldaplus:liste_act2'));
			echo recuperer_fond('fonds/gerer_memberof', array('titre'=>_T('ldaplus:liste_actions_act2')));
		break;
		
		default:
			liste_actions();
		break;
	}       
	echo fin_cadre_relief(true);   
	echo fin_gauche();
	echo fin_page();
}

function liste_actions() {
	echo '<ul>';
	echo '<li><a href="?exec=ldaplus&page=gerer_infos">'._T('ldaplus:liste_act1').'</a></li>';
	echo '<li><a href="?exec=ldaplus&page=gerer_memberof">'._T('ldaplus:liste_act2').'</a></li>';
	echo '</ul>';
}
?>