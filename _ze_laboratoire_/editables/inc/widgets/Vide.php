<?php

include_spip('inc/widgets/Widget');

// Un Widget qui n'affiche pas de widget, mais insere les hidden qui vont bien
// C'est a l'utilisateur de mettre la zone de saisie, qui DOIT etre nommee
// content_XYZ, avec XYZ le nom passe en premier argument de l'appel a #EDITABLE

class Vide extends Widget {
	function input() {
		return '';
	}
}

?>
