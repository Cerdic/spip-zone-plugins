<?php
if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip("public/assembler"); // Pour pouvoir utiliser recuperer_fond

function exec_lilypond_edit_dist() {
	echo recuperer_fond('editeur/lilypond_edit');
}
?>