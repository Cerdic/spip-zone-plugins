<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & François de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


if (!defined("_ECRIRE_INC_VERSION")) return;

// envoi du mail aux destinataires sélectionnés et chgt du statut de relance

function action_modifier_relances() {
		
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$count = $securiser_action();

	$sujet=$_POST['sujet'];
	$message=$_POST['message'] ;
	$email_tab=(isset($_POST["email"])) ? $_POST["email"]:array();
	$statut_tab=(isset($_POST["statut"])) ? $_POST["statut"]:array();
	$id_tab=(isset($_POST["id"])) ? $_POST["id"]:array();

	$adresse=$GLOBALS['association_metas']['email'];
	$exp=$GLOBALS['association_metas']['nom'].'<'.$adresse.'>'; 
	$envoyer_mail = charger_fonction('envoyer_mail', 'inc');

	for ( $i=0 ; $i < $count ; $i++ ) {
		if ($id = intval($id_tab[$i]) AND $email = $email_tab[$i]) {
			if (!$envoyer_mail($email, $sujet, $message, $exp))
			   spip_log("non envoi du mail a $email");
			elseif ($statut_tab[$i]=="echu"){
				sql_updateq(_ASSOCIATION_AUTEURS_ELARGIS, 
					array("statut_interne"=> 'relance'),
					    "id_auteur=$id");
			}
		}
	}
}
?>
