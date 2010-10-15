<?php
function get_url_contents($url){
       $crl = curl_init();
       $timeout = 5;
       curl_setopt ($crl, CURLOPT_URL,$url);
       curl_setopt ($crl, CURLOPT_RETURNTRANSFER, 1);
       curl_setopt ($crl, CURLOPT_CONNECTTIMEOUT, $timeout);
       $ret = curl_exec($crl);
       curl_close($crl);
       return split("\n", $ret);
}

 $ip = $_SERVER['REMOTE_ADDR'];
 $xml = get_url_contents("http://ipinfodb.com/ip_query.php?ip=" . $ip);

 $latitude = preg_replace("/<\/?Latitude>/", "", $xml[10]);
 $longitude = preg_replace("/<\/?Longitude>/", "", $xml[11]);


function balise_LATITUDE($p){
   $ip = $p->param[0][1][0]->texte ;
   $p->code = "calculer_balise_LATITUDE($ip)";
   $p->interdire_scripts = false;
   return $p;
}

function calculer_balise_LATITUDE() {
   $xml = get_url_contents("http://ipinfodb.com/ip_query.php?ip=" . $_SERVER['REMOTE_ADDR']);
   $latitude = preg_replace("/<\/?Latitude>/", "", $xml[10]);
   return round($latitude, 1);
}


function balise_LONGITUDE($z){
   $ip = $z->param[0][1][0]->texte ;
   $z->code = "calculer_balise_LONGITUDE($ip)";
   $z->interdire_scripts = false;
   return $z;
}

function calculer_balise_LONGITUDE() {
   $xml = get_url_contents("http://ipinfodb.com/ip_query.php?ip=" . $_SERVER['REMOTE_ADDR']);
   $longitude = preg_replace("/<\/?Longitude>/", "", $xml[11]);
   return round($longitude, 1);
  

?>