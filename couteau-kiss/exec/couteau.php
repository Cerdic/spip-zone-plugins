<?php
/*
 * Plugin Couteau Kiss
 * (c) 2010 Cedric
 * Distribue sous licence GPL
 *
 */

function exec_couteau_dist() {

	if (!autoriser('configurer', 'ck')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {

		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('ck:titre_page_couteau'), "configuration", "ck");

		echo gros_titre(_T('ck:titre_page_couteau'),'',false);
		echo barre_onglets("configuration", "ck");

		echo debut_gauche('plugin',true);
		echo debut_boite_info(true);
		$s .= propre(_T('ck:texte_boite_info'));
		echo $s;
		echo fin_boite_info(true);

		echo debut_droite('plugin', true);
		echo pipeline('affiche_milieu',
			array(
			'args'=>array('exec'=>'ck'),
			'data'=>recuperer_fond('prive/ck',$_GET)
			)
		);

		echo fin_gauche(), fin_page();

	}
}
?>