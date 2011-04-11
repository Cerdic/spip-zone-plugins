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

function flickr_rand($str,$tags,$license=5,$align='',$size='Small',$safesearch=1,$id) {
    $api_key =  _KEY_API_FLICKR_RAND;
    
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
        $params = array(
        	'api_key'	=> $api_key,
        	'method'	=> 'flickr.photos.search',
        	'tags'	=> $tags,
        	'license' => $license,   
        	'safe_search' => $safesearch,  
        	'format'	=> 'php_serial',
        	'per_page' => '100'        // pool ds lequel on pioche
        );
    
        $rsp_obj =  fetch_data_flickr($params);
        
        if ($rsp_obj) {
          $photos = $rsp_obj['photos']['photo'];
          if (!$photos) return false;     // pas de resultats
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
            
            switch ($_photo_rand_licence)  {
                case 4:   $license_name = "Creative Commons BY";          break;
                case 6:   $license_name = "Creative Commons BY-ND";       break;
                case 3:   $license_name = "Creative Commons BY-NC-ND";    break;
                case 2:   $license_name = "Creative Commons BY-NC";       break;
                case 1:   $license_name = "Creative Commons BY-NC-SA";    break;
                case 5:   $license_name = "Creative Commons BY-SA";       break;
                case 7:   $license_name = "No known copyright restrictions";       break;
                case 0:   $license_name = "Copyright";       break;
                default:  $license_name = "Unknown License,";         
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
                  if ($_photo_rand_url)
                    return  "<dl class='flick_rand flickr_".strtolower($size)." spip_documents_$align' style='width:".$_photo_rand_width."px'>
                                  <dt><a href='$_photo_rand_url_page'><img src='$_photo_rand_url' alt='flickr' /></a></dt>
                                  <dt class='spip_doc_titre'><strong>$_photo_rand_title</strong><dt>
                                  <dt class='spip_doc_descriptif'>par $_photo_rand_owner<dt>
                                  <dt class='spip_doc_licence'><small>$license_name</small><dt>
                            </dl>";                             
            }
    
           
       }     
            
       
}    
    


?>