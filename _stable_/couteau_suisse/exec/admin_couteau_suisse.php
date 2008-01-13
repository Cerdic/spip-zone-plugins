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

// compatibilite spip 1.9
if(defined('_SPIP19100') && !function_exists('fin_gauche')) { function fin_gauche(){return '';} }
function cs_compat_boite($b) {if(defined('_SPIP19200')) echo $b('', true); else $b(); }

function cs_admin_styles_et_js($cs_version) {
	global $afficher_outil;
	$a = defined('_SPIP19100')||defined('_SPIP19200')
		?'div.cadre-info a { background:none; padding:0; border:0; } div.cadre-info { margin-bottom:1em; }'
		:'';
	echo <<<EOF
<style type='text/css'>$a

.cs_hidden { display:none; }
div.cadre-padding form{ padding:0; margin:0; }
div.cadre_padding form{	padding:0; margin:0; }
div.cs-cadre{ padding:0.5em; margin:1px; width=100%; border:1px solid #666666; }
div.cs-cadre h3 { margin:0.2em 0; border-bottom:1px solid #666666; }
div.cs_infos { }
div.cs_infos p { margin:0.3em 1em 0.3em 0pt; padding:0pt; }
div.cs_infos h3.titrem { border-bottom:solid 1px; font-weight:bold; display:block; }
div.cs_infos legend { font-weight:bold; }
div.cs_infos fieldset {	margin:.8em 4em .5em 4em; /* -moz-border-radius:8px; */ }
div.cs_infos sup { font-size:85%; font-variant:normal; vertical-align:super; }
div.cs_infos hr { border:0; border-top:1px solid #67707F; }

.conteneur {
	clear:both;
	width:100%;
	margin:0.8em 0 0 0;
	padding:0;
}

a.cs_href {
	font-weight:normal;
}
a.outil_on {
	font-weight:bold;
	border:1px dotted;
}
div.cs_liste {
	float:left;
	width:45%;
}

div.cs_outils {
	clear:both;
	float:none;
	width:100%;
}

div.cs_actifs {
	float:right;
}
div.cs_toggle {
	float:left;
	width:9.6%; /* pour IE6 */
	text-align:center;
	margin:50px 0 0 0;
}

div.categorie {
	margin-top:.6em;
	padding:2px;
	font-weight:bold;
	display:block;
	cursor:pointer;
}
div.categorie span {
	font-size:85%;
}
div.categorie span.light {
	font-weight:normal;
}

</style>
EOF;
	echo "<script type=\"text/javascript\"><!--

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
function set_categ(id) {
	nb = jQuery('#'+id+' a.outil_on').length;
	if(nb>0) jQuery('#'+id).prev().children().removeClass('light');
		else jQuery('#'+id).prev().children().addClass('light');
}
function outils_toggle() {
	if(cs_selected.length>1) {
		msg=\""._T('desc:permuter_outils')."\";
		msg=msg.replace(/@nb@/, cs_selected.length);
	} else {
		msg=\""._T('desc:permuter_outil')."\";
		msg=msg.replace(/@text@/, jQuery('a.outil_on').text());
	}
	if (!confirm(msg)) return false;
	jQuery('#cs_selection').attr('value', cs_selected.join(','));
	document.csform.submit();
}

if (window.jQuery) jQuery(function(){
	jQuery('div.cs_liste script').remove();
	// clic sur un titre de categorie
	jQuery('div.categorie').click( function() {
		jQuery(this).children().toggleClass('cs_hidden');
		next = jQuery(this).next();
		next.toggleClass('cs_hidden');
		cs_EcrireCookie(next[0].id, '+'+next[0].className, dixans);
		// annulation du clic
		return false;
	})
	.dblclick(function(){
		id = '#'+this.nextSibling.id;
		a = jQuery(id+' a.outil_on').length;
		b = jQuery(id+' a.cs_href').length;
		if(a==b) jQuery(id+' a.outil_on').removeClass('outil_on');
		else jQuery(id+' a.cs_href').addClass('outil_on');
		jQuery(this).children().addClass('cs_hidden');
		next = jQuery(this).next();
		next.removeClass('cs_hidden');
		cs_EcrireCookie(next[0].id, '+'+next[0].className, dixans);
		set_selected();
		set_categ(this.nextSibling.id);
		return false;
	});

	// clic sur un outil
	jQuery('a.cs_href').click( function() {
//alert(jQuery('#cs_infos')[0].className);
		jQuery(this).toggleClass('outil_on');
//jQuery('#cs_infos').show();
		set_selected();
		set_categ(this.parentNode.id);
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
	})
	.dblclick(function(){
		jQuery('a.outil_on').removeClass('outil_on');
		jQuery('div.categorie span').addClass('light');
		jQuery(this).addClass('outil_on');
		set_selected();
		set_categ(this.parentNode.id);
		outils_toggle();
		return false;
	});
	
	// clic surle bouton de permutation
	jQuery('#cs_toggle_a').click( function() {
		outils_toggle();
		// annulation du clic
		return false;
	});

	// clic sur le bouton de reset
	jQuery('#cs_reset_a').click( function() {
		jQuery('a.outil_on').removeClass('outil_on');
		jQuery('div.cs_toggle div').hide();
		jQuery('div.categorie span').addClass('light');
		// annulation du clic
		return false;
	});
	
	// clic sur le bouton 'tous les actifs'	
	jQuery('#cs_tous_a').click( function() {
		jQuery('div.cs_actifs a.cs_href').addClass('outil_on');
		jQuery('div.categorie span').removeClass('light');
		set_selected();
		// annulation du clic
		return false;
	});
	
	// verifier la version du CS
	jQuery('span.cs_version').load('".generer_url_ecrire('cs_version', 'version='.$cs_version, true)."');
	// afficher la boite rss, si elle existe
	jQuery('div.cs_boite_rss').load('".generer_url_ecrire('cs_boite_rss')."');

});

var dixans=new Date;
dixans.setFullYear(dixans.getFullYear()+10);

// ref : http://www.actulab.com/ecrire-les-cookies.php
function cs_EcrireCookie(nom, valeur){
	var argv=cs_EcrireCookie.arguments;
	var argc=cs_EcrireCookie.arguments.length;
	var expires=(argc > 2) ? argv[2] : null;
	var path=(argc > 3) ? argv[3] : null;
	var domain=(argc > 4) ? argv[4] : null;
	var secure=(argc > 5) ? argv[5] : false;
	document.cookie=nom+'='+escape(valeur)+
	((expires==null) ? '' : ('; expires='+expires.toGMTString()))+
	((path==null) ? '' : ('; path='+path))+
	((domain==null) ? '' : ('; domain='+domain))+
	((secure==true) ? '; secure' : '');
}
function cs_getCookieVal(offset){
	var endstr=document.cookie.indexOf (';', offset);
	if (endstr==-1) endstr=document.cookie.length;
	return unescape(document.cookie.substring(offset, endstr)); 
}
function cs_LireCookie(nom){
	var arg=nom+'=';
	var alen=arg.length;
	var clen=document.cookie.length;
	var i=0;
	while (i<clen){
		var j=i+alen;
		if (document.cookie.substring(i, j)==arg) return cs_getCookieVal(j);
		i=document.cookie.indexOf(' ',i)+1;
		if (i==0) break;
	}
	return null; 
}
function cs_EffaceCookie(nom){
	date=new Date;
	date.setFullYear(date.getFullYear()-1);
	cs_EcrireCookie(nom,null,date); 
}

function cs_Categorie(nom){
	c=cs_LireCookie(nom);
	return (window.jQuery && (c===null || c=='+cs_hidden'))?'cs_hidden':'';
}
function cs_Titre(nom){
	c=cs_LireCookie(nom);
	return (window.jQuery && (c===null || c=='+cs_hidden'))?'':' cs_hidden';
}

//--></script>";
}

// mise a jour des donnees si envoi via formulaire
function enregistre_modif_outils($cmd){
cs_log("INIT : enregistre_modif_outils()");
	global $outils, $metas_outils;
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
	if(defined('_SPIP19300')) $connect_id_auteur = $GLOBALS['auteur_session']['id_auteur'];
		else global $connect_id_auteur;
	spip_log("Changement de statut ($i) des outils par l'auteur id=$connect_id_auteur : ".implode(', ',array_keys(${$i})));
	ecrire_meta("tweaks_{$i}s", serialize(${$i}));
	if($cmd=='toggle') $metas_outils = ${$i};

	include_spip('inc/plugin');
	verif_plugin();	

cs_log(" FIN : enregistre_modif_outils()");
}

function cout_exec_redirige($p = '') {
	ecrire_metas();
	cs_initialisation(true);
	include_spip('inc/invalideur');
	purger_repertoire(_DIR_SKELS);
	purger_repertoire(_DIR_CACHE);
	if (defined('_SPIP19200')) include_spip('inc/headers');
	redirige_par_entete(generer_url_ecrire(_request('exec'), $p, true));
}

function exec_admin_couteau_suisse() {
cs_log("INIT : exec_admin_couteau_suisse()");
	global $spip_lang_right;
	global $outils, $afficher_outil, $metas_vars, $metas_outils;

	if (!cout_autoriser()) {
		include_spip('inc/minipres');
		echo defined('_SPIP19100')?minipres( _T('avis_non_acces_page')):minipres();
		exit;
	}
	$cmd = _request('cmd');
	$exec = _request('exec');

@unlink(_DIR_TMP."couteau-suisse.plat");
include_spip('inc/plugin');
verif_plugin();	

	// id de l'auteur en session
	if(defined('_SPIP19300')) $connect_id_auteur = $GLOBALS['auteur_session']['id_auteur'];
		else global $connect_id_auteur;

	// reset general
	if ($cmd=='resetall'){
		spip_log("Reset General du Couteau Suisse par l'auteur id=$connect_id_auteur");
		foreach(array_keys($GLOBALS['meta']) as $meta) {
			if(strpos($meta, 'tweaks_') === 0) effacer_meta($meta);
			if(strpos($meta, 'cs_') === 0) effacer_meta($meta);
		}
		$metas_vars = $metas_outils = array();
		// ici, pas d'initialisation...
		include_spip('cout_lancement');
		cout_exec_redirige('cmd=resetjs');
	}
	// installation personnalisee
	if ($cmd=='install' && isset($_GET['pack']) && isset($GLOBALS['cs_installer'][$_GET['pack']]['outils'])){
		spip_log("Installation peronnalisee de '$_GET[outils]' par l'auteur id=$connect_id_auteur");
		$pack = &$GLOBALS['cs_installer'][$_GET['pack']];
		effacer_meta('tweaks_actifs');
		$metas_outils = array();
		foreach(explode('|', $pack['outils']) as $o) $metas_outils[trim($o)]['actif'] = 1;
		if(isset($pack['variables'])) foreach($pack['variables'] as $i=>$v) $metas_vars[$i] = $v;
		ecrire_meta('tweaks_actifs', serialize($metas_outils));
		ecrire_meta('tweaks_variables', serialize($metas_vars));
		cout_exec_redirige();
	}
	// reset des variables d'un outil
	if ($cmd=='reset' && strlen($_GET['outil'])){
		spip_log("Reset des variables de '$_GET[outil]' par l'auteur id=$connect_id_auteur");
		global $outils;
		include_spip('cout_utils');
		include_spip('config_outils');
		include_spip('inc/cs_outils');
		cs_initialisation_d_un_outil($_GET['outil'], charger_fonction('description_outil', 'inc'), true);
		foreach ($outils[$_GET['outil']]['variables'] as $a) unset($metas_vars[$a]);
		ecrire_meta('tweaks_variables', serialize($metas_vars));
		cout_exec_redirige("cmd=descrip&outil={$_GET[outil]}#cs_infos");
	}
	// reset de l'affichage
	if ($cmd=='showall'){
		spip_log("Reset de tous les affichages du Couteau Suisse par l'auteur id=$connect_id_auteur");
		effacer_meta('tweaks_caches');
		cout_exec_redirige();
	}

	// afficher la description d'un outil ?
	$afficher_outil = ($cmd=='descrip' OR $cmd=='toggle')?$_GET['outil']:'';

	// initialisation generale forcee : recuperation de $outils;
	cs_initialisation(true);
	cs_installe_outils();

	// mise a jour des donnees si envoi via formulaire
	// sinon fait une passe de verif sur les outils
	if ($cmd=='toggle' OR $cmd=='hide'){
		enregistre_modif_outils($cmd);
		cout_exec_redirige(strlen($_GET['outil'])?"cmd=descrip&outil={$_GET[outil]}#cs_infos":'');
	}
//	else
//		verif_outils();

	if(defined('_SPIP19100'))
  		debut_page(_T('desc:titre'), 'configuration', 'couteau_suisse');
  	else {
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('desc:titre'), "configuration", 'couteau_suisse');
	}
	// pour la  version du plugin
	include_spip('inc/plugin');
	if(isset($GLOBALS['meta']['plugin'])) {
		$t = unserialize($GLOBALS['meta']['plugin']);
		$dir = $t['COUTEAU_SUISSE']['dir'];
		$cs_version = $t['COUTEAU_SUISSE']['version'];
		$bt_dir = $t['BARRETYPOENRICHIE']['dir'];
		$bt_version = $t['BARRETYPOENRICHIE']['version'];
		unset($t);
	}
	if(!strlen($dir)) $dir = 'couteau_suisse';
	if(!strlen($bt_dir)) $bt_dir = 'barre_typo_v2';
	if(!strlen($cs_version)) { $cs_version = plugin_get_infos($dir); $cs_version = $cs_version['version']; }
	if(!strlen($bt_version)) { $bt_version = plugin_get_infos($bt_dir); $bt_version = $bt_version['version']; }

	cs_admin_styles_et_js($cs_version);
	echo "<br /><br /><br />";
	gros_titre(_T('desc:titre'), '', false);
	echo barre_onglets("configuration", 'couteau_suisse');
echo '<div style="font-size:85%"><p style="color:red;">Ancienne interface : <a href="', generer_url_ecrire('admin_couteau_suisse_old'), '">par ici</a></p>';
// verification d'une base venant de SPIP 1.8
$res = spip_query("DESCRIBE spip_meta valeur");
$resultat = function_exists('spip_fetch_array')?spip_fetch_array($res):sql_fetch($res);
if($resultat['Type']!='text') echo "<p style=\"color:red;\">Attention : votre base semble ancienne et le Couteau Suisse ne va pas bien fonctionner.</p><p>La table 'spip_meta' a pour type de valeur '$resultat[Type]' au lieu de 'text'.</p>";
// verification de la barre typo V2
if (strlen($bt_version) and (version_compare($bt_version,'2.4.0','<'))) echo "<p><span style=\"color:red;\">Attention :</span> la barre typographique (version $bt_version) semble ancienne.<br />Le Couteau Suisse est compatible avec une version sup&eacute;rieure ou &eacute;gale &agrave; 2.4.0.</p>";
echo "<script type=\"text/javascript\"><!-- 
if (!window.jQuery) document.write('<p><span style=\"color:red;\">Note :</span> jQuery semble inactif sur cette page. Veuillez consulter <a href=\"http://www.spip-contrib.net/?article2166\">ici</a> le paragraphe sur les d&eacute;pendances du plugin.');
//--></script>";
echo '</div>';

	cs_compat_boite('debut_gauche');
	// pour la liste des docs sur spip-contrib
	$contribs = isset($GLOBALS['meta']['tweaks_contribs'])?unserialize($GLOBALS['meta']['tweaks_contribs']):array();
	foreach($contribs as $i=>$v) $contribs[$i] = preg_replace('/@@(.*?)@@/e', "couper(_T('\\1'), 25)", $v);
	sort($contribs);
	$aide = '';
	if(isset($GLOBALS['cs_installer'])) foreach(array_keys($GLOBALS['cs_installer']) as $pack)
		$aide .= "\n_ " . _T('desc:pour', array('pack'=>"{[{$pack}->" . generer_url_ecrire($exec,'cmd=install&pack='.urlencode($pack)) . ']}'));
	$aide = _T('desc:help', array(
		'reset' => generer_url_ecrire($exec,'cmd=resetall'),
		'hide' => generer_url_ecrire($exec,'cmd=showall'),
		'version' => $cs_version,
		'distant' => '<span class="cs_version"><br/>Version distante...</span>',
		'contribs' => join('', $contribs),
		'install' => $aide,
		'pack' => '['._T('desc:pack').'->'.generer_url_ecrire($exec,'cmd=pack#cs_infos').']',
	));
	echo debut_boite_info(true), propre($aide), fin_boite_info(true);
	$aide = cs_aide_raccourcis();
	if(strlen($aide))
		echo debut_boite_info(true), $aide, fin_boite_info(true);
	$aide = cs_aide_pipelines();
	if(strlen($aide))
		echo debut_boite_info(true), $aide, fin_boite_info(true);
	echo pipeline('affiche_gauche',array('args'=>array('exec'=>$exec),'data'=>''));

	cs_compat_boite('creer_colonne_droite');
	// on telecharge les news...
	if (defined('boites_privees_CS')) cs_boite_rss(!$quiet);
	echo pipeline('affiche_droite',array('args'=>array('exec'=>$exec),'data'=>''));
	cs_compat_boite('debut_droite');

	echo debut_cadre_trait_couleur(find_in_path('img/couteau-24.gif'),true,'','&nbsp;'._T('desc:liste_outils')),
		_T('desc:presente_outils2'),
		"\n<table border='0' cellspacing='0' cellpadding='5' style='width:100%;'><tr><td class='sansserif'>";

	include_spip('inc/cs_outils');
	$_GET['source'] = $exec;
	echo '<div class="conteneur">', liste_outils(),
		'</div><br class="conteneur" /><div class="conteneur">',
		$cmd=='pack'?cs_description_pack():description_outil2($afficher_outil),
		'<script type="text/javascript"><!--
var cs_descripted = "', $afficher_outil, '";
document.write("<style>#csjs{display:none;}</style>");
//--></script><div id="csjs" style="color:red;"><br/>', _T('desc:erreur:js'),'</div>
<noscript><style>#csjs{display:none;}</style><div style="color:red;"><br/>', _T('desc:erreur:nojs'),
$_GET['modif']=='oui'?'<br/>'._T('desc:vars_modifiees').'.':'','</div></noscript>',
		'</div>',
		"</td></tr></table>\n",
		fin_cadre_trait_couleur(true),

		pipeline('affiche_milieu',array('args'=>array('exec'=>$exec),'data'=>'')),
		fin_gauche(), fin_page();
cs_log(" FIN : exec_admin_couteau_suisse()");
}

function cs_boite_rss($force) {
	echo debut_boite_info(true), '<p><b>'._T('desc:rss_titre').'</b></p><div class="cs_boite_rss"><p>Attente RSS...</p><noscript>D&eacute;sactiv&eacute; !</noscript></div>'
		/*.'<div style="text-align: right; font-size: 87%;"><a title="'._T('desc:desactiver_rss').'" href="'
		.generer_url_ecrire(_request('exec'),'cmd=toggle&outil=rss_couteau_suisse').'">'._T('desc:supprimer_cadre').'</a></div>'*/
		, fin_boite_info(true);
}

?>