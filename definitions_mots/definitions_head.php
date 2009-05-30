<?php


function definitions_insert_head($flux){
		$script_new = "<link rel=\"stylesheet\" href=\""._DIR_PLUGINS."definitions_mots/definitions_style.css\" type=\"text/css\" media=\"all\" />
<script type=\"text/javascript\">
	function afficher_definition(iddef)
	{
		document.getElementById(iddef).style.display = \"block\";
		
	}
	function fermer_definition(iddef)
	{
		document.getElementById(iddef).style.display = \"none\";
		
	}
</script>";
			return $script_new.$flux;
	}

?>
