<?php

include_spip('inc/panoramas_edit');
	

function Panoramas_visitevirtuelle_confirme_suppression($id_visite,$nb_lieux,$redirect,$retour){
	global $spip_lang_right;
	$out = "<div class='verdana3'>";
	if ($nb_lieux){
			$out .= "<p><strong>"._T("panoramas:attention")."</strong> ";
			$out .= _T("panoramas:info_supprimer_visite")."</p>\n";
	}
	else{
		$out .= "<br />";
		$out .= _T("panoramas:info_supprimer_visite")."</p>\n";
	}
	$link = generer_action_auteur('visitesvirtuelles_supprime',"$id_visite",_DIR_RESTREINT_ABS.($retour?(str_replace('&amp;','&',$retour)):generer_url_ecrire('visitesvirtuelles_toutes',"",false,true)));
	$out .= "<form method='POST' action='$link' style='float:$spip_lang_right'>";
	$out .= form_hidden($link);
	$out .= "<div style='text-align:$spip_lang_right'>";
	$out .= "&nbsp;<input type='submit' name='supp_confirme' value=\""._T('item_oui')."\" class='fondo'>";
	$out .= "</div>";
	$out .= "</form>\n";

	$out .= "<form method='POST' action='$redirect' style='float:$spip_lang_right'>\n";
	$out .= form_hidden($redirect);
	$out .= "<div style='text-align:$spip_lang_right'>";
	$out .= "&nbsp;<input type='submit' name='supp_rejet' value=\""._T('item_non')."\" class='fondo'>";
	$out .= "</div>";
	$out .= "</form><br />\n";
	$out .= "</div>";

	return $out;
}


function exec_visitesvirtuelles_edit(){
	
	$retour = _request('retour');

	$id_visite = intval(_request('id_visite'));
	
	$new = _request('new');
	$supp_visite = intval(_request('supp_visite'));
	$supp_rejet = _request('supp_rejet');
	$titre = _request('titre');
	
	if ($supp_visite) {
		$id_visite = $supp_visite;
	}
	if ($retour)
		$retour = urldecode($retour);
	else 
		$retour = generer_url_ecrire('visitesvirtuelles_toutes',"","",true);
  
	include_spip("inc/presentation");
	include_spip("inc/config");

	$nb_lieux = 0;
	if ($id_visite)
		if ($row = spip_fetch_array(spip_query("SELECT COUNT(*) AS num FROM spip_visites_virtuelles_lieux WHERE id_visite="._q($id_visite))))
			$nb_lieux = $row['num'];


	$redirect = generer_url_ecrire('visitesvirtuelles_edit',(intval($id_visite)?"id_visite=$id_visite":""));
	if ($retour) 
		$redirect = parametre_url($redirect,"retour",urlencode($retour));
		
	//
	// Affichage de la page
	//
	if ($id_visite){
		$result = spip_query("SELECT * FROM spip_visites_virtuelles WHERE id_visite="._q($id_visite));
		if ($row = spip_fetch_array($result)) {
			$id_visite = $row['id_visite'];
			$titre = $row['titre'];
			$descriptif = $row['descriptif'];
		}
		$focus = "";
		$action_link = generer_action_auteur("visitesvirtuelles_edit","$id_visite",urlencode($redirect));
	}

	$ajax_charset = _request('var_ajaxcharset');
	$bloc = _request('bloc');
	if ($ajax_charset && $bloc=='dummy') {
		ajax_retour("");
	}
	if ($ajax_charset && $bloc=='apercu') {
		include_spip('public/assembler');
		$GLOBALS['var_mode']='calcul';
		$apercu = recuperer_fond('modeles/visitevirtuelle',array('id_visite'=>$id_visite,'var_mode'=>'calcul'));
		ajax_retour($apercu);
	}
	if ($ajax_charset && $bloc=='resume') {
		include_spip('public/assembler');
		$GLOBALS['var_mode']='calcul';
		$apercu = recuperer_fond('modeles/visitevirtuelle',array('id_visite'=>$id_visite,'var_mode'=>'calcul'));
		ajax_retour(contenu_boite_resume($id_visite, $row, $apercu));
	}
	if ($ajax_charset && $bloc=='proprietes') {
		ajax_retour(boite_proprietes($id_visite, $row, $focus, $action_link, $redirect));
	}
	$bloc = explode("-",$bloc);
		
	
	debut_page("&laquo; $titre &raquo;", "documents", "visitesvirtuelles","");

	// Recupere les donnees ---------------------------------------------------------------
	if ($new == 'oui' && !$titre) {
		
		$titre = _T("panoramas:nouvelle_visite");
		include_spip('inc/charset');
		$row['titre'] = $titre = unicode2charset(html2unicode($titre));
		$row['descriptif'] = "";
		
		$action_link = generer_action_auteur("visitesvirtuelles_edit","new",urlencode($redirect));
	}
	
	// gauche raccourcis ---------------------------------------------------------------
	debut_gauche();
	
	echo "<br /><br />\n";
	debut_boite_info();
	if ($id_visite>0)
		echo "<div align='center' style='font-size:3em;font-weight:bold;'>$id_visite</div>\n";
	if ($retour) {
		icone_horizontale(_T('icone_retour'), $retour, "../"._DIR_PLUGIN_PANORAMAS."img_pack/logo_panoramas.png", "rien.gif",'right');
	}
	if (!include_spip('inc/autoriser'))
		include_spip('inc/autoriser_compat');
	if (autoriser('administrer','visitevirtuelle',$id_visite)) {
		$nretour = urlencode(self());
		
		$link = parametre_url(self(),'new','');
		$link = parametre_url($link,'supp_visite', $id_visite);
		if (!$retour) {
			$link=parametre_url($link,'retour', urlencode(generer_url_ecrire('visitesvirtuelles_edit')));
		}
		echo "<p>";
		icone_horizontale(_T("panoramas:supprimer_visite"), "?exec=visitesvirtuelles_edit&supp_visite=".$id_visite, "../"._DIR_PLUGIN_PANORAMAS."img_pack/supprimer-24.png", "rien.gif");
		echo "</p>";
	}
	fin_boite_info();
	
	// droite ---------------------------------------------------------------
	creer_colonne_droite();
	debut_droite();

	if (!$new){
		echo gros_titre($row['titre'],'',false);
	
		if ($supp_visite && $supp_rejet==NULL)
			echo Panoramas_visitevirtuelle_confirme_suppression($id_visite,$nb_lieux,$redirect,$retour);
		echo "<div id='barre_onglets'>";
		echo debut_onglet();
		echo onglet(_L("Aper&ccedil;u"),ancre_url(self(),"resume"),'','resume');
		echo onglet(_L("Propri&eacute;t&eacute;s"),ancre_url(self(),"proprietes"),'','proprietes');
		echo onglet(_L("Lieux"),ancre_url(self(),"champs"),'','champs');
		echo fin_onglet();
		echo "</div>";
	}

	$out = "";
	if ($id_visite){
		$out .= "<div id='resume' name='resume'>";
		include_spip('public/assembler');
		$GLOBALS['var_mode']='calcul';
		$out .= recuperer_fond('modeles/visitevirtuelle',array('id_visite'=>$id_visite,'var_mode'=>'calcul'));
		$out .= "</div>";
	}

	// centre proprietes ---------------------------------------------------------------
	$out .= "<div id='proprietes' name='proprietes'>";
	$out .= Panoramas_boite_proprietes($id_visite, $row, $focus, $action_link, $redirect);
	$out .= "</div>";

	// edition des lieux ---------------------------------------------------------------
	$out .= "<div id='Lieux' name='lieux'>";
	//$out .= "Panoramas_zone_edition_lieux($id_visite, $champ_visible, $nouveau_champ,$redirect)";
	$out .= "</div>\n";

	echo $out;

	if ($GLOBALS['spip_version_code']>=1.9203)
		echo fin_gauche();
	echo fin_page();
}


?>
