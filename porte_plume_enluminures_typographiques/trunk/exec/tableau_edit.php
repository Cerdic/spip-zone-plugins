<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function exec_tableau_edit_dist() {
	include_spip("public/assembler"); // Pour pouvoir utiliser recuperer_fond
	echo recuperer_fond('editeur/tableau_edit');
}
?>