<?php
function panoramas_insertion_in_head($flux)
{
	return "<script type=\"text/javascript\" src=\""._DIR_PLUGIN_PANORAMAS."js/jquery.js\"></script>
	".$flux."<link rel=\"stylesheet\" type=\"text/css\" href=\""._DIR_PLUGIN_PANORAMAS."css/jquery.panorama.css\" />
	<script type=\"text/javascript\" src=\""._DIR_PLUGIN_PANORAMAS."js/jquery.panorama.js\"></script>";
}
function panoramas_insertion_in_header_prive($flux)
{
	return "<script type=\"text/javascript\" src=\""._DIR_PLUGIN_PANORAMAS."js/jquery.js\"></script>
	".$flux."<link rel=\"stylesheet\" type=\"text/css\" href=\""._DIR_PLUGIN_PANORAMAS."css/jquery.panorama.css\" />
	<script type=\"text/javascript\" src=\""._DIR_PLUGIN_PANORAMAS."js/jquery.panorama.js\"></script>";
}
	
function panoramas_ajouter_boutons($boutons_admin){
	// si on est admin
	if ($GLOBALS['connect_statut'] == "0minirezo") {
	  	// on voit le bouton dans la barre "naviguer"
	  	$boutons_admin['naviguer']->sousmenu['visitesvirtuelles_toutes']= new Bouton(
		_DIR_PLUGIN_PANORAMAS.'img_pack/logo_panoramas.png', 'Visites virtuelles');
	}
	return $boutons_admin;
}	


?>