
<?php

// compatibilite spip 1.9
if ($GLOBALS['spip_version_code']<1.92) { function fin_gauche(){return false;} }

function exec_lilyspip() {

	if ($post = _request('lilyspip_server')) {		
		ecrire_meta('lilyspip_server', $post);
		ecrire_metas();
		lire_metas();
	}
	$url_serveur = $GLOBALS['meta']['lilyspip_server'];
	if (!strlen($url_serveur)) $url_serveur = 'http://';
	
	include_spip('inc/presentation');
	if ($GLOBALS['spip_version_code']<1.92) 
  		debut_page(_T('lilyspip:lilyspip_plugin'), '', '');
  	else {
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('lilyspip:lilyspip_plugin'), '', '');
	}
	echo "<br /><br /><br />";

	if ($GLOBALS['connect_statut'] !== "0minirezo") {
		gros_titre(_T('info_acces_refuse'));
		echo fin_page();
		return;
	}

	gros_titre(_T('lilyspip:lilyspip_plugin'));
	debut_gauche();

	debut_boite_info();
	echo propre(_T('lilyspip:info_message'));	
	fin_boite_info();

	echo pipeline('affiche_gauche',array('args'=>array('exec'=>'lilyspip'),'data'=>''));
	creer_colonne_droite();
	echo pipeline('affiche_droite',array('args'=>array('exec'=>'lilyspip'),'data'=>''));
	debut_droite();

	debut_cadre_trait_couleur('base-24.gif', false, '', _T('lilyspip:parametrages'));

	echo generer_url_post_ecrire("lilyspip");	
	echo "<strong><label for='lilyspip_server'>"._T("lilyspip:adresse_serveur")."</label></strong> ";
	echo "<input type='text' name='lilyspip_server' CLASS='formo' value='$url_serveur' size='40'><br />\n";
	echo "<div align='right'>";
	echo "<input type='submit' name='Valider' value='"._T('bouton_valider')."' class='fondo'></div>\n";
	echo pipeline('affiche_milieu',array('args'=>array('exec'=>'lilyspip'),'data'=>''));
	echo "</form>";

	echo "<strong>"._T("lilyspip:previsualisation")."</strong>";
	debut_cadre_relief();
	$url_test = $url_serveur.'?format=test';
	spip_log("Lilypond - prévisualisation : $url_test");
	echo "\n<p class=\"spip\" style=\"text-align: center;\">"."<img src=\"$url_test\" style=\"vertical-align:middle;\" />";
	fin_cadre_relief();

	fin_cadre_trait_couleur();
	
	echo fin_gauche(), fin_page();
}
?>