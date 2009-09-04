<?php

//juste pour E dans l'O
function oeuf($str){
	$pattern = "(Î|Ï|&#338;)";

if($str!="") {
$str= preg_replace( "$pattern" , "Oe" , $str);
}
 return $str;

}

  // supprimer le # dans une chaine (cf couleurs)
function no_diese($txt) {
        return str_replace('#', '', $txt);
}

?>