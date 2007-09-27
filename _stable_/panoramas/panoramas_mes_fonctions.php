<?php
/*
Plugin Panoramas 360° pour Spip 1.9.1
Copyright (C) 2007 (Arnault PACHOT)

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

Contact :
OpenStudio, Arnault PACHOT 
Le bourg, 43160 Saint Pal de Senouire, FRANCE.
info@openstudio.fr*/


function panoramas_affichage_final($chaine)


{
	$min_height = 0; //taille minimale des panoramas recherchés
	

	if( preg_match_all( '/<\s*panorama[^>]*>/Ums', $chaine, $resultats, PREG_SET_ORDER)) 
	{

	foreach ($resultats as $val) {

	
	$total = $val[0];

	$src = substr(trim(substr($total, strpos($total, "src=")+4)), 1);
	if (strpos($src, "\"") != FALSE)
		$src = substr($src, 0, strpos($src, "\""));
	if (strpos($src, "'") != FALSE)
		$src = substr($src, 0, strpos($src, "'"));

	$width = substr(trim(substr($total, strpos($total, "width=")+6)), 1);
	if (strpos($width, "\"") != FALSE)
		$width = substr($width, 0, strpos($width, "\""));
	if (strpos($width, "'") != FALSE)
		$width = substr($width, 0, strpos($width, "'"));
	
	$height = substr(trim(substr($total, strpos($total, "height=")+7)), 1);
	if (strpos($height, "\"") != FALSE)
		$height = substr($height, 0, strpos($height, "\""));
	if (strpos($height, "'") != FALSE)
		$height = substr($height, 0, strpos($height, "'"));
	
	$class = substr(trim(substr($total, strpos($total, "class=")+6)), 1);
	if (strpos($class, "\"") != FALSE)
		$class = substr($class, 0, strpos($class, "\""));
	if (strpos($class, "'") != FALSE)
		$class = substr($class, 0, strpos($class, "'"));
	
	$largeur = substr(trim(substr($total, strpos($total, "largeur=")+8)), 1);
	if (strpos($largeur, "\"") != FALSE)
		$largeur = substr($largeur, 0, strpos($largeur, "\""));
	if (strpos($largeur, "'") != FALSE)
		$largeur = substr($largeur, 0, strpos($largeur, "'"));
	
	$boucler = substr(trim(substr($total, strpos($total, "boucler=")+8)), 1);
	if (strpos($boucler, "\"") != FALSE)
		$boucler = substr($boucler, 0, strpos($boucler, "\""));
	if (strpos($boucler, "'") != FALSE)
		$boucler = substr($boucler, 0, strpos($boucler, "'"));
	

	if ((strpos($boucler, "oui") !== FALSE))
	{
		$type_360 = 1;
		
	}
	else
	{
		$type_360 = 0;
	}
	//echo("<br/>width : $width");
	//echo("<br/>height : $height");
	//echo("<br/>tests en cours<br/>");
	//echo("<br/>src : $src");
	//echo("<br/>class : $class");
	//echo("<br/>boucler : $boucler");
	//echo("<br/>largeur : $largeur");
	
	
	{

	
	$alt = "vue panoramique"; 
	$photo_filename = $src;
	$icone_size = 70; //largeur des icones flèche gauche et flèche droite
	$id = md5($src);


	$decaly1 = (int)($height/2 - $icone_size/2);
	$decaly2 = (int)($height/2 - $icone_size/2);
	$decaly3 = $decaly2 + $icone_size/2;
	
	$viewport_width = (int)($height*1.5);
	if ($largeur != "auto")
	{
		$viewport_width = (int)($largeur);
	}
	$decalx1 = (int)(($viewport_width*3) / 100);
	$decalx2 = (int)( $viewport_width - (($viewport_width*3) / 100) - $icone_size);

	
	



$header = "<style type=\"text/css\">
#zone_js_div$id {
	position: relative; 
	top: 0px;
	left: 0px; 
	overflow: hidden;
	width: ".$viewport_width."px; 
	
	height: auto !important;
height: ".$height."px;
min-height: ".$height."px;
	z-index: 0;
}
#tableau_image_js_div$id {
	position: relative; 
	top: 0px;
	left: 0px;
	z-index: 40;
}
#zone_image_js_div$id {
	position: relative; 
	top: 0px;
	left: 0px; 
	width: ".$viewport_width."px; 
	height: ".$height."px; 
	overflow: hidden;
	z-index: 30;
	
}
#zone_aller_gauche_js_div$id {
	position: absolute;
	margin-top: ".$decaly2."px;
	padding-left: ".$decalx1."px; 
	width: ".$icone_size."px; 
	height: ".$icone_size."px; 
	z-index: 99;
}
#zone_aller_droite_js_div$id {
	position: absolute;
	margin-top: ".$decaly2."px;
	padding-left: ".$decalx2."px; 
	width: ".$icone_size."px; 
	height: ".$icone_size."px;
	z-index: 98; 
}

#zone_attente_js_div$id{
	
	position: absolute;
	margin-top: ".$decaly2."px;
	margin-left: 50px; 
	z-index: 0;
	font-size: 12px;
	
}
</style>
<script type=\"text/javascript\">
	var  mode_auto$id, aller_gauche$id, aller_droite$id, panorama$id, largeur_image$id, image1$id, image2$id, tableau_image_js_div$id, zone_image_js_div$id, decalage_x$id, refresh$id, arret$id;
	function init$id()
	{
		
		setLargeurImage$id($width);
		setPanorama$id($type_360);
		mode_auto$id  = 1;
		deplace_droite_auto$id();			
		

	}
	function setLargeurImage$id(largeur)
	{
		largeur_image$id = largeur;

	}
	function setPanorama$id(pano)
	{
		panorama$id = pano;

	}

	function deplace_gauche$id() 
	{
			mode_auto$id =0;
			var val_left = $(\"#tableau_image_js_div$id\").css(\"left\");
			val_left = parseInt(val_left);
	
			if (val_left >= 0)
			{
				if (panorama$id == 1)
				{
					$(\"#tableau_image_js_div$id\").css(\"left\", \"-\"+largeur_image$id+\"px\");
				
				}
			}
			else
			{
				val_left = parseInt(val_left) + 5;
				$(\"#tableau_image_js_div$id\").css(\"left\", val_left+\"px\");

			}
			refresh$id = setTimeout(\"deplace_gauche$id()\",5);
		
	}
	function deplace_droite$id() 
	{
			
			mode_auto$id =0;
			var val_left = $(\"#tableau_image_js_div$id\").css(\"left\");
			val_left = parseInt(val_left);
	
			if (panorama$id == 1)
			{
				
				if (val_left <= -largeur_image$id)
				{
					
						
					$(\"#tableau_image_js_div$id\").css(\"left\", 5);
						
						
					
				}
				else
				{
					val_left = val_left - 5;
					$(\"#tableau_image_js_div$id\").css(\"left\", val_left+\"px\");
					
				}
			}
			else
			{
				if (val_left  <= -largeur_image$id+$viewport_width)
				{
					
					;
						
					
				}
				else
				{
					val_left = val_left - 5;
					$(\"#tableau_image_js_div$id\").css(\"left\", val_left+\"px\");
				}

			}
			refresh$id = setTimeout(\"deplace_droite$id()\",5);
		

	}
	function deplace_gauche_auto$id() 
	{
		
		if (mode_auto$id  == 1)
		{	
			var val_left = $(\"#tableau_image_js_div$id\").css(\"left\");
			val_left = parseInt(val_left);
	
			if (val_left >= 0)
			{
				if (panorama$id == 1)
				{
					$(\"#tableau_image_js_div$id\").css(\"left\", \"-\"+largeur_image$id+\"px\");
					refresh$id = setTimeout(\"deplace_gauche_auto$id()\",50);
				}
				else
				{
					refresh$id = setTimeout(\"deplace_droite_auto$id()\",50);
				}
			}
			else
			{
				val_left = val_left + 1;
				$(\"#tableau_image_js_div$id\").css(\"left\", val_left+\"px\");
				refresh$id = setTimeout(\"deplace_gauche_auto$id()\",50);
			}
			
		
		}
	}

	function deplace_droite_auto$id() 
	{
		if (mode_auto$id  == 1)
		{	
			var val_left = $(\"#tableau_image_js_div$id\").css(\"left\");
			val_left = parseInt(val_left);
	

			if (panorama$id == 1)
			{
				if (val_left <= -largeur_image$id)
				{
					
							$(\"#tableau_image_js_div$id\").css(\"left\", 0);
					
						
					
				}
				else
				{
						val_left = val_left - 1;
					$(\"#tableau_image_js_div$id\").css(\"left\", val_left+\"px\");

				}
				refresh$id = setTimeout(\"deplace_droite_auto$id()\",50);
			}
			else
			{
			if (val_left <= -largeur_image$id+$viewport_width)
				{
					
					refresh$id = setTimeout(\"deplace_gauche_auto$id()\",50);
						
					
				}
				else
				{
					val_left = val_left - 1;
					$(\"#tableau_image_js_div$id\").css(\"left\", val_left+\"px\");
					refresh$id = setTimeout(\"deplace_droite_auto$id()\",50);
				}

			}
			
		
		}
	}

	function arreter$id()
	{

		clearTimeout(refresh$id);
		
	}
	
 	

</script>";



$newstr = "
<br class=\"nettoyeur\" />
<div id=\"zone_js_div$id\">


	<div id=\"zone_aller_gauche_js_div$id\" class=\"zone_aller_gauche_js_div$id\">
		<a href=\"javascript:void(0);\" onmouseover=\"setLargeurImage$id($width);setPanorama$id($type_360);deplace_gauche$id();\" onmouseout=\"arreter$id();\">
			<img id=\"aller_gauche$id\" src=\""._DIR_PLUGINS."/panoramas/squelettes//aller_gauche.png\" class=\"format_png\" alt=\"fleche gauche\"/>

		</a>
	</div>

	<div id=\"zone_aller_droite_js_div$id\" class=\"zone_aller_droite_js_div$id\">
		<a href=\"javascript:void(0);\" onmouseover=\"setLargeurImage$id($width);setPanorama$id($type_360);deplace_droite$id();\" onmouseout=\"arreter$id();\">
			<img id=\"aller_droite$id\" src=\""._DIR_PLUGINS."/panoramas/squelettes//aller_droite.png\" class=\"format_png\" alt=\"fleche droite\"/>
		</a>
	</div>
<div id=\"zone_attente_js_div$id\" class=\"zone_attente_js_div$id\">

			Chargement en cours...
	</div>

<div id=\"zone_image_js_div$id\" class=\"zone_image_js_div$id\">
	<div id=\"tableau_image_js_div$id\" class=\"tableau_image_js_div$id\">
	<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\">
	  <tr>	
		<td><img id=\"image1$id\" src=\"$photo_filename\" alt=\"image 1 du panorama\"/></td>

					<td><img id=\"image2$id\" src=\"$photo_filename\" alt=\"image 2 du panorama\"/></td>
			  </tr>
	</table>
	</div>
	
</div>
</div>
";
	$chaine = str_replace($total, $newstr, $chaine);
	$chaine = preg_replace('/<head>/Ums', "<head>".$header, $chaine);
	$chaine = preg_replace('/<\/body>/Ums', "<script type=\"text/javascript\">$(document).ready(function(){init$id();});</script></body>", $chaine);
	}
	}
	}

return ($chaine);
}

function panoramas_affiche_milieu($chaine)
{	
	return ($chaine);
}
function affiche_milieu($chaine)
{	
	return ($chaine);
}



?>