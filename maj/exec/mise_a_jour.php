<?php

# securite
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/autoriser');
include_spip('inc/mise_a_jour');

function exec_mise_a_jour() {
	global $couleur_foncee;

	pipeline('exec_init',
		array('args'=>array('exec'=>'mise_a_jour'),'data'=>''));

	$spip_loader_liste = array();	

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('titre_admin_tech'), "configuration", "base");

	echo "<br /><br /><br />";
	gros_titre(_T('titre_admin_tech').' : '._T('maj:mise_a_jour'));
	echo barre_onglets("administration", "mise_a_jour");

	debut_gauche();

		debut_boite_info();
			echo "<span class='verdana1'><b>"
				. _T('maj:info_gauche_mise_a_jour1')
				. "</b></span>"
				. "<p>"._T('maj:info_gauche_mise_a_jour2')."</p>";
		fin_boite_info();

		if(!autoriser('mise_a_jour', 'webmestre')) {
			creer_colonne_droite();
			debut_droite();
			echo _T('avis_non_acces_page');
			echo fin_gauche(), fin_page();
			exit;
		}

	$tester_maj_methode = charger_fonction('tester_maj_methode', 'inc');
	$tester = $tester_maj_methode();

	echo $tester;
		
	$tester_maj_source = charger_fonction('tester_maj_source', 'inc');
	$tester = $tester_maj_source();

	echo $tester;

	echo pipeline('affiche_gauche',array('args'=>array('exec'=>'mise_a_jour'),'data'=>''));
	creer_colonne_droite();
	echo pipeline('affiche_droite',array('args'=>array('exec'=>'mise_a_jour'),'data'=>''));
	debut_droite();

	if (!_SPIP_MAJ_FILE) {
		echo _L("Fichier de configuration absent");
		fin_page();
		exit;
	}
	else {
		list($test_spip_loader, $test_svn) = array(tester_spip_loader(), tester_svn()); 
		$spip_loader_liste = spip_loader_liste(_SPIP_MAJ_FILE, $test_spip_loader, $test_svn);
		$menu_maj_liste = mettre_en_page($spip_loader_liste);

		echo bandeau_titre_boite2(_L('Liste des paquets du fichier ').'<tt>'.joli_repertoire(_SPIP_MAJ_FILE).'</tt>' , find_in_path('images/paquets.png'), $couleur_foncee, 'white', false);
		//echo afficher_tranches_requete($num_rows, $tmp_var);
		echo '<table width="100%" cellpadding="2" cellspacing="0" border="0">';
		echo afficher_liste(array('', ''), $menu_maj_liste, array('arial11', 'arial1'));
		echo '</table>';
	}
	
	echo pipeline('affiche_milieu',array('args'=>array('exec'=>'mise_a_jour'),'data'=>''));
	echo fin_gauche(), fin_page();

}

?>