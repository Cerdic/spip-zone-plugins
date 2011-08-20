<?php
	$headers .= "MIME-Version: 1.0 \n";
	$headers .= "Content-type: text/html; charset=iso-8859-1 \n";
	$email = "";
	$headers .= "from:";
	$subject = _T('amap:panier_dispo');
	$lemail = _T('amap:panier_dispo_auteur{date_distribution=#DATE_DISTRIBUTION, nom=#NOM}');
	$envoyer_mail($email, $subject, $lemail, $headers);
	
?>