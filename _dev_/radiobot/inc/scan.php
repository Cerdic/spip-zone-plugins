<?php


function pecho_sons($page,$id_syndic,$maj="non"){
/*
Associe les docs trouvés dans $page au site $id_syndic, dans l'article d'url $page
*/

if(!function_exists(analyser_sites)) include_spip('exec/sites') ;

$url_scan = $page ;
$id_syndic = $id_syndic ;
	
$item=scan_it($url_scan);

//print_r($item)."<hr>";

$track_url=$item['documents']['urls'];
$track_titre=$item['documents']['titres'];
	
	//echo "<hr>";
	//echo sizeof($track_url);
	//print_r($track_titre);
	
	$my_res = spip_fetch_array(spip_query(
	"SELECT id_syndic_article, UNIX_TIMESTAMP(maj) FROM spip_syndic_articles
	WHERE id_syndic='$id_syndic' AND url='$url_scan'"));

	//print_r($my_res);

	if($my_res){
	
	$id_syndic_article = $my_res['id_syndic_article'];
	$maj_art = $my_res['UNIX_TIMESTAMP(maj)'];

//echo $id_syndic_article."<br>" ;
//echo $maj_art ;

	}

	//echo "<< $maj" ;
	
	// deja vu ?
	if (spip_num_rows(spip_query("SELECT id_document FROM spip_documents_syndic
	WHERE id_syndic_article='$id_syndic_article' ")) > 0 AND $maj != 'oui' )
		return;
		
	if(sizeof($track_url)>0){
	$k = 0;
	
	foreach ($track_url as $enclosure) {

			$url = urldecode($enclosure);
			$type = "audio/mpeg"; //a preciser

			// Verifier que le content-type nous convient
			list($id_type) = spip_fetch_array(spip_query("SELECT id_type
			FROM spip_types_documents WHERE mime_type='$type'"), SPIP_NUM);
			if (!$id_type) {spip_log("ps de type");}#continue;

			if($url != "http://www.mp3"){
//echo $url;
			// Inserer l'enclosure dans la table spip_documents
			if ($t = spip_fetch_array(spip_query("SELECT id_document FROM
			spip_documents WHERE fichier='$url' AND distant='oui'"))){
				$id_document = $t['id_document'];
				//echo "$id_document<br>"; 
				}
			else {
			
				$d = spip_fetch_array(spip_query("SELECT titre, descriptif FROM
			spip_syndic_articles WHERE id_syndic_article='$id_syndic_article'"));
				
				if($track_titre[$k] AND $track_titre[$k] ) {
				
				$titre = supprimer_tags(addslashes($track_titre[$k])) ;
				$descriptif = supprimer_tags(addslashes($d['descriptif'])) ;
				} else { 
				$titre = supprimer_tags(addslashes($d['titre'])) ;
				$descriptif = supprimer_tags(addslashes($d['descriptif'])) ;
				}
				
				spip_query("INSERT INTO spip_documents
				(id_type, titre, fichier, date, distant, taille, mode)
				VALUES ($id_type,'$titre','$url',NOW(),'oui','0', 'document')");
				$id_document = spip_insert_id();
				spip_log("pecho_son: '$url' => id_document=$id_document");
				//echo "pecho_son: '$url' => id_document=$id_document" ;

			}

			// lier avec l'article syndique
			spip_query("INSERT INTO spip_documents_syndic
			(id_document, id_syndic, id_syndic_article)
			VALUES ($id_document, $id_syndic, $id_syndic_article)");
			
			}
			$k++;
		
	} //for
	
	} //if

//reculer la date de maj de 10j pour pas qu'il soit retesté
			$avant=20*24*3600;
			$maj_art2 = $maj_art - $avant ;
			
			$row=spip_fetch_array(spip_query("SELECT maj FROM spip_syndic_articles WHERE id_syndic_article='$id_syndic_article'"));
			//echo "<h2>".$row['maj']."</h2>";
			
			//echo date("m.d.y", $maj_art)."  -> ".date("m.d.y", $maj_art2)." -> $id_syndic_article<hr>" ;
			//echo $maj_art."  -> ".$maj_art2." -> $id_syndic_article" ;

			spip_query("UPDATE spip_syndic_articles SET
			maj = FROM_UNIXTIME($maj_art2) WHERE id_syndic_article='$id_syndic_article'");
			spip_log("recule maj de $id_syndic_article de 20j  ");

			//$row2=spip_fetch_array(spip_query("SELECT maj FROM spip_syndic_articles WHERE id_syndic_article='$id_syndic_article'"));
			//echo "<h2>".$row2['maj']."</h2>";
}



function scan_it($url_scan){
	$tip=time();
	spip_log("----------------  scan it  -------------------  ");
	$texte_patron_bg="" ;
	unset($tableau_bg) ;	
 
	$url_scan = ereg_replace("Ñ","%d1",$url_scan);
	$url_scan = ereg_replace(" ","%20",$url_scan);
	$url_scan = ereg_replace("\[","%5b",$url_scan);
	$url_scan = ereg_replace("\]","%5d",$url_scan);
	
	$pathparts = pathinfo($url_scan);
	   
	$dirname = $pathparts["dirname"];
	$basename = $pathparts["basename"];	
	
	// url du site
	$url=parse_url($url_scan);
	//print_r($url);
	
	$path=$url['path'];
	$part=pathinfo($path);
	//print_r($part);
	if($part['extension']) $dir=$part['dirname'];
	elseif($part['dirname'] == "/") $dir=$part['dirname'].$part['basename'] ;
	elseif($part['dirname']) $dir=$part['dirname']."/".$part['basename'] ;
	$url=$url['scheme']."://".$url['host'].$dir;
	//echo"<hr>";echo "url ->$url $dir";
 
	//echo $url_scan ;
		
	/*ob_start();
	file_get_contents($url_scan);
	// on recupère le buffer
	$texte_patron_bg = ob_get_contents();
	// on vide et ferme le buffer
	ob_end_clean(); */
	
	$texte_patron_bg = file_get_contents($url_scan);
	
$reg_formats="mp3|ogg|ram|mid|rm|avi|mp4|ra|mpg";
	
	unset($matches) ;
	 
	//trouver des liens complets
	unset($matches) ;
	preg_match_all("/<a href=['\"]?(http:\/\/[a-zA-Z0-9 ()\/\._%\?+'=~-]*\.($reg_formats))['\"]?[^>]*>(.*)<\/a>/iU", $texte_patron_bg, $matches);
	//print_r($matches);
	
	if(sizeof($matches[1]) > 0){
    $trouve = oui;	
	//reset($matches) ;
	for($i=0;$i<sizeof($matches[1]);$i++) 
	{
	$track_url[$i] = $matches[1][$i] ;
	$track_titre[$i] = $matches[3][$i] ;       
	}

	}else{
	
	//trouver des url relatives
	unset($matches) ;
	
	preg_match_all("/<a(.*)href=['\"]?([a-zA-Z0-9 ()\/\._&%\?+'=~-]*\.($reg_formats))['\"]?(.*)[^>]*>(.*)<\/a>/iU", $texte_patron_bg, $matches);
	
	/*echo"<hr>";
	print_r($matches);
	echo"<hr>";*/
	
	if(sizeof($matches[1]) > 0){
	$trouve = oui;	
	$track_list = "" ;
	
		
		
		for($i=0;$i<sizeof($matches[2]);$i++) 
			{
			
			$track_url[$i] = $url."/".$matches[2][$i] ;
			
$renomette="\[dl\]|Listen|Download|Hear here|Here here|\.mp3|\.ogg|\.ram|\.mid|\.mp4|\.avi|rm";
			
			$track_titre[$i] = eregi_replace($renomette,"",$matches[5][$i]) ;      
		    $track_list .= 	$matches[5][$i]."\n";
			
			//if(!ereg("\.", $dir_name)) $track_url[$i] = $pathparts["basename"]."/".$track_url;
			//$track_url[$i] = $pathparts["dirname"]."/".$track_url;
			//if(!ereg(".mp3",$track_url))$track_url = $pathparts["dirname"]."/".$track_url."/".
		}
	
	
	}
	//////
	
	
	
	}
//echo "<hr>";					
//print_r($track_url)	;
// encore un test, trouver des liens simples	

if($trouve != oui){
unset($matches) ;
preg_match_all("/<a href=['\"]?(http:\/\/[a-zA-Z0-9 ()\/\._%\?+&'=~-]*\.($reg_formats))['\"]?[^>]*>/iU", $texte_patron_bg, $matches);
//echo "3->";//print_r($matches);	
	if(sizeof($matches[1]) > 0){
		//reset($matches) ;
		for($i=0;$i<sizeof($matches[1]);$i++) 
			{
			$track_url[$i] = $matches[1][$i] ;
			$track_titre[$i] = $matches[1][$i] ;       
			}
	}		

	unset($matches) ;
preg_match_all("/<a href=['\"]?(http:\/\/[a-zA-Z0-9 ()\/\._%\?+&'=~-]*\.($reg_formats))['\"]?[^>]*>/iU", $texte_patron_bg, $matches);
//echo "3->";//print_r($matches);	
	if(sizeof($matches[1]) > 0){
		//reset($matches) ;
		for($i=0;$i<sizeof($matches[1]);$i++) 
			{
			$track_url[$i] = $matches[1][$i] ;
			$track_titre[$i] = $matches[1][$i] ;       
			}
	}

preg_match_all("/src=['\"]?(http:\/\/[a-zA-Z0-9 ()\/\._%\?+&'=~-]*\.($reg_formats))['\"]?/iU", $texte_patron_bg, $matches);
//echo "3->";//print_r($matches);	
	if(sizeof($matches[1]) > 0){
		//reset($matches) ;
		for($i=0;$i<sizeof($matches[1]);$i++) 
			{
			$track_url[$i] = $matches[1][$i] ;
			$track_titre[$i] = $matches[1][$i] ;       
			}
	}	
	
}
	
	
	// y'a il une playliste ?
unset($matches) ;
preg_match_all("/http:\/\/[a-zA-Z0-9 \/\._%\?=~-]*\.m3u/i", $texte_patron_bg, $matches);
if(sizeof($matches[0]) > 0){	
//print_r($matches);
for($i=0;$i<sizeof($matches[0]);$i++) // tant que $i est inferieur au nombre d'éléments du tableau...
{
$url_patron_bg = "" ;
$texte_patron_bg="" ;
unset($tableau_bg) ;
unset($plist) ;	
$mat = $matches[0][$i] ;
//echo "<br><a href='$PHP_SELF?playlist_dist=$mat'>$mat</a>" ;
}
}

	//info sur le site
	
	$info_site=analyser_site($url);
	$info_syndic=analyser_site($url_scan);

	$site=array();
	
	$site['nom_site']=$info_site['nom_site'];
	$site['description_site']=$info_site['description'];
	$site['url_site'] = $url ;
	$site['nom_syndic_article'] = $info_syndic['nom_site'];
	$site['description_syndic_article'] = $info_syndic['description'];
	$site['url_syndic_article'] = $url_scan ;
	$site['documents']['titres'] = $track_titre ;
	$site['documents']['urls'] = $track_url ;
	$tip = ($time - $top)*1000 ;
	spip_log("---------------- // scan it  -------------------  ".$tip);
	
	return $site ;
	

}



?>