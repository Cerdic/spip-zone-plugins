<?php
#---------------------------------------------------#
#  Plugin  : Tweak SPIP                             #
#  Auteur  : Patrice Vanneufville, 2006             #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net #
#  Licence : GPL                                    #
#---------------------------------------------------#

include_spip('inc/texte');
include_spip('inc/layer');

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(dirname(__FILE__)))));
define('_DIR_PLUGIN_TWEAK_SPIP',(_DIR_PLUGINS.end($p)));

function tweak_styles() {
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
}

function exec_tweak_spip_admin() {
  global $connect_statut, $connect_toutes_rubriques;
  global $spip_lang_right;
  global $couleur_claire;
  global $tweaks;

  include_spip('tweak_spip_config');
  include_spip("inc/presentation");
//  include_spip ("base/abstract_sql");

  if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {
	debut_page(_T('icone_admin_plugin'), "configuration", "plugin");
	echo _T('avis_non_acces_page');
	fin_page();
	exit;
  }
/*
	// mise a jour des donnees si envoi via formulaire
	// sinon fait une passe de verif sur les tweaks
	if (_request('changer_tweaks')=='oui'){
		enregistre_modif_tweaks();
		// pour la peine, un redirige, 
		// que les tweaks charges soient coherent avec la liste
		redirige_par_entete(generer_url_ecrire('tweak_spip_admin'));
	}
	else
		verif_tweaks();
*/
	global $spip_version_code;
	if ($spip_version_code<1.92) 
  		debut_page(_T('tweak:titre'), 'configuration', 'tweak_spip');
  	else {
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('tweak:titre'), "configuration", "tweak_spip");
	}
	
  tweak_styles();

	echo "<br /><br /><br />";
	gros_titre(_T('tweak:titre'));
	echo barre_onglets("configuration", "tweak_spip");

//  debut_page(_T('tweak:titre'), 'configuration', 'tweak_spip');

//	echo '<br><br><br>';
//	gros_titre(_T('tweak:titre'));
	debut_gauche();
	debut_boite_info();
	echo propre(_T('tweak:help'));
	echo pipeline('affiche_gauche',array('args'=>array('exec'=>'tweak_spip_admin'),'data'=>''));
	creer_colonne_droite();
	echo pipeline('affiche_droite',array('args'=>array('exec'=>'tweak_spip_admin'),'data'=>''));
	debut_droite();
	lire_metas();

	echo generer_url_post_ecrire('tweak_spip_admin');
	echo "\n<input type='hidden' name='changer_tweaks' value='oui'>";

//	debut_cadre_relief();
	debut_cadre_trait_couleur('administration-24.gif','','',_T('tweak:tweaks_liste'));

	global $couleur_foncee;
	echo "\n<table border='0' cellspacing='0' cellpadding='5' >";

	echo "<tr><td class='serif'>";
	echo _T('tweak:texte_presente_tweaks');

	echo '<ul>';
	foreach($temp = $tweaks as $tweak) echo '<li>' . ligne_tweak($tweak) . "</li>\n"; 
	echo '</ul>';
	

	echo "\n<div style='text-align:$spip_lang_right'>";
	echo "<input type='submit' name='Valider' value='"._T('bouton_valider')."' class='fondo' onclick=\"alert('à faire, si vous trouvez un moyen simple de stocker l\'état des tweaks !')\">";
	echo "</div>";

# ce bouton est trop laid :-)
# a refaire en javascript, qui ne fasse que "decocher" les cases
#	echo "<div style='text-align:$spip_lang_left'>";
#	echo "<input type='submit' name='desactive_tous' value='"._T('bouton_desactive_tout')."' class='fondl'>";
#	echo "</div>";

	echo "</td></tr></table>\n";

//  ecrire_meta('SquelettesMots:fond_pour_groupe',serialize($fonds));
//  ecrire_metas();

	fin_cadre_trait_couleur();
//	fin_cadre_relief();

	echo pipeline('affiche_milieu',array('args'=>array('exec'=>'tweak_spip_admin'),'data'=>''));
	echo "</form>";

	echo $spip_version_code>=1.92?fin_gauche():'', fin_page();
	
}

// est-ce que $pipe est un pipeline ?
function is_tweak_pipeline($pipe) {
	global $tweak_exclude;
	return !in_array($pipe, $tweak_exclude);
}

// affiche un tweak sur une ligne
function ligne_tweak($tweak){
	static $id_input=0;
	$inc = $tweak['include'];
	$actif = $tweak['actif'];
	$puce = $actif?'puce-verte.gif':'puce-rouge.gif';
	$titre_etat = _T('tweak:'.($actif?'':'in').'actif');
	$tweak_id = $inc.$id_input;
	
	$s = "<div id='$tweak_id' class='nomplugin".($actif?'_on':'')."'>";
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

	$s .= "<input type='checkbox' name='tweak_$inc' value='O' id='label_$id_input'";
	$s .= $actif?" checked='checked'":"";
	$s .= " onclick='verifchange.apply(this,[\"$inc\"])' /> <label for='label_$id_input' style='display:none'>"._T('tweak:activer_tweak')."</label>";

	$s .= bouton_block_invisible($tweak_id) . propre($tweak['nom']);

	$s .= "</div>";

	$s .= debut_block_invisible($tweak_id);

	$s .= "\n<div class='detailplugin'>";
	if (isset($tweak['description'])) $s .= propre($tweak['description']);
	if (isset($tweak['auteur'])) $s .= "<p>" . _T('auteur') .' '. propre($tweak['auteur']) . "</p>";
	$s .= "<hr/>" . _T('tweak:tweak') . " $inc.php";
	if ($tweak['options']) $s .= ' | options';
	if ($tweak['fonctions']) $s .= ' | fonctions';
	foreach ($tweak as $pipe=>$fonc) if(is_tweak_pipeline($pipe)) $s .= ' | '.$pipe;
	$s .= "</div>";

	$s .= fin_block();
	$id_input++;

	return $s;
}
?>
