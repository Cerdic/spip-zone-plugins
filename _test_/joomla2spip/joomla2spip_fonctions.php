<?php
include_spip("inc/filtres");

function joomla2spip_nettoyer_texte($texte,$champ="texte"){

$result = joomla2spip_nettoyer_texte_perso($texte);

$texte = $result['texte'] ;

$texte = ereg_replace("(\n|\r|\r\n)+","",$texte);
//$texte = preg_replace("#<br[^>]*>#i","\n\n",$texte);

//var_dump($texte);


foreach(extraire_balises($texte,"strong") as $val){
$inter = supprimer_tags($val);	
$texte = str_replace($val,"\n\n{{{".$inter."}}}\n\n",$texte);
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
$href = preg_replace("/index.php?option=com_content&task=view&id=/","spip.php?page",$href);

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
$texte = supprimer_tags(textebrut($texte));



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

return $retour[$champ] ;
}

function joomla2spip_nettoyer_texte_perso($texte){

$titre_temp = extraire_balise($texte,"h1");	
$titre = supprimer_tags(ereg_replace("(\n|\r\n|\r)"," ",$titre_temp));	
$texte = str_replace($titre_temp,"",$texte);

$chapo_temp = extraire_balise($texte,"h4") ;
$chapo = supprimer_tags($chapo_temp);	
$texte = str_replace($chapo_temp,"",$texte);


// demontage de texte
$texte = preg_replace("#<\/*font[^>]*>#","",$texte);

//echo "<pre>YOYO" ;
//var_dump($texte);
//echo "</pre>" ;

//preg_match_all("/<div[^>]*id=\"infos[0-9]\"[^>]*>\n*<div align=\"justify\">/Uims",$texte,$matches);
//var_dump($matches);

return array("texte"=>$texte,"titre"=>$titre,"chapo"=>$chapo) ;

}

function pas_d_inter($texte) {
$texte = preg_replace("/({{{|}}})/","",$texte);
return $texte ;
}


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


function article_import($mon_article) {
	$err = '';
        
	// chercher la rubrique
	$titre_rub = $mon_article['rubrique'];
    
    $result = sql_fetsel('id_rubrique','spip_rubriques','titre='.sql_quote($titre_rub)) ; 
    
    if($result){
     $id_rubrique = $result['id_rubrique'] ;
    }
    
    
	// creer article vide
	include_spip('action/editer_article');
	$id_article = insert_article($id_rubrique);

	// le remplir
	$c = array();
	foreach (array(
		'surtitre', 'titre', 'soustitre', 'descriptif',
		'nom_site', 'url_site', 'chapo', 'texte', 'maj', 'ps','visites'
	) as $champ)
		$c[$champ] = $mon_article[$champ];

	include_spip('inc/modifier');
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
	$err .= article_referent($id_article, array('lier_trad' => _request('lier_trad')));
	
	// ajouter les extras
	
	
	return $err; 
}



?>