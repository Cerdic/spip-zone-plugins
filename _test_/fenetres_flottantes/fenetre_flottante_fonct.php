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

	if(preg_match('/(<div (id|class)="'.lire_config('FenFlo/reglage2_attribut_FenFlo','contenu').'">)/i',$texte))
	{
	
		ereg('<div (id|class)=("|\')'.lire_config('FenFlo/reglage2_attribut_FenFlo','contenu').'("|\')>(.*)',$texte,$resultat);
		
		$res = compter_div(substr($resultat[0], 4), 1);

		$texte = preg_replace('/(<div (id|class)="'.lire_config('FenFlo/reglage2_attribut_FenFlo','contenu').'">)/i',"<div ".$resultat[1]."=\"".lire_config('FenFlo/reglage2_attribut_FenFlo','contenu')."\"> <div id=\"window\"> 
	<div id=\"windowTop\">
	<div id=\"windowTopContent\"></div>
		<img src=\""._DIR_PLUGINS."fenetres_flottantes/images/".lire_config('FenFlo/reglage1_couleur_FenFlo','vert')."/window_min.jpg\" class=\"format_png\" id=\"windowMin\" alt=\"minimiser la fenetre\" />
		<img src=\""._DIR_PLUGINS."fenetres_flottantes/images/".lire_config('FenFlo/reglage1_couleur_FenFlo','vert')."/window_max.jpg\" class=\"format_png\" id=\"windowMax\" alt=\"agrandir la fenetre\"/>
		<img src=\""._DIR_PLUGINS."fenetres_flottantes/images/".lire_config('FenFlo/reglage1_couleur_FenFlo','vert')."/window_close.jpg\" class=\"format_png\" id=\"windowClose\" alt=\"fermer la fenetre\"/>
	</div>
	<div id=\"windowBottom\"><div id=\"windowBottomContent\">&nbsp;</div></div>

	<div id=\"windowContent\">",$texte);
	}

		$fina="</div>
		<img src=\""._DIR_PLUGINS."fenetres_flottantes/images/".lire_config('FenFlo/reglage1_couleur_FenFlo','vert')."/window_resize.gif\" class=\"format_png\"  id=\"windowResize\" alt=\"resize\" />
		</div> <a href=\"#\"  id=\"windowOpen\">&nbsp;</a>";

		$texte=str_replace($res, $fina.$res, $texte);

$texte = modif_fenetre($texte);
	

return $texte;

	
}

function modif_fenetre($arg)
{
	
	$marge_haut= lire_config('FenFlo/reglage1_hauteur_FenFlo','300')-30;
	$marge2_haut= lire_config('FenFlo/reglage1_hauteur_FenFlo','300')-45;
	$marge_larg= lire_config('FenFlo/reglage1_largeur_FenFlo','400')-25;

	if(preg_match('/(<\/head>)/i',$arg))
	{
	  $arg = preg_replace('/(<\/head>)/i',"<style type=\"text/css\" media=\"screen\">
	  #window
	  {
	   left:".lire_config('FenFlo/reglage1_posx_FenFlo','100')."px;
           top:".lire_config('FenFlo/reglage1_posy_FenFlo','100')."px;
           width:".lire_config('FenFlo/reglage1_largeur_FenFlo','400')."px;
	   height:".lire_config('FenFlo/reglage1_hauteur_FenFlo','300')."px;
	   }
          #windowBottom
 	  {
           	height:".$marge_haut."px;
		background-image: url("._DIR_PLUGINS."fenetres_flottantes/images/".lire_config('FenFlo/reglage1_couleur_FenFlo','vert')."/window_bottom_end.png);  
	  }
	  #windowBottomContent
	  {
	   	height:".$marge_haut."px;
		background-image: url("._DIR_PLUGINS."fenetres_flottantes/images/".lire_config('FenFlo/reglage1_couleur_FenFlo','vert')."/window_bottom_start.png);
	  }
          #windowContent
	  {
	   height:".$marge2_haut."px;
           width: ".$marge_larg."px;
		border: 1px solid ".lire_config('FenFlo/reglage1_couleurbordure_FenFlo','#6caf00').";
	  }
	  #windowTop
	  {
		background-image: url("._DIR_PLUGINS."fenetres_flottantes/images/".lire_config('FenFlo/reglage1_couleur_FenFlo','vert')."/window_top_end.png);

	  }
	  #windowTopContent
	 {
		background-image:url("._DIR_PLUGINS."fenetres_flottantes/images/".lire_config('FenFlo/reglage1_couleur_FenFlo','vert')."/window_top_start.png);

	 }
          </style>
	  </head>",$arg);
	}

return $arg;
	
}

?>