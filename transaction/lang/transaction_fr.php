<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/transaction/lang/
if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	'saisie_montant_titre' => 'Saisie montant',
	'saisie_montant_selection_titre' => 'S&eacute;lection montant',
	'saisie_radio_defaut_choix1' => '15 &euro;',
	'saisie_radio_defaut_choix2' => '50 &euro;',
	'saisie_radio_defaut_choix3' => '100 &euro;',
	'traiter_paiement_cic_titre' => 'Traitement du paiement CIC',
	'traiter_paiement_cic_description' => 'Envoi des donn&eacute;es de paiement vers les serveurs CIC',
	'traiter_paiement_cmcic_titre' => 'Traitement du paiement CMCIC',
	'traiter_paiement_cmcic_description' => 'Envoi des donn&eacute;es de paiement vers les serveurs CMCIC',
	'traiter_choix_banque_label' => 'Choix de la banque',
	'traiter_choix_banque_explication' => 'Quelle API bancaire doit &ecirc;tre appel&eacute;e ?',
	'banque_selection_1' => 'CIC',
	'banque_selection_2' => 'Cr&eacute;dit Mutuel',
	'banque_selection_3' => 'Banque OBC',
	'traiter_choix_test_label' => 'Mode API',
	'traiter_choix_test_explication' => 'Quel mode doit &ecirc;tre utilis&eacute; pour les communications avec les serveurs de la banque ?',
	'banque_test' => 'Mode de test',
	'banque_prod' => 'Mode de production',
	'traiter_paiement_cheque_titre' => 'Paiement par ch&egrave;que',
	'traiter_paiement_cheque_description' => 'Proposer le paiement par ch&egrave;que',
	'traiter_cheque_label' => 'Message de retour',
	'traiter_cheque_explication' => 'Indiquez ici le message de retour ainsi que l\'adresse &agrave; laquelle le ch&egrave;que devra &ecirc;tre envoy&eacute;',
	'traiter_cheque_message_defaut' => '<h1>Paiement par ch&egrave;que.</h1><p>Veuillez envoyer votre r&egrave;glement par ch&egrave;que &agrave; l\'ordre de Mon Organisme et &agrave; l\'adresse :</p> <p>adresse de votre organisme</p>',
	'traiter_message_cmcic' => '<h1>Paiement s&eacute;curis&eacute;</h1><p>Notre organisme met en oeuvre tous les moyens pour assurer la s&eacute;curit&eacute; et la confidentialit&eacute; des donn&eacute;es transmises en ligne.</p><p>Dans ce but, la transaction s\'effectue via l\'&eacute;tablissement bancaire '.$_SESSION['banque_nom'].' qui seul dispose des informations bancaires fournies au moment du paiement.</p><p><a href="'.find_in_path("paiement/cmcic/paiement.php").'"  class="valider"><span>Validez le paiement</span></a></p>',
	'traiter_message_cmcic_erreur' => 'Le paiement s&eacute;curis&eacute; est actuellement en mode test, aucune transaction ne sera effectu&eacute;e sur votre carte bancaire.'
	
);

?>
