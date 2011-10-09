<?php
/*
 * Plugin Couteau Kiss
 * (c) 2010 Cedric
 * Distribue sous licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_configurer_ck_dist() {

	if (!autoriser('configurer', 'ck')) {
		include_spip('inc/minipres');
		echo minipres();
	} else {

		include_spip('inc/presentation');
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('ck:titre_page_couteau'), "configuration", "ck");

		echo gros_titre(_T('ck:titre_page_couteau'),'',false);
		echo barre_onglets("configuration", "ck");

		echo debut_gauche('plugin',true);
		echo debut_droite('plugin', true);
		echo pipeline('affiche_milieu',
			array(
			'args'=>array('exec'=>'ck'),
			'data'=>recuperer_fond('prive/squelettes/contenu/configurer_ck',$_GET)
			)
		);

		echo fin_gauche(), fin_page();

	}
}


if (!include_spip('inc/filtres_ecrire')
	OR !function_exists('sinon_interdire_acces')){
	/**
	 * Bloquer l'acces a une page en renvoyant vers 403
	 * @param bool $ok
	 * @return string
	 */
	function sinon_interdire_acces($ok=false) {
		if ($ok) return '';
		// vider tous les tampons
		while (ob_get_level())
			ob_end_clean();
		include_spip('inc/minipres');
		minipres();
		exit;
	}
}
?>