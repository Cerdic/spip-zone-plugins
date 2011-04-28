<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://www.spip.net/trad-lang/
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'alerte_publie_contenant' => 'Be careful, this content is published, but contains links to other contents that are not!',
	'alerte_publie_contenant_ko' => 'Attention, ce contenu est publié, mais contient des liens vers des contenus qui n\'existent pas !', # NEW
	'aucun_objets_avec_lien_depuis_courant' => 'This content doesn\'t contain any link to another content.',
	'aucun_objets_avec_lien_vers_courant' => 'No other content contain link to this one.',

	// C
	'confirmation_depublication' => 'Be careful, a published content contains at least one link to this one and will be affected if you change its status!nnDo you still want to change the status?', # MODIF
	'confirmation_publication' => 'Be careful, this content links to at least one other content that is not published!nnDo you still want to change the status?', # MODIF
	'confirmation_suppression' => 'Be careful, a published content contains at least one link to this one and will be affected if you delete it!nnDo you still want to delete it?', # MODIF

	// I
	'inexistant' => 'inexistant', # NEW
	'information_element_contenu' => 'Be careful, another content links to this one!', # MODIF

	// L
	'legende_liens_faux_objets' => 'Red and striked links are linked content that don\'t exist.',
	'liens_entre_contenus' => 'Liens entre contenus', # NEW

	// O
	'objets_avec_liens_depuis_courant' => 'This content contains links to these ones:', # MODIF
	'objets_avec_liens_vers_courant' => 'These contents contain links to this one:', # MODIF

	// S
	'statut_poubelle' => 'In the dustbin',
	'statut_prepa' => 'Editing in progress',
	'statut_prop' => 'Submitted for evaluation',
	'statut_publie' => 'Published online',
	'statut_refuse' => 'Rejected',

	// T
	'type_auteur' => '@nom@', # NEW
	'type_modele' => 'Modèle "@id_objet@"', # NEW
	'type_syndic' => 'Site "@nom_site@" (@id_objet@)' # NEW
);

?>
