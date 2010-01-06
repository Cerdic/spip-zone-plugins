<?php
function envoyer_mail_html($dest,$sujet,$exp,$reply,$texte){
	// function envoyer_mail_html($dest,$sujet,$exp,$reply,$texte,$pj,$pj_nom){

	// Preparation de la pièce jointe, on la fait belle pour l'envoi !
	// $fichier = file_get_contents($pj);
	// $fichier = chunk_split( base64_encode($fichier) );

	$message = 'This is a multi-part message in MIME format.'."\n\n"; 
	// Delimiteur
	$frontiere = '-----=' . md5(uniqid(mt_rand())); 

	// Entetes
	$entetes = 'From: '.$exp."\n"; 
	$entetes .= 'Return-Path: <'.$reply.'>'."\n"; 
	$entetes .= 'MIME-Version: 1.0'."\n"; 
	$entetes .= 'Content-Type: multipart/mixed; boundary="'.$frontiere.'"'; 


	// On envoit le message en HTML, s'plus classe
	$message .= '--'.$frontiere."\n"; 
	$message .= 'Content-Type: text/html; charset="utf-8"'."\n"; 
	$message .= 'Content-Transfer-Encoding: 8bit'."\n\n"; 
	$message .= '<html> 
					<head><title>'.$sujet.'</title></head> 
					<body>'.$texte.'</body>
				</html>'.
				"\n\n"; 

	// On ajoute la pièce jointe, c'est bien le seul truc qui nous intéresse en fait ! :D
	// $message .= '--'.$frontiere."\n"; 
	// Attention ! On n'envoi QUE des zip
	// $message .= 'Content-Type: application/zip; name="'.$pj_nom.'"'."\n"; 
	// $message .= 'Content-Transfer-Encoding:base64'."\n"; 
	// $message .= 'Content-Disposition:inline; filename="'.$pj_nom.'"'."\n\n".$fichier;

	if(mail($dest,$sujet,$message,$entetes)){ 
		// return "Votre fichier a bien été envoyé. N'hésitez pas à en soumettre de nouveaux."; 
		return true;
	}
	else{ 
		// return "Une erreur s'est produite lors de l'envoi de votre fichier."; 
		return false;
	} 
}
