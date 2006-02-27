<?php

//
// Lit un document 'jpg' et extrait des infos exif
//

// NOTE : l'extracteur n'est pas oblige de convertir le contenu dans
// le charset du site, mais il *doit* signaler le charset dans lequel
// il envoie le contenu, de facon a ce qu'il soit converti au moment
// voulu ; dans le cas contraire le document sera lu comme s'il etait
// dans le charset iso-8859-1

function extracteur_jpg($fichier, &$charset) {
  $interesting = array('IFD0' => array('ImageDescription','Make','Model'),
					   'COMMENT' => '');

  $sections = '';
  foreach($interestring as $section => $f) $sections .= "$section,";
  $sections = substr($sections,0,1);

  $exif = @exif_read_data($fichier,$sections,true);

  $to_ret = '';
  if($exif) {
	foreach($interestring as $section => $fields) {
	  if(is_array($fields))
		foreach($fields as $field)
		  $to_ret .= $exif[$section][$field].' ';
	  else
		foreach($exif[$section] as $val)
		  $to_ret .= $val.' ';
		  
	}
  }
  
  $c = ini_get('exif.encode_unicode');
  if($c)
	$charset = $c;

  return substr($to_ret,0,-1);
}

// Sait-on extraire ce format ?
// TODO: ici tester si les binaires fonctionnent
$GLOBALS['extracteur']['jpg'] = 'extracteur_jpg';

?>