<?php
#-----------------------------------------------------#
#  Plugin  : Couteau Suisse - Licence : GPL           #
#  Auteur  : Patrice Vanneufville, 2006               #
#  Contact : patrice�.!vanneufville�@!laposte�.!net   #
#  Infos : http://www.spip-contrib.net/?article1554   #
#-----------------------------------------------------#

include_spip('inc/texte');
include_spip('inc/layer');
include_spip("inc/presentation");
/*
$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(dirname(__FILE__)))));
define('_DIR_PLUGIN_COUTEAU_SUISSE',(_DIR_PLUGINS.end($p)));
*/
// compatibilite spip 1.9
if ($GLOBALS['spip_version_code']<1.92) { 
	function fin_gauche(){return false;}
	@define('_CS_NETTOYEUR','<br style="border:0pt none; clear:both; font-size:1px; height:0pt; line-height:1px; margin:0pt; padding:0pt;"/>');
}
@define('_CS_NETTOYEUR', '<br class="nettoyeur"/>');

function cs_admin_styles_et_js() {
	global $afficher_outil;
	echo <<<EOF
<style type='text/css'>

div.cadre-padding form{
	padding:0;
	margin:0;
}

/* debut SPIP v1.93 */
div.cadre_padding form{
	padding:0;
	margin:0;
}
/* fin SPIP v1.93 */

div.cs_infos hr {
	border-top:1px solid #67707F;
	border-bottom:0;
	border-left:0;
	border-right:0;
	}

div.cs_infos p {
	margin:0.3em 1em .3em 0;
	padding:0;
}

div.cs_infos fieldset {
	margin:.8em 4em .5em 4em;
/*	-moz-border-radius:8px; */
}

div.cs_infos legend {
	font-weight:bold;
}

div.cs_infos sup {
	font-size:85%;
	font-variant:normal;
	vertical-align:super;
}

/* V2.0 */
a.cs_href {
	font-weight:normal;
}
a.outil_on {
	font-weight:bold;
}
div.cs_liste {
	float:left;
	width:46%;
}

div.cs_outils {
	clear:both;
	float:left;
	width:100%;
	margin-top:0.8em;
}

div.cs_toggle {
	float:left;
	width:8%;
	text-align:center;
	margin-top:50px;
}

div.cs_infos {
	margin-top:1em;
}
div.cs_infos h3.titrem {
	border-bottom:solid 1px;
	font-weight:bold;
	display:block;
}

div.cs_infos p {
	margin:0.3em 1em 0.3em 0pt;
	padding:0pt;
}
</style>
EOF;
	echo "<script type=\"text/javascript\"><!--
// V2.0

var cs_selected, cs_descripted;
function set_selected() {
	cs_selected = new Array();
	jQuery('a.outil_on').each( function(i){
		cs_selected[i] = this.name;
	});
	if(cs_selected.length) {
			jQuery('div.cs_toggle div').show();
			jQuery('#cs_toggle_p').html('('+cs_selected.length+')');
		} else jQuery('div.cs_toggle div').hide();
}

jQuery(function(){";
//affichage en gras
//if(strlen($afficher_outil)) echo "\n\tjQuery('#href_$afficher_outil').addClass('outil_on');";
echo "
	// clic sur un outil
	jQuery('a.cs_href').click( function() {
		jQuery(this).toggleClass('outil_on');
		set_selected();
		// on s'en va si l'outil est deja affiche
		if(cs_descripted==this.name) return false;
		cs_descripted=this.name;
		// on charge la nouvelle description
		jQuery('#cs_infos')
			.css('opacity', '0.5')
			.parent()
			.prepend(ajax_image_searching)
			.load('".generer_url_ecrire('charger_description_outil', 'source='._request('exec').'&outil=', '\\x26')."'+this.name);
		// annulation du clic
		return false;
	});
	
	// clic surle bouton de permutation
	jQuery('#cs_toggle_a').click( function() {
		if(cs_selected.length>1) {
			msg=\""._T('cout:permuter_outils')."\";
			msg=msg.replace(/@nb@/, cs_selected.length);
		} else {
			msg=\""._T('cout:permuter_outil')."\";
			msg=msg.replace(/@text@/, jQuery('a.outil_on').text());
		}
		if (!confirm(msg)) return false;
		jQuery('#cs_selection').attr('value', cs_selected.join(','));
//		jQuery('#csform').submit();
		document.csform.submit();
/*		// on charge la nouvelle liste, actualisee
		jQuery('#cs_outils')
			.css('opacity', '0.5')
			.parent()
			.prepend(ajax_image_searching)
			.load('".generer_url_ecrire('activer_desactiver', 'outils=', '\\x26')."'+cs_selected.join(','));*/
		// annulation du clic
		return false;
	});

	// clic sur le bouton de reset
	jQuery('#cs_reset_a').click( function() {
		jQuery('a.outil_on').removeClass('outil_on');
		jQuery('div.cs_toggle div').hide();
		// annulation du clic
		return false;
	});
	
	// clic sur le bouton 'tous les actifs'	
	jQuery('#cs_tous_a').click( function() {
		jQuery('div.cs_actifs a.cs_href').addClass('outil_on');
		set_selected();
		// annulation du clic
		return false;
	});
	

});

//--></script>";
}

// mise a jour des donnees si envoi via formulaire
function enregistre_modif_outils($cmd){
cs_log("D�but : enregistre_modif_outils()");
	global $outils;
	// recuperer les outils dans $_POST ou $_GET
	$toggle = array();
	if(isset($_GET['outil'])) $toggle[] = $_GET['outil'];
		elseif(isset($_POST['cs_selection'])) $toggle = explode(',', $_POST['cs_selection']);
		else return;
	$_GET['outil'] = ($cmd!='hide' && count($toggle)==1)?$toggle[0]:'';

	$i = $cmd=='hide'?'cache':'actif';
	${$i} = isset($GLOBALS['meta']["tweaks_{$i}s"])?unserialize($GLOBALS['meta']["tweaks_{$i}s"]):array();
	foreach($toggle as $o) if(isset(${$i}[$o][$i]))
			unset(${$i}[$o][$i]);
			else ${$i}[$o][$i] = 1;
	
	global $connect_id_auteur, $connect_login;
	spip_log("Changement de statut ($i) des outils par l'auteur id=$connect_id_auteur : ".implode(', ',array_keys(${$i})));
	ecrire_meta("tweaks_{$i}s", serialize(${$i}));
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

function exec_admin2() {
cs_log("D�but : exec_admin_couteau_suisse()");
	global $spip_lang_right;
	global $outils, $afficher_outil;

	if (!autoriser('configurer', 'plugins')) {
		include_spip('inc/minipres');
		echo ($GLOBALS['spip_version_code']<1.92)?minipres( _T('avis_non_acces_page')):minipres();
		exit;
	}
	$cmd = _request('cmd');

	// reset general
	if ($cmd=='resetall'){
		spip_log("Reset de tous les outils par l'auteur id=$connect_id_auteur");
		foreach(array_keys($GLOBALS['meta']) as $meta) {
			if(strpos($meta, 'tweaks_') === 0) effacer_meta($meta);
			if(strpos($meta, 'cs_') === 0) effacer_meta($meta);
		}
		ecrire_metas();
		cs_initialisation(true);
		if ($GLOBALS['spip_version_code']>=1.92) include_spip('inc/headers');
		redirige_par_entete(generer_url_ecrire(_request('exec')));
	}
	// reset de l'affichage
	if ($cmd=='showall'){
		cs_log("Reset de tous les affichages par l'auteur id=$connect_id_auteur");
		effacer_meta('tweaks_caches');
		ecrire_metas();
	}

	// afficher la description d'un outil ?
	$afficher_outil = ($cmd=='descrip' OR $cmd=='toggle')?$_GET['outil']:'';

	// initialisation generale forcee : recuperation de $outils;
	cs_initialisation(true);
	// mise a jour des donnees si envoi via formulaire
	// sinon fait une passe de verif sur les outils
	if ($cmd=='toggle' OR $cmd=='hide'){
		enregistre_modif_outils($cmd);
		// pour la peine, un redirige,
		// que les outils charges soient coherent avec la liste
		if ($GLOBALS['spip_version_code']>=1.92) include_spip('inc/headers');
		redirige_par_entete(generer_url_ecrire(_request('exec'), strlen($_GET['outil'])?"cmd=descrip&outil={$_GET[outil]}#cs_infos":'', true));
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
	include_spip('inc/plugin');
	$cs_infos = plugin_get_infos('couteau_suisse');
	echo propre(_T('cout:help', array(
		'reset' => generer_url_ecrire(_request('exec'),'cmd=resetall'),
		'hide' => generer_url_ecrire(_request('exec'),'cmd=showall'),
		'version' => $cs_infos['version']
	)));
	fin_boite_info();
	$aide_racc = cs_aide_raccourcis();
	if(strlen($aide_racc)) {
		echo '<br />';
		debut_boite_info();
		echo $aide_racc;
		fin_boite_info();
	}
	$aide_pipes = cs_aide_pipelines();
	if(strlen($aide_pipes)) {
		echo '<br />';
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
	echo _T('cout:presente_outils2');
	echo "\n<table border='0' cellspacing='0' cellpadding='5' style='width:100%;'><tr><td class='sansserif'>";

	include_spip('inc/cs_outils');
	$_GET['source'] = _request('exec');
	echo '<div>' . liste_outils()
	. '</div>' . _CS_NETTOYEUR . '<div>'
	. description_outil2(strlen($afficher_outil)?$afficher_outil:'') . '</div>';

	echo "</td></tr></table>\n";
	fin_cadre_trait_couleur();

	echo pipeline('affiche_milieu',array('args'=>array('exec'=>'admin_couteau_suisse'),'data'=>''));

	echo fin_gauche(), fin_page();
cs_log("Fin   : exec_admin_couteau_suisse()");
}

?>
