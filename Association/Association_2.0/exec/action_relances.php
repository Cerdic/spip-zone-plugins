<?php
	/**
	* Plugin Association
	*
	* Copyright (c) 2007-2008
	* Bernard Blazin & François de Montlivault
	* http://www.plugandspip.com 
	* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	* Pour plus de details voir le fichier COPYING.txt.
	*  
	**/
if (!defined("_ECRIRE_INC_VERSION")) return;


	include_spip ('inc/presentation');
	include_spip ('inc/navigation_modules');
	include_spip ('inc/mail');
	//include_spip ('inc/charsets');

	function exec_action_relances(){
		
		include_spip('inc/autoriser');
		if (!autoriser('configurer')) {
			include_spip('inc/minipres');
			echo minipres();
			exit;
		}
		
		$url_action_relances = generer_url_ecrire('action_relances','agir=send');
		$url_retour=$_POST['url_retour'];
		
		//On récupère les données globales
		$action=$_REQUEST['agir'];
		$sujet=$_POST['sujet'];
		$message=$_POST['message'] ;
		$statut=$_POST['statut'];
		$email_tab=(isset($_POST["email"])) ? $_POST["email"]:array();
		$statut_tab=(isset($_POST["statut"])) ? $_POST["statut"]:array();
		$id_tab=(isset($_POST["id"])) ? $_POST["id"]:array();
		$count=count ($email_tab);
		
		// CONFIRMATION
		if ($action=="confirm") {

			 $commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('asso:titre_gestion_pour_association')) ;
			
			association_onglets();
			
			echo debut_gauche("",true);
			
			echo debut_boite_info(true);
			echo association_date_du_jour();	
			echo fin_boite_info(true);
			
			
			$res=association_icone(_T('asso:bouton_retour'),  $url_retour, "retour-24.png");	
			echo bloc_des_raccourcis($res);
			
			echo debut_droite("",true);
			
			debut_cadre_relief(  "", false, "", $titre = _T('Relance de cotisations'));
			echo '<p><strong> Vous vous appr&ecirc;tez &agrave; envoyer '.$count;
			if ($count==1)
			{ echo ' relance';}
			else
			{ echo ' relances';}
			echo '</strong></p>';
			echo '<p>'.$sujet.'</p>';
			echo '<fieldset>';
			echo nl2br($message);
			echo '</fieldset>';
			
			echo '<form method="post" action="'.$url_action_relances.'">';
			for ( $i=0 ; $i < $count ; $i++ ) {
			  echo '<input name="id[]" type="hidden" value="'.intval($id_tab[$i]).'">';
				echo '<input name="statut[]" type="hidden" value="'.$statut_tab[$i].'">';
				echo '<input name="email[]" type="hidden" value="'.$email_tab[$i].'">';
			}
			echo '<input name="sujet" type="hidden" value="'.$sujet.'">';
			echo '<input name="message" type="hidden" value="'.$message.'">';
			echo '<input name="agir" type="hidden" value="send">';
			echo '<input name="url_retour" type="hidden" value="'.$url_retour.'">';
			echo '<div style="float:right;"><input name="submit" type="submit" value="'._T('asso:bouton_envoyer').'" class="fondo" /></div>';
			echo '</form>';	
			//remettre le champ 0 à  1 et réactualiser la date
			//spip_query("UPDATE spip_auteurs_elargis SET regle_le='relance',date_jour=NOW() WHERE id_ad=$id");	
			
			fin_cadre_relief();  
			  echo fin_gauche(),fin_page(); 
			exit;
		}
		
		//ENVOI
		if ($action=="send") {
			//On prépare le mail et on envoi! On peut modifier le $headers à  sa guise
			$nomasso=lire_config('association/nom');
			$adresse=lire_config('association/email');
			$expediteur=$nomasso.'<'.$adresse.'>'; 
			$expediteur=$nomasso.'<'.$adresse.'>';      					//expéditeur Association
			//$entetes .= "X-Mailer: PHP/" . phpversion();         			// mailer
			//$entetes .= "X-Sender: < ".$adresse.">\n";
			//$entetes .= "X-Priority: 1\n";                					// Message urgent ! 
			//$entetes .= "X-MSMail-Priority: High\n";        				// définition de la priorité
			//$entetes .= "Return-Path: <".$adresse.">\n"; 									// En cas d' erreurs 
			//$entetes .= "Errors-To: < ".$adresse.">\n";    									// En cas d' erreurs 
			//$entetes .= "cc:  \n"; 													// envoi en copie à
			//$entetes .= "bcc: \n";          												// envoi en copie cachée à
			
			//On envoit le mail aux destinataires sélectionnés et on change le statut de relance
			
			for ( $i=0 ; $i < $count ; $i++ ) {
				$email = $email_tab[$i];
				$statut = $statut_tab[$i];
				$id = intval($id_tab[$i]);
				
				if ($id) {
					envoyer_mail ( $email, $sujet, $message, $expediteur, "");
					if ($statut=="echu"){
					  sql_updateq(_ASSOCIATION_AUTEURS_ELARGIS, 
						array("statut_interne"=> 'relance'),
						"id_auteur=$id");
					}
				}
			}
			header ('location:'.$url_retour);
			exit;
		}
	} 
?>
