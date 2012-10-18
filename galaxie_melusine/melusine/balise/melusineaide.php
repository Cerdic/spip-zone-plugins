<?php
function balise_MELUSINEAIDE($p) {
	$num=interprete_argument_balise (1,$p);
  	$add="http://datice.ac-creteil.fr/spip/spip.php?page=aide&id_article=".$num."?keepThis=true&amp;TB_iframe=true&amp;height=500&amp;width=500"  ;
	if(strstr($_SERVER['REQUEST_URI'],"/ecrire/")){$vers="../";}
	else{$vers="";};
	$destination=$vers."prive/images/aide.gif";		
	//$texte= "<a href='".$add."' class='mediabox' > <img src='".$destination."'> </a>";
	//$p->code = "<a href='".$add."' class='mediabox' > <img src='".$destination."'> </a>";	
	//$p->code ="bonjour";
	 $p->code = "'<a href=\"http://datice.ac-creteil.fr/spip/spip.php?page=aide&amp;id_article='.$num.'\" class=\"mediabox boxIframe boxWidth-700px boxHeight-600px \" ><img src=$destination > </a>'";
	return $p;
}

?>