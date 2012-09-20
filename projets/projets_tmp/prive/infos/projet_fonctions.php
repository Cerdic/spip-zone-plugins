<?php
function instituer_projet($id_projet, $id_parent, $statut){
	$instituer_projet = charger_fonction('instituer_projet', 'inc');
	return $instituer_projet($id_projet, $statut, $id_parent);
}
?>