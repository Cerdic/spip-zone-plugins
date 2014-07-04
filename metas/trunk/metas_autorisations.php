<?php
// declarer la fonction du pipeline
function metas_autoriser(){}

function autoriser_editermetas_dist($faire, $type, $id, $qui, $opt) {
	return autoriser('modifier', $type, $id, $qui, $opt);
}

?>