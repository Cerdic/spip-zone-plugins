<?php

	if (!defined("_ECRIRE_INC_VERSION")) return;

	$cryptinstall="plugins/cryptographp/cryptographp/cryptographp.fct.php";

 	if(session_id() == "") session_start();
	$_SESSION['cryptdir']= dirname($cryptinstall);
 

function balise_CRYPTOGRAPHP ($params)
{
	$params->code = "dsp_crypt_html(0,1)";
	$params->type = 'php';
	$params->interdire_scripts = true;

	return $params;
}

function balise_CRYPTOGRAPHPIMG ($params)
{
	$params->code = "dsp_crypt_img(0)";
	$params->type = 'php';
	$params->interdire_scripts = true;

	return $params;
}

function balise_CRYPTOGRAPHPBTN ($params)
{
	$params->code = "dsp_crypt_btn(0)";
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
	//spip_log("code: $code");
	include_spip("cryptographp/cryptographp.fct");
	if ( chk_crypt($code) == true )
	{
		return "oui";
	}
	else
	{
		return "non";
	}
}

function dsp_crypt_img($cfg=0)
{
	$out = "<img id='cryptogram' src='".$_SESSION['cryptdir']."/cryptographp.php?cfg=".$cfg.(SID==""?'':"&amp;".SID)."' alt='' title='' />";

 	return $out;
}

function dsp_crypt_btn($cfg=0)
{
 	$out = "<a title='".($reload==1?'':$reload)."' style=\"cursor:pointer\" onclick=\"javascript:document.images.cryptogram.src='".$_SESSION['cryptdir']."/cryptographp.php?cfg=".$cfg.(SID==""?'':"&amp;".SID)."&amp;'+Math.round(Math.random(0)*1000)+1\"><img src=\"".$_SESSION['cryptdir']."/images/reload.png\" alt='' title='' /></a>";
	
 	return $out;
}

function dsp_crypt_html($cfg=0,$reload=0)
{ 
	$out = "<table><tr><td><img id='cryptogram' src='".$_SESSION['cryptdir']."/cryptographp.php?cfg=".$cfg."&".SID."'></td>";
	if ($reload)
	{
		$out .= "<td><a title='".($reload==1?'':$reload)."' style=\"cursor:pointer\" onclick=\"javascript:document.images.cryptogram.src='".$_SESSION['cryptdir']."/cryptographp.php?cfg=".$cfg."&".SID."&'+Math.round(Math.random(0)*1000)+1\"><img src=\"".$_SESSION['cryptdir']."/images/reload.png\"></a></td>";
	}
	$out .= "</tr></table>";
}


?>
