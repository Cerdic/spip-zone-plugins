<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/mots');


function exec_contact_un_message_dist()
{
	exec_contact_un_message_args(intval(_request('id_message')));
}

// Marque le message comme vu et demande à afficher le message dont l'id_message est passé en paramètre
function exec_contact_un_message_args($id_message)
{
	global  $connect_id_auteur;

	marquer_message_vu($id_message, $connect_id_auteur);
	exec_affiche_un_message_dist($id_message);

}

// Affiche le message
function http_affiche_un_message($id_message, $expediteur, $texte, $titre, $date_heure)
{
	$la_couleur = "#3874b0";
	$fond = "#edf3fe";

	// affichage du conteneur
	echo "<div style='border: 1px solid $la_couleur; background-color: $fond; padding: 5px;'>"; // debut cadre de couleur
	echo debut_cadre_relief("contact-24.png", true);
	echo "\n<table width='100%' cellpadding='0' cellspacing='0' border='0'>";
	echo "<tr><td>"; # uniques

	echo "<span style='color: $la_couleur' class='verdana1 spip_small'><b>", _T('contact:msg_contact'), "</b></span><br />";
	echo "<span class='verdana1 spip_large'><b>$titre</b></span>";

	echo "<br /><span style='color: #666666;' class='verdana1 spip_small'><b>".nom_jour($date_heure).' '.affdate_heure($date_heure)."</b></span>";


	//////////////////////////////////////////////////////
	// Le message lui-meme
	//
	echo '<br/>';
	echo _T('contact:msg_expediteur');
	echo '<a href="' . generer_url_ecrire("auteur_infos","id_auteur=".$expediteur['id_auteur']) . '">' . sinon($expediteur['nom'], $expediteur['email']) . '</a>'; 
	echo "\n<br />" . "<div class='serif'> $texte </div>";
	echo "</td></tr></table>\n";
	echo "</div>"; // fin du cadre de couleur

	echo "\n<table width='100%'><tr><td>";


	//////////////////////////////////////////////////////
	// Les pièces-jointes, s'il y en a
	//
	$documents = afficher_pieces_jointes('message', intval($id_message));
	echo $documents;
	
	// Lien pour revenir à la boite de réception
	echo "<br/><a href='" . generer_url_ecrire("contact_messages") . "'><span style='color: $la_couleur' class='verdana1 spip_small'>", _T('contact:msg_revenir_accueil'), "</span></a>";
	
	// Lien pour supprimer le message concerné
	echo "<br/><a href='" . redirige_action_auteur("supprimer_message", "$id_message", 'contact_messages') . "'><span style='color: $la_couleur' class='verdana1 spip_small'>";
	echo _T('contact:msg_supprimer_message');
	echo "</span><br/></a></td></tr></table>";
}

// Fonction d'affichage des pièces jointes
function afficher_pieces_jointes($type, $id) {
	$documenter = charger_fonction('documenter', 'inc');
	
	$flag_editable = autoriser('modifier', $type, $id);

	return "<div id='portfolio'>" . $documenter($id, $type, 'portfolio') . "</div><br />"
	. "<div id='documents'>" . $documenter($id, $type, 'documents') . "</div>";
}

// Affichage de la page + message
function exec_affiche_un_message_dist($id_message)
{
  $row = sql_fetsel("*", "spip_messages", "id_message=$id_message");
  if ($row) {
	$id_message = $row['id_message'];
	$date_heure = $row["date_heure"];
	$titre = typo($row["titre"]);
	$texte = propre($row["texte"]);
	$id_auteur = $row['id_auteur'];

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo $commencer_page($titre, "accueil", "messagerie");

	echo debut_gauche('', true);
		echo debut_boite_info(true);

		echo "<a href='" . generer_url_ecrire("contact_messages") . "'>", _T('contact:msg_revenir_accueil'), "</a>";

		echo fin_boite_info(true);

	echo debut_droite('', true);
	
	//On cherche quel est l'adresse mail correspondant à l'id
	$expediteur = sql_fetsel("id_auteur, nom, email", "spip_auteurs", "id_auteur=$id_auteur");
	// On affiche le message
	http_affiche_un_message($id_message, $expediteur, $texte, $titre, $date_heure);

  }

  echo fin_gauche(), fin_page();
}

// Permet de marquer un message comme vu.
function marquer_message_vu($id_message, $id_auteur) {
	sql_updateq("spip_auteurs_messages", array("vu" => 'oui'), "id_message=$id_message AND id_auteur=$id_auteur");
}

?>
