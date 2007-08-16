
<?php

// compatibilite spip 1.9 ajout de Patrice  VANNEUFVILLE
if ($GLOBALS['spip_version_code']<1.92) { function fin_gauche(){return false;} }

function exec_lilyspip(){}
include_ecrire('inc/presentation');
 		

	if (isset($_POST['lilyspip_server'])){		
	ecrire_meta('lilyspip_server',$_POST['lilyspip_server']);
 	ecrire_metas();
 	lire_metas();
	
	}
 	$adresse_serveur=$GLOBALS['meta']['lilyspip_server'];


  	debut_page(_T('lilyspip:lilyspip_plugin'), '', '');


	echo "<br /><br /><br />";
	gros_titre(_T('lilyspip:lilyspip_plugin'));
	debut_gauche();
	
	debut_boite_info();
	echo propre(_T('lilyspip:info_message'));	
	fin_boite_info();
	
	debut_droite();
	debut_cadre_trait_couleur("", false, "", _T('lilyspip:parametrage_lilyspip'));


if ($GLOBALS['connect_statut'] == "0minirezo") {
	echo generer_url_post_ecrire("lilyspip");	
					
	echo "<p>";
	echo "<strong><label for='lilyspip_server'>"._T("lilyspip:adresse_serveur")."</label></strong> ";
	echo "<input type='text' name='lilyspip_server' CLASS='formo' value='$adresse_serveur' size='40'><br />\n";
	echo "<strong>";
	echo "<div align='right'>";
	echo "<input type='submit' name='Valider' value='"._T('bouton_valider')."' class='fondo'></div>\n";
	echo "</p>";
	echo "</form>";

	echo "<p>";
	echo "<strong>"._T("lilyspip:previsualisation")."</strong>";
	debut_cadre_relief();
	spip_log($url = $adresse_serveur.'?format=test');
	echo "\n<p class=\"spip\" style=\"text-align: center;\">"."<img src=\"$url\" style=\"vertical-align:middle;\" />";
	fin_cadre_relief();
	echo "</p>";
	}
	
	else echo "<strong>"._T("avis_non_acces_page")."</strong>";
	
	echo "</span>";
	
	
fin_cadre_trait_couleur();
fin_gauche();
fin_page();
?>
