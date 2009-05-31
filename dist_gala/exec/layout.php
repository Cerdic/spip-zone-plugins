<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2007                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/config');
include_spip('inc/plugin');
include_spip('inc/presentation');
include_spip('inc/securiser_action');

// http://doc.spip.org/@exec_admin_plugin
function exec_layout() {
	global $connect_statut;
	global $connect_toutes_rubriques;
	global $spip_lang_right;
	$surligne = "";

	if (!autoriser('administrer','layout')) {
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('icone_admin_layout'), "configuration", "layout");
		echo _T('avis_non_acces_page');
		echo fin_gauche(), fin_page();
		exit;
	}

	global $couleur_claire,$couleur_foncee;
	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page(_T('icone_admin_layout'), "configuration", "layout");
	$dir_img_pack = _DIR_IMG_PACK;
	echo "<style type='text/css'>\n";
	echo <<<EOF
ul#gallery{float:left}
ul#gallery, ul#gallery li{list-style:none;margin:0;padding:0}
ul#gallery li{float:left;display:inline;margin: 0 0 20px 20px;width:120px;text-align:center}
ul#gallery img{display:block;width:100px;height:70px;border:0px solid;margin:0 auto 5px}
ul#gallery a{display:block;height:90px;padding: 10px 0;background: $couleur_claire;color: #333;border:1px solid $couleur_foncee;text-decoration: none}
ul#gallery a:hover,ul#gallery a.selected{background: $couleur_foncee;color: #FFF;border-color:#000}
div#details{clear:left}
EOF;
	echo "</style>\n";
	
	echo gros_titre(_T('icone_admin_layout'),'',false);
	echo "<div style='margin:1em;'>";
	echo debut_cadre_relief('',true);

	if ( ($l = _request('layout'))!==NULL
	  AND $f = find_in_path("layout/$l.css")){
	  	include_spip('inc/metas');
	  	ecrire_meta('layout',"$l.css");
	  	ecrire_metas();
	}
	global $couleur_foncee;
	
	$layouts = find_all_in_path("layout/","[.]css$");
	if (count($layouts)){
		echo '<ul id="gallery">';
		foreach($layouts as $l){
			$base = basename($l,".css");
			$dir = dirname($l);
			$img = "";
			$selected = "";
			if (isset($GLOBALS['meta']['layout']))
				$selected = ($GLOBALS['meta']['layout']== "$base.css")?" class='selected'":"";
			$url = generer_url_ecrire("layout","layout=$base");
			if (file_exists($i = "$dir/$base.gif"))
				$img = "<img src='$i' alt='$base' />";
			echo "<li><a href='$url'$selected>$img$base</a></li>\n";
		}
		echo "</ul>";
	}
	echo "</div>";
	echo fin_page();
}


?>