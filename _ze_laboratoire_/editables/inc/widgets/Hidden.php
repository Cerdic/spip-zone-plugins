<?php

include_spip('inc/widgets/Widget');

//
// Un Widget qui affiche un hidden
//
class Hidden extends Widget {
	function input() {
		return '<input type="hidden" name="content_'.$this->key.'" value="'
				. htmlspecialchars($this->text) . "\"/>\n";
	}
}

?>
