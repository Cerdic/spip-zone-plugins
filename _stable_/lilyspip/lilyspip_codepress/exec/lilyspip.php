
<?php



// compatibilite spip 1.9 de Patrice  VANNEUFVILLE
if(defined('_SPIP19100') && !function_exists('fin_gauche')) { function fin_gauche(){return '';} }
	function lily_cs_compat_boite($b) {if(defined('_SPIP19200')) echo $b('', true); else $b(); }


function exec_lilyspip(){}
include_spip('inc/presentation');
 		

	if (isset($_POST['lilyspip_server'])){		
	ecrire_meta('lilyspip_server',$_POST['lilyspip_server']);
 	ecrire_metas();
 	lire_metas();
	
	}
 	$adresse_serveur=$GLOBALS['meta']['lilyspip_server'];

	 if(defined('_SPIP19100'))
	           	debut_page(_T('lilyspip:lilyspip_plugin'), '', 'lilyspip');
 	      else {
 	        $commencer_page = charger_fonction('commencer_page', 'inc');
	        echo $commencer_page(_T('lilyspip:lilyspip_plugin'), '', 'lilyspip');
	    }



	echo "<br /><br /><br />";
	echo gros_titre(_T('lilyspip:lilyspip_plugin'),'',false);

	lily_cs_compat_boite('debut_gauche');
	
	echo debut_boite_info(true), propre(_T('lilyspip:info_message')), fin_boite_info(true);

	lily_cs_compat_boite('debut_droite');
	echo debut_cadre_trait_couleur("", true, "", _T('lilyspip:parametrage_lilyspip'));
	//if(defined('_SPIP19100'))debut_cadre_formulaire(); else echo debut_cadre_formulaire('', true);


if ($GLOBALS['connect_statut'] == "0minirezo") {
		
	echo "<form method='post' name='lilyspip_serv'>\n";				
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
	
	

echo fin_cadre_trait_couleur(true); fin_gauche(); fin_page();
?>
