<?php

function autoriser_urledit_dist($faire, $type, $id, $qui, $opt) {
	return ($qui['statut'] == '0minirezo');
}

?>