<?php
function balise_ID_PHOTOSET_dist($p) {
	$_type = $p->type_requete;
	if($_type == 'flickr_photos_getallcontexts') {
	  $t = champ_sql('type',$p);
	  $id = champ_sql('id_contexte',$p);
	  $p->code = "($t == 'set')?$id:''";
	} else $p->code = champ_sql('id_photoset',$p);
	return $p;
}
?>
