<?php
/**
 * Définit les variables de langue du plugin Pensebetes (anglais / english)
 *
 * @plugin     Pensebetes
 * @copyright  2019-2020
 * @author     Vincent CALLIES
 * @licence    GNU/GPL
 * @package    SPIP\Pensebetes\Lang
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// C
	'cfg_boites'=>"Activity box on the side of :",
	'cfg_lieux'=>"Sticky notes in (places):",
	'cfg_objets'=>"Sticky notes on (objects):",
	'cfg_explication' => "Explanation :",
	'cfg_publique' => "Public space",
	'cfg_explication_publique' => "The sticky note can be visualized, by incorporating a MODELE.",
	'cfg_privee' => "Private space",
	'cfg_explication_privee' => "The sticky note is a means of communication between the authors, invisible from the public.",
	'cfg_partie_publique' => "Style in the public part (for using the MODELE):",
	'cfg_explication_partie_publique' => "Installing the plugin's private style sheet in the public part allows you to benefit from the appearance of the sticky note as you see it in the private part. This is only a configuration option as your theme for the public space could provide a different appearance.",
		
	// E
	'explication_titre'=> "Your title must be brief (17 characters).",
	'explication_texte'=> "Your text must have gone straight to the point (110 characters).",
	'erreur_suppression'=> "You do not have permission to delete this sticky note",
	'erreur_titre'=> "Something is wrong",
	'erreur_association'=>"The sticky note is created (n° @id_pensebete@) but the association with the editorial object could not be made.",

	// I
	'info_lassociation'=>"The association",
	'icone_creer_pensebete' => "Create a sticky note",
	'icone_modifier_pensebete' => "Edit this sticky note",
	'info_aucun_pensebete' => "No sticky note",
	'info_le_pensebete' => "Your sticky note",
	'info_1_pensebete' => "A sticky note",
	'info_nb_pensebetes' => "@nb@ sticky notes",
	'info_aucun_pensebete_donne'=>"No sticky note given",
	'info_aucun_pensebete_recu'=>"No sticky note received",
	'info_1_pensebete_donne' => "One sticky note given",
	'info_nb_pensebetes_donnes' => "@nb@ sticky notes given",
	'info_1_pensebete_recu' => "One sticky note received",
	'info_nb_pensebetes_recus' => "@nb@ sticky notes received",
 	
	// L
	'label_date' => "Date",
	'label_de' => "From",
	'label_a' => "to",
	'label_donneur' => "From",
	'label_receveur' => "To",
	'label_titre' => "Title",
	'label_texte' => "Message",
	'label_infos' => "Information",
	'lien_ajouter_pensebete' => "Add this sticky note",
	'lien_retirer_pensebete' => "Remove this sticky note",
	'lien_retirer_pensebetes' => "Remove all sticky notes",

	
	// T
	'texte_ajouter_pensebete' => "Add a sticky note",
	'texte_avertissement_retrait' => "Are you sure you want to remove this sticky note?",
	'texte_changer_statut' => "This sticky note is :",
	'texte_creer_associer_pensebete'=> "Create and associate a sticky note",
	'texte_associer_pensebete'=> "Do you want to associate this sticky note ti this object ?",
	'texte_nouveau_pensebete'=> "New sticky note",
	'texte_association'=> "Association",
	'titre_pensebete' => "Sticky note",
	'titre_pensebetes' => "Sticky notes",
	'titre_activite_mur' => "Wall activity",
	'titre_pensebetes_rubrique' => "Sticky notes of the rubric",
	'titre_langue_pensebete' => "Language of this sticky note",
	'titre_logo_pensebete' => "Sticky note logo",
	'titre_murs' => "Sticky notes...",
	'titre_mur_mien' => "on my wall",
	'titre_murs_autres' => "on the neighbors' walls",
	'titre_mur_de' => "on @auteur@'s wall",
	'titre_sur_mur' => "on my wall from @auteur@",
);

?>
