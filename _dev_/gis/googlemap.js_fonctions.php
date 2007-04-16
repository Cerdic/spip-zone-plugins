<?php

function gis_cache_map($script){
	if (preg_match(",http://maps\.google\.com/mapfiles/(.*)\.js,Uims",$script,$regs))
		$script = str_replace($regs[0],generer_url_public('mapfiles.js','map='.$regs[1].".js",true),$script);
	return $script;
}