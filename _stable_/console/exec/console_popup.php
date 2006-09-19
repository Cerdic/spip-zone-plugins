<?php

function exec_console_popup(){
	global $connect_statut;
	global $connect_id_auteur;
	global $connect_toutes_rubriques;
	global $spip_lang_right;

	if ($connect_statut == "0minirezo" && $connect_toutes_rubriques) {
		include_spip('inc/console');
		echo console_code_flash('100%','100%');
	}
	
}

?>