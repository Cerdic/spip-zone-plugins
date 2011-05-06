<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://www.spip.net/trad-lang/
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'alerte_publie_contenant' => 'Achtung, dieses Objekt ist veröffentlicht, enthält jedoch Links zu nicht veröffentlichten Inhalten!',
	'alerte_publie_contenant_ko' => 'Attention, ce contenu est publié, mais contient des liens vers des contenus qui n\'existent pas !', # NEW
	'aucun_objets_avec_lien_depuis_courant' => 'Dieses Objekt enthält keinen Links zu anderen Inhalten.',
	'aucun_objets_avec_lien_vers_courant' => 'Kein anderes Objekt enthält Links zu diesem.',

	// C
	'confirmation_depublication' => 'Achtung, ein veröffentlichtes Objekt enthält Links zu diesem!nnWollen Sie den Status wirklich ändern?', # MODIF
	'confirmation_publication' => 'Achtung, ein Links in diesem Objekt verweist auf nicht veröffentlichte Inhalte!nnWollen Sie den Status wirklich ändern?', # MODIF
	'confirmation_suppression' => 'Achtung, ein veröffentlichter Link verweist auf dieses Objekt und wird von seiner Löschung betroffen sein.!nnWollen Sie es wirklich löschen?', # MODIF

	// I
	'inexistant' => 'inexistant (@id_objet@)', # NEW
	'information_element_contenu' => 'Achtung, ein anderes Objekt verweist auf dieses!', # MODIF

	// L
	'legende_liens_faux_objets' => 'Die roten durchgestrichenen Links zeigen auf nicht existente Objekte.',
	'liens_entre_contenus' => 'Liens entre contenus', # NEW

	// O
	'objets_avec_liens_depuis_courant' => 'Dieses Objekt enthält Links zu folgenden:', # MODIF
	'objets_avec_liens_vers_courant' => 'Diese Objekte enthalten Links zu folgenden:', # MODIF

	// S
	'statut_poubelle' => 'Im Papierkorb',
	'statut_prepa' => 'In Vorbereitung',
	'statut_prop' => 'Vorgeschlagen',
	'statut_publie' => 'Veröffentlicht',
	'statut_refuse' => 'Abgelehnt',

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
