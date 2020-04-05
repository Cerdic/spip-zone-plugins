<?php

function FormulaireUpload_declarer_tables_interfaces($interface){
	// permettre <BOUCLE_a(AUTEURS){id_document}>
	$interface['tables_jointures']['spip_auteurs'][]= 'documents_liens';

	return $interface;
}

?>