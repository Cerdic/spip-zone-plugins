<?php

function seostats_header_prive($flux){
        $flux .= "<link href="._DIR_PLUGIN_SEOSTATS."css/seostats.css\"  type=\"text/css\" rel=\"stylesheet\" media=\"all\" />\n";
        return $flux;
}

function seostats_insert_head_css($flux) {
	$flux .= '<link rel="stylesheet" type="text/css" href="'.find_in_path('seostats.css').'" media="all" />'."\n";
	return $flux;
}

?>
