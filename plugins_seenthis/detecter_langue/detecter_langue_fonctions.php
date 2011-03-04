<?php

function detecter_langue($texte) {
	include_spip("inc/detecter_langue");
	return _detecter_langue($texte);

}

?>