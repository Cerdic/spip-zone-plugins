<?php 
if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/presentation');

function exec_liste_zones_dist() {
	// si pas autorise : message d'erreur
	if (!autoriser('voir', 'nom')) {
		include_spip('inc/minipres');
		echo minipres();
		exit;
	}

	$id_groupe = _request('id_groupe');
	
	$result = sql_select('id_zone', 'spip_groupes_zones', 'id_groupe='.$id_groupe);
	
	$zones_sel = array();
	while($r = sql_fetch($result)) {
		$zones_sel[] = $r['id_zone'];
	}
	
	if(!empty($zones_sel))
		$zones_sel2 = array_flip($zones_sel);
	$res_zones = sql_select('*', 'spip_zones');
	
	echo '<select id="zones_'.$id_groupe.'" name="zones[]" MULTIPLE size=3>';
	while($r = sql_fetch($res_zones)) {
		echo '<option value="'.$r['id_zone'].'"';
		if(!empty($zones_sel) && array_key_exists($r['id_zone'], $zones_sel2)) 
			echo ' SELECTED ';
		echo '>'.$r['titre'].'</option>';
	}
	echo '</select>';
	
}

?>