<?php
/*
 MERCURE 
 TCHAT POUR LES REDACTEURS DANS L'ESPACE PRIVE DE SPIP
 v. 0.10 - 07/2009 - SPIP 1.9.2
 Patrick Kuchard - www.encyclopedie-incomplete.com
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');

function exec_mercure_conf() {

# elements spip
global 	$connect_statut,
		$connect_toutes_rubriques,
		$connect_id_auteur,
		$couleur_claire, $couleur_foncee;

#
# function requises ...
#
include_spip("inc/mercure_init");
include_spip("inc/form_config");

#
# affichage
#
$commencer_page = charger_fonction('commencer_page', 'inc');
echo $commencer_page(_T('mercure:mercure_titre'), "suivi", "mercure_conf");
echo "<a name='haut_page'></a>";


# Vérifier si Admin principal du site
if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {
	echo _T('avis_non_acces_page');
	echo fin_gauche(), fin_page();
	exit;
}

debut_gauche();

	echo entete_page();

echo "<p class='space_20'></p>";
echo "<p class='space_20'></p>";


	echo signature_plugin();

/*---------------------------------------------------------------------------*\
atteindre page php info
\*---------------------------------------------------------------------------*/
	echo "<p class='space_10'></p>";
	debut_boite_info();
		echo "\n<a href='".generer_url_ecrire("info")."'>"._T('mercure:page_phpinfo')."</a>\n";
	fin_boite_info();


/*---------------------------------------------------------------------------*\
version de mysql du serveur :
\*---------------------------------------------------------------------------*/
	echo "<p class='space_10'></p>";
	debut_boite_info();
		$vers = mysql_query("select version()");
		$rep = mysql_fetch_array($vers);
		echo "MySQL v. ".$rep[0];
	fin_boite_info();

echo "<p class='space_10'></p>";
	debut_boite_info();
	echo _T('mercure:mercure_avertissement');
	fin_boite_info();

debut_droite();

/*---------------------------------------------------------------------------*\
Onglets pages sup.
\*---------------------------------------------------------------------------*/
	echo onglets_mercure(_request('exec'));

/*---------------------------------------------------------------------------*\
tableau de conf
\*---------------------------------------------------------------------------*/
	$aff = '<p class="space_20"></p>';

	$aff.= debut_boite_info(true);
	if($GLOBALS['mercure']['first_use']){
    $aff.= _T('mercure:configuration_first_use');
  }else{
    $aff.= _T('mercure:configuration_after_use');
  }
	$aff.= fin_boite_info()
	  . '<p class="space_20"></p>';
	
	$aff.= '<form action="'.generer_url_action('mercure_config').'" method="post">'
		. '<input type="hidden" name="redirect" value="'.generer_url_ecrire("mercure_conf").'" />'
		. '<input type="hidden" name="hash" value="'.calculer_action_auteur("mercure_config-rien").'" />'
		. '<input type="hidden" name="id_auteur" value="'.$connect_id_auteur.'" />'			
		. '<input type="hidden" name="arg" value="rien" />'
		. '<input type="hidden" name="version_plug" value="'.$GLOBALS['mercure_plug_version'].'" />'
		. '<input type="hidden" name="mercure_first_use" value="FALSE" />';

	$aff.= debut_cadre_trait_couleur('',true,'',_T('mercure:configuration_general'))
		. '<table width="100%" cellpadding="2" cellspacing="0" border="0" class="table_conf">'
		. '<tr><td class="col_info">'._T('mercure:conf_general_menu_question').'</td>'
		. '<td class="col_val">'
		. '<input type="radio" name="mercure_menu" value="accueil" '.($GLOBALS['mercure']['menu'] == 'accueil' ? 'checked':'').' />'._T('mercure:conf_general_menu_accueil').'<br />'
		. '<input type="radio" name="mercure_menu" value="naviguer" '.($GLOBALS['mercure']['menu'] == 'naviguer' ? 'checked':'').' />'._T('mercure:conf_general_menu_naviguer').'<br />'
		. '<input type="radio" name="mercure_menu" value="forum" '.($GLOBALS['mercure']['menu'] == 'forum' ? 'checked':'').' />'._T('mercure:conf_general_menu_forum').'<br />'
		. '<input type="radio" name="mercure_menu" value="auteurs" '.($GLOBALS['mercure']['menu'] == 'auteurs' ? 'checked':'').' />'._T('mercure:conf_general_menu_auteurs').'<br />'
		. '<input type="radio" name="mercure_menu" value="statistiques_visites" '.($GLOBALS['mercure']['menu'] == 'statistiques_visites' ? 'checked':'').' />'._T('mercure:conf_general_menu_statistiques_visites').'<br />'
		. '<input type="radio" name="mercure_menu" value="configuration" '.($GLOBALS['mercure']['menu'] == 'configuration' ? 'checked':'').' />'._T('mercure:conf_general_menu_configuration').'<br />'
		. '<input type="radio" name="mercure_menu" value="aide_index" '.($GLOBALS['mercure']['menu'] == 'aide_index' ? 'checked':'').' />'._T('mercure:conf_general_menu_aide_index').'<br />'
		. '<input type="radio" name="mercure_menu" value="visiter" '.($GLOBALS['mercure']['menu'] == 'visiter' ? 'checked':'').' />'._T('mercure:conf_general_menu_visiter').'<br />'
		. '</td></tr>'
/*
		. '<tr><td class="col_info">'._T('mercure:conf_maj_connectes_question').'</td>'
		. '<td class="col_val">'
		. '<input type="text" size="4" name="mercure_maj_connectes" value="'.$GLOBALS['mercure']['maj_connectes'].'" class="fondl" /><br />'
		. '</td></tr>'
*/	
		. '</table>'
		. fin_cadre_trait_couleur(true);

	$aff.= debut_cadre_trait_couleur('',true,'',_T('mercure:configuration_messages'))
		. '<table width="100%" cellpadding="2" cellspacing="0" border="0" class="table_conf">'
		. '<tr><td class="col_info">'._T('mercure:conf_notify_question').'</td>'
		. '<td class="col_val">'
		. '<input type="radio" name="mercure_notify" value="on" '.($GLOBALS['mercure']['notify'] == 'on' ? 'checked':'').' />'._T('mercure:conf_oui').'<br />'
		. '<input type="radio" name="mercure_notify" value="off" '.($GLOBALS['mercure']['notify'] == 'off' ? 'checked':'').' />'._T('mercure:conf_non').'<br />'
		. '</td></tr>'
		. '<tr><td class="col_info">'._T('mercure:conf_volume_question').'</td>'
		. '<td class="col_val">'
		. '<input type="text" size="4" name="mercure_notify_volume" value="'.$GLOBALS['mercure']['notify_volume'].'" class="fondl" /><br />'
		. '</td></tr>'
		. '<tr><td class="col_info">'._T('mercure:conf_notify_sound_question').'</td>'
		. '<td class="col_val">';	
    for($i=1;$i<12;$i++) $aff.= '<input type="radio" NAME="mercure_notify_sound" VALUE="'.$i.'" '.($GLOBALS['mercure']['notify_sound'] == $i ? 'checked':'').'> Sound '.$i.' <a href="'._DIR_SOUND_MERCURE.'notify_'.$i.'.wav" target="_blank"><img border="0" src="'._DIR_IMG_MERCURE.'play.png"> Play</a><br />';  
    $aff.= '</td></tr>'
		. '<tr><td class="col_info">'._T('mercure:conf_notify_avertissement').'</td>'
		. '<td class="col_val">'
		. '</td></tr>'    
		. '<tr><td class="col_info">'._T('mercure:conf_refresh_question').'</td>'
		. '<td class="col_val">'
		. '<input type="text" size="4" name="mercure_refresh" value="'.$GLOBALS['mercure']['refresh'].'" class="fondl" /><br />'
		. '</td></tr>'
		. '<tr><td class="col_info">'._T('mercure:conf_nb_lignes_question').'</td>'
		. '<td class="col_val">'
		. '<input type="text" size="4" name="mercure_nb_lignes" value="'.$GLOBALS['mercure']['nb_lignes'].'" class="fondl" /><br />'
		. '</td></tr>'
		. '</table>'
		. fin_cadre_trait_couleur(true);

  if($db = sqlite_open(':memory:')){
	  $info_bdd = _T('mercure:conf_bdd_info_sqlite_ok');
    sqlite_close($db);    
  }else{
	  $info_bdd = _T('mercure:conf_bdd_info_sqlite_nok');
  }

	$aff.= debut_cadre_trait_couleur('',true,'',_T('mercure:configuration_bdd'))
		. '<table width="100%" cellpadding="2" cellspacing="0" border="0" class="table_conf">'
/*
		. '<tr><td class="col_info">'._T('mercure:conf_bdd_question').'</td>'
		. '<td class="col_val">'
		. '<input type="radio" name="mercure_bdd" value="txt" '.($GLOBALS['mercure']['bdd'] == 'txt' ? 'checked':'').'" class="fondl" />'._T('mercure:conf_general_bdd_txt').'<br />'
		. '<input type="radio" name="mercure_bdd" value="bdd" '.($GLOBALS['mercure']['bdd'] == 'bdd' ? 'checked':'').'" class="fondl" />'.str_replace('%info_sqlite%',$info_bdd,_T('mercure:conf_general_bdd_bdd')).'<br />'
		. '</td></tr>'
*/
		. '<tr><td class="col_info">'._T('mercure:conf_bdd_item_limit').'</td>'
		. '<td class="col_val">'
		. '<input type="text" size="4" name="mercure_bdd_item_limit" value="'.$GLOBALS['mercure']['item_limit'].'" class="fondl" /><br />'
		. '</td></tr>'
		. '<tr><td class="col_info">'._T('mercure:conf_bdd_purge_question').'</td>'
		. '<td class="col_val">'
		. '<input type="text" size="4" name="mercure_bdd_time_limit" value="'.$GLOBALS['mercure']['time_limit'].'" class="fondl" /><br />'
		. '</td></tr>'
		. '</table>'
		. fin_cadre_trait_couleur(true);

	$aff.= '<div align="right"><input type="submit" value="'._T('valider').'" class="fondo" /></div>'
		. '</form>';

	echo $aff;


# retour haut de page
echo bouton_retour_haut();

echo fin_gauche(), fin_page();

} // fin exec
?>
