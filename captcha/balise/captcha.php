<?php

if (!defined("_ECRIRE_INC_VERSION")) return;
session_start();
function balise_CAPTCHA($p) {
    return calculer_balise_dynamique($p, 'CAPTCHA', array());
	
}
function balise_CAPTCHA_dyn() {
return array('formulaires/captcha', 0, 
		array(
			//'captcha' => ($lien ? $lien : generer_url_public('captcha')),
		
			
		));
}


$id_article=$_POST['id_article'];
//$id_article= substr ($id_article2, 16);

if ( ($_POST["txtCaptcha"] == $_SESSION["security_code"]) && 
    (!empty($_POST["txtCaptcha"]) && !empty($_SESSION["security_code"])) ) {
		//print "<meta http-equiv='refresh' content=\"0;URL=spip.php?page=forum&id_article=$id_article\">";
		header("location:spip.php?page=forum&id_article=$id_article");
		exit;

} else {
//  echo "<h1>Erreur! Essayez &agrave; nouveau!</h1>";
}

?>
