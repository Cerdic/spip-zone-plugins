<?php
#-----------------------------------------------------#
#  Plugin  : Tweak SPIP - Licence : GPL               #
#  Auteur  : Patrice Vanneufville, 2006               #
#  Contact : patrice�.!vanneufville�@!laposte�.!net   #
#  Infos : http://www.spip-contrib.net/?article1554   #
#-----------------------------------------------------#

include_spip('inc/texte');
include_spip('inc/layer');

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(dirname(__FILE__)))));
define('_DIR_PLUGIN_TWEAK_SPIP',(_DIR_PLUGINS.end($p)));

// compatibilite spip 1.9
if(!function_exists(fin_gauche)) { function fin_gauche(){return false;} }


function tweak_styles_et_js() {
	global $couleur_claire;
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
div.cadre-padding ul li li div.nomtweak, div.cadre-padding ul li li div.nomtweak_on {
	border:1px solid #AFAFAF;
	padding:.3em .3em .6em .3em;
	font-weight:normal;
}
div.cadre-padding ul li li div.nomtweak a, div.cadre-padding ul li li div.nomtweak_on a {
	outline:0;
	outline:0 !important;
	-moz-outline:0 !important;
}
div.cadre-padding ul li li div.nomtweak_on {
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
div.detailtweak {
	border-top:1px solid #B5BECF;
	padding:.6em;
	background:#F5F5F5;
}
div.detailtweak hr {
	border-top:1px solid #67707F;
	border-bottom:0;
	border-left:0;
	border-right:0;
	}
EOF;
	echo "</style>";
	echo "<script type=\"text/javascript\"><!--
function verifchange(tweak, index, nb_var) {
 if(this.checked == true) {
 	classe = 'nomtweak_on';
	html = 'input';
	test = 1
 } else {
 	classe = 'nomtweak';
	html = 'valeur';
	test = 0
 }
 document.getElementById(tweak).className = classe;
 document.getElementById('tweak_'+tweak).value = test;
 for(i=1;i<=nb_var;i++) {
  j = index+i;
  var chaine=document.getElementById('tweak_'+j+'-'+html).innerHTML;
  if(html=='input') chaine=chaine.replace(/HIDDENTWEAKVAR__/,'');
  document.getElementById('tweak_'+j+'-visible').innerHTML = chaine;
 }
}
//--></script>";
}

// mise � jour des donn�es si envoi via formulaire
function enregistre_modif_tweaks(){
tweak_log("D�but : enregistre_modif_tweaks()");
	global $tweaks;
	// recuperer les tweaks dans l'ordre des $_POST
	$test = array();
	foreach($tweaks as $tweak) $test["tweak_".$tweak['id']] = $tweak['id'];
	$liste = array();
	if (!isset($_POST['desactive_tous']))
		foreach($_POST as $choix=>$val) if (isset($test[$choix]) && $val=='1') $liste[$test[$choix]]['actif'] = 1;
	global $connect_id_auteur, $connect_login;
	spip_log("Changement des tweaks actifs par l'auteur id=$connect_id_auteur : ".implode(', ',array_keys($liste)));
	ecrire_meta('tweaks',serialize($liste));
	ecrire_metas();
	// on force la r�installation complete des tweaks
	tweak_initialisation(true);
	// reinitialisation des pipelines, au cas ou
	unlink(_DIR_TMP."charger_pipelines.php");
tweak_log("Fin   : enregistre_modif_tweaks()");
}

function exec_tweak_spip_admin() {
tweak_log("D�but : exec_tweak_spip_admin()");
	global $connect_statut, $connect_toutes_rubriques;
	global $spip_lang_right;
	global $couleur_claire;
	global $tweaks, $spip_version_code;
	
	include_spip('tweak_spip_config');
	include_spip("inc/presentation");
	
	if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {
		debut_page(_T('icone_admin_plugin'), "configuration", "plugin");
		echo _T('avis_non_acces_page');
		fin_page();
		exit;
	}
	
	// mise a jour des donnees si envoi via formulaire
	// sinon fait une passe de verif sur les tweaks
	if (_request('changer_tweaks')=='oui'){
		enregistre_modif_tweaks();
		// pour la peine, un redirige, 
		// que les tweaks charges soient coherent avec la liste
		if ($spip_version_code>=1.92) include_spip('inc/headers');
		redirige_par_entete(generer_url_ecrire('tweak_spip_admin'));
	}
//	else
//		verif_tweaks();

	global $spip_version_code;
	if ($spip_version_code<1.92) 
  		debut_page(_T('tweak:titre'), 'configuration', 'tweak_spip');
  	else {
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('tweak:titre'), "configuration", "tweak_spip");
	}
	
	tweak_styles_et_js();
	echo "<br /><br /><br />";
	gros_titre(_T('tweak:titre'));
	echo barre_onglets("configuration", "tweak_spip");

	debut_gauche();
	debut_boite_info();
	echo propre(_T('tweak:help'));
	echo pipeline('affiche_gauche',array('args'=>array('exec'=>'tweak_spip_admin'),'data'=>''));
	creer_colonne_droite();
	echo pipeline('affiche_droite',array('args'=>array('exec'=>'tweak_spip_admin'),'data'=>''));
	debut_droite();
	lire_metas();

	debut_cadre_trait_couleur('administration-24.gif','','',_T('tweak:tweaks_liste'));

	echo "\n<table border='0' cellspacing='0' cellpadding='5' >",
		"<tr><td class='serif'>",
		'<p>'._T('tweak:presente_tweaks').'</p><br/>';
	foreach($temp = $tweaks as $tweak) $categ[$tweak['categorie']] = 1; ksort($categ);
	foreach(array_keys($categ) as $c) {
		echo '<p><strong>'._T('tweak:'.$c).'</strong></p>';
		echo '<ul>';
		foreach($temp = $tweaks as $tweak) if ($tweak['categorie']==$c) echo '<li>' . ligne_tweak($tweak) . "</li>\n";
		echo '</ul>';
	}
	echo "</td></tr></table>\n";

	echo generer_url_post_ecrire('tweak_spip_admin');
	echo "\n<input type='hidden' name='changer_tweaks' value='oui'>";
	foreach($temp = $tweaks as $tweak) echo "<input type='hidden' id='tweak_".$tweak['id']."' name='tweak_".$tweak['id']."' value='".($tweak['actif']?"1":"0")."' />";
	echo "\n<div style='text-align:$spip_lang_right'>",
		"<input type='submit' name='Valider' value='"._T('bouton_valider')."' class='fondo' ",
		"\"></div>";

# ce bouton est trop laid :-)
# a refaire en javascript, qui ne fasse que "decocher" les cases
#	echo "<div style='text-align:$spip_lang_left'>";
#	echo "<input type='submit' name='desactive_tous' value='"._T('bouton_desactive_tout')."' class='fondl'>";
#	echo "</div>";

	fin_cadre_trait_couleur();
//	fin_cadre_relief();

	echo pipeline('affiche_milieu',array('args'=>array('exec'=>'tweak_spip_admin'),'data'=>''));
	echo "</form>";

	echo fin_gauche(), fin_page();
tweak_log("Fin   : exec_tweak_spip_admin()");
}

// affiche un tweak sur une ligne
function ligne_tweak($tweak){
	static $id_input=0;
	$inc = $tweak_id = $tweak['id'];
	$actif = $tweak['actif'];
	$puce = $actif?'puce-verte.gif':'puce-rouge.gif';
	$titre_etat = _T('tweak:'.($actif?'':'in').'actif');
	$nb_var = intval($tweak['nb_variables']);
	$index = intval($tweak['basic']);
	
	$s = "<form><div id='$tweak_id' class='nomtweak".($actif?'_on':'')."'>";
/*
	if (isset($info['erreur'])){
		$s .=  "<div style='background:".$GLOBALS['couleur_claire']."'>";
		$erreur = true;
		foreach($info['erreur'] as $err)
			$s .= "/!\ $err <br/>";
		$s .=  "</div>";
	}
*/
	$s .= "<img src='"._DIR_IMG_PACK."$puce' width='9' height='9' style='border:0;' alt=\"$titre_etat\" title=\"$titre_etat\" />&nbsp;";

	$s .= "<input type='checkbox' name='foo_$inc' value='O' id='label_$id_input'";
	$s .= $actif?" checked='checked'":"";
	$s .= " onclick='verifchange.apply(this,[\"$inc\", $index, $nb_var])'";
	$s .= "/> <label for='label_$id_input' style='display:none'>"._T('tweak:activer_tweak')."</label>";

	$s .= bouton_block_invisible($tweak_id) . propre($tweak['nom']);

	$s .= "</div></form>";

	$s .= debut_block_invisible($tweak_id);

	$s .= "\n<div class='detailtweak'>";
	$s .= $tweak['description'];
	if ($tweak['auteur']!='') $s .= "<p>" . _T('auteur') .' '. $tweak['auteur'] . "</p>";
	$s .= "<hr/>" . _T('tweak:tweak') . (isset($tweak['code'])?" code":" $inc.php");
	if ($tweak['options']) $s .= ' | options';
	if ($tweak['fonctions']) $s .= ' | fonctions';
	foreach ($tweak as $pipe=>$fonc) if (is_tweak_pipeline($pipe, $pipe2)) $s .= ' | '.$pipe2;
	$s .= "</div>";

	$s .= fin_block();
	$id_input++;
	return $s;
}
?>