<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip("public/assembler"); // Pour pouvoir utiliser recuperer_fond

function exec_tableau_edit_dist() {
	echo recuperer_fond('editeur/tableau_edit');
}
?>