<?php

//
// le bon lien ical
//
function urlical($url){
$url=str_replace("http://", "webcal://", "$url");
return $url;
}


?>