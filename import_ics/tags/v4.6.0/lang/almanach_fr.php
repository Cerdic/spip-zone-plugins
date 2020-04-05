<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans https://git.spip.net/spip-contrib-extensions/import_ics.git
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'ajouter_lien_almanach' => 'Ajouter cet almanach',
	'alamnachs_corbeille_tous' => '@nb@ almanachs à la corbeille',
	'almanach' => 'Almanach',
	'almanachs_corbeille_un' => 'Un almanach à la corbeille',
	'attendee' => 'Intervenant·e',
	'aucun_decalage' => 'Aucun décalage',
	'aucun_evenement' => 'Cet almanach ne contient aucun événement.',

	// C
	'choix_salle' => 'Tous les événements se verront attribuer cette salle dans le gestionnaire de ressources.',
	'confirmation_appliquer_decalage' => 'Êtes-vous bien certain de vouloir appliquer ce décalage ?',
	'confirmation_forcer_install_import_ics' => 'Êtes vous certain·e de vouloir forcer la réinstallation ?',
	'confirmation_mise_a_jour_evenements' => 'Voulez-vous réellement mettre à jour la liste des événements de l’almanach \\"@titre_almanach@\\" ?\\nCela peut prendre un certain temps.',
	'confirmation_suppression_evenements' => 'Êtes vous certain·e de vouloir supprimer les événements de l’almanach \\"@titre_almanach@\\" ?',

	// D
	'decalage_ete' => 'Décalage pour l’heure d’été',
	'decalage_ete_explication' => 'Vous pouvez tenter un décalage global des horaires s’ils n’apparaissent pas à la bonne heure, pour des raisons de fuseaux horaires. La valeur choisie correspond au nombre d’heures à ajouter à l’horaire fournie par le site distant. Ce champ concerne les évènements ayant lieu lorsque l’heure d’été est en vigueur.',
	'decalage_hiver' => 'Décalage pour l’heure d’hiver',
	'decalage_hiver_explication' => 'Vous pouvez tenter un décalage global des horaires s’ils n’apparaissent pas à la bonne heure, pour des raisons de fuseaux horaires. La valeur choisie correspond au nombre d’heures à ajouter à l’horaire fournie par le site distant. Ce champ concerne les évènements ayant lieu lorsque l’heure d’hiver est en vigueur.',
	'dtend_inclus' => 'Le flux ICAL considère que la date de fin est incluse dans l’évènement',
	'dtend_inclus_explication' => 'La norme ICAL implique normalement que la date de fin (DTEND) ne soit pas incluse dans l’évènement. Certains flux ne respectent pas cette norme. Cocher cette case si la date de fin des évènements importés est décalée d’un jour.',

	// E
	'erreur_synchro' => 'Erreur à la dernière synchronisation',
	'explication_id_article' => 'Choisissez un article qui va recevoir les événements importés.',
	'explication_resa_auto' => 'On peut réserver automatiquement une salle pour tous les événements d’un même almanach (modifiable individuellement ensuite).',
	'explication_titre' => 'Titre de l’almanach',
	'explication_url' => 'URL d’origine du calendrier',

	// F
	'forcer_install_import_ics' => 'Forcer la réinstallation',
	'forcer_install_import_ics_explication' => 'Il n’y a aucun évènement, voulez vous forcer la réinstallation des tables ?',
	'forcer_install_import_ics_titre' => 'Il semble que des tables manquent',

	// H
	'heure' => '@nb@ heure',
	'heures' => '@nb@ heures',

	// I
	'icone_creer_almanach' => 'Créer un almanach',
	'icone_modifier_almanach' => 'Modifier cet almanach',
	'info_1_almanach' => 'Un almanach',
	'info_almanachs_auteur' => 'Les almanachs de cet auteur',
	'info_aucun_almanach' => 'Aucun almanach',
	'info_derniere_synchronisation' => 'La dernière synchronisation de cet almanach a été effectuée le',
	'info_erreur_synchronisation' => 'Erreur lors de la synchronisation de cet almanach (@date@)',
	'info_evenement_almanach' => 'Les événements de cet almanach',
	'info_jamais_synchro' => 'Cet almanach n’a jamais été synchronisé',
	'info_nb_almanachs' => '@nb@ almanachs',
	'info_supprimer_almanach' => 'Supprimer',
	'info_supprimer_evenements' => 'Supprimer ces événements',

	// L
	'label_id_article' => 'Article d’accueil de l’almanach',
	'label_titre' => 'Titre',
	'label_url' => 'URL',
	'last_modified_distant' => 'Dernière modification distante',
	'lien_synchro_almanach' => 'Mettre à jour cet almanach maintenant',
	'lieu' => 'Lieu',

	// N
	'notes' => 'Notes',

	// O
	'origin' => 'Origine',

	// P
	'pas_de_decalage' => 'Pas de décalage.',
	'plusieurs_evenements' => '@nb@ événements',
	'purger_almanach' => 'Si vous voulez supprimer cet almanach, vous devez tout d’abord en supprimer le contenu.',

	// R
	'regenerer_almanach' => 'Vous pouvez aussi restaurer son contenu en tentant une nouvelle synchronisation.',
	'resa_auto' => 'Réservation automatique',
	'reservation' => 'Choix de la salle à réserver',
	'retirer_lien_almanach' => 'Retirer cet almanach',
	'retirer_tous_liens_almanachs' => 'Retirer tous les almanachs',

	// S
	'sequence' => 'Version distante',

	// T
	'texte_ajouter_almanach' => 'Ajouter un almanach',
	'texte_changer_statut_almanach' => 'Cet almanach est :',
	'texte_creer_associer_almanach' => 'Créer et associer un almanach',
	'titre_almanach' => 'Almanach',
	'titre_almanachs' => 'Almanachs',
	'titre_almanachs_rubrique' => 'Almanachs de la rubrique',
	'titre_langue_almanach' => 'Langue de cet almanach',
	'titre_logo_almanach' => 'Logo de cet almanach',
	'type_evenement' => 'Tous les événements de l’almanach auront le même type. Vous pourrez modifier cela individuellement.',

	// U
	'uid' => 'Identifiant distant',
	'un_evenement' => '@nb@ événement'
);
