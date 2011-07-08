<?php

include_spip('inc/presentation');
include_spip('exec/contact_select_message');

function exec_contact_messages() {

global $connect_id_auteur, $connect_statut, $spip_lang_rtl;

$commencer_page = charger_fonction('commencer_page', 'inc');
echo $commencer_page("Messages de contact", "forum", "contact_messages");

echo debut_gauche("contact_messages",true);

echo debut_boite_info(true);

echo _T('contact:msg_accueil');

echo fin_boite_info(true);


echo debut_droite("contact_messages", true);

$messages_vus = array();

$nouveaux_messages = afficher_ses_messages(_T('contact:msg_nouveaux'), ", spip_auteurs_liens AS lien", "lien.id_auteur=$connect_id_auteur AND vu='non' AND statut='publie' AND type='contac' AND lien.id_objet=messages.id_message AND lien.objet='message'", $messages_vus,  true, false);

if ($nouveaux_messages)
	echo $nouveaux_messages;
else {
	echo debut_boite_info(true);
	echo _T('contact:msg_pas_nouveaux');
	echo fin_boite_info(true);
}
	     

echo afficher_ses_messages('<b>' . _T('contact:msg_lus') . '</b>', ", spip_auteurs_liens AS lien", "lien.id_auteur=$connect_id_auteur AND vu!='non' AND statut='publie' AND type='contac' AND lien.id_objet=messages.id_message AND lien.objet='message'",  $messages_vus, true, false);


echo fin_gauche(), fin_page();

}
?>
