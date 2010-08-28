<?php 
if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/presentation');

function exec_groupes_dist() {
	if (!autoriser('')){
		include_spip('inc/minipres');
		echo minipres(_T('groupes:titre_page'),_T('groupes:minipres_corps'));
		exit;
	}
	$commencer_page = charger_fonction('commencer_page','inc');
	echo $commencer_page(_T('groupes:titre_page'));
	echo gros_titre(_T('groupes:titre_page'),'',false);

	echo debut_gauche("",true);
	echo debut_boite_info(true);
	liste_actions();
	echo fin_boite_info(true);
	echo debut_droite('',true);
	
	switch(_request('page')) {
		case "gerer_groupe":
			echo debut_cadre_relief($icone,true,'',_T('groupes:gerer_groupe_titre'));
			echo recuperer_fond('fonds/groupe_gerer', array('titre'=>_T('groupes:gerer_groupe_titre'), 'urlajax'=>$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF']));
		break;
		
		case "liste_auteurs":
			echo debut_cadre_relief($icone,true,'',_T('groupes:liste_actions_act2'));
			echo recuperer_fond('fonds/liste_auteurs', array('titre'=>_T('groupes:liste_actions_act2')));
		break;
		
		case "gerer_auteur":
			echo debut_cadre_relief($icone,true,'',_T('groupes:liste_auteurs_gerer'));
			echo recuperer_fond('fonds/auteur_gerer', array('titre'=>_T('groupes:liste_auteurs_gerer'), 'urlajax'=>$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'], 'id_auteur'=>_request('id_auteur')));
		break;
		
		case "liste_auteurs_groupe":
			echo debut_cadre_relief($icone,true,'',_T('groupes:liste_auteurs_groupe'));
			echo recuperer_fond('fonds/liste_auteurs_groupe', array('titre'=>_T('groupes:liste_auteurs_groupe'),'id_groupe'=>_request('id_groupe')));
		break;
		
		case "gerer_liens":
			echo debut_cadre_relief($icone,true,'',_T('groupes:gerer_liens'));
			echo recuperer_fond('fonds/gerer_liens', array('titre'=>_T('groupes:gerer_liens')));
		break;
		
		default:
			echo debut_cadre_relief($icone,true,'',_T('groupes:liste_actions_titre'));
			liste_actions();
		break;
	}       
	echo fin_cadre_relief(true);   
	echo fin_gauche();
	echo fin_page();
}

function liste_actions() {
	echo "<ul>";
	echo '<li><a href="?exec=groupes&page=gerer_groupe">'._T('groupes:liste_actions_act1').'</a></li>';
	echo '<li><a href="?exec=groupes&page=liste_auteurs">'._T('groupes:liste_actions_act2').'</a></li>';
	echo '<li><a href="?exec=groupes&page=gerer_liens">'._T('groupes:gerer_liens').'</a></li>';
	echo "</ul>";
}
?>