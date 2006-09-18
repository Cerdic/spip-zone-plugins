<?php

// Classe pour un widget vide, permettant d'inserer les hidden necessaires
// et de laisser l'auteur definir les details de son input lui meme
// ATTENTION : l'input DOIT avoir un name content_XX, avec XX=le nom passe
// en premier argument a #EDITABLE

include_spip('inc/widgets/Widget');

class Vide extends Widget {
	function input() {
		return '';
	}
}

?>
