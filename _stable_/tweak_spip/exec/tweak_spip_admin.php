<?php
#-----------------------------------------------------#
#  Plugin  : Tweak SPIP - Licence : GPL               #
#  Auteur  : Patrice Vanneufville, 2006               #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net   #
#  Infos : http://www.spip-contrib.net/?article1554   #
#-----------------------------------------------------#

include_spip('inc/texte');
include_spip('inc/layer');
include_spip("inc/presentation");
/*
$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(dirname(__FILE__)))));
define('_DIR_PLUGIN_TWEAK_SPIP',(_DIR_PLUGINS.end($p)));
*/
// compatibilite spip 1.9
if ($GLOBALS['spip_version_code']<1.92) { function fin_gauche(){return false;} }

function tweak_styles_et_js() {
	global $couleur_claire;
	echo "<style type='text/css'>\n";
	echo <<<EOF
div.cadre-padding *{
/*	padding:0;
	margin:0;*/
}
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
input.checkbox {
	margin:0;
	cursor:pointer;
}
div.detailtweak {
	border-top:1px solid #B5BECF;
	padding:0 .5em .5em .5em;
	background:#F5F5F5;
}
div.detailtweak hr {
	border-top:1px solid #67707F;
	border-bottom:0;
	border-left:0;
	border-right:0;
	}

div.detailtweak p {
	margin:.5em 0 .5em 0;
	padding:0;
}

EOF;
	echo "</style>";
	echo "<script type=\"text/javascript\"><!--

var Tweaks = new Array(); // Listes des tweaks

function tweakcheck(ischecked, index) {
 tweak = Tweaks[index][0];
 if(ischecked == true) {
 	classe = 'nomtweak_on';
	html = '-input';
	test = 1
 } else {
 	classe = 'nomtweak';
	html = '-valeur';
	test = 0
 }
 document.getElementById(tweak).className = classe;
 document.getElementById('tweak_'+tweak).value = test;

 for(ti=1;ti<=Tweaks[index][1];ti++) {
  tj = index+ti;
  var chaine=document.getElementById('tweak_'+tj+html).innerHTML;
  if(html=='-input') chaine=chaine.replace(/HIDDENTWEAKVAR__/,'');
  document.getElementById('tweak_'+tj+'-visible').innerHTML = chaine;
 }
}

function tweakcateg(categ, lestweaks, count) {
 for(tk=0;tk<count;tk++) {
 	name = Tweaks[lestweaks[tk]][0];
	if (!document.getElementsByName('foo_'+name)[0].disabled) {
		document.getElementsByName('foo_'+name)[0].checked = this.checked;
		tweakcheck(this.checked, lestweaks[tk]);
	}
 }
}

function tweakchange(index) {
 tweakcheck(this.checked, index);
}
//--></script>";
}

// mise à jour des données si envoi via formulaire
function enregistre_modif_tweaks(){
tweak_log("Début : enregistre_modif_tweaks()");
	global $tweaks;
	// recuperer les tweaks dans l'ordre des $_POST
	$test = array();
	foreach($tweaks as $tweak) $test["tweak_".$tweak['id']] = $tweak['id'];
	$liste = array();
	if (!isset($_POST['desactive_tous']))
		foreach($_POST as $choix=>$val) if (isset($test[$choix]) && $val=='1') $liste[$test[$choix]]['actif'] = 1;
	global $connect_id_auteur, $connect_login;
	spip_log("Changement des tweaks actifs par l'auteur id=$connect_id_auteur : ".implode(', ',array_keys($liste)));
	ecrire_meta('tweaks_actifs', serialize($liste));
	ecrire_metas();
		include_spip('inc/invalideur');
@unlink(_DIR_TMP."charger_pipelines.php");
@unlink(_DIR_TMP."charger_plugins_fonctions.php");
@unlink(_DIR_TMP."charger_plugins_options.php");
		supprime_invalideurs();
		purger_repertoire(_DIR_CACHE);
		purger_repertoire(_DIR_SKELS);
		@unlink(_DIR_TMP."tweak-spip.plat");
	tweak_initialisation_totale();

tweak_log("Fin   : enregistre_modif_tweaks()");
}

function exec_tweak_spip_admin() {
tweak_log("Début : exec_tweak_spip_admin()");
	global $connect_statut, $connect_toutes_rubriques;
	global $spip_lang_right;
	global $couleur_claire;
	global $tweaks;

	// reset general
	if (_request('reset')=='oui'){
		spip_log("Reset de tous les tweaks par l'auteur id=$connect_id_auteur");
		foreach(array_keys($GLOBALS['meta']) as $meta)
			if(strpos($meta, 'tweaks_') !== false) effacer_meta($meta);
		ecrire_metas();
	}

	if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {
		debut_page(_T('icone_admin_plugin'), "configuration", "plugin");
		echo _T('avis_non_acces_page');
		fin_page();
		exit;
	}

	// definitions manuelles de tous les tweaks : remplissage de $tweaks
//	include_spip('tweak_spip_config');
	// initialisation generale forcee : recuperation de $tweaks;
	tweak_initialisation(true);
	// mise a jour des donnees si envoi via formulaire
	// sinon fait une passe de verif sur les tweaks
	if (_request('changer_tweaks')=='oui'){
		enregistre_modif_tweaks();
		// pour la peine, un redirige,
		// que les tweaks charges soient coherent avec la liste
		if ($GLOBALS['spip_version_code']>=1.92) include_spip('inc/headers');
		redirige_par_entete(generer_url_ecrire('tweak_spip_admin'));
	}
//	else
//		verif_tweaks();

	if ($GLOBALS['spip_version_code']<1.92)
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
	fin_boite_info();
	$aide_racc = tweak_aide_raccourcis();
	if(strlen($aide_racc)) {
		echo '<br />';
		debut_boite_info();
		echo $aide_racc;
		fin_boite_info();
	}
	$aide_pipes = tweak_aide_pipelines();
	if(strlen($aide_pipes)) {
		echo '<br />';
		debut_boite_info();
		echo $aide_pipes;
		fin_boite_info();
	}

	echo pipeline('affiche_gauche',array('args'=>array('exec'=>'tweak_spip_admin'),'data'=>''));
	creer_colonne_droite();
	echo pipeline('affiche_droite',array('args'=>array('exec'=>'tweak_spip_admin'),'data'=>''));
	debut_droite();
	lire_metas();

	debut_cadre_trait_couleur('administration-24.gif','','',_T('tweak:tweaks_liste'));

	$valider = "\n<div style='text-align:$spip_lang_right'>"
		. "<input type='submit' name='Valider1' value='"._T('bouton_valider')."' class='fondo' onclick='document.forms.submitform.submit()' /></div>";
	echo _T('tweak:presente_tweaks'), $valider;
	echo "\n<table border='0' cellspacing='0' cellpadding='5' ><tr><td class='sansserif'>";
	foreach($temp = $tweaks as $tweak) $categ[_T('tweak:'.$tweak['categorie'])] = $tweak['categorie']; ksort($categ);

	$js = ''; $marge = '0';
	foreach($categ as $c=>$i) {
		$basics = array(); $s = '';
		foreach($temp = $tweaks as $tweak) if ($tweak['categorie']==$i) {
			$s .= ligne_tweak($tweak, $js) . "\n";
			$basics[] = $tweak['basic'];
		}
		$ss = "<input type='checkbox' class='checkbox' name='foo_$i' value='O' id='label_{$i}_categ'";
//		$ss .= $actif?" checked='checked'":"";
		$ss .= " onclick='tweakcateg.apply(this,[\"$i\", [".join(', ', $basics).'], '.count($basics)."])' />";
		$ss .= "<label for='label_{$i}_categ' style='display:none'>"._T('tweak:activer_tweak')."</label>";
		preg_match(',([0-9]+)\.?\s*(.*),', _T('tweak:'.$c), $reg);
		echo "<form style='margin-top:$marge; margin-left:2em;'>$ss&nbsp;<strong>$reg[2]</strong></form>\n", $s;
		$marge = '.8em';
	}
	echo "</td></tr></table>\n";
	echo "<script type=\"text/javascript\"><!--\n$js\n//--></script>";

	echo generer_url_post_ecrire('tweak_spip_admin', '', 'submitform');
	echo "\n<input type='hidden' name='changer_tweaks' value='oui'>";
	foreach($temp = $tweaks as $tweak) echo "<input type='hidden' id='tweak_".$tweak['id']."' name='tweak_".$tweak['id']."' value='".($tweak['actif']?"1":"0")."' />";
	$valider = "\n<div style='margin-top:0.4em; text-align:$spip_lang_right'>"
		. "<input type='submit' name='Valider2' value='"._T('bouton_valider')."' class='fondo' /></div>";
	echo $valider;

# ce bouton est trop laid :-)
# a refaire en javascript, qui ne fasse que "decocher" les cases
#	echo "<div style='text-align:$spip_lang_left'>";
#	echo "<input type='submit' name='desactive_tous' value='"._T('bouton_desactive_tout')."' class='fondl' />";
#	echo "</div>";

	fin_cadre_trait_couleur();
//	fin_cadre_relief();

	echo pipeline('affiche_milieu',array('args'=>array('exec'=>'tweak_spip_admin'),'data'=>''));
	echo "</form>";

	echo fin_gauche(), fin_page();
tweak_log("Fin   : exec_tweak_spip_admin()");
}

// affiche un tweak sur une ligne
function ligne_tweak($tweak, &$js){
	static $id_input=0;
	$inc = $tweak_id = $tweak['id'];
	$actif = $tweak['actif'];
	$erreur_version = (isset($tweak['version-min']) && $GLOBALS['spip_version']<$tweak['version-min'])
		|| (isset($tweak['version-max']) && $GLOBALS['spip_version']>$tweak['version-max']);
	$puce = $actif?'puce-verte.gif':'puce-rouge.gif';
	$titre_etat = _T('tweak:'.($actif?'':'in').'actif');
	$nb_var = intval($tweak['nb_variables']);
	$index = intval($tweak['basic']);

	$s = "<form  style='margin:0 0 0 1em;'><div id='$tweak_id' class='nomtweak".($actif?'_on':'')."'>";
/*
	if (isset($info['erreur'])){
		$s .=  "<div style='background:".$GLOBALS['couleur_claire']."'>";
		$erreur = true;
		foreach($info['erreur'] as $err)
			$s .= "/!\ $err <br/>";
		$s .=  "</div>";
	}
*/
	$p = '<p style="margin:0 0 0 2em;">';
	$p .= "<img src='"._DIR_IMG_PACK."$puce' name='puce_$id_input' width='9' height='9' style='border:0;' alt=\"$titre_etat\" title=\"$titre_etat\" />&nbsp;";

	$p .= "<input type='checkbox' class='checkbox' name='foo_$inc' value='O' id='label_$id_input'";
	$p .= $actif?" checked='checked'":"";
	$p .= $erreur_version?" disabled='disabled'":"";
	$p .= " onclick='tweakchange.apply(this,[$index])'";
	$p .= "/> <label for='label_$id_input' style='display:none'>"._T('tweak:activer_tweak')."</label>";
	$js .= "Tweaks[$index] = new Array(\"$inc\", $nb_var);\n";
	$p .= bouton_block_invisible($tweak_id) . $tweak['nom'] . '</p>';

	$s .= propre($p) . "</div></form>";

	$p = debut_block_invisible($tweak_id);

	$p .= "\n<div class='detailtweak'>";
	$p .= $tweak['description'];
	if (isset($tweak['auteur']) && strlen($tweak['auteur'])) $p .= "<p>" . _T('auteur') .' '. ($tweak['auteur']) . "</p>";
	$s .= propre($p) . '<hr style="margin:0"/>' . _T('tweak:tweak').' ';
	if ($erreur_version) $s .= _T('tweak:erreur:version');
	else {
		$a = array();
		if(isset($tweak['code:options'])) $a[] = "code options";
		if(isset($tweak['code:fonction'])) $a[] = "code fonctions";
		if (find_in_path('tweaks/'.($temp=$tweak_id.'.php'))) $a[] = $temp;
		if (find_in_path('tweaks/'.($temp=$tweak_id.'_options.php'))) $a[] = $temp;
		if (find_in_path('tweaks/'.($temp=$tweak_id.'_fonctions.php'))) $a[] = $temp;
		foreach ($tweak as $pipe=>$fonc) if (is_tweak_pipeline($pipe, $pipe2)) $a[] = $pipe2;
		if (find_in_path('tweaks/'.($temp=$tweak_id.'.js'))) $a[] = $temp;
		if (find_in_path('tweaks/'.($temp=$tweak_id.'.css'))) $a[] = $temp;
		$s .= join(' | ', $a);
	}
	$s .= "</div>";

	$s .= fin_block();
	$id_input++;
	return $s;
}
?>