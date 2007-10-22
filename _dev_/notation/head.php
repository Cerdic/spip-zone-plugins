<?php
/**
* Plugin Notation v.0.1
* par JEM (jean-marc.viglino@ign.fr)
* 
* Copyright (c) 2007
* Logiciel libre distribue sous licence GNU/GPL.
*  
* Placer le css et les scripts
*  
**/

function notation_insert_head($flux){
  // Recherche de l'image
  $img = find_in_path('img_pack/notation-on1.gif');
  $multi = ($img != '');
  if ($multi) $img = str_replace('-on1.gif','',$img);
  else
  {  $img = find_in_path('img_pack/notation-on.gif');
     $img = str_replace('-on.gif','',$img);
     $multi=0;
  }

	$flux .=
'<script type="text/javascript">
	var notation_img = "'.$img.'";
	var notation_multi = '.$multi.';
</script>
<script type="text/javascript" src="'.find_in_path('notation.js').'"></script>
<link rel="stylesheet" href="'.find_in_path('css/notation.css').'" type="text/css" media="all" />';

	return $flux;
}
?>