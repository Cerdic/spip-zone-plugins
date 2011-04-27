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

	$sujet = _request('sujet');
	$message=html_entity_decode(_request('message'), ENT_QUOTES, 'UTF-8');
	$statut_tab=(isset($_POST["statut"])) ? $_POST["statut"]:array(); /* contient un tableau id_auteur => statut_interne */

	$adresse=$GLOBALS['association_metas']['email'];
	$exp=$GLOBALS['association_metas']['nom'].'<'.$adresse.'>'; 
	$envoyer_mail = charger_fonction('envoyer_mail', 'inc');
	
	/* on recupere les adresses emails de tous les auteurs selectionnes, a reprendre quand on pourra interfacer avec Coordonnees */
	$id_auteurs_list = sql_in('id_auteur', array_keys($statut_tab));
	$auteurs_info = sql_select('id_auteur, email', 'spip_auteurs', $id_auteurs_list);
	
	/* boucle sur tous les auteurs selectionnés */
	while ($auteur_info = sql_fetch($auteurs_info)) {
		$id_auteur = $auteur_info['id_auteur'];
		$email = $auteur_info['email'];
		if (!$envoyer_mail($email, $sujet, $message, $exp)) {
			spip_log("non envoi du mail a ".$email);
		} elseif ($statut_tab[$id_auteur]=="echu") {
				sql_updateq(_ASSOCIATION_AUTEURS_ELARGIS, 
					array("statut_interne"=> 'relance'),
					    "id_auteur=$id_auteur");
			}
	}
}
?>
