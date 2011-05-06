<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://www.spip.net/trad-lang/
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'alerte_publie_contenant' => 'Attention, ce contenu est publié, mais contient des liens vers des contenus qui ne le sont pas !',
	'alerte_publie_contenant_ko' => 'Attention, ce contenu est publié, mais contient des liens vers des contenus qui n\'existent pas !', # NEW
	'aucun_objets_avec_lien_depuis_courant' => 'Ce contenu ne contient aucun lien vers un autre contenu.',
	'aucun_objets_avec_lien_vers_courant' => 'Aucun autre contenu ne contient de lien vers celui-ci.',

	// C
	'confirmation_depublication' => 'ttention, un contenu publié pointe vers celui-ci, et sera affecté si tu le dépublies !nnVeux-tu vraiment changer le statut ?', # MODIF
	'confirmation_publication' => 'Attention, un contenu vers lequel pointe celui-ci n\'est pas publié !nnVeux-tu vraiment changer le statut ?', # MODIF
	'confirmation_suppression' => 'Attention, un contenu publié pointe vers celui-ci, et sera affecté si tu le supprimes !nnVeux-tu vraiment le supprimer ?', # MODIF

	// I
	'inexistant' => 'inexistant (@id_objet@)', # NEW
	'information_element_contenu' => 'Attention, un autre contenu pointe vers celui-ci !', # MODIF

	// L
	'legende_liens_faux_objets' => 'Les liens en rouge et barrés indiquent des contenus liés qui n\'existent pas.',
	'liens_entre_contenus' => 'Liens entre contenus', # NEW

	// O
	'objets_avec_liens_depuis_courant' => 'Ce contenu contient des liens vers ceux-ci :', # MODIF
	'objets_avec_liens_vers_courant' => 'Ces contenus contiennent des liens vers celui-ci :', # MODIF

	// S
	'statut_poubelle' => 'À la poubelle',
	'statut_prepa' => 'En préparation',
	'statut_prop' => 'Proposé',
	'statut_publie' => 'Publié',
	'statut_refuse' => 'Refusé',

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
