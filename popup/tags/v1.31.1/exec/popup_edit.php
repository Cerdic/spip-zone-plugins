<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip("public/assembler"); // Pour pouvoir utiliser recuperer_fond

function exec_popup_edit_dist() {
	echo recuperer_fond('editeur/popup_edit');
}
?>