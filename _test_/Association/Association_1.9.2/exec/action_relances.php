<?php
	/**
	* Plugin Association
	*
	* Copyright (c) 2007-2008
	* Bernard Blazin & Fran�ois de Montlivault
	* http://www.plugandspip.com 
	* Ce programme est un logiciel libre distribue sous licence GNU/GPL.
	* Pour plus de details voir le fichier COPYING.txt.
	*  
	**/

	include_spip ('inc/presentation');
	include_spip ('inc/navigation_modules');
	include_spip ('inc/mail');
	//include_spip ('inc/charsets');

	function exec_action_relances(){
		global $connect_statut, $connect_toutes_rubriques;
		
		include_spip ('inc/acces_page');
		
		$url_action_relances = generer_url_ecrire('action_relances','action=send');
		$url_retour=$_POST['url_retour'];
		
		//On r�cup�re les donn�es globales
		$action=$_REQUEST['action'];
		$sujet=$_POST['sujet'];
		$message=$_POST['message'] ;
		$statut=$_POST['statut'];
		$email_tab=(isset($_POST["email"])) ? $_POST["email"]:array();
		$statut_tab=(isset($_POST["statut"])) ? $_POST["statut"]:array();
		$id_tab=(isset($_POST["id"])) ? $_POST["id"]:array();
		$count=count ($email_tab);
		
		// CONFIRMATION
		if ($action=="confirm") {

			debut_page(_T('Gestion pour  Association'), "", "");
			
			association_onglets();
			
			debut_gauche();
			
			debut_boite_info();
			echo association_date_du_jour();	
			fin_boite_info();
			
			debut_raccourcis();
			icone_horizontale(_T('asso:bouton_retour'), $url_retour, _DIR_PLUGIN_ASSOCIATION."/img_pack/retour-24.png","rien.gif");	
			fin_raccourcis();
			
			debut_droite();
			
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
				echo '<input name="id[]" type="hidden" value="'.$id_tab[$i].'">';
				echo '<input name="statut[]" type="hidden" value="'.$statut_tab[$i].'">';
				echo '<input name="email[]" type="hidden" value="'.$email_tab[$i].'">';
			}
			echo '<input name="sujet" type="hidden" value="'.$sujet.'">';
			echo '<input name="message" type="hidden" value="'.$message.'">';
			echo '<input name="action" type="hidden" value="send">';
			echo '<input name="url_retour" type="hidden" value="'.$url_retour.'">';
			echo '<div style="float:right;"><input name="submit" type="submit" value="'._T('asso:bouton_envoyer').'" class="fondo" /></div>';
			echo '</form>';	
			//remettre le champ 0 � 1 et r�actualiser la date
			//spip_query("UPDATE spip_auteurs_elargis SET regle_le='relance',date_jour=NOW() WHERE id_ad=$id");	
			
			fin_cadre_relief();  
			fin_page();
			exit;
		}
		
		//ENVOI
		if ($action=="send") {
			//On pr�pare le mail et on envoi! On peut modifier le $headers � sa guise
			$nomasso=lire_config('association/nom');
			$adresse=lire_config('association/email');
			$expediteur=$nomasso.'<'.$adresse.'>'; 
			$expediteur=$nomasso.'<'.$adresse.'>';      					//exp�diteur Association
			//$entetes .= "X-Mailer: PHP/" . phpversion();         			// mailer
			//$entetes .= "X-Sender: < ".$adresse.">\n";
			//$entetes .= "X-Priority: 1\n";                					// Message urgent ! 
			//$entetes .= "X-MSMail-Priority: High\n";        				// d�finition de la priorit�
			//$entetes .= "Return-Path: <".$adresse.">\n"; 									// En cas d' erreurs 
			//$entetes .= "Errors-To: < ".$adresse.">\n";    									// En cas d' erreurs 
			//$entetes .= "cc:  \n"; 													// envoi en copie �
			//$entetes .= "bcc: \n";          												// envoi en copie cach�e �
			
			//On envoit le mail aux destinataires s�lectionn�s et on change le statut de relance
			
			for ( $i=0 ; $i < $count ; $i++ ) {
				$email = $email_tab[$i];
				$statut = $statut_tab[$i];
				$id = $id_tab[$i];
				
				if ( isset ( $id ) ) {
					envoyer_mail ( $email, $sujet, $message, $expediteur, "");
					if ($statut=="echu"){
						spip_query("UPDATE spip_auteurs_elargis SET statut_interne='relance' WHERE id_auteur = '$id' ");
					}
				}
			}
			header ('location:'.$url_retour);
			exit;
		}
	} 
?>
