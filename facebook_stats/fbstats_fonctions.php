<?php

function paie_ton_buzz($name,$src="",$q=""){
	
	include_spip("inc/distant");
	
	if($src){
		$resultat = recuperer_page($src);
	}elseif($q){
		$resultat = recuperer_page("https://graph.facebook.com/search?q=".urlencode($q));
	}else{
		$resultat = recuperer_page("https://graph.facebook.com/search?q=\"".urlencode($name));
	}
	
	$json_tab = json_decode($resultat,true);
	
	$s ='<ul style="list-style-type:none;margin:10px;padding:0;">' ;
	
	foreach($json_tab['data'] as $k){
		$s .= "<li style='clear:left;margin-bottom:10px;padding-left:50px;position:relative;border-top:1px solid #EEEEEE'>";
		$s .= "<div style='float:left;padding-right:5px;position:absolute;left:-5px'><img src='https://graph.facebook.com/" . $k["from"]["id"] ."/picture' width='50' /></div>" ;
		$s .= "<div style='float:left;padding-right:5px;'><strong><a href='http://facebook.com/profile.php?id=" . $k['from']['id'] . "'>" . $k['from']['name'] . "</a></strong></div>" ;
		
		if($k['message']){
			$s .= "<div style='padding:5px 0'>" . couper($k['message'], 200) . "</div>" ;
		}else{
			$s .= "<br style='clear:left' />";
		}
		$s .= "<div style='float:left;padding-right:5px;clear:left'><img src='" . $k["picture"] ."' width='50' /></div>" ;
		$s .= "<div style='padding-right:5px;'><a href='". $k['link'] ."'>" . $k['name'] ."</a></div>" ;
		$s .= "<div style='padding-right:5px;'><a href='". $k['caption'] ."'>" . $k['caption'] ."</a></div>" ;
		$s .= "<div style='padding-right:5px;color:#808080'>" . $k['description'] ."</div>" ;

		$s .= "<div style='clear:left;padding:3px;color:#808080'>". jour($k["created_time"]) ." ". nom_mois($k["created_time"]) . " ". annee($k["created_time"]) . " ". heures_minutes($k["created_time"]) ;
		if($k["updated_time"] != $k["created_time"])
			$s .= " - maj : " . jour($k["updated_time"]) ." ". nom_mois($k["updated_time"]) . " ". annee($k["updated_time"]) . " ". heures_minutes($k["created_time"]) . " (" .$k["type"] .") " ;
		$s .= "</div>" ;
		
		$s .= "<div style='background-color:#EDEFF4;padding:3px'> " ;
		if($k['likes'])
			$s .= $k['likes'] . " personnes aiment ça." ;
		$nb_share = paie_ton_share($k['link']) ;	
		if($nb_share)
			$s .= " Article partagé " . $nb_share . " fois par ailleurs." ;
		$s .= "</div>" ;
		
		$s .= "</li>";
	}
	
	$s .="</ul>";
	
	$s .= "<ul style='list-style-type:none;margin:10px;padding:0;height:50px;'>";
	$s .= "<li style='float:left;margin-right:50px'> <a href='". parametre_url(self(),"src",urlencode($json_tab['paging']['previous'])) ."'> Posts ultérieurs </a> </li>"  ;
	$s .= "<li style='float:left;margin-right:50px'> <a href='". parametre_url(self(),"src",urlencode($json_tab['paging']['next'])) ."'> Posts antérieurs </a> </li>"  ; 
	$s .= "</ul>";
		


return $s ;
}

function paie_ton_share($url){
	
	include_spip("inc/distant");
	$resultat = recuperer_page("https://graph.facebook.com/".urlencode($url));
	$json_tab = json_decode($resultat,true);
	$r = intval($json_tab['shares']) ;
	return $r ;
}



?>