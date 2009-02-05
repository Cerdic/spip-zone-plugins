<?php

/*
 * Abomailmans
 * MaZiaR - NetAktiv
 * tech@netaktiv.com
 * Printemps 2007
 * Inspire de Spip-Listes
 * $Id$
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/distant');
include_spip('inc/affichage');
include_spip('inc/meta');
include_spip('inc/filtres');
include_spip('inc/lang');
include_spip ("inc/abomailmans");

function exec_abomailmans_affiche_template(){

	$template 		= _request('template');
	$sujet 			= _request('sujet');
	$message 		= _request('message');
	$confirmer  	= _request('confirmer');
	$date 			= _request('date');
	$id_rubrique	= _request('id_rubrique');
	$id_mot			= _request('id_mot');
	$charset 		= lire_meta('charset');

	include_spip('public/assembler');
	$contexte_template = array('date' => trim ($date),
							   'id_rubrique' => $id_rubrique,
							   'id_mot' => $id_mot,
							   'template'=>$template,
							   'lang'=>$lang, 
							   'sujet'=>$sujet,
							   'message'=>$message );
	
	$generer_template = generer_url_public('abomailman_template').'&'.abomailman_http_build_query($contexte_template,"","&");
	$texte_template = recuperer_page($generer_template,true);

	echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\n";
	echo "<html lang='$lang' dir='ltr'>";
	echo "<head><meta http_equiv='Content-Type' content='text/html; charset=".$charset."'>\n<meta http-equiv=\"Pragma\" content=\"no-cache\">\n
	\n
	</head><body>\n";

		// si confirmation
	if ($confirmer) {
		$email_liste = _request('email_liste');
		$nomsite=lire_meta("nom_site");
		$email_webmaster = lire_meta("email_webmaster");
		

		if (abomailman_mail($nomsite, $email_webmaster, "", $email_liste, $sujet, $texte_template, true, $charset)) {
			echo "E-mail envoy&eacute; &agrave; la liste de diffusion : ".$email_liste;
		}
	}
	else {
		$texte_template = liens_absolus($texte_template);
		$listes = "";
		$result = sql_select("email, titre","spip_abomailmans");
		$listes .= "<select name='email_liste' class='formo'>";
		while ($row = sql_fetch($result)) {
			$listes .= "<option value='".$row['email']."'>".$row['titre']." -> ".$row['email']."</option>\n";
		}
		$listes .= "</select>";
		
		echo recuperer_fond("prive/abomailman_affiche_template",array("template"=>$template, "sujet"=>$sujet, "message"=>$message, "date"=> $date, "id_rubrique"=>$id_rubrique, "id_mot"=>$id_mot, "listes" => $listes, "texte_template" => $texte_template,"message_erreur" => $message_erreur));
	}
	echo "</body></html>";
	unset ($_POST);
}
?>
