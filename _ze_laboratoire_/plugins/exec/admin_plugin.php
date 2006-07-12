<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/config');
include_spip('inc/plugin');
include_spip('inc/presentation');
include_spip('inc/layer');


function exec_admin_plugin() {
	global $connect_statut;
	global $connect_toutes_rubriques;
	global $spip_lang_right;
	$surligne = "";

	if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {
		debut_page(_T('icone_admin_plugin'), "administration", "plugin");
		echo _T('avis_non_acces_page');
		fin_page();
		exit;
	}

	// mise a jour des donnees si envoi via formulaire
	// sinon fait une passe de verif sur les plugin
	if ($_POST['changer_plugin']=='oui'){
		enregistre_modif_plugin();
		// pour la peine, un redirige, 
		// que les plugin charges soient coherent avec la liste
		redirige_par_entete(generer_url_ecrire('admin_plugin'));
	}
	else
		verif_plugin();
	if (isset($_GET['surligne']))
		$surligne = $_GET['surligne'];

	debut_page(_T('icone_admin_plugin'), "administration", "plugin");
	echo "<style type='text/css'>\n";
	echo <<<EOF
div.cadre-padding ul li {
	list-style:none ;
}
div.cadre-padding ul {
	padding-left:1em;
	margin:.5em 0 .5em 0;
}
div.cadre-padding ul ul {
	border-left:5px solid #DFDFDF;
}
div.cadre-padding ul li li {
	border:1px solid #AFAFAF;
	margin:0 0 .5em 0;
}
div.cadre-padding ul li li div.nomplugin, div.cadre-padding ul li li div.nomplugin_on {
	padding:.3em .3em .6em .3em;
	font-weight:normal;
}
div.cadre-padding ul li li div.nomplugin a, div.cadre-padding ul li li div.nomplugin_on a {
	outline:0;
	outline:0 !important;
	-moz-outline:0 !important;
}
div.cadre-padding ul li li div.nomplugin_on {
	background:#FFF8EF;
	font-weight:bold;
}
div.cadre-padding div.droite label {
	padding:.3em;
	background:#EFEFEF;
	border:1px dotted #95989F !important;
	border:1px solid #95989F;
	cursor:pointer;
	margin:.2em;
	display:block;
	width:10.1em;
}
div.cadre-padding input {
	cursor:pointer;
}
div.detailplugin {
	border-top:1px solid #B5BECF;
	padding:.6em;
	background:#F5F5F5;
}
div.detailplugin hr {
	border-top:1px solid #67707F;
	border-bottom:0;
	border-left:0;
	border-right:0;
	}
EOF;
	echo "</style>";

	echo "<br/><br/><br/>";
	
	gros_titre(_T('icone_admin_plugin'));
	// barre_onglets("administration", "plugin"); // a creer dynamiquement en fonction des plugin charges qui utilisent une page admin ?
	
	debut_gauche();
	debut_boite_info();
	echo _T('info_gauche_admin_tech');
	fin_boite_info();

	debut_droite();

	debut_cadre_relief();

	global $couleur_foncee;
	echo "<table border='0' cellspacing='0' cellpadding='5' width='100%'>";
	echo "<tr><td bgcolor='$couleur_foncee' background='' colspan='4'><b>";
	echo "<font face='Verdana,Arial,Sans,sans-serif' size='3' color='#ffffff'>";
	echo _T('plugins_liste')."</font></b></td></tr>";

	echo "<tr><td class='serif' colspan=4>";
	echo _T('texte_presente_plugin');

	echo generer_url_post_ecrire("admin_plugin");

	$GLOBALS['plug_actifs']= liste_plugin_actifs();
	echo "<ul>";
	recursDirs(100, _DIR_PLUGINS);
	echo "</ul>";

	echo "</table></div>\n";

	echo "\n<input type='hidden' name='id_auteur' value='$connect_id_auteur' />";
	echo "\n<input type='hidden' name='hash' value='" . calculer_action_auteur("valide_plugin") . "'>";
	echo "\n<input type='hidden' name='changer_plugin' value='oui'>";

	echo "\n<p>";

	echo "<div style='text-align:$spip_lang_right'>";
	echo "<input type='submit' name='Valider' value='"._T('bouton_valider')."' class='fondo'>";
	echo "</div>";

# ce bouton est trop laid :-)
# a refaire en javascript, qui ne fasse que "decocher" les cases
#	echo "<div style='text-align:$spip_lang_left'>";
#	echo "<input type='submit' name='desactive_tous' value='"._T('bouton_desactive_tout')."' class='fondl'>";
#	echo "</div>";

	echo "</form></tr></table>\n";

	echo "<br />";

	fin_page();

}

function recursDirs($maxfiles, $racine, $dir='', $depth=0, $nbfiles= 0) {
//error_log("recursDirs($maxfiles, $racine, $dir, $depth, $nbfiles)");
	if ($dir == '') {
		$dir = '.';
		$categ='racine';
	} else {
		$categ=$dir;
	}

	$aSuivre= array();
	$plugins= array();

	if (@is_dir("$racine$dir")
		&& is_readable("$racine$dir")
		&& $d = @opendir("$racine$dir")) {
		while (($f = readdir($d)) !== false && ($nbfiles<$maxfiles)) {
			if($f{0} == '.') continue;
			if(is_dir("$racine$dir/$f")) {
				if(is_readable("$racine$dir/$f/plugin.xml")) {
					$plugins[]=$f;
					$nbfiles++;
				} else {
					$aSuivre[]= $f;
				}
			}
		}

		closedir($d);

		if(!empty($plugins)) {
			sort($plugins);
			$visible = false;
			foreach($plugins as $p) {
				if (@in_array("$dir/$p",$GLOBALS['plug_actifs'])) $visible = true;
			}
			echo "<li>";
			echo $visible? bouton_block_visible($categ):bouton_block_invisible($categ);
			echo "$categ\n<ul>";
			
			echo $visible? debut_block_visible($categ):debut_block_invisible($categ);
			
			$iteration = 0;
			foreach($plugins as $p) {
				$iteration .= abs(crc32($dir));
				echo "<li>";
				echo ligne_plug("$dir/$p", $iteration);
				$iteration++;
				echo "</li>\n";
			}
			echo "</ul></li>\n";
			echo fin_block();
		}

		if(!empty($aSuivre)) {
			sort($aSuivre);
			if($dir=='.') {
				$sub='';
			} else {
				$sub="$dir/";
			}
			foreach($aSuivre as $d) {
				recursDirs($maxfiles, $racine, "$sub$d", $depth+1, $nbfiles);
			}
		}
	}
}


function ligne_plug($plug_file, $iteration){
	static $id_input=0;

	$erreur = false;
	$vals = array();
	$info = plugin_get_infos($plug_file);
	$plugok=@in_array($plug_file,$GLOBALS['plug_actifs']);
	$s = "<script type='text/javascript'>";
$s .= <<<EOF
function verifchange$iteration(inputp) {
	if(inputp.checked == true)
	{
		document.getElementById('$plug_file').className = 'nomplugin_on';
	}
	else {
		document.getElementById('$plug_file').className = 'nomplugin';
	}
	}
EOF;
	$s .= "</script>";
	$s .= "<div id='$plug_file' class='nomplugin".($plugok?'_on':'')."'>";
	if (isset($info['erreur'])){
		$s .=  "<div style='background:".$GLOBALS['couleur_claire']."'>";
		$erreur = true;
		foreach($info['erreur'] as $err)
			$s .= "/!\ $err <br/>";
		$s .=  "</div>";
	}

	// puce d'etat du plugin
	// <etat>dev|experimental|test|stable</etat>
	$etat = 'dev';
	if (isset($info['etat']))
		$etat = $info['etat'];
	switch ($etat) {
		case 'experimental':
			$puce = 'puce-rouge.gif';
			$titre_etat = _T('plugin_etat_experimental');
			break;
		case 'test':
			$puce = 'puce-orange.gif';
			$titre_etat = _T('plugin_etat_test');
			break;
		case 'stable':
			$puce = 'puce-verte.gif';
			$titre_etat = _T('plugin_etat_stable');
			break;
		default:
			$puce = 'puce-poubelle.gif';
			$titre_etat = _T('plugin_etat_developpement');
			break;
	}
	$s .= "<img src='"._DIR_IMG_PACK."$puce' width='9' height='9' style='border:0;' alt=\"$titre_etat\" title=\"$titre_etat\" />&nbsp;";
	if (!$erreur){
		$s .= "<input type='checkbox' name='statusplug_$plug_file' value='O' id='label_$id_input'";
		$s .= $plugok?" checked='checked'":"";
		$s .= " onclick='verifchange$iteration(this)' /> <label for='label_$id_input' style='display:none'>"._T('activer_plugin')."</label>";
	}
	$id_input++;
	
	$s .= bouton_block_invisible("$plug_file");
	$s .= ($plugok?"":"").typo($info['nom']).($plugok?"":"");


	$s .= "</div>";
	$s .= debut_block_invisible("$plug_file");
	$s .= "<div class='detailplugin'>";
	$s .= _T('version') .' '.  $info['version'] . " | <strong>$titre_etat</strong><br/>";
	$s .= _T('repertoire_plugins') .' '. $plug_file . "<br/>";

	if (isset($info['description']))
		$s .= "<hr/>" . propre($info['description']) . "<br/>";

	if (isset($info['auteur']))
		$s .= "<hr/>" . _T('auteur') .' '. propre($info['auteur']) . "<br/>";
	if (isset($info['lien']))
		$s .= "<hr/>" . _T('info_url') .' '. propre($info['lien']) . "<br/>";
	$s .= "</div>";
	$s .= fin_block();


	return $s;
}

?>
