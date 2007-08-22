<?php

/*
 * Abomailmans
 * MaZiaR - NetAktiv
 * tech@netaktiv.com
 * Printemps 2007
 * Inspire de Spip-Listes
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
	$Confirmer  	= _request('Confirmer');
	$date 			= _request('date');
	$id_rubrique	= _request('id_rubrique');
	$id_mot			= _request('id_mot');
	$charset 		= lire_meta('charset');
	
/*	echo "<pre>";
	print_r($GLOBALS);
	echo "</pre>";*/
	include_spip('public/assembler');
	$contexte_template = array('date' => trim ($date),
							   'id_rubrique' => $id_rubrique,
							    'id_mot' => $id_mot,
							   'template'=>$template,
							   'lang'=>$lang, 
							   'sujet'=>$sujet,
							   'message'=>$message );
	
	$texte_template = recuperer_fond('templates/'.$template, $contexte_template);



	echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\">\n";
	echo "<html lang='$lang' dir='ltr'>";
	echo "<head><meta http_equiv='Content-Type' content='text/html; charset=".$charset."'>\n<meta http-equiv=\"Pragma\" content=\"no-cache\">\n
	<script src=\"spip.php?page=jquery.js\" type=\"text/javascript\"></script>\n
	<script type=\"text/javascript\">
			$('#Confimrer').click(function(){
				$('#Confimrer').hide();
			 });
			$('#confirmation').submit(function(){
				var	 data = $('input,textarea,radio,select, checkbox', this).serialize();
				$.ajax({ type: 'POST', 
							url: '/ecrire/?exec=abomailmans_affiche_template', 
							data: data, 
							success: function(msg){  $('#envoyer').html(msg); }
					});
				$('#apercu').hide();
				return false;
				
			});
	</script>\n
	
	</head><body>\n";

		// si confirmation
	if ($Confirmer) {
		$email_liste 	= _request('email_liste');
		$nomsite=lire_meta("nom_site");
		$email_webmaster = lire_meta("email_webmaster");
		

		if (abomailman_mail ($nomsite, $email_webmaster, "", $email_liste, $sujet, $texte_template, true, $charset)) {
			echo "E-mail envoy&eacute; &agrave; la liste de diffusion : ".$email_liste;
		}
	}
	else {
	
		echo "<form action=\"/ecrire/?exec=abomailmans_affiche_template\" method=\"post\" id=\"confirmation\">";

		echo "<input type=\"hidden\" name=\"template\" value=\"".$template."\">";
		echo "<input type=\"hidden\" name=\"sujet\" value=\"".$sujet."\">";
		echo "<input type=\"hidden\" name=\"message\" value=\"".$message."\">";
		echo "<input type=\"hidden\" name=\"date\" value=\"".$date."\">";

		echo liens_absolus($texte_template).$message_erreur."";
		echo "<br/><br/>";

		debut_cadre_formulaire();
		echo "Envoyer ce courrier &agrave; cette liste de diffusion :<br />";
		$result = spip_query("SELECT email, titre FROM spip_abomailmans");
			echo "<select name='email_liste' class='formo'>";
			while ($row = spip_fetch_array($result)) {
				echo "<option value='".$row['email']."'>".$row['titre']." -> ".$row['email']."</option>\n";
			}
			echo "</select>";
		echo "<div id='cacher_confirmer'><br /><input name=\"Confirmer\" type=\"submit\" value=\""._T("abomailmans:envoi_confirmer")."\" id=\"Confimrer\"></div>";
		echo "</form>";
		fin_cadre_formulaire();

	}

	echo "</body></html>";
	unset ($_POST);

}	

?>
