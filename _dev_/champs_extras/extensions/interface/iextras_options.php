<?php
function autoriser_iextras_onglet_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('configurer', 'iextra', $id, $qui, $opt);
}

function autoriser_iextras_configurer_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('webmestre', $type, $id, $qui, $opt);
}
?>
