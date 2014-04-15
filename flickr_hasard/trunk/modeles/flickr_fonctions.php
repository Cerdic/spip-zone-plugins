<?php
//
// doc API Flickr: 
// http://www.flickr.com/services/api/


define ("_KEY_API_FLICKR_RAND", "867b665fba7129ffe540684714ab50e0");


#
#  function fetch_data_flickr
#  call the API and decode the response
function fetch_data_flickr($params,$debug=false) {
   $encoded_params = array();
   foreach ($params as $k => $v){
       $encoded_params[] = urlencode($k).'='.urlencode($v);
   }
   $url = "http://api.flickr.com/services/rest/?".implode('&', $encoded_params);
   $rsp = file_get_contents($url);
   $rsp_obj = unserialize($rsp);
   if ($debug) var_dump($rsp_obj);
   
   if ($rsp_obj['stat'] == 'ok') return $rsp_obj;
                            else return false;
   
}



//
//  Main
// 

function flickr_rand($str,$tags,$license=5,$align='',$size='Small',$safesearch=1,$id,$iteration=0) {
    $api_key =  _KEY_API_FLICKR_RAND;
    
    // etape -1: recuper config sur present
    if (function_exists(lire_config))  {         
        $plage = (int) lire_config('flickr_rand/plage'); 
        if ($plage>4000) $plage = 4000; 
        if ($plage<0)    $plage = 1;
        
        $blacklist =   explode(";",lire_config('flickr_rand/blacklist', ""));
    } else {
        $plage = 100;
        $blacklist = array();
    }
    
    // etape 0: traiter des parametres et ajouter les valeurs par defaut (le php ne les prends pas via fonction a cause du modele)
    $tags = strip_tags($tags);
    $license = strip_tags($license);
    if ($license == 0 )             
                  $license = 9;               // valeur par defaut
    if ($license == -1 )             
                  $license = 0;               // copyright 
    if ($license == 8 )             
                  $license = "1,2,3,4,5,6";   // alias
    if ($license == 9 )             
                  $license = "1,2,3,4,5,6,7"; // alias                         
        
    $align = strip_tags($align);
    if ($align=="") $align = "non_aligne"; 
    
    $size = ucfirst(strip_tags($size));
    if ($size=="") $size = "Small";           // valeur par defaut
    
    $safesearch =  (int) $safesearch;
    if ($safesearch == 0 )  
                  $safesearch = 1;           // valeur par defaut

    // etape 1: recuperer une image donnee
    if ($id>0) {
        $id_photo_rand  =  $id;
    }  else { 
        // etape 1bis: ou recuperer une image au hasard selon nos criteres               
        $page_rnd = rand(1, $plage); 
        
        $params = array(
        	'api_key'	=> $api_key,
        	'method'	=> 'flickr.photos.search',
        	'tags'	=> $tags,
        	'license' => $license,   
        	'safe_search' => $safesearch,  
        	'format'	=> 'php_serial',
        	'per_page' => '1',         
          'page' => $page_rnd        // page (=photo) au hasard selon la taille du pool
        );
    
        $rsp_obj =  fetch_data_flickr($params); 
        
        if ($rsp_obj) {
          $photos = $rsp_obj['photos']['photo'];
          if (!$photos) {
              // pas de resultat, la plage est sans doute trop grand pour la requete
              // on refait une tentative en ignorant la parametre de plage
              $params['page'] = 1;
              $params['per_page'] = 100;
              $rsp_obj =  fetch_data_flickr($params); 
              if ($rsp_obj) {
                $photos = $rsp_obj['photos']['photo'];
                if (!$photos)
                      return false;    // vraiment pas de resultats
              } else {
                      return false;  
              }
          } 
          $id_photo_rand = $photos[array_rand($rsp_obj['photos']['photo'], 1)]['id'];   // on prend une image au hasard           
        }  
   }    
    
    
    // etape 2: chercher l'image et l'afficher
    $params = array(
        	'api_key'	=> $api_key,
        	'method'	=> 'flickr.photos.getInfo',
        	'photo_id'	=> $id_photo_rand,
        	'format'	=> 'php_serial',
     );
    
     $rsp_obj =  fetch_data_flickr($params);
        
     if ($rsp_obj){
            $_photo_rand_title = @$rsp_obj['photo']['title']['_content'];
            $_photo_rand_owner = @$rsp_obj['photo']['owner']['username'];
            $_photo_rand_licence = @$rsp_obj['photo']['license'];  
            $_photo_rand_url_page = @$rsp_obj['photo']['urls']['url'][0]['_content'];   
            
            // si l'auteur est ds liste noire
            // on refait une recherche
            if (in_array($_photo_rand_owner, $blacklist) && strlen($_photo_rand_owner)>0) {
                if ($iteration<10) {
                     return flickr_rand($str,$tags,$license,$align,$size,$safesearch,$id,++$iteration);
                } else {
                    return "<!-- flickr_rand: no match -->";
                }
            }          
            
            // doc licence: http://www.flickr.com/services/api/flickr.photos.licenses.getInfo.html            
            switch ($_photo_rand_licence)  {
                case 4:   $license_name = "Creative Commons BY";          $licence_url = "http://creativecommons.org/licenses/by/2.0/";   break;
                case 6:   $license_name = "Creative Commons BY-ND";       $licence_url = "http://creativecommons.org/licenses/by-nd/2.0/";   break;
                case 3:   $license_name = "Creative Commons BY-NC-ND";    $licence_url = "http://creativecommons.org/licenses/by-nc-nd/2.0/";   break;
                case 2:   $license_name = "Creative Commons BY-NC";       $licence_url = "http://creativecommons.org/licenses/by-nc/2.0/";   break;
                case 1:   $license_name = "Creative Commons BY-NC-SA";    $licence_url = "http://creativecommons.org/licenses/by-nc-sa/2.0/";   break;
                case 5:   $license_name = "Creative Commons BY-SA";       $licence_url = "http://creativecommons.org/licenses/by-sa/2.0/";   break;
                case 7:   $license_name = "No known copyright restrictions";    $licence_url = "http://flickr.com/commons/usage/";   break;
                case 0:   $license_name = "Copyright";                    $licence_url = "http://www.flickr.com/help/general/#147";   break;
                default:  $license_name = "Unknown License";              $licence_url = "http://www.flickr.com/help/general/#147";   break;                          
            } 
            
            //echo "Title is $_photo_rand_title - $_photo_rand_owner - :::: $_photo_rand_licence :::  !";
            
            // etape 3: recuperer la taille requise la plus proche
            $params = array(
              	'api_key'	=> $api_key,
              	'method'	=> 'flickr.photos.getSizes',
              	'photo_id'	=> $id_photo_rand,
              	'format'	=> 'php_serial',
            );
            $rsp_obj =  fetch_data_flickr($params);
            if ($rsp_obj){
                  foreach($rsp_obj['sizes']['size'] as $rsp_size) { 
                      $_photo_rand_url =    $rsp_size['source'];
                      $_photo_rand_width =  $rsp_size['width'];              
                      if ($rsp_size['label']==$size)
                                   break;
                     
                  }                                     
                  if ($_photo_rand_url) {
                       $output = "<dl class='flick_rand flickr_".strtolower($size)." spip_documents_$align' style='width:".$_photo_rand_width."px'>\n
                                  <dt><a href='$_photo_rand_url_page'><img src='$_photo_rand_url' alt='flickr' /></a></dt>\n";
                       if ($_photo_rand_title)
                                 $output .=  "<dt class='spip_doc_titre'><strong>$_photo_rand_title</strong><dt>\n";
                       $output .= "<dt class='spip_doc_descriptif'>par $_photo_rand_owner<dt>\n
                                   <dt class='spip_doc_licence'><a href='$licence_url' rel='license'>$license_name</a><dt>\n
                                  </dl>"; 
                       return $output;                   
                  }                            
            }
    
           
       }     
            
       
}    
    


?>