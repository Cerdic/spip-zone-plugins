<?php

	if (!defined("_ECRIRE_INC_VERSION")) return;

	include_spip("cryptographp/functions");

function balise_CRYPTOGRAPHP ($params)
{
	$params->code = "dsp_crypt(0,1,1)";
	$params->type = 'php';
	$params->interdire_scripts = true;

	return $params;
}

function balise_CRYPTOGRAPHPIMG ($params)
{
	$params->code = "dsp_crypt_img(0,1)";
	$params->type = 'php';
	$params->interdire_scripts = true;

	return $params;
}

function balise_CRYPTOGRAPHPBTN ($params)
{
	$params->code = "dsp_crypt_btn(0,1)";
	$params->type = 'php';
	$params->interdire_scripts = true;

	return $params;
}

function balise_CRYPTOGRAPHPCHK ($params)
{

	$code = "";
	$f = $params->fonctions;
	if ($a = $params->param)
	{
        	$sinon = array_shift($a);
        	if  (!array_shift($sinon))
		{
			$params->fonctions = $a;
			array_shift( $params->param );
			$section = array_shift($sinon);
			$section = ($section[0]->type=='texte') ? $section[0]->texte : '';
			//$tag = array_shift($sinon);
			//$tag = ($tag[0]->type=='texte') ? $tag[0]->texte : '';
		}
	}

	spip_log("section: $section tag: $tag");

	if ( $section )
	{
		if ( isset($_GET[$section])) $code = $_GET[$section];
		if ( isset($_POST[$section])) $code = $_POST[$section];
	}

	$params->code = "verifier_code('$code')";
	$params->type = 'php';
	$params->interdire_scripts = false;


	return $params;

}

function verifier_code($code)
{
	// spip_log("code: $code");
	if ( chk_crypt($code) == true )
	{
		// Securite v1.3 en attendant la correction de la version 1.4
		// Evite une reutilisation du code pour soumettre d'autres formulaires
		unset($_SESSION['cryptcode']);
		return "oui";
	}
	else
	{
		return "non";
	}
}

?>
