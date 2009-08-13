<?php

/*
 MERCURE 
 TCHAT POUR LES REDACTEURS DANS L'ESPACE PRIVE DE SPIP
 v. 0.10 - 07/2009 - SPIP 1.9.2
 Patrick Kuchard - www.encyclopedie-incomplete.com

+--------------------------------------------+
| formulaire de configuration                |
+--------------------------------------------+
*/

function formulaire_configuration_mercure(){
	# elements spip
	global 	
      $connect_statut,
			$connect_toutes_rubriques,
			$connect_id_auteur;
	
	$aff = '<p class="space_20"></p>';
	
	$aff.= '<form action="'.generer_url_action('mercure_config').'" method="post">'
		. '<input type="hidden" name="hash" value="'.calculer_action_auteur("mercure_config-rien").'" />'
		. '<input type="hidden" name="id_auteur" value="'.$connect_id_auteur.'" />'		
		. '<input type="hidden" name="arg" value="rien" />'
		. '<input type="hidden" name="version_plug" value="'.$GLOBALS['mercure_plug_version'].'" />'
		. '<input type="hidden" name="redirect" value="'.generer_url_ecrire("mercure_conf").'" />'
		. '<input type="hidden" name="first_use" value="FALSE" />';
		
  $aff.= 'COUCOU';		
		
	$aff.= debut_cadre_trait_couleur('',true,'',_T('mercure:configuration_general'))
		. '<table width="100%" cellpadding="2" cellspacing="0" border="0" class="table_conf">'
		. '<tr><td class="col_info">'._T('mercure:conf_menu_question').'</td>'
		. '<td class="col_val">'
		. '<input type="radio" name="mercure_menu" value="accueil" '.($GLOBALS['mercure']['mercure_menu'] == 'accueil' ? 'checked':'').'" class="fondl" />'._T('mercure:conf_general_menu_accueil').'<br />'
		. '<input type="radio" name="mercure_menu" value="naviguer" '.($GLOBALS['mercure']['mercure_menu'] == 'naviguer' ? 'checked':'').'" class="fondl" />'._T('mercure:conf_general_menu_naviguer').'<br />'
		. '<input type="radio" name="mercure_menu" value="forum" '.($GLOBALS['mercure']['mercure_menu'] == 'forum' ? 'checked':'').'" class="fondl" />'._T('mercure:conf_general_menu_forum').'<br />'
		. '<input type="radio" name="mercure_menu" value="auteurs" '.($GLOBALS['mercure']['mercure_menu'] == 'auteurs' ? 'checked':'').'" class="fondl" />'._T('mercure:conf_general_menu_auteurs').'<br />'
		. '<input type="radio" name="mercure_menu" value="statistiques_visites" '.($GLOBALS['mercure']['mercure_menu'] == 'statistiques_visites' ? 'checked':'').'" class="fondl" />'._T('mercure:conf_general_menu_statistiques_visites').'<br />'
		. '<input type="radio" name="mercure_menu" value="configuration" '.($GLOBALS['mercure']['mercure_menu'] == 'configuration' ? 'checked':'').'" class="fondl" />'._T('mercure:conf_general_menu_configuration').'<br />'
		. '<input type="radio" name="mercure_menu" value="aide_index" '.($GLOBALS['mercure']['mercure_menu'] == 'aide_index' ? 'checked':'').'" class="fondl" />'._T('mercure:conf_general_menu_aide_index').'<br />'
		. '<input type="radio" name="mercure_menu" value="visiter" '.($GLOBALS['mercure']['mercure_menu'] == 'visiter' ? 'checked':'').'" class="fondl" />'._T('mercure:conf_general_menu_visiter').'<br />'
		. '</td></tr>'
		. '<tr><td class="col_info">'._T('mercure:conf_maj_connectes_question').'</td>'
		. '<td class="col_val">'
		. '<input type="text" size="4" name="mercure_maj_connectes" value="'.$GLOBALS['mercure']['maj_connectes'].'" class="fondl" /><br />'
		. '</td></tr>'
	
		. '</table>'
		. fin_cadre_trait_couleur(true);

	$aff.= debut_cadre_trait_couleur('',true,'',_T('mercure:configuration_messages'))
		. '<table width="100%" cellpadding="2" cellspacing="0" border="0" class="table_conf">'
		. '<tr><td class="col_info">'._T('mercure:conf_notify_question').'</td>'
		. '<td class="col_val">'
		. '<input type="radio" name="mercure_notify" value="TRUE" '.($GLOBALS['mercure']['notify'] == 'TRUE' ? 'checked':'').'" class="fondl" />'._T('mercure:conf_oui').'<br />'
		. '<input type="radio" name="mercure_notify" value="FALSE" '.($GLOBALS['mercure']['notify'] == 'FALSE' ? 'checked':'').'" class="fondl" />'._T('mercure:conf_non').'<br />'
		. '</td></tr>'
		. '<tr><td class="col_info">'._T('mercure:conf_volume_question').'</td>'
		. '<td class="col_val">'
		. '<input type="text" size="4" name="mercure_notify_volume" value="'.$GLOBALS['mercure']['notify_volume'].'" class="fondl" /><br />'
		. '</td></tr>'
		. '<tr><td class="col_info">'._T('mercure:conf_notify_sound_question').'</td>'
		. '<td class="col_val">';	
    for($i=1;$i<12;$i++) $aff.= '<input type="radio" NAME="mercure_notify_sound" VALUE="'.$i.'" '.($GLOBALS['mercure']['notify_sound'] == $i ? 'checked':'').'> Sound '.$i.' <a href="'._DIR_SOUND_MERCURE.'notify'.$i.'.wav"><img border="0" src="'._DIR_IMG_MERCURE.'play.png"> Play</a><br />';  
    $aff.= '</td></tr>'
		. '<tr><td class="col_info">'._T('mercure:conf_refresh_question').'</td>'
		. '<td class="col_val">'
		. '<input type="text" size="4" name="mercure_refresh" value="'.$GLOBALS['mercure']['mercure_refresh'].'" class="fondl" /><br />'
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
		. '<tr><td class="col_info">'._T('mercure:conf_bdd_question').'</td>'
		. '<td class="col_val">'
		. '<input type="radio" name="mercure_bdd" value="txt" '.($GLOBALS['mercure']['mercure_bdd'] == 'txt' ? 'checked':'').'" class="fondl" />'._T('mercure:conf_general_bdd_txt').'<br />'
		. '<input type="radio" name="mercure_bdd" value="bdd" '.($GLOBALS['mercure']['mercure_bdd'] == 'bdd' ? 'checked':'').'" class="fondl" />'.str_replace('%info_sqlite%',$info_bdd,_T('mercure:conf_general_bdd_bdd')).'<br />'
		. '</td></tr>'
		. '<tr><td class="col_info">'._T('mercure:conf_bdd_item_limit').'</td>'
		. '<td class="col_val">'
		. '<input type="text" size="4" name="mercure_bdd_item_limit" value="'.$GLOBALS['mercure']['mercure_bdd_item_limit'].'" class="fondl" /><br />'
		. '</td></tr>'
		. '<tr><td class="col_info">'._T('mercure:conf_bdd_purge_question').'</td>'
		. '<td class="col_val">'
		. '<input type="text" size="4" name="mercure_bdd_time_limit" value="'.$GLOBALS['mercure']['mercure_bdd_time_limit'].'" class="fondl" /><br />'
		. '</td></tr>'
		. '</table>'
		. fin_cadre_trait_couleur(true);

	$aff.= '<div align="right"><input type="submit" value="'._T('valider').'" class="fondo" /></div>'
		. '</form>';
	
	return $aff;
}
?>
