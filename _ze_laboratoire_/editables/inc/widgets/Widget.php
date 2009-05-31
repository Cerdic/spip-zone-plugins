<?php

// Classe generale pour un widget basique
// A priori, tous les widgets devraient d�river de celui ci

class Widget {
	// la valeur de l'element manipule par le widget
	var $text;
	// un identifiant unique pour ce widget
	var $key;
	// pour determiner si le contenu a change
	var $md5text;
	// une cle md5 permettant de valider qu'on a pas tripote les donn�es
	var $md5secu;

	// un constructeur avec en argument un nom et une valeur initiale
	function Widget($id, $text='') {
		$this->text= $text;
		$this->md5text= md5($this->text);
		$this->key = $id;
	}

	// le code html a inserer avant le formulaire lui meme
	// les "vrais" widgets peuvent surcharger cette methode pour ins�rer
	// des hidden sp�cifiques par exemple
	function code() {
		$res=
		  '<input type="hidden" name="widgets[]" value="'.$this->key.'" />'."\n"
		. '<input type="hidden" name="md5_'.$this->key
			. '" value="'.$this->md5text.'" />'."\n";
		return $res;
	}

	// le code html correspondant au formulaire de saisie
	// les "vrais" widgets doivent surcharger cette methode
	function input() {
		return '<input type="text" width="80"'
			. ' name="content_'.$this->key.'" value="'
			. htmlspecialchars($this->text)   # entites_html
			. '" />'."\n";
	}
}

?>
