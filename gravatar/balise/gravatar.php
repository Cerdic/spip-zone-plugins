<?php
	/**
	 *
	 * Gravatar : Globally Recognized AVATAR
	 * Author : Thomas Beaumanoir
	 * Clever Age <http://www.clever-age.com>
	 * Copyright (c) 2006 - Distribue sous licence GNU/GPL
	 * Revisee 2010 C.Morin pour passage en balise statique qui permet l'application de filtrer
	 * et la mise en cache
	 *
	 **/


function balise_GRAVATAR($p) {
	$_email = interprete_argument_balise(1,$p);
	$_size = interprete_argument_balise(2,$p);
	$_default = interprete_argument_balise(3,$p);

	$p->code = "inserer_attribut(filtrer('image_reduire',sinon(gravatar($_email),$_default), (\$s=$_size) ?\$s: 80), 'alt', '')";
	return $p;

}
	
?>