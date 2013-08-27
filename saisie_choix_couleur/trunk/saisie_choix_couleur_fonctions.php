<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function lambda_to_css_hexa($donnee,$gamma=0.8){
	// conversion d'une couleur exprimée sous la forme lambdelongueur d'onde dans la vide (en nm) vers du css_hexa  décimal.
	// algorithme : http://www.physics.sfasu.edu/astro/color/spectra.html, mais en fait http://mirrors.ctan.org/macros/latex/contrib/xcolor/xcolor.pdf, "The wave model"
	// n'insère pas automatiquement le #
	
	if (substr($donnee,0,6)!="lambda"){// vérifier que bien longueur d'onde
		return $donnee;
		}
	$lambda = str_replace("lambda","",$donnee);
	if ($lambda > 780 or $lambda < 380){ // dans la limite du visible, œuf corse
			return "000000";
		}
		
	// déterminer les r,g,b (paragraphe 97 de xcolor)
	if ($lambda < 440){
		$r = (440-$lambda)/(440-380);
		$g = 0;
		$b = 1;
		}
	elseif ($lambda < 490){
		$r = 0;
		$g = ($lambda-440)/(490-440);
		$b = 1;
		}
	elseif ($lambda < 510){
		$r = 0;
		$g = 1;
		$b = (510-$lambda)/(510-490);
		}
	elseif ($lambda < 580){
		$r = ($lambda-510)/(580-510);
		$g = 1;
		$b = 0;
		}
	elseif ($lambda < 645){
		$r = 1;
		$g = (645-$lambda)/(645-580);
		$b = 0;
		}
	else{
		$r = 1;
		$g = 0;
		$b = 0;
		}
	
	// coefficient d'intensité pour les longueurs d'ondes proches de la limite des yeux (paragraphe 98 de xcolor)
	if ($lambda < 420){
		$f= 0.3 + 0.7*(($lambda-380)/(420-380));
		}
	elseif ($lambda < 700){
		$f = 1;
		}
	else{
		$f = 0.3 + 0.7*((780-$lambda)/(780-700)); 
		}
	// les r,v,b final (paragraphe 99 de xcolor) * 255, arrondi puis mis en hexadecimal
	$r = str_pad(dechex(round((pow($f*$r,$gamma))*255)),2,"0",STR_PAD_LEFT);
	$g = str_pad(dechex(round((pow($f*$g,$gamma))*255)),2,"0",STR_PAD_LEFT);
	$b = str_pad(dechex(round((pow($f*$b,$gamma))*255)),2,"0",STR_PAD_LEFT);
	// on met un 0 devant les composant si moins de 2 caractères
	return $r.$g.$b;
}
?>