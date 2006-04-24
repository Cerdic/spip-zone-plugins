<?php
define('_DIR_PLUGIN_DOJO',(_DIR_PLUGINS.end(explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__)))))));

function Dojo_header_prive($flux) {
	$exec = _request('exec');
	if ($exec == 'articles_edit'){
		$flux .= '<script src="' ._DIR_PLUGIN_DOJO . '/img_pack/dojo.js" type="text/javascript"></script>'. "\n";
		$flux .= '<script type="text/javascript">dojo.require("dojo.widget.Editor");</script>'. "\n";
		$flux .= '<link rel="stylesheet" href="'._DIR_PLUGIN_DOJO.'/img_pack/dojoedit.css" />'."\n";
	}
	return $flux;
}

function Dojo_body_prive($flux){
	$exec = _request('exec');
	if ($exec == 'articles_edit'){
		$load = preg_replace('{.*?<body[^>]*onLoad="([^"]*)"[^>]*>.*}',"\\1",$flux);
		$flux = preg_replace('{(.*?<body[^>]*)onLoad="[^"]*"([^>]*>.*)}',"\\1\\2",$flux);
		
		$load = explode(";",$load);
		if (count($load)){
			$flux .= "\n";
			$flux .= '<script type="text/javascript">';
			foreach($load as $fonct)
				$flux.= "dojo.onLoad($fonct);";
			$flux.= "</script>\n";
		}
	}
	return $flux;
}

?>