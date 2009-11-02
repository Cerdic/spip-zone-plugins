<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// $messages_vus en reference pour interdire l'affichage de message en double


function afficher_ses_messages($titre, $from, $where, &$messages_vus, $afficher_auteurs = false, $important = false) {

	$requete = array('SELECT' => 'messages.id_message, messages.date_heure, messages.date_fin, messages.titre, messages.type, messages.rv', 'FROM' => "spip_messages AS messages$from", 'WHERE' => $where .(!$messages_vus ? '' : ' AND messages.id_message NOT IN ('.join(',', $messages_vus).')'), 'ORDER BY'=> 'date_heure DESC');
	
	// Même si on ne s'en sert pas (car presenter_liste le demande)
	$styles = array(array('arial2'), array('arial1', 20), array('arial1', 120));
	
	$presenter_liste = charger_fonction('presenter_liste', 'inc');
	$tmp_var = 't_' . substr(md5(join('', $requete)), 0, 4);
	
	// cette variable est passe par reference et recevra les valeurs du champ indique 
	$les_messages = 'id_message'; 
	$res = 	$presenter_liste($requete, 'presenter_message_boucles', $les_messages, $afficher_auteurs, $important, $styles, $tmp_var, $titre, find_in_path('contact-24.png', 'images/', false));
	$messages_vus =  array_merge($messages_vus, $les_messages);
	
	if (!$res) return  '';
	else
	  return 
	    (debut_cadre_couleur('',true)
			. $res
	     . fin_cadre_couleur(true));
}

function presenter_message_boucles($row, $afficher_auteurs)
{
	global $connect_id_auteur, $spip_lang_left, $spip_lang_rtl;
	
	$vals = array();

	$id_message = $row['id_message'];
	$date = $row["date_heure"];
	$titre = sinon($row['titre'], _T('ecrire:info_sans_titre'));

	//Titre
	$s = "<a href='" . generer_url_ecrire("contact_un_message","id_message=$id_message") . "' style='display: block;'>";
	$s .= typo($titre)."</a>";
	$vals[] = $s;
	
	// Auteurs
	if ($afficher_auteurs) {
		$auteur = sql_fetsel(
			"auteur.id_auteur, auteur.nom, auteur.email",
			"spip_auteurs AS auteur, spip_messages AS message",
			"message.id_message=$id_message AND auteur.id_auteur=message.id_auteur"
		);
		
		$id_auteur = $auteur['id_auteur'];
		$s = "<a href='" . generer_url_ecrire("auteur_infos","id_auteur=$id_auteur") . "'>".sinon(typo($auteur['nom']), $auteur['email'])."</a>";
		$vals[] = $s;
	}
	
	// Date
	$s = affdate($date);
	$s = "<span style='color: #999999'>$s</span>";
		
	$vals[] = $s;

	return $vals;
}

?>
