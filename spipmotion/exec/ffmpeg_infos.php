<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');

function exec_ffmpeg_infos_dist(){
	global $spip_lang_right;

	// pipeline d'initialisation
	pipeline('exec_init', array('args'=>array('exec'=>'ffmpeg_infos'),'data'=>''));

	// entetes
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('spipmotion:titre_page_ffmpeg_infos'), "spipmotion", "spipmotion");

	// titre
	echo "<br /><br /><br />\n"; // outch que c'est vilain !
	echo gros_titre(_T('spipmotion:titre_page_ffmpeg_infos'),'', false);

	// barre d'onglets
	echo barre_onglets("spipmotion", "ffmpeg_infos");

	// colonne gauche
	echo debut_gauche('', true);
	echo recuperer_fond('prive/infos/ffmpeg_infos');
	echo pipeline('affiche_gauche', array('args'=>array('exec'=>'ffmpeg_infos'),'data'=>''));

	// colonne droite
	echo creer_colonne_droite('', true);
	echo pipeline('affiche_droite', array('args'=>array('exec'=>'ffmpeg_infos'),'data'=>''));

	// centre
	echo debut_droite('', true);

	// contenu
	$infos_ffmpeg = charger_fonction('ffmpeg_infos','inc');
	$infos = $infos_ffmpeg();
	if(is_array($infos)){
		$contexte = array_merge($_GET,$infos);
	}
 	echo recuperer_fond('prive/contenu/ffmpeg_infos', $contexte);

	// fin contenu
	echo pipeline('affiche_milieu', array('args'=>array('exec'=>'ffmpeg_infos'),'data'=>''));

	echo fin_gauche(), fin_page();
}

?>