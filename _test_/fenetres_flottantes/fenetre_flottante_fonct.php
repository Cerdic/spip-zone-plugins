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

	if(preg_match('/(<div (id|class)="'.lire_config('FenFlo/attribut_FenFlo','contenu').'">)/i',$texte))
	{
	
		ereg('<div (id|class)=("|\')'.lire_config('FenFlo/attribut_FenFlo','contenu').'("|\')>(.*)',$texte,$resultat);
		
		$res = compter_div(substr($resultat[0], 4), 1);

		$texte = preg_replace('/(<div (id|class)="'.lire_config('FenFlo/attribut_FenFlo','contenu').'">)/i',"<div ".$resultat[1]."=\"".lire_config('FenFlo/attribut_FenFlo','contenu')."\"> <div id=\"window\"> 
	<div id=\"windowTop\">
	<div id=\"windowTopContent\"></div>
		<img src=\""._DIR_PLUGINS."fenetres_flottantes/images/".lire_config('FenFlo/couleur_FenFlo','vert')."/window_min.jpg\" class=\"format_png\" id=\"windowMin\" alt=\"minimiser la fenetre\" />
		<img src=\""._DIR_PLUGINS."fenetres_flottantes/images/".lire_config('FenFlo/couleur_FenFlo','vert')."/window_max.jpg\" class=\"format_png\" id=\"windowMax\" alt=\"agrandir la fenetre\"/>
		<img src=\""._DIR_PLUGINS."fenetres_flottantes/images/".lire_config('FenFlo/couleur_FenFlo','vert')."/window_close.jpg\" class=\"format_png\" id=\"windowClose\" alt=\"fermer la fenetre\"/>
	</div>
	<div id=\"windowBottom\"><div id=\"windowBottomContent\">&nbsp;</div></div>

	<div id=\"windowContent\">",$texte);
	}

		$fina="</div>
		<img src=\""._DIR_PLUGINS."fenetres_flottantes/images/".lire_config('FenFlo/couleur_FenFlo','vert')."/window_resize.gif\" class=\"format_png\"  id=\"windowResize\" alt=\"resize\" />
		</div> <a href=\"#\"  id=\"windowOpen\">&nbsp;</a>";

		$texte=str_replace($res, $fina.$res, $texte);

$texte = modif_fenetre($texte);
	

return $texte;

	
}

function modif_fenetre($arg)
{
	
	//on teste si les dimensions de la fenêtre sont enregistrées dans les cookies
	$valeur_top_FenFlo = $_COOKIE['top_FenFlo'];
	$valeur_left_FenFlo = $_COOKIE['left_FenFlo'];
	$valeur_width_FenFlo = $_COOKIE['width_FenFlo'];
	$valeur_height_FenFlo = $_COOKIE['height_FenFlo'];

	//si pas de cookies, on applique les valeurs de configuration
	if ($valeur_top_FenFlo == "")
		$valeur_top_FenFlo = lire_config('FenFlo/posy_FenFlo','100px');
	if ($valeur_left_FenFlo == "")
		$valeur_left_FenFlo = lire_config('FenFlo/posx_FenFlo','100px');
	if ($valeur_width_FenFlo == "")
		$valeur_width_FenFlo = lire_config('FenFlo/largeur_FenFlo','400');
	if ($valeur_height_FenFlo == "")
		$valeur_height_FenFlo = lire_config('FenFlo/hauteur_FenFlo','300');

	

	$marge_haut= $valeur_height_FenFlo-30;
	
	$marge2_haut= $valeur_height_FenFlo-45;
	$marge_larg= $valeur_width_FenFlo-25;

	
	$afficher_close = "none";
	$pos_bouton_close = "10";
	if (lire_config('FenFlo/close_FenFlo') == "on")
	{
		$afficher_close = "block";
		$pos_bouton_close = "25";
	}
	if(preg_match('/(<\/head>)/i',$arg))
	{
	  $arg = preg_replace('/(<\/head>)/i',"<style type=\"text/css\" media=\"screen\">
	  #window
	  {
	   left:".$valeur_left_FenFlo.";
           top:".$valeur_top_FenFlo.";
           width:".$valeur_width_FenFlo."px;
	   height:".$valeur_height_FenFlo."px;
	   }
          #windowBottom
 	  {
           	height:".$marge_haut."px;
		background-image: url("._DIR_PLUGINS."fenetres_flottantes/images/".lire_config('FenFlo/couleur_FenFlo','vert')."/window_bottom_end.png);  
	  }
	  #windowBottomContent
	  {
	   	height:".$marge_haut."px;
		background-image: url("._DIR_PLUGINS."fenetres_flottantes/images/".lire_config('FenFlo/couleur_FenFlo','vert')."/window_bottom_start.png);
	  }
          #windowContent
	  {
	   height:".$marge2_haut."px;
           width: ".$marge_larg."px;
		border: 1px solid ".lire_config('FenFlo/couleurbordure_FenFlo','#6caf00').";
	  }
	  #windowTop
	  {
		background-image: url("._DIR_PLUGINS."fenetres_flottantes/images/".lire_config('FenFlo/couleur_FenFlo','vert')."/window_top_end.png);

	  }
	  #windowTopContent
	 {
		background-image:url("._DIR_PLUGINS."fenetres_flottantes/images/".lire_config('FenFlo/couleur_FenFlo','vert')."/window_top_start.png);

	 }
	#windowMax, #windowMin{
		right: ".$pos_bouton_close."px;
	}
	#windowClose
	{
		display: ".$afficher_close.";
		
	}
          </style>
	  </head>",$arg);
	}

return $arg;
	
}

?>