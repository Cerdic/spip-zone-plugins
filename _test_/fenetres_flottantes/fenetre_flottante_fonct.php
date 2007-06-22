<?php
function compter_div($chaine, $nb_div)
{
	if ((strpos($chaine, "<div") == FALSE) AND  (strpos($chaine, "</div") == FALSE))
		return $nb_div;
	
	if (strpos($chaine, "<div") < strpos($chaine, "</div"))
	{
		$nb_div	++;	
	
		$sous_chaine = substr($chaine, strpos($chaine, "<div") + 5);
		
	}
	else
	{
		$nb_div--;		
		$sous_chaine = substr($chaine, strpos($chaine, "</div") + 6);
	}

	/*echo("<br/>nb_div=".$nb_div."<br/>");*/

	if($nb_div == 0)
	{
		return substr($chaine, strpos($chaine, "</div"));;
	}
	else
	{
	return compter_div($sous_chaine, $nb_div);
	}
	
}
function FenFlo_Affichage_fenetre($texte)
{
	$resultat;

	if(preg_match('/(<div (id|class)="'.$GLOBALS['meta']['fenetresflottantes_reglage2_attribut'].'">)/i',$texte))
	{
	
		ereg('<div (id|class)=("|\')'.$GLOBALS['meta']['fenetresflottantes_reglage2_attribut'].'("|\')>(.*)',$texte,$resultat);
		
		$res = compter_div(substr($resultat[0], 4), 1);

		$texte = preg_replace('/(<div (id|class)="'.$GLOBALS['meta']['fenetresflottantes_reglage2_attribut'].'">)/i',"<div ".$resultat[1]."=\"".$GLOBALS['meta']['fenetresflottantes_reglage2_attribut']."\"> <div id=\"window\"> 
	<div id=\"windowTop\">
	<div id=\"windowTopContent\"></div>
		<img src=\""._DIR_PLUGINS."fenetres_flottantes/images/".$GLOBALS['meta']['fenetresflottantes_reglage1_couleur']."/window_min.jpg\" class=\"format_png\" id=\"windowMin\" alt=\"minimiser la fenetre\" />
		<img src=\""._DIR_PLUGINS."fenetres_flottantes/images/".$GLOBALS['meta']['fenetresflottantes_reglage1_couleur']."/window_max.jpg\" class=\"format_png\" id=\"windowMax\" alt=\"agrandir la fenetre\"/>
		<img src=\""._DIR_PLUGINS."fenetres_flottantes/images/".$GLOBALS['meta']['fenetresflottantes_reglage1_couleur']."/window_close.jpg\" class=\"format_png\" id=\"windowClose\" alt=\"fermer la fenetre\"/>
	</div>
	<div id=\"windowBottom\"><div id=\"windowBottomContent\">&nbsp;</div></div>

	<div id=\"windowContent\">",$texte);
	}

		$fina="</div>
		<img src=\""._DIR_PLUGINS."fenetres_flottantes/images/".$GLOBALS['meta']['fenetresflottantes_reglage1_couleur']."/window_resize.gif\" class=\"format_png\"  id=\"windowResize\" alt=\"resize\" />
		</div> <a href=\"#\"  id=\"windowOpen\">&nbsp;</a>";

		$texte=str_replace($res, $fina.$res, $texte);

$texte = modif_fenetre($texte);
	

return $texte;

	
}

function modif_fenetre($arg)
{
	
	$marge_haut= $GLOBALS['meta']['fenetresflottantes_reglage1_hauteur']-30;
	$marge2_haut= $GLOBALS['meta']['fenetresflottantes_reglage1_hauteur']-45;
	$marge_larg= $GLOBALS['meta']['fenetresflottantes_reglage1_largeur']-25;

	if(preg_match('/(<\/head>)/i',$arg))
	{
	  $arg = preg_replace('/(<\/head>)/i',"<style type=\"text/css\" media=\"screen\">
	  #window
	  {
	   left:".$GLOBALS['meta']['fenetresflottantes_reglage1_posx']."px;
           top:".$GLOBALS['meta']['fenetresflottantes_reglage1_posy']."px;
           width:".$GLOBALS['meta']['fenetresflottantes_reglage1_largeur']."px;
	   height:".$GLOBALS['meta']['fenetresflottantes_reglage1_hauteur']."px;
	   }
          #windowBottom
 	  {
           	height:".$marge_haut."px;
		background-image: url("._DIR_PLUGINS."fenetres_flottantes/images/".$GLOBALS['meta']['fenetresflottantes_reglage1_couleur']."/window_bottom_end.png);  
	  }
	  #windowBottomContent
	  {
	   	height:".$marge_haut."px;
		background-image: url("._DIR_PLUGINS."fenetres_flottantes/images/".$GLOBALS['meta']['fenetresflottantes_reglage1_couleur']."/window_bottom_start.png);
	  }
          #windowContent
	  {
	   height:".$marge2_haut."px;
           width: ".$marge_larg."px;
		border: 1px solid ".$GLOBALS['meta']['fenetresflottantes_reglage1_couleurbordure'].";
	  }
	  #windowTop
	  {
		background-image: url("._DIR_PLUGINS."fenetres_flottantes/images/".$GLOBALS['meta']['fenetresflottantes_reglage1_couleur']."/window_top_end.png);

	  }
	  #windowTopContent
	 {
		background-image:url("._DIR_PLUGINS."fenetres_flottantes/images/".$GLOBALS['meta']['fenetresflottantes_reglage1_couleur']."/window_top_start.png);

	 }
          </style>
	  </head>",$arg);
	}

return $arg;
	
}

?>