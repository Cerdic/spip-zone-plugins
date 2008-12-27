<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');

function exec_iextra_edit_dist(){

	// si pas autorise : message d'erreur
	if (!autoriser('configurer', 'iextra')) {
		include_spip('inc/minipres');
		echo minipres();
		die();
	}

	// pipeline d'initialisation
	pipeline('exec_init', array('args'=>array('exec'=>'iextra_edit'),'data'=>''));

	// entetes
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('iextra:titre_page_iextra'), "configuration", "configuration");
	
	// titre
	echo "<br /><br /><br />\n"; // outch que c'est vilain !
	echo gros_titre(_T('iextra:titre_iextra_edit'),'', false);
	
	// barre d'onglets
	echo barre_onglets("configuration", "interface_extra");
	
	// colonne gauche
	echo debut_gauche('', true);
	echo pipeline('affiche_gauche', array('args'=>array('exec'=>'iextra_edit'),'data'=>''));
	
	// colonne droite
	echo creer_colonne_droite('', true);
	echo pipeline('affiche_droite', array('args'=>array('exec'=>'iextra_edit'),'data'=>''));
	
	// centre
	echo debut_droite('', true);
	
	// contenu
	$id_extra = intval(_request('id_extra'));
	$id_extra = $id_extra ? $id_extra : 'new' ;
	echo recuperer_fond('prive/editer/champs_extras', array(
		'id_extra'=>$id_extra,
		'titre'=>$id_extra=='new' ? _T('iextra:info_nouveau_champ_extra') : _T('iextra:info_modifier_champ_extra'),
		'redirect'=>generer_url_ecrire("iextra"),
		'icone_retour'=>icone_inline(_T('icone_retour'), generer_url_ecrire('iextra'), find_in_path("images/iextra-24.png"), "rien.gif",$GLOBALS['spip_lang_left']),
		));

	echo pipeline('affiche_milieu', array('args'=>array('exec'=>'iextra_edit'),'data'=>''));

	echo fin_gauche(), fin_page();
}
?>
