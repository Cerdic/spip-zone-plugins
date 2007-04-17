<?php
#####################################################################################
# Base sur le plugin ICOP 1.0 (03/2007) de H. AROUX . Scoty . koakidi.com. Une      #
# tres grosse partie du code a ete produit par ses soins et adapte au plugin        #
# "habillages" par franck.ducas at free.fr.                                         #
#####################################################################################

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_HABILLAGES',(_DIR_PLUGINS.end($p)));

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/config');
include_spip('inc/plugin');
include_spip('inc/presentation');
include_spip('inc/layer');
include_spip('inc/actions');
include_spip('inc/meta');

// http://doc.spip.org/@exec_admin_plugin
function exec_habillages_icones() {
global 	$connect_statut,
		$connect_toutes_rubriques,
		$connect_id_auteur,
		$couleur_claire, $couleur_foncee,
		$mes_couleurs;
	
	if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {
	debut_page(_T('commun:titre_plug'), "configuration", "habillages");
		echo _T('avis_non_acces_page');
		fin_page();
		exit;
	}

		
	#
	# changer le pack icones
	#
		if (($cp=_request('change_pack'))!==NULL ) {
			ecrire_meta('habillages_img_pack',$cp);
			ecrire_metas();
		}
    
	#
	# Def.
	#
	$check = "checked='checked'";

	if (isset($_GET['surligne']))
	$surligne = $_GET['surligne'];
		
	debut_page(_T('habillages:icone_habillages_icones'), "configuration", "habillages");
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
	margin:0;
	padding:0 0 0.25em 0;
}
div.cadre-padding ul li li div.nomplugin, div.cadre-padding ul li li div.nomplugin_on {
	border:1px solid #AFAFAF;
	padding:.3em .3em .6em .3em;
	font-weight:normal;
}
div.cadre-padding ul li li div.nomplugin a, div.cadre-padding ul li li div.nomplugin_on a {
	outline:0;
	outline:0 !important;
	-moz-outline:0 !important;
}
div.cadre-padding ul li li div.nomplugin_on {
	background:$couleur_claire;
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

	echo "<br/><br/>";
	
	echo '<img src="' . _DIR_PLUGIN_HABILLAGES. '/../img_pack/habillages_icone-48.png">';
	gros_titre(_T('commun:titre_plug'));

	echo barre_onglets("habillages", "");
	
	debut_gauche();
	
#
# lister couleur dispo
#

$meta_coul = array();
if($GLOBALS['meta']['habillages_couleurs']!='') {
	$meta_coul = explode(',',$GLOBALS['meta']['habillages_couleurs']);
}

	debut_boite_info();
	echo "<div class='intro_grotitre'>";
	echo gros_titre(_T('icones:ajout_couleur'))."</div><br />";
	echo '<form action="'.generer_url_action('habillages_ecrirecouleur').'" method="post">';
	echo "<input type='hidden' name='redirect' value='".generer_url_ecrire("habillages_icones")."' />\n";
	echo "<input type='hidden' name='hash' value='".calculer_action_auteur("ecrirecouleur-rien")."' />\n";
	echo "<input type='hidden' name='id_auteur' value='".$connect_id_auteur."' />\n";
	
	echo "<table width='105' cellpadding='2' cellspacing='0' border='0' align='center'>";
	$i=0;
	foreach($mes_couleurs as $nc => $val) {
		$i++;
		$aff_check = (in_array($nc,$meta_coul))? $check: '';
		// au dela de 9 (icop) => couleurs perso : mes_couleurs.php
		if($i==10) {
			echo "<tr bgcolor='".$couleur_claire."'><td colspan='3'></td></tr>\n";
		}
		echo "<tr><td width='20'><input type='checkbox' name='ajout_coul[]' value='$nc' ".$aff_check." /></td>";
		echo "<td>".http_img_pack("rien.gif",'',"width='35' height='15' style='background-color:".$val['couleur_foncee'].";'")."</td>";
		echo "<td>".http_img_pack("rien.gif",'',"width='35' height='15' style='background-color:".$val['couleur_claire'].";'")."</td></tr>";
		
	}

	echo "</table>";
	echo "<div align='right'><input type='submit' value='"._T('valider')."' class='fondo' /></div>";
	echo "</form>"; 
	echo "<div class='intro'>";
	echo _T('icones:def_couleurs');
	echo "</div>";
	fin_boite_info();
	
	debut_boite_info();
	ecrire_meta('habillages_theme_prive','plugins/habillages/themes_natifs/styles_prives/spipZ/');
	ecrire_metas();
	echo $GLOBALS['meta']['habillages_theme_prive'];
	fin_boite_info();
	

	# lister les repertoires d'icones
	$meta_pack = $GLOBALS['meta']['habillages_img_pack'];
	$derrep = strtok($meta_pack,'/');
	while($derrep = strtok('/')) { $pack_actif=$derrep; }
	
	if($meta_pack=='' || $meta_pack==_DIR_IMG_PACK) {
		$meta_pack=_DIR_IMG_PACK;
		$pack_actif='Spip';
	}

	
	# on force spip en premier !
	$packs=array();
	$packs[]='spip';
	
	$d = dir(_DIR_PLUGIN_HABILLAGES.'/themes_natifs/icones_prives/');
    while (false !== ($entry = $d->read()) ) {
		if($entry!= "." && $entry != "..") {
			$packs[]=$entry;
		}
	}
	$d->close();


	debut_droite();
    
	echo generer_url_post_ecrire("habillages_icones");
	
	debut_cadre_couleur(_DIR_PLUGIN_HABILLAGES."/../img_pack/habillages_icones-22.png");	
	
	debut_boite_info();
	echo gros_titre(_T('icones:gros_titre_selecteur'));
	echo _T('icones:def_page_selecteur');
	echo "<p><strong>";
	echo (_T('icones:pack_actif', array('pack' => majuscules($pack_actif))));
	echo "</strong></p>";
	fin_boite_info();
	echo "<br />";

# affichage

	debut_boite_info();
	echo '<form action="'.generer_url_ecrire('habillages_icones').'" method="post">';
	echo "<div align='right'><input type='submit' value='"._T('valider')."' class='fondo' /></div>";
	
	foreach($packs as $pack) {
		
		if($pack=='spip') {
			$repert = _DIR_IMG_PACK;
			$nom_theme = 'Spip';
			#$value='spip';
		}
		else {
			$repert = _DIR_PLUGIN_HABILLAGES.'/themes_natifs/icones_prives/'.$pack."/";
			$theme = $repert."/theme.xml";
			lire_fichier($theme, $texte);
				$arbre = parse_plugin_xml($texte);
				$arbre = $arbre['theme'][0];
				$type_theme = trim(applatit_arbre($arbre['type']));
				$nom_theme = applatit_arbre($arbre['nom']);
				$auteur_theme = applatit_arbre($arbre['auteur']);
				$version_theme = applatit_arbre($arbre['version']);
				$description_theme = applatit_arbre($arbre['description']);
			#$value = $repert;
		}
		$pack_select = "<img src='".$repert."puce-verte.gif' />";
	
	echo "<p></p>";
    echo "<table border='0' cellpadding='0' cellspacing='0' id='subtab' align='center'>";

	echo "<tr><td id='hab_input' class='toile_foncee hab_stitre'>";
	echo "<input type='radio' name='change_pack' value='$repert' ".(($meta_pack==$repert)? $check : '')." /></td>";
	echo "<td id='hab_inputxt' class='toile_foncee hab_stitre'>";
	echo (($meta_pack==$repert)? $pack_select."&nbsp;" : '')."<span class='verdana3'><b>".$nom_theme."</b></span>
			&nbsp;&middot;&middot;&nbsp;<span class='verdana2'>".$version_theme."</span>";

	echo "</td>";
	echo "<td id='hab_inpuico' class='toile_foncee hab_stitre'>";

    echo "<a href='".generer_url_ecrire('icop_listing','pack='.$pack)."' title='"._T('icones:voir_toutes_icones')."'>";
    echo "<img src='".$repert."cal-suivi.png' /></a>";
    
	echo "</td></tr>";
	echo "<tr>";
	echo "<td colspan='3' class='toile_claire hab_fondclair cellule48' onmouseover='changestyle('bandeauaccueil');>";
	echo "<a href='#'></a>";
	echo "</td></tr>";
		echo "<tr>";
	echo "<td colspan='3' class='toile_claire hab_fondclair cellule48' onmouseover='changestyle('bandeauaccueil');>";
	echo "<a href='#'>";
	echo "<img src='".$repert."asuivre-48.png' title='' alt='ico' />";
	echo "<img src='".$repert."documents-48.png' title='' alt='ico' />";
	echo "<img src='".$repert."messagerie-48.png' title='' alt='ico' />";
	echo "<img src='".$repert."redacteurs-48.png' title='' alt='ico' />";
	echo "<img src='".$repert."statistiques-48.png' title='' alt='ico' />";
	echo "<img src='".$repert."administration-48.png' title='' alt='ico' /></a>";
	echo "</td></tr>";
	echo "</table>";
		
	}

	
	fin_boite_info();
	
	echo "<br />";
	
	echo "\n<input type='hidden' name='id_auteur' value='$connect_id_auteur' />";
	echo "\n<input type='hidden' name='hash' value='" . calculer_action_auteur("valide_plugin") . "'>";
	echo "\n<input type='hidden' name='changer_gestion' value='oui'>";

	echo "\n<p>";

	echo "<div style='text-align:$spip_lang_right'>";
	echo "<input type='submit' name='Valider' value='"._T('bouton_valider')."' class='fondo'>";
	echo "</div>";
	echo "</form>";
	fin_cadre_couleur();
	
	
	echo fin_gauche(), fin_page();
	

}

?>
