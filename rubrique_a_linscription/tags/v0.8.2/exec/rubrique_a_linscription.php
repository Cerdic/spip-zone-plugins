<?php
function exec_rubrique_a_linscription_dist(){
	if (!autoriser('configurer', 'rubrique_a_linscription')) {
		include_spip('inc/minipres');
		echo minipres();
		die();
	}

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('rubrique_a_linscription:rubrique_a_linscription'),'configuration','configuration');
	//echo barre_onglets('configuration','rubrique_a_linscription');
	echo "<br /> <br />";
	include_spip('inc/presentation');
	echo gros_titre(_T('rubrique_a_linscription:rubrique_a_linscription'),'', false);
	echo barre_onglets("configuration", "rubriquelinscription");

	// colonne gauche
	echo debut_gauche('', true);
	
	echo pipeline('affiche_gauche', array('args'=>array('exec'=>'rubrique_a_linscription'),'data'=>recuperer_fond('fonds/rubrique_a_linscription_bascule')));
	
	// colonne droite
	echo creer_colonne_droite('', true);
	echo pipeline('affiche_droite', array('args'=>array('exec'=>'rubrique_a_linscription'),'data'=>''));
	
	// centre
	echo debut_droite('', true);

	echo recuperer_fond('fonds/rubrique_a_linscription',array());
	echo pipeline('affiche_milieu', array('args'=>array('exec'=>'rubrique_a_linscription'),'data'=>''));

	//echo fin_gauche(), fin_page();
}
?>