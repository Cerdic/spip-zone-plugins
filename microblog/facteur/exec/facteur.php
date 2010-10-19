<?php
/*
 * Plugin Facteur
 * (c) 2009-2010 Collectif SPIP
 * Distribue sous licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/presentation');
include_spip('inc/config');
include_spip('inc/meta');
include_spip('classes/facteur');


function exec_facteur() {

	if (!autoriser('configurer', 'facteur')) {
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}

	pipeline('exec_init',array('args'=>array('exec'=>'facteur'),'data'=>''));

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('titre_configuration'), "configuration", "configuration");

	echo "<br /><br /><br />\n";
	echo gros_titre(_T('titre_configuration'),'',false);
	echo barre_onglets("configuration", "facteur");

	echo debut_gauche('', true);

	echo pipeline('affiche_gauche',array('args'=>array('exec'=>'facteur'),'data'=>''));

	echo debut_droite('', true);

	echo pipeline('affiche_milieu', array('args'=>array('exec'=>'facteur'),
		'data'=>recuperer_fond("prive/configurer_facteur", $_GET)));

	echo fin_gauche();

	echo fin_page();

}


?>