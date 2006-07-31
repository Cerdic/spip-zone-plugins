<?php

/*
génére une regexp OU pour la liste de mot
*/
function tags_enregexp($liste,$id_groupe) {
    include_ecrire('_libs_/tag-machine/inc_tag-machine.php');
	$tags = new ListeTags(filtrer_entites($liste),'',$id_groupe);
	$mots = $tags->getTags();
	$str = '';
	foreach ($mots as $mot) {
	  $str .= $mot->titre.'|';
	}
	$str = substr($str,0,-1);
	if ($str) {
	  return '('.$str.')';
	} else
	  return '';
}


//ajax

function seulement_tout($tag) {
  
  if(is_object($tag)) {
	return $tag->echapper();
  } else {
	$cgroupe = '';
	$ctag = $tag;
  }
  
  $cgroupe = (strpos($cgroupe,' ') || strpos($cgroupe,':'))?'"'.$cgroupe.'"':$cgroupe;
  $ctag = (strpos($ctag,' ') || strpos($ctag,':'))?'"'.$ctag.'"':$ctag;

  return ($cgroupe.($cgroupe)? ':':'').$ctag;
  
}

function seulement_titre($tag) {
  
  if(is_object($tag)) {
	return $tag->getTitreEchappe();
  } else {
  	$tag = (strpos($tag,' ') || strpos($tag,':') || strpos($tag,','))?'"'.$tag.'"':$tag;
	return $tag;
  }
  
}

function seulement_type($tag) {
  
  if(is_object($tag)) {
	return $tag->getTypeEchappe();
  } else {
  	$tag = (strpos($tag,' ') || strpos($tag,':') || strpos($tag,','))?'"'.$tag.'"':$tag;
	return $tag;
  }
  
}

//marche pas ce truc!!
function dernier_quote($lst) {
  if(substr_count($lst,'"')%2 != 0)
	return $lst.'"';
  return $lst;
}

function trouve_debut($liste,$id_groupe) {
  include_ecrire('_libs_/tag-machine/inc_tag-machine.php');
  $liste = dernier_quote($liste);
  $tags = new ListeTags(filtrer_entites($liste),'',$id_groupe);
  return array_slice($tags->getTags(),0,-1);
}

function tout_debut($liste,$id_groupe) {
  $mots = array_map('seulement_tout',trouve_debut($liste,$id_groupe));
  return join(' ',$mots);
}

function titre_debut($liste,$id_groupe) {
  $mots = array_map('seulement_titre',trouve_debut($liste,$id_groupe));
  return join(' ',$mots);
}

function type_debut($liste,$id_groupe) {
  $mots = array_map('seulement_type',trouve_debut($liste,$id_groupe));
  return join(' ',$mots);
}

function trouve_fin($liste,$id_groupe) {
  $liste = dernier_quote($liste);
  $tags = new ListeTags(filtrer_entites($liste),'',$id_groupe);
  $tags = $tags->getTags();
  return $tags[count($tags)-1];
}

function titre_fin($liste,$id_groupe) {
  $mots = seulement_titre(trouve_fin($liste,$id_groupe));
  return str_replace('"','',$mots);

}

function type_fin($liste,$id_groupe) {
  $mots = seulement_type(trouve_fin($liste,$id_groupe));
  return str_replace('"','',$mots);

}

function tags_escape_quote($tag) {
  return str_replace('"','\\"',$tag);
}

/*
combien il y a de mots dans le paramétre
*/
if(!function_exists('compte_having')) {
  function compte_having($liste) {
    include_ecrire('_libs_/tag-machine/inc_tag-machine.php');
	$tags_liste = new ListeTags(filtrer_entites($liste),'FAQ',1);
	return count($tags_liste->getTags())-1;
  }
}

/*
un critére pour le HAVING sql
*/
if(!function_exists('critere_having')) {
  function critere_having($idb, &$boucles, $crit){	
	$hav = calculer_liste($crit->param[0], array(), $boucles, $boucles[$idb]->id_parent);
	$boucles[$idb]->having = "'.$hav.'";
  }
}

?>
