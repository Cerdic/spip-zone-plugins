<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


function pb_charts_afficher_charts($fichier, $largeur, $hauteur, $alt='') {
	global $compt_afficher_charts;
	$GLOBALS['filtrer_javascript'] = 1;
	
	$licence = "I1XHRFEWO-BZNOX5T4Q79KLYCK07EK";
	
	$p = explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_PB_CHARTS',(_DIR_PLUGINS.end($p)));
	

	$compt_afficher_charts++;
	$delai = (500 * $compt_afficher_charts) + 500;
	$charts = ($f = find_in_path('lib/charts/charts.swf'))
		? $f
		: _DIR_PLUGIN_PB_CHARTS."/charts/charts.swf";
	$charts_lib = dirname($charts)."/charts_library";
	$id = md5($fichier);
	
	$deb = "<div style='margin-top: 10px; margin-bottom: 10px; height: ".$hauteur."px; text-align: center;'>";
	$fin = "</div>";

	$obj ="<object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000'"
		." codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0'"
		." width='$largeur' height='$hauteur' id='chartsobj_$id' align=''>"
		."<param name='movie' value='$charts?license=$licence&amp;library_path=$charts_lib&amp;xml_source=$fichier' />"
		."<param name='wmode' value='transparent' /><param name='quality' value='high' />"
		."<embed src='$charts?license=$licence&amp;library_path=$charts_lib&amp;xml_source=$fichier' quality='high'  width='$largeur' height='$hauteur' name='charts' align=''"
		." wmode='transparent' type='application/x-shockwave-flash' pluginspage='http://www.macromedia.com/go/getflashplayer'></embed>"
		."</object>";

	$script = "<div id='chart_$id'>$alt</div>";
	
	$script .= "<script type='text/javascript' language='javascript'><!--\n setTimeout(function(){document.getElementById(\"chart_$id\").innerHTML = \"$obj\"}, $delai );\n--></script>";
//	$script .= "<noscript>$obj</noscript>";

	$ret = $deb.$script.$fin;

	return $ret;

}

?>