<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://www.spip.net/trad-lang/
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'alerte_publie_contenant' => 'Pozor, tento text je publikovaný, ale sú v ňom odkazy na iný text, ktorý nie je!',
	'alerte_publie_contenant_ko' => 'Attention, ce contenu est publié, mais contient des liens vers des contenus qui n\'existent pas !', # NEW
	'aucun_objets_avec_lien_depuis_courant' => 'V tomto texte nie sú odkazy na iný text.',
	'aucun_objets_avec_lien_vers_courant' => 'Žiaden iný text neodkazuje na tento.',

	// C
	'confirmation_depublication' => 'Pozor, v publikovanom texte je aspoň jeden odkaz na tento text a ovplyvní ho to, ak nezmeníte stav aktuálneho textu!nnStále chcete zmeniť jeho stav?',
	'confirmation_publication' => 'Pozor, tento text odkazuje aspoň na jeden ďalší text, ktorý nie je publikovaný!nnStále chcete zmeniť jeho stav?',
	'confirmation_suppression' => 'Pozor, v publikovanom texte je aspoň jeden odkaz na tento text, bude tým ovplyvnený, ak aktuálny text odstránite!nnStále ho chcete odstrániť?',

	// I
	'inexistant' => 'inexistant (@id_objet@)', # NEW
	'information_element_contenu' => 'Pozor, ďalšie textové odkazy na tento text!',

	// L
	'legende_liens_faux_objets' => 'Červené prečiarknuté odkazy sú prepojenia na texty, ktoré neexistujú.',
	'liens_entre_contenus' => 'Liens entre contenus', # NEW

	// O
	'objets_avec_liens_depuis_courant' => 'V tomto texte sú odkazy na tieto texty:',
	'objets_avec_liens_vers_courant' => 'V týchto textoch sú odkazy na tento:',

	// S
	'statut_poubelle' => 'V koši',
	'statut_prepa' => 'Upravuje sa',
	'statut_prop' => 'Odoslaný na schválenie',
	'statut_publie' => 'Publikovaný online',
	'statut_refuse' => 'Zamietnutý',

	// T
	'type_article' => '@titre@ (@id_objet@)', # NEW
	'type_article_inexistant' => 'Article inexistant (@id_objet@)', # NEW
	'type_auteur' => '@titre@', # NEW
	'type_auteur_inexistant' => 'Auteur inexistant (@id_objet@)', # NEW
	'type_breve' => '@titre@ (@id_objet@)', # NEW
	'type_breve_inexistant' => 'Brève inexistante (@id_objet@)', # NEW
	'type_document' => '@titre@ (@id_objet@)', # NEW
	'type_document_inexistant' => 'Document inexistant (@id_objet@)', # NEW
	'type_forum' => '@titre@ (@id_objet@)', # NEW
	'type_forum_inexistant' => 'Message inexistant (@id_objet@)', # NEW
	'type_modele' => 'Modèle "@id_objet@"', # NEW
	'type_modele_inexistant' => 'Modèle inexistant (@id_objet@)', # NEW
	'type_rubrique' => '@titre@ (@id_objet@)', # NEW
	'type_rubrique_inexistant' => 'Rubrique inexistante (@id_objet@)', # NEW
	'type_syndic' => '@titre@ (@id_objet@)', # NEW
	'type_syndic_inexistant' => 'Site inexistant (@id_objet@)' # NEW
);

?>
