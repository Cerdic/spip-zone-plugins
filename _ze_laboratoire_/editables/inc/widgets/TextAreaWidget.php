<?php

include_spip('inc/widgets/Widget.php');

//
// Un Widget qui affiche un textarea basique
//
class TextAreaWidget extends Widget {

	function input() {
		return '<textarea name="content_'.$this->key.'">'
				. htmlspecialchars($this->text) . '</textarea>'."\n";
	}
}

?>
