<?php

function cotes_ajouterHeaderPrive($flux){
	 
	 if($_GET['exec']=="admin_plugin") {
	include_spip("base/initialise_cotes");
	initialise_cotes();
	 } 
	$flux.= "<link rel=\"stylesheet\" type=\"text/css\" href=\""._DIR_PLUGIN_COTES."/css/cotes_css.css\" />\n";
	return $flux;
}

?>
