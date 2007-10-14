<?php
#-----------------------------------------------------#
#  Plugin  : Couteau Suisse - Licence : GPL           #
#  Auteur  : Patrice Vanneufville, 2006               #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net   #
#  Infos : http://www.spip-contrib.net/?article2166   #
#-----------------------------------------------------#

include_spip('inc/texte');
include_spip('inc/layer');
include_spip("inc/presentation");
/*
$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(dirname(__FILE__)))));
define('_DIR_PLUGIN_COUTEAU_SUISSE',(_DIR_PLUGINS.end($p)));
*/
// compatibilite spip 1.9
if ($GLOBALS['spip_version_code']<1.92) { function fin_gauche(){return false;} }

function cs_admin_styles_et_js() {
	global $couleur_claire, $couleur_foncee;
	// SPIP v193
/*
	if (!$couleur_claire) { 
		$couleurs = charger_fonction('couleurs', 'inc'); 
		$couleurs = $couleurs($GLOBALS['auteur_session']['prefs']['couleur']);
		print_r($couleurs);
	}
*/
	echo "<style type='text/css'>\n";
	echo <<<EOF
div.cadre-padding *{
/*	padding:0;
	margin:0;*/
}
div.cadre-padding form{
	padding:0;
	margin:0;
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
div.cadre-padding ul li li div.nomoutil, div.cadre-padding ul li li div.nomoutil_on {
	border:1px solid #AFAFAF;
	padding:.3em .3em .6em .3em;
	font-weight:normal;
}
div.cadre-padding ul li li div.nomoutil a, div.cadre-padding ul li li div.nomoutil_on a {
	outline:0;
	outline:0 !important;
	-moz-outline:0 !important;
}
div.cadre-padding ul li li div.nomoutil_on {
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
/* debut SPIP v1.93 */
div.cadre_padding form{
	padding:0;
	margin:0;
}

div.cadre_padding .titrem {
	display:inline;
	font-weight:normal;
	background-position:left 1pt;
	background-color:white;
	padding:0 0 0 12pt;
	cursor:help;
}
div.cadre-padding .deplie {
	cursor:default;
}
div.cadre-padding .hover {
	background-color:$couleur_foncee;
}
/* fin SPIP v1.93 */
input.checkbox {
	margin:0;
	cursor:pointer;
}
div.detail_outil {
	border-top:1px solid #B5BECF;
	padding:0 .5em .5em .5em;
	background:#F5F5F5;
}
div.detail_outil hr {
	border-top:1px solid #67707F;
	border-bottom:0;
	border-left:0;
	border-right:0;
	}

div.detail_outil p {
	margin:0.3em 1em .3em 0;
	padding:0;
}

div.detail_outil fieldset {
	margin:.8em 4em .5em 4em;
	-moz-border-radius:8px;
}

div.detail_outil legend {
	font-weight:bold;
}

div.detail_outil sup {
	font-size:85%;
	font-variant:normal;
	vertical-align:super;
}

EOF;
	echo "</style>";
	echo "<script type=\"text/javascript\"><!--

var Outils = new Array(); // Listes des outils

function submit_general(outil) {
	document.forms.submitform.afficher_outil.value = outil;
	document.forms.submitform.submit();
}

function outilcheck(ischecked, index) {
 outil = Outils[index][0];
 if(ischecked == true) {
 	classe = 'nomoutil_on';
	html = '-input';
	test = 1
 } else {
 	classe = 'nomoutil';
	html = '-valeur';
	test = 0
 }
 document.getElementById(outil).className = classe;
 document.getElementById('tweak_'+outil).value = test;

 if (Outils[index][1]>0) {
  var chaine=document.getElementById('tweak'+index+html).innerHTML;
  if(html=='-input') chaine=chaine.replace(/HIDDENCSVAR__/g,'');
  document.getElementById('tweak'+index+'-visible').innerHTML = chaine;
 }
}

function categ_outil(categ, lesoutils, count) {
 for(tk=0;tk<count;tk++) {
 	name = Outils[lesoutils[tk]][0];
	if (!document.getElementsByName('foo_'+name)[0].disabled) {
		document.getElementsByName('foo_'+name)[0].checked = this.checked;
		outilcheck(this.checked, lesoutils[tk]);
	}
 }
}

function outilchange(index) {
 outilcheck(this.checked, index);
}
//--></script>";
}

// mise à jour des données si envoi via formulaire
function enregistre_modif_outils(){
cs_log("Début : enregistre_modif_outils()");
	global $outils;
	// recuperer les outils dans l'ordre des $_POST
	$test = array();
	foreach($outils as $outil) $test["tweak_".$outil['id']] = $outil['id'];
	$liste = array();
	if (!isset($_POST['desactive_tous']))
		foreach($_POST as $choix=>$val) if (isset($test[$choix]) && $val=='1') $liste[$test[$choix]]['actif'] = 1;
	global $connect_id_auteur, $connect_login;
	spip_log("Changement des outils actifs par l'auteur id=$connect_id_auteur : ".implode(', ',array_keys($liste)));
	ecrire_meta('tweaks_actifs', serialize($liste));
	ecrire_metas();
		include_spip('inc/invalideur');
@unlink(_DIR_TMP."charger_pipelines.php");
@unlink(_DIR_TMP."charger_plugins_fonctions.php");
@unlink(_DIR_TMP."charger_plugins_options.php");
//		supprime_invalideurs();
		purger_repertoire(_DIR_CACHE);
		purger_repertoire(_DIR_SKELS);
		@unlink(_DIR_TMP."couteau-suisse.plat");
	cs_initialisation_totale();

cs_log("Fin   : enregistre_modif_outils()");
}

function exec_admin_couteau_suisse() {
cs_log("Début : exec_admin_couteau_suisse()");
	global $connect_statut, $connect_toutes_rubriques;
	global $spip_lang_right;
	global $couleur_claire;
	global $outils;

	if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {
		debut_page(_T('icone_admin_plugin'), "configuration", "plugin");
		echo _T('avis_non_acces_page');
		fin_page();
		exit;
	}

	// reset general
	if (_request('cmd')=='resetall'){
		spip_log("Reset de tous les outils par l'auteur id=$connect_id_auteur");
		foreach(array_keys($GLOBALS['meta']) as $meta) {
			if(strpos($meta, 'tweaks_') === 0) effacer_meta($meta);
			if(strpos($meta, 'cs_') === 0) effacer_meta($meta);
		}
		ecrire_metas();
		cs_initialisation(true);
		if ($GLOBALS['spip_version_code']>=1.92) include_spip('inc/headers');
		redirige_par_entete(generer_url_ecrire('admin_couteau_suisse'));
	}

	// afficher un outil completement ?
	$afficher_outil = $_GET['afficher_outil'];
	if (!strlen($afficher_outil) || $afficher_outil=='non' ) $afficher_outil = -1;
		else $afficher_outil = intval($afficher_outil);

	// initialisation generale forcee : recuperation de $outils;
	cs_initialisation(true);
	// mise a jour des donnees si envoi via formulaire
	// sinon fait une passe de verif sur les outils
	if (_request('changer_outils')=='oui'){
		enregistre_modif_outils();
		// pour la peine, un redirige,
		// que les outils charges soient coherent avec la liste
		if ($GLOBALS['spip_version_code']>=1.92) include_spip('inc/headers');
		$afficher_outil = _request('afficher_outil');
		if (strlen($afficher_outil) && $afficher_outil!=='non')
			redirige_par_entete(generer_url_ecrire('admin_couteau_suisse', "afficher_outil=$afficher_outil", true) . "#outil$afficher_outil");
			else redirige_par_entete(generer_url_ecrire('admin_couteau_suisse'));
	}
//	else
//		verif_outils();

	if ($GLOBALS['spip_version_code']<1.92)
  		debut_page(_T('cout:titre'), 'configuration', 'couteau_suisse');
  	else {
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('cout:titre'), "configuration", 'couteau_suisse');
	}

	cs_admin_styles_et_js();
	echo "<br /><br /><br />";
	gros_titre(_T('cout:titre'));
	echo barre_onglets("configuration", 'couteau_suisse');

	debut_gauche();
	debut_boite_info();
	echo propre(_T('cout:help', array(
		'reset' => generer_url_ecrire(_request('exec'),'cmd=resetall'),
		'hide' => generer_url_ecrire(_request('exec'),'cmd=showall'))));
	fin_boite_info();
	$aide_racc = cs_aide_raccourcis();
	if(strlen($aide_racc)) {
//		echo '<br />';
		debut_boite_info();
		echo $aide_racc;
		fin_boite_info();
	}
	$aide_pipes = cs_aide_pipelines();
	if(strlen($aide_pipes)) {
//		echo '<br />';
		debut_boite_info();
		echo $aide_pipes;
		fin_boite_info();
	}

	echo pipeline('affiche_gauche',array('args'=>array('exec'=>'admin_couteau_suisse'),'data'=>''));
	creer_colonne_droite();
	echo pipeline('affiche_droite',array('args'=>array('exec'=>'admin_couteau_suisse'),'data'=>''));
	debut_droite();
	lire_metas();

	debut_cadre_trait_couleur(find_in_path('img/couteau-24.gif'),'','','&nbsp;'._T('cout:liste_outils'));

	$valider = "\n<div style='text-align:$spip_lang_right'>"
		. "<input type='submit' name='Valider1' value='"._T('bouton_valider')."' class='fondo' onclick='document.forms.submitform.submit()' /></div>";
	echo _T('cout:presente_outils', array('triangle'=>'<img src="'._DIR_IMG_PACK.'deplierhaut.gif" />')), $valider;
echo '<p style="color:red;">Testez la nouvelle interface du plugin : <a href="', generer_url_ecrire('admin2'), '">par ici !</a></p>';
	echo "\n<table border='0' cellspacing='0' cellpadding='5' ><tr><td class='sansserif'>";
	foreach($temp = $outils as $outil) $categ[_T('cout:'.$outil['categorie'])] = $outil['categorie']; ksort($categ);

	$js = ''; $marge = '0';
	foreach($categ as $c=>$i) {
		$basics = array(); $s = '';
		foreach($temp = $outils as $outil) if ($outil['categorie']==$i) {
			$s .= ligne_outil($outil, $js, $afficher_outil==$outil['index']) . "\n";
			$basics[] = $outil['index'];
		}
		$ss = "<input type='checkbox' class='checkbox' name='foo_$i' value='O' id='label_{$i}_categ'";
//		$ss .= $actif?" checked='checked'":"";
		$ss .= " onclick='categ_outil.apply(this,[\"$i\", [".join(', ', $basics).'], '.count($basics)."])' />";
		$ss .= "<label for='label_{$i}_categ' style='display:none'>"._T('cout:activer_outil')."</label>";
		preg_match(',([0-9]+)\.?\s*(.*),', _T('cout:'.$c), $reg);
		echo "<form style='margin-top:$marge; margin-left:2em;'>$ss&nbsp;<strong>$reg[2]</strong></form>\n", $s;
		$marge = '.8em';
	}
	echo "</td></tr></table>\n";
	echo "<script type=\"text/javascript\"><!--\n$js\n//--></script>";

	echo generer_url_post_ecrire('admin_couteau_suisse', '', 'submitform');
	echo "\n<input type='hidden' name='changer_outils' value='oui'>";
	echo "\n<input type='hidden' name='afficher_outil' value='non'>";
	foreach($temp = $outils as $outil) echo "<input type='hidden' id='tweak_".$outil['id']."' name='tweak_".$outil['id']."' value='".($outil['actif']?"1":"0")."' />";
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

	echo pipeline('affiche_milieu',array('args'=>array('exec'=>'admin_couteau_suisse'),'data'=>''));
	echo "</form>";

	echo fin_gauche(), fin_page();
cs_log("Fin   : exec_admin_couteau_suisse()");
}

// affiche un outil sur une ligne
function ligne_outil($outil, &$js, $afficher){
	static $id_input=0;
	$inc = $outil_id = $outil['id'];
	$actif = $outil['actif'];
	$erreur_version = (isset($outil['version-min']) && $GLOBALS['spip_version']<$outil['version-min'])
		|| (isset($outil['version-max']) && $GLOBALS['spip_version']>$outil['version-max']);
	$puce = $actif?'puce-verte.gif':'puce-rouge.gif';
	$titre_etat = _T('cout:'.($actif?'':'in').'actif');
	$nb_var = intval($outil['nb_variables']);
	$index = intval($outil['index']);
	$pliage_id = 'plie_'.$outil_id;

	$s = "<a name='outil$index' id='outil$index'></a><form  style='margin:0 0 0 1em;'><div id='$outil_id' class='nomoutil".($actif?'_on':'')."'>";
/*
	if (isset($info['erreur'])){
		$s .=  "<div style='background:".$GLOBALS['couleur_claire']."'>";
		$erreur = true;
		foreach($info['erreur'] as $err)
			$s .= "/!\ $err <br/>";
		$s .=  "</div>";
	}
*/
	$p = '<div style="margin:0 0 0 2em;">';
	$p .= "<img src='"._DIR_IMG_PACK."$puce' name='puce_$id_input' width='9' height='9' style='border:0;' alt=\"$titre_etat\" title=\"$titre_etat\" />&nbsp;";
	$p .= "<input type='checkbox' class='checkbox' name='foo_$inc' value='O' id='label_$id_input' style=''";
	$p .= $actif?" checked='checked'":"";
	$p .= $erreur_version?" disabled='disabled'":"";
	$p .= " onclick='outilchange.apply(this,[$index])'";
	$p .= "/> <label for='label_$id_input' style='display:none'>"._T('cout:activer_outil')."</label>";
	$js .= "Outils[$index] = Array(\"$inc\", $nb_var);\n";
	// compatibilite SPIP < v1.93
	if(function_exists('bouton_block_depliable'))
		$p .= bouton_block_depliable($outil['nom'], $afficher, $pliage_id);
		else $p .= ($afficher?bouton_block_visible($pliage_id):bouton_block_invisible($pliage_id)) . $outil['nom'];
	$p .= '</div>';
	$s .= propre($p) . "</div></form>";

	// compatibilite SPIP < v1.93
	if(function_exists('debut_block_depliable'))
		$p = debut_block_depliable($afficher, $pliage_id);
		else $p = $afficher?debut_block_visible($pliage_id):debut_block_invisible($pliage_id);
	$p .= "\n<div class='detail_outil'>";
	$p .= $outil['description'];
	if (isset($outil['jquery']) && $outil['jquery']=='oui') $p .= '<p>' . _T($GLOBALS['spip_version_code']<1.92?'cout:jquery1':'cout:jquery2') . '</p>';
	if (isset($outil['auteur']) && strlen($outil['auteur'])) $p .= '<p>' . _T('auteur') .' '. ($outil['auteur']) . '</p>';
	if (isset($outil['contrib']) && strlen($outil['contrib'])) $p .= '<p>' . _T('cout:contrib', array('id'=>$outil['contrib'])) . '</p>';
	$s .= propre($p) . '<hr style="margin:6pt 0 0 0;"/><div style="font-size:85%;">' . _T('cout:detail_outil').' ';
	if ($erreur_version) $s .= _T('cout:erreur:version');
	else {
		$a = array();
		if(isset($outil['code:options'])) $a[] = "code options";
		if(isset($outil['code:fonction'])) $a[] = "code fonctions";
		if(isset($outil['code:js'])) $a[] = "code javascript";
		if(isset($outil['code:css'])) $a[] = "code styles";
		if (find_in_path('outils/'.($temp=$outil_id.'.php'))) $a[] = $temp;
		if (find_in_path('outils/'.($temp=$outil_id.'_options.php'))) $a[] = $temp;
		if (find_in_path('outils/'.($temp=$outil_id.'_fonctions.php'))) $a[] = $temp;
		foreach ($outil as $pipe=>$fonc) if (is_pipeline_outil($pipe, $pipe2)) $a[] = $pipe2;
		if (find_in_path('outils/'.($temp=$outil_id.'.js'))) $a[] = $temp;
		if (find_in_path('outils/'.($temp=$outil_id.'.css'))) $a[] = $temp;
		$s .= join(' | ', $a);
	}
	$s .= "</div></div>";

	$s .= fin_block();
	$id_input++;
	return $s;
}
?>
