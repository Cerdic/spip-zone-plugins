<?php

include_spip('public/assembler');
function exec_editer_societe() {

	global $connect_statut, $connect_toutes_rubriques, $table_prefix;

	if (!($connect_statut == '0minirezo' AND $connect_toutes_rubriques)) {
		echo _T('avis_non_acces_page');
		fin_page();
		exit;
	}
	
	$new = _request('new');
	if(!isset($new)){
		$id_societe = intval(_request('id_societe'));
		if(!$id_societe = sql_getfetsel("id_societe","spip_societes","id_societe=$id_societe"))
			$id_societe = 'new';
	}
	pipeline('exec_init',array('args'=>array('exec'=>'editer_societe','id_societe'=>$id_societe),'data'=>''));
	
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page("", "", "", "");
	
	echo debut_gauche("",true);

	echo pipeline('affiche_gauche',array('args'=>array('exec'=>'editer_societe','id_societe'=>$id_societe),'data'=>''));
	echo creer_colonne_droite("",true);
	echo pipeline('affiche_droite',array('args'=>array('exec'=>'editer_societe','id_societe'=>$id_societe),'data'=>''));
	echo debut_droite("",true);
	
	if($id_societe){
		$titre = sql_getfetsel('nom','spip_societes','id_societe='.intval($id_societe));
	}
	
	$oups = _request('retour') ? _request('retour') : 
		($id_societe ?
	     generer_url_ecrire("societes")
	     : generer_url_ecrire()     
		);

	$contexte = array(
	'icone_retour'=>icone_inline(_T('icone_retour'), $oups,"article-24.gif", "rien.gif",$GLOBALS['spip_lang_left']),
	'redirect'=>_request('retour') ? _request('retour') : generer_url_ecrire('societes'),
	'titre'=>$titre,
	'new'=>$new?$new:$id_societe
	);
	
	$milieu = recuperer_fond('prive/editer/editer_societe', $contexte);
	
	echo pipeline('affiche_milieu',array('args'=>array('exec'=>'editer_societe','id_societe'=>$id_societe),'data'=>$milieu));

	echo fin_gauche(), fin_page();
}
?>
