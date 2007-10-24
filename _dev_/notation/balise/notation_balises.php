<?php
/**
* Plugin Notation v.0.1
* par JEM (jean-marc.viglino@ign.fr)
*
* Copyright (c) 2007
* Logiciel libre distribue sous licence GNU/GPL.
*
* Definition des balises #NOTATION_ETOILE{n}
*
**/
include_spip('inc/notation_util');

// Affichage des etoiles cliquables
function notation_etoile_click($nb, $id) { 
	$ret = '';
	if ($nb>0 && $nb<=0.5) {
		$nb=1;
	} else {
		$nb = round($nb);
	}
	// Recherche de l'image
	$img = find_in_path('img_pack/notation-off1.gif');
	$multi = ($img != '');
	if ($multi) { 
		$img0 = str_replace('-off1.gif','',$img);
	} else {
		$img = find_in_path('img_pack/notation-off.gif');
	}
	for ($i=1; $i<=notation_get_nb_notes(); $i++) {
		if ($multi) {
			$img = $img0.'-on'.$i.'.gif';
		}
		$ret .= "<button type='submit' name='note' value='$i' title='Noter: $i !'><img src='$img' alt='' /></button>";
	}
	return $ret;
}

// Affichage d'un nombre sous forme d'etoiles
function notation_etoile($nb)
{ if ($nb>0 && $nb<=0.5) $nb=1;
  else $nb = round($nb);
  // Recherche de l'image
  $img = find_in_path('img_pack/notation-on1.gif');
  $multi = ($img != '');
  if ($multi) $img0 = str_replace('-on1.gif','',$img);
  else $img = find_in_path('img_pack/notation-on.gif');
  for ($i=1; $i<=$nb; $i++)
  {  if ($multi) $img = $img0.'-on'.$i.'.gif';
     $ret .= '<img src="'.$img.'" title="'._T('notation:note_'.$nb).'" style="vertical-align:middle" class="notation" />';
  }
  $img = str_replace('-on.gif','-off.gif',$img);
  for ($i=$nb+1; $i<=notation_get_nb_notes(); $i++)
  {  if ($multi) $img = $img0.'-off'.$i.'.gif';
     $ret .= '<img src="'.$img.'" title="'._T('notation:note_'.$nb).'" style="vertical-align:middle" class="notation" />';
  }
  return $ret;
}

// Affichage de la note sous forme d'etoiles
function balise_NOTATION_ETOILE($p){
  // Parametre de la balise
  $param = interprete_argument_balise(1,$p);
  // Code...
	$p->code = "notation_etoile(".$param.")";
	$p->interdire_scripts = false;
	return $p;
}
?>