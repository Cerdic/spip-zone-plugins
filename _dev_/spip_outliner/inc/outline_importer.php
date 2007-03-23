<?php
function tagname($tag){
	if (preg_match(',^([a-z][\w:]*),i',$tag,$reg))
		return $reg[1];
	return "";
}
function spip_xml_decompose_tag($tag){
	$tagname = tagname($tag);
	$liste = array();
	$p=strpos($tag,' ');
	$tag = substr($tag,$p);
	$p=strpos($tag,'=');
	while($p!==false){
		$attr = trim(substr($tag,0,$p));
		$tag = ltrim(substr($tag,$p+1));
		$quote = $tag{0};
		$p=strpos($tag,$quote,1);
		$cont = substr($tag,1,$p-1);
		$liste[$attr] = $cont;
		$tag = substr($tag,$p+1);
		$p=strpos($tag,'=');
	}
	return array($tagname,$liste);
}

function spip_xml_match_nodes($regexp,&$arbre,&$matches){
	if(is_array($arbre) && count($arbre))
		foreach(array_keys($arbre) as $tag){
			if (preg_match($regexp,$tag))
				$matches[$tag] = &$arbre[$tag];
			foreach(array_keys($arbre[$tag]) as $occurences)
				spip_xml_match_nodes($regexp,$arbre[$tag][$occurences],$matches);
		}
	return (count($matches));
}

function opml2tree($id_form,$id_parent,&$arbre,&$trans){
	$id_enfant = 0;
	if(is_array($arbre) && count($arbre))
		foreach($arbre as $tag=>$sousarbre){
			list($tagname,$attributs) = spip_xml_decompose_tag($tag);
			$c=array();
			foreach($attributs as $col=>$v){
				if (!isset($trans[$col]))
					$trans[$col]=Forms_creer_champ($id_form,'texte',$col,array('public'=>'oui'));
				$c[$trans[$col]] = $v;
			}
			list($id_enfant,$erreur) = $id_parent?Forms_arbre_inserer_donnee($id_form,$id_parent,'fils_cadet',$c):Forms_arbre_inserer_donnee($id_form,$id_enfant,'petit_frere',$c);
			if ($id_enfant)
				foreach($sousarbre as $opmls)
					opml2tree($id_form,$id_enfant,$opmls,$trans);
		}
}

function inc_outline_importer($opml_arbre,$nom_fichier){
	$titre = _L("Sans titre");
	$descriptif = $nom_fichier;
	if (spip_xml_match_nodes(",^head,i",$opml_arbre,$heads)){
		if (spip_xml_match_nodes(",^title,i",$heads,$titles))
			$titre = spip_xml_aplatit(end($titles),' ');
	}
	$colonnes = array();
	$table = array();
	$trans=array('text'=>'ligne_1','_status'=>'_status');
	if (spip_xml_match_nodes(",^body,i",$opml_arbre,$body_matched)){
		include_spip('base/forms_api');
		$f = find_in_path('base/Outliner.xml');
		$id_form = Forms_creer_table($f,'outline',false,array('titre'=>$titre,'descriptif'=>$descriptif));
		
		foreach($body_matched as $bodys)
			foreach($bodys as $body){
				opml2tree($id_form,0,&$body,&$trans);
			}
	}
}

?>