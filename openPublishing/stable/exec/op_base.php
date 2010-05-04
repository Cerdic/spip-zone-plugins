<?php

/*
 * Ce fichier disparaitra dans les prochaine versions
 * il fournie les fonctions de transitions entre openPublishing et Publication ouverte
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');

function exec_op_base() {

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('opconfig:op_base_titre'), "op_base", "op_base");

	echo "<br /><br /><br />";
	echo gros_titre(_T('opconfig:op_base_titre'),'',false);

	// Seul l'administrateur y a acces ...
	if ($admin AND $connect_statut != "0minirezo") {
		echo _T('avis_non_acces_page');
		exit;
	}

	echo debut_gauche();

	echo debut_boite_alerte();
	echo _T('opconfig:base_attention');
	echo fin_boite_alerte();
	echo '<br />';
	debut_boite_info();
	echo '<a href="'.generer_url_ecrire('cfg','cfg=op').'">'._T('opconfig:configuration').'</a>';
	fin_boite_info();

	echo debut_droite();

	echo debut_cadre('op_base_main');

	echo debut_boite_info();
	echo '<b>'._T('opconfig:cas_neuve').'</b>';
	echo '<p>'._T('opconfig:premiere_fois').'</p>';
	echo '<p>'._T('opconfig:rien_a_faire').'</p>';
	echo '<p>'._T('opconfig:aller_config').'</p>';
	echo fin_boite_info();
	echo '<br />';

	echo debut_boite_info();
	echo '<b>'._T('opconfig:cas_mise_a_jour').'</b>';
	echo '<p>'._T('opconfig:telecharge_install').'</p>';
	echo '<p>'._T('opconfig:structure').'</p>';
	echo '<p>'._T('opconfig:expliq_sup_table').'</p>';
	echo '<form method="POST" action="'.generer_url_action("op_base", "arg=SupTables").'">';
	echo '<input type="hidden" name="redirect" value="'.generer_url_ecrire("op_base").'"/>';
	echo '<input style="background: red;" type="submit" value="'._T('opconfig:sup_table').'" /></form>';

	
	echo '<p>'._T('opconfig:expliq_transfert_auteurs').'</p>';
	echo '<form method="POST" action="'.generer_url_action("op_base", "arg=Maj").'">';
	echo '<input type="hidden" name="redirect" value="'.generer_url_ecrire("op_base").'"/>';
	echo '<input style="background: red;" type="submit" value="'._T('opconfig:transfert_auteurs').'" /></form>';
	echo '<p>'._T('opconfig:transfert_auteurs_suite').'</p>';
	echo '<p>'._T('opconfig:attention_temps').'</p>';
	echo '<p>'._T('opconfig:transfert_auteurs_ok').'</p>';
	echo '<form method="POST" action="'.generer_url_action("op_base", "arg=SupAuteur").'">';
	echo '<input type="hidden" name="redirect" value="'.generer_url_ecrire("op_base").'"/>';
	echo '<input style="background: red;" type="submit" value="'._T('opconfig:sup_auteurs').'" /></form>';
	
	echo fin_boite_info();

	echo fin_cadre('op_base_main');
	echo fin_gauche(), fin_page();

}

?>