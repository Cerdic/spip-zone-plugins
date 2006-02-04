<?php

 include ("inc.php3");
  include_ecrire('_libs_/tag-machine/inc_tag-machine.php');

function seulement_titre($tag) {
  
  if(is_object($tag)) {
	return $tag->getTitreEchappe();
  } else {
  	$tag = (strpos($tag,' ') || strpos($tag,':') || strpos($tag,','))?'"'.$tag.'"':$tag;
	return $tag;
  }
  
}

function trouve_debut($liste,$id_groupe) {
  $tags = new ListeTags(filtrer_entites($liste),'Tag',$id_groupe);
  return array_slice($tags->getTags(),0,-1);
}

function titre_debut($liste,$id_groupe) {
  $mots = array_map('seulement_titre',trouve_debut($liste,$id_groupe));
  return join(' ',$mots);
}

function trouve_fin($liste,$id_groupe) {
  $tags = new ListeTags(filtrer_entites($liste),'Tag',$id_groupe);
  $tags = $tags->getTags();
  return $tags[count($tags)-1];
}

function titre_fin($liste,$id_groupe) {
  $mots = seulement_titre(trouve_fin($liste,$id_groupe));
  return str_replace('"','',$mots);

}

$res = spip_query("SELECT titre
FROM spip_mots WHERE".(isset($_GET['id_groupe'])?(" id_groupe = ".$_GET['id_groupe']." AND"):'')." titre LIKE
'%".titre_fin($_GET['titre'],$_GET['id_groupe'])."%'");

$xml = "new Array(";
$mots = array();
  
if (spip_num_rows($res) > 0) {
  while ($row = spip_fetch_array($res)) {
	$nouveau = new Tag($row['titre'],'Tag',$_GET['id_groupe']);
	$mots[] = '"'.str_replace('"','\\"',titre_debut($_GET['titre'],$_GET['id_groupe'])." ".$nouveau->getTitreEchappe()).'"';
  }
 }
$xml .= join(',',$mots).");";
echo $xml;

?>

