<?php
function panoramas_insertion_in_head($flux)
{
	return "<script type=\"text/javascript\" src=\""._DIR_PLUGIN_PANORAMAS."js/jquery.js\"></script>
	".$flux.panoramas_stylesheet_html("panorama")."
	<link rel=\"stylesheet\" type=\"text/css\" href=\""._DIR_PLUGIN_PANORAMAS."css/thickbox.css\" />
	<script type=\"text/javascript\" src=\""._DIR_PLUGIN_PANORAMAS."js/jquery.dimensions.min.js\"></script>
	<script type=\"text/javascript\" src=\""._DIR_PLUGIN_PANORAMAS."js/jquery-ui-personalized-1.5b3.min.js\"></script>
	<script type=\"text/javascript\" src=\""._DIR_PLUGIN_PANORAMAS."js/jquery.panorama.js\"></script>";
}
function panoramas_insertion_in_header_prive($flux)
{
	return "<script type=\"text/javascript\" src=\""._DIR_PLUGIN_PANORAMAS."js/jquery.js\"></script>
	".$flux.panoramas_stylesheet_html("panorama")."
	<link rel=\"stylesheet\" type=\"text/css\" href=\""._DIR_PLUGIN_PANORAMAS."css/thickbox.css\" />
	<script type=\"text/javascript\" src=\""._DIR_PLUGIN_PANORAMAS."js/jquery.dimensions.min.js\"></script>
	<script type=\"text/javascript\" src=\""._DIR_PLUGIN_PANORAMAS."js/jquery-ui-personalized-1.5b3.min.js\"></script>
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
// pour inserer un css.html
function panoramas_stylesheet_html($b) {
 $f = find_in_path("$b.css.html");
 $args = 'ltr=' . $GLOBALS['spip_lang_left'];
 return $f?"<link rel=\"stylesheet\" type=\"text/css\" href=\"".generer_url_public("$b.css", $args)."\" >\n"."\n":'';
}


?>