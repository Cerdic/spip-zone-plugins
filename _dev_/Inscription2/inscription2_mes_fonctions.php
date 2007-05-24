<?php
function confirmation($id, $mode, $cle){
	$q = spip_query("SELECT statut, alea_actuel FROM spip_auteurs WHERE id_auteur = '$id'");
	$q = spip_fetch_array($q);
	if($q['statut'] == 'aconfirmer' and $mode == 'conf' and $cle ==  $q['alea_actuel']){
		return 'pass';
	}elseif($q['statut'] == 'aconfirmer' and $mod == 'sup' and $cle ==  $q['alea_actuel']){
		return 'sup';
	}else
		return 'rien';
}
?>
	