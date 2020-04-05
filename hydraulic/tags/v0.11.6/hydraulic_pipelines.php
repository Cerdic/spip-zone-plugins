<?php

function hydraulic_insert_head($flux) {
	$flux .= "\n<!-- Debut Plugin hydraulic -->\n";
	$flux .= "\n<!-- Plugin jQuery jqPlot pour les graphiques de ligne d'eau ! -->\n";
	$flux .= '  <!--[if IE]><script language="javascript" type="text/javascript" src="'.url_absolue(find_in_path('lib/dist/excanvas.js')).'"></script><![endif]-->
			<link rel="stylesheet" type="text/css" href="'.generer_url_public('hydraulic.css').'" />
			<link rel="stylesheet" type="text/css" href="'.url_absolue(find_in_path('lib/dist/jquery.jqplot.css')).'" />
			<script language="javascript" type="text/javascript" src="'.url_absolue(find_in_path('lib/dist/jquery.min.js')).'"></script>
			<script language="javascript" type="text/javascript" src="'.url_absolue(find_in_path('lib/dist/jquery.jqplot.min.js')).'"></script>
			<script type="text/javascript" src="'.url_absolue(find_in_path('lib/dist/plugins/jqplot.cursor.min.js')).'"></script>'."\n";
	$flux .= "\n<!-- Fin Plugin hydraulic -->\n";
	return $flux;
}
?>