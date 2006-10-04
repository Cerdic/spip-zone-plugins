<?php


// Recuperation des donnees
#var_dump($_POST);

#$meta['alea_ephemere'] = $meta['alea_ephemere_ancien'] = 'x';


function post_widgets() {
	$results = array();

	if (isset($_POST['widgets']) AND is_array($_POST['widgets']))
	foreach ($_POST['widgets'] as $widget) {

		$name = $_POST['name_'.$widget];
		$content = $_POST['content_'.$widget];

		// Si les donnees POSTees ne correspondent pas a leur md5,
		// il faut les traiter
		if (md5($_POST['content_'.$widget]) <> $_POST['md5_'.$widget]) {

			if (!isset($_POST['secu_'.$widget]))
				$results[] = array($name, $content, true);

			elseif (verif_secu($name, $_POST['secu_'.$widget]))
				$results[] = array($name, $content, true);
			else
				return false; // erreur secu
		}
		// cas inchange
		else
			$results[] = array($name, $content, false);
	}

	return $results;
}

function verif_secu($w, $secu) {
	return (
		$secu == md5($GLOBALS['meta']['alea_ephemere'].'='.$w)
	OR
		$secu == md5($GLOBALS['meta']['alea_ephemere_ancien'].'='.$w)
	);
}


// Definition des widgets
class Widget {
	var $name;
	var $text;
	var $key;
	var $md5;
	var $clean;

	function Widget($name, $text='') {
		$this->name = $name;
		$this->text = $text;
		$this->key = strtr(uniqid('wid', true), '.', '_');
	}

	function md5() {
		return md5($this->text);
	}

	function code() {
		return
		'<input type="hidden" name="widgets[]" value="'.$this->key.'" />'."\n"
		. '<input type="hidden" name="name_'.$this->key.'" value="'.$this->name.'" />'."\n"
		. '<input type="hidden" name="md5_'.$this->key
		.'" value="'.$this->md5().'" />'."\n";
	}

	function input($type = 'ligne') {
		switch ($type) {
			case 'texte':
				return '<textarea'
				. ' name="content_'.$this->key.'">'
				. $this->text   # entites_html
				. '</textarea>'."\n";
			case 'ligne':
			default:
				return '<input type="text"'
				. ' name="content_'.$this->key.'"'
				. ' value="'
				. entites_html($this->text)   # entites_html
				. '" />'."\n";
		}
	}
}

//
// Un Widget avec une verification de code de securite
//
class SecureWidget extends Widget {

	function SecureWidget($name, $text='') {
		parent::Widget($name, $text);
	}

	function code() {
		$code = parent::code();
		$secu = md5($GLOBALS['meta']['alea_ephemere']. '=' . $this->name);

		return
			$code
			.'<input type="hidden" name="secu_'.$this->key.'" value="'.$secu.'" />'."\n";
	}
}

?>
