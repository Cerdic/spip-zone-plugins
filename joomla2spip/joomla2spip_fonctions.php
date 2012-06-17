<?php
include_spip("inc/filtres");

//
//
function joomla2spip_nettoyer_texte($texte,$champ="texte"){

    $result = joomla2spip_nettoyer_texte_perso($texte);
    
    $texte = $result['texte'] ;
    
    //$texte = ereg_replace("(\n|\r|\r\n)+","",$texte);    
    //$texte = preg_replace("#<br[^>]*>#i","\n\n",$texte);
    $texte = str_replace("<br />","\n_ ",$texte);  // pas semantique mais efficace
    //var_dump($texte);
    
    // extra regex
    $texte = preg_replace("#<li[^>]*>#i","-* ",$texte);
    $texte = preg_replace("#<h1[^>]*>#i","{{{",$texte);
    $texte = preg_replace("#</h1>*>#i","}}}",$texte);
    $texte = preg_replace("#<div[^>]*>#i","\n",$texte);
    // extra  regx
    
    /*foreach(extraire_balises($texte,"h1") as $val){
    $inter = supprimer_tags($val);	
    $texte = str_replace($val,"{{{".$inter."}}}",$texte);
    }*/
    
    foreach(extraire_balises($texte,"strong") as $val){
    $inter = supprimer_tags($val);	
    $texte = str_replace($val,"{{".$inter."}}",$texte);
    }
    
    foreach(extraire_balises($texte,"em") as $val){
    $inter = supprimer_tags($val);	
    $texte = str_replace($val,"{".$inter."}",$texte);
    }
    
    foreach(extraire_balises($texte,"a") as $val){
    $html = ereg_replace("\n","",supprimer_tags($val));	
    $name = extraire_attribut($val,"name");
    $lename = ($name)? $name : "" ;
    $href = extraire_attribut($val,"href");
    $href = preg_replace("/index\.php\?option=com_content&task=view&id=([0-9]+)&Itemid=[0-9]+/","spip.php?page=article&id_article=\\1",$href);
    
    if($lename){
    // notes	
    $texte = str_replace($val,"@@@".$lename."@@@",$texte);
    }else{
    $texte = str_replace($val,"[".$html."->".$href."]",$texte);
    }
    
    }
    
    
    //embed
    preg_match_all("/<(embed|img)[^>]*>/i",$texte,$matches);
    //var_dump($matches);
    $i = 0 ;
    foreach($matches[0] as $val){
    $emb = preg_replace("#<#","@@@",$val);		
    $texte = preg_replace("#$val#",$emb,$texte);
    $i++;
    }
    
    // <script> </script>
    $texte = preg_replace("/<script type=\"text\/javascript\">.*?<\/script>/Uims","",$texte);
    
    // supprimer le html (editeurs wysiwyg)       
    //$texte = supprimer_tags(textebrut($texte));
    $texte = supprimer_tags($texte); // ne pas utiliser textebrut sinon on perd les \n
    
    if (substr($texte,-2)=="_ ")  
                  $texte = substr($texte,0,-2);

    
    
    //notes
    preg_match_all("/@@@([a-z0-9]+)@@@/",$texte,$matches);
    //var_dump($matches[1]);
    $i = 0 ;
    foreach($matches[0] as $val){
    $texte = preg_replace("#$val#","<a name='".$matches[1][$i]."'></a>",$texte);
    $i++;
    }
    
    //embed
    preg_match_all("/@@@(embed|img)[^>]*>/i",$texte,$matches);
    //var_dump($matches);
    $i = 0 ;
    foreach($matches[0] as $val){
    $emb = preg_replace("#@@@#","<",$val);		
    $texte = preg_replace("#$val#",$emb,$texte);
    $i++;
    }
    
    // {audio}images/XXX.mp3{/audio}
    preg_match_all("/\{audio\}(.*?)\{\/audio\}/",$texte,$matches);
    //var_dump($matches);
    $i = 0 ;
    foreach($matches[0] as $val){
    $url = preg_replace("#images\/#","documents/",$matches[1][$i]);	
    $texte = preg_replace("#$val#","[".$url."->".$url."]",$texte);
    $i++;
    }
 
    
    $pattern = array("/&nbsp;/Uims");
    $replacement = array(" ");
    $texte = preg_replace($pattern,$replacement,$texte);
    
    
    include_spip('inc/charset');
    $texte = unicode2charset(html2unicode($texte));
    $chapo = unicode2charset(html2unicode($result['chapo']));
    $titre = unicode2charset(html2unicode($result['titre']));
    
    $retour = array("texte"=>$texte,"chapo"=>$chapo,"titre"=>$titre) ;
    
    return $retour[$champ];
}

//
//
function joomla2spip_nettoyer_texte_perso($texte){

  /* desactive titre et chapo (il peut avoir plusieurs h1 et h4)
  $titre_temp = extraire_balise($texte,"h1");	
  $titre = supprimer_tags(ereg_replace("(\n|\r\n|\r)"," ",$titre_temp));	
  $texte = str_replace($titre_temp,"",$texte);
  
  $chapo_temp = extraire_balise($texte,"h4") ;
  $chapo = supprimer_tags($chapo_temp);	
  $texte = str_replace($chapo_temp,"",$texte);
  */
  
  // demontage de texte
  $texte = preg_replace("#<\/*font[^>]*>#","",$texte);
  
  //echo "<pre>YOYO" ;
  //var_dump($texte);
  //echo "</pre>" ;
  
  //preg_match_all("/<div[^>]*id=\"infos[0-9]\"[^>]*>\n*<div align=\"justify\">/Uims",$texte,$matches);
  //var_dump($matches);
  
  return array("texte"=>$texte,"titre"=>$titre,"chapo"=>$chapo) ;

}

//
//
function pas_d_inter($texte) {
  $texte = preg_replace("/({{{|}}})/","",$texte);
  return $texte ;
}


//
//
function rubrique_import($ma_rubrique,$id_parent=0,$id_secteur=0) {

  $result = sql_fetsel('id_rubrique','spip_rubriques','titre='.sql_quote($ma_rubrique['titre'])) ; 
      
      if($result){
       $id_rubrique = $result['id_rubrique'] ;
       }
      else{
  	$id_rubrique = sql_insertq('spip_rubriques',array('titre'=>$ma_rubrique['titre'],'id_parent' => $id_parent,'id_secteur' => $id_secteur,'descriptif' => $ma_rubrique['descriptif'])) ;	
      }
  
  return $id_rubrique ;
}


//
//
function article_import($mon_article) {
	$err = '';
	
	// chercher si l'article n'a pas deja ete importe
	$ancien_id = $mon_article['id_article'];
	$result = sql_fetsel('id_article','spip_articles','id_article='.intval($ancien_id)) ;
	if($result)
	       return;
	
        
	// chercher la rubrique
	$titre_rub = $mon_article['rubrique'];
  $result = sql_fetsel('id_rubrique','spip_rubriques','titre='.sql_quote($titre_rub)) ; 
  if($result){
     $id_rubrique = $result['id_rubrique'] ;
  }
    
    
	// creer article vide
	include_spip('action/editer_article');
	$id_article = insert_article($id_rubrique);	
	$sql = "UPDATE spip_articles SET id_article = '$ancien_id' WHERE id_article = '$id_article'";
	spip_query($sql);

	if (spip_version_compare($GLOBALS['spip_version_branche'], '3.0.0', '>=')){
		include_spip('action/editer_article');
		$sql = "UPDATE spip_auteurs_liens SET id_objet = '$ancien_id' WHERE id_objet = '$id_article' AND objet = 'article'";
	}
	else{
		include_spip('inc/modifier');
		$sql = "UPDATE spip_auteurs_articles SET id_article = '$ancien_id' WHERE id_article = '$id_article'";
	}
	spip_query($sql);
	$id_article = $ancien_id ;
	// le remplir
	$c = array();
	foreach (array(
		'surtitre', 'titre', 'soustitre', 'descriptif',
		'nom_site', 'url_site', 'chapo', 'texte', 'maj', 'ps','visites'
	) as $champ)
		$c[$champ] = $mon_article[$champ];

	revisions_articles($id_article, $c);

	// Modification de statut, changement de rubrique ?
	$c = array();
	foreach (array(
		'date', 'statut', 'id_parent'
	) as $champ)
		$c[$champ] = $mon_article[$champ];
	$c['id_parent'] = $id_rubrique ;
	$err .= instituer_article($id_article, $c);

	// Un lien de trad a prendre en compte
	if (!spip_version_compare($GLOBALS['spip_version_branche'], '3.0.0', '>=')) $err .= article_referent($id_article, array('lier_trad' => _request('lier_trad')));
	
	// ajouter les extras
	
	
	return $err; 
}

//
//
function forum_import($mon_forum) {

	if (!spip_version_compare($GLOBALS['spip_version_branche'], '3.0.0', '>=')){
		sql_insertq('spip_forum',
			array('id_objet'=>$mon_forum['id_objet'],
				'objet'=>'article',
				'date_heure'=>$mon_forum['date_heure'],
				'date_thread'=>$mon_forum['date_thread'],
				'titre'=>$mon_forum['titre'],
				'texte'=>$mon_forum['texte'],
				'auteur'=>$mon_forum['auteur'],
				'email_auteur'=>$mon_forum['email_auteur'],
				'statut'=>$mon_forum['statut'],
				'ip'=>$mon_forum['ip'])) ;	
	}else{
		sql_insertq('spip_forum',
			array('id_article'=>$mon_forum['id_objet'],
				'date_heure'=>$mon_forum['date_heure'],
				'date_thread'=>$mon_forum['date_thread'],
				'titre'=>$mon_forum['titre'],
				'texte'=>$mon_forum['texte'],
				'auteur'=>$mon_forum['auteur'],
				'email_auteur'=>$mon_forum['email_auteur'],
				'statut'=>$mon_forum['statut'],
				'ip'=>$mon_forum['ip'])) ;	
	}
	return;
}

//
//
function sef_url($titre){
	$titre = str_replace("&#8217;","'",$titre);
	$titre = preg_replace("/« | »|\?|\./","",$titre);
	$titre = preg_replace("/'/","",trim($titre));
	$titre = str_replace("°","-",$titre);
	$pattern = "/[^a-zA-Z0-9,_-]/";
	include_spip('inc/charsets');
	$url = preg_replace($pattern,"-",translitteration($titre));	
	$url = strtolower($url);	
	$url = preg_replace("/-+/","-",$url);	
		
	$vilains = array("points-de-vente","le-planbnet");	
	if(in_array($url,$vilains)) return 'DEGAGE_VILAIN';
		
	if(strlen($url) > 15){
		$pattern = substr($url, -8) ;
		$url = preg_replace("+".$pattern."$+","",$url);
		}
	return $url ;
}

function joomla2spip_nettoyer_url($url){
	if(preg_match('/search|file:\/\/\/|#n[1-9]|anonymouse\.org|webwarper.net|:80/',$url)){
	return '' ;
	}	
	return $url ;
}

function joomla2spip_url_relative($url){
$url = preg_replace("+http://www.leplanb.org/|\.html$+","",$url);
return $url ;
}

function joomla2spip_url_import($mon_url){
$url = $mon_url['url'];
$type = $mon_url['type'];
$id_objet = $mon_url['id_objet'];
$date = $mon_url['date'];

sql_insertq('spip_urls', array('url' => $url,'type' => $type,'id_objet' => $id_objet,'date' => $date));

return  ;
}


//
// importer auteur
function joomla2spip_auteur_import($mon_auteur){
	if($mon_auteur['statut'] == "Super Administrator" || $mon_auteur['statut'] == "Administrator") $statut = "0minirezo" ;
	else if($mon_auteur['statut'] == "Editor" || $mon_auteur['statut'] == "Publisher") $statut = "1comite" ;
	else($mon_auteur['statut'] == "visiteur") $statut = "6forum" ;

	$email = $mon_auteur['email'];
	$login = $mon_auteur['login'];
	$nom = $mon_auteur['nom'];

	sql_insertq('spip_auteurs', array('email' => $email,'login' => $login,'statut' => $statut,'nom' => $nom));

	return ;	
}


?>