<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/liens_contenus/trunk/lang/
if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'alerte_publie_contenant' => 'Attention, ce contenu est publié, mais contient des liens vers des contenus qui ne le sont pas !',
	'alerte_publie_contenant_ko' => 'Attention, ce contenu est publié, mais contient des liens vers des contenus qui n’existent pas !',
	'aucun_objets_avec_lien_depuis_courant' => 'Ce contenu ne contient aucun lien vers un autre contenu.',
	'aucun_objets_avec_lien_vers_courant' => 'Aucun autre contenu ne contient de lien vers celui-ci.',

	// C
	'confirmation_depublication' => 'Attention, des liens internes pointent vers ce contenu !\\n\\nSon changement de statut aurait des répercussions sur le fonctionnement du site.\\n\\nVoulez-vous vraiment changer le statut ?',
	'confirmation_publication' => 'Attention, ce contenu contient un lien pointant vers un contenu qui n’est pas publié !\\n\\nSon changement de statut aurait des répercussions sur le fonctionnement du site.\\n\\nVoulez-vous vraiment changer le statut ?',
	'confirmation_suppression' => 'Attention, des liens internes pointent vers ce contenu !\\n\\nSa suppression aurait des répercussions sur le fonctionnement du site.\\n\\nVoulez-vous vraiment le supprimer ?',

	// I
	'inexistant' => 'inexistant (@id_objet@)',
	'information_element_contenu' => 'Attention, des liens internes pointent vers ce contenu !',

	// L
	'legende_liens_faux_objets' => 'Les liens en rouge et barrés indiquent des contenus liés qui n’existent pas.',
	'liens_entre_contenus' => 'Liens entre contenus',

	// O
	'objets_avec_liens_depuis_courant' => 'Ce contenu contient des liens vers ces autres :',
	'objets_avec_liens_vers_courant' => 'Ces autres contenus contiennent des liens vers celui-ci :',

	// S
	'statut_poubelle' => 'A la poubelle',
	'statut_prepa' => 'En préparation',
	'statut_prop' => 'Proposé',
	'statut_publie' => 'Publié',
	'statut_refuse' => 'Refusé',

	// T
	'type_article' => '@titre@ (@id_objet@)',
	'type_article_inexistant' => 'Article inexistant (@id_objet@)',
	'type_auteur' => '@titre@',
	'type_auteur_inexistant' => 'Auteur inexistant (@id_objet@)',
	'type_breve' => '@titre@ (@id_objet@)',
	'type_breve_inexistant' => 'Brève inexistante (@id_objet@)',
	'type_document' => '@titre@ (@id_objet@)',
	'type_document_inexistant' => 'Document inexistant (@id_objet@)',
	'type_forum' => '@titre@ (@id_objet@)',
	'type_forum_inexistant' => 'Message inexistant (@id_objet@)',
	'type_modele' => 'Modèle "@id_objet@"',
	'type_modele_inexistant' => 'Modèle inexistant (@id_objet@)',
	'type_rubrique' => '@titre@ (@id_objet@)',
	'type_rubrique_inexistant' => 'Rubrique inexistante (@id_objet@)',
	'type_syndic' => '@titre@ (@id_objet@)',
	'type_syndic_inexistant' => 'Site inexistant (@id_objet@)'
);

?>
