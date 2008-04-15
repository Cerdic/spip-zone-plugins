<?php
function panoramas_insertion_in_head($flux)
{
	return $flux."<link rel=\"stylesheet\" type=\"text/css\" href=\""._DIR_PLUGIN_PANORAMAS."css/jquery.panorama.css\" />
	<script type=\"text/javascript\" src=\""._DIR_PLUGIN_PANORAMAS."js/jquery.panorama.js\"></script>";
}
		


?>