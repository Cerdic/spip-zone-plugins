<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/amap?lang_cible=en
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'action' => 'Action ?',
	'action_modifier' => 'Modify',
	'action_supprimer' => 'Delete',
	'adherent' => 'Member',
	'adherent_sans_type_panier_sans_type_adherent' => 'Your subscribers do not have any type of basket or any subscriber type.',
	'adherents_jour' => 'Today’s subcribers',
	'adhesion' => 'Membership (ie:2008)',
	'adhesion_auteur' => 'Membership :',
	'amapiens_explication' => 'On this page, you will find the subscribers list. By clicking on their name, you wil go on their author page. Keeping updated information is mandatory for each new season.',
	'attention' => 'WARNING !',
	'attention_modifications' => '<p>You have just activated the CSA (Community Supported Agriculture) plugin. It created a new section "Season agenda" which includes two sub-sections "Delivery" and "Events". It also created the section "Archives".</p>
<ol>
<li>Before continuing, please fill up all the dates of the season via the plugin agenda.</li>
<li>This has to be done before each new season begins.</li>
<li>At the end of each season, you have to update the CSA members list and archive into the appropriate sections, the dates of last season, this is needed for a proper functioning of this plugin.</li>
<li>Update the <b>sucriber type</b>, <b>subscriptions</b> and the <b>basket type</b> on each author page, or no basket  delivery will be attributed.</li>
</ol>
<p><b>NON RESPECT OF THOSE RULES WILL RESULT IN THE PLUGIN DYSFUNCTION</b></p>',
	'aucun_panier_pour_vous' => 'We do not have a basket for you.',
	'aucun_panier_produit_par_vous' => 'You have not produced a basket.',
	'autorise_envoie_email_explication' => 'Sending e-mails is used for making baskets available and recovering them. "No" is advised for big CSA, instead you can use the management interface.',
	'autorise_envoie_email_label' => 'Do you want to send mails ?',
	'avant_le' => 'before',

	// C
	'configurer_amap' => 'CSA plugin configuration',
	'confirmation_envoi' => 'The availability of your basket for @date_distribution@ is confirmed, thank you.',
	'contenu_panier' => 'Basket content',
	'contenu_panier_explication' => 'You can edit content the same way as in SPIP.',
	'creer_paniers_pour_nom' => 'Create baskets for @nom@.',

	// D
	'date' => 'Date',
	'date_distribution' => 'Giving date',
	'date_livraison' => 'Delivery date',
	'depuis_le' => 'since',
	'disponible' => 'Available',
	'distribution_paniers' => 'Basket delivery of @nb@',
	'distribution_paniers_mois' => 'Distribution of the baskets from @date_debut@ to @date_fin@',

	// E
	'enregistrement' => 'Registration',
	'enregistrement_livraison' => 'Delivery recording',
	'enregistrement_livraison_explication' => 'Using this form you can record the content of the deliveries. Only past dates are visible',
	'enregistrement_paniers' => 'Baskets recording',
	'enregistrement_paniers_explication' => 'This form allows you to record all of a subscriber’s baskets in one go.',
	'enregistrement_responsable' => 'Recording a person in charge',
	'enregistrement_responsable_explication' => 'This form allows you to manage those in charge of each distribution',
	'envoyer' => 'Send',
	'envoyez_email_non' => 'No, do not send e-mails to subscribers.',
	'envoyez_email_oui' => 'Yes, do send e-mails to subscribers.',

	// G
	'gestion_amap' => 'CSA administration',
	'grand' => 'Big',

	// I
	'impression' => 'Print',
	'impression_donnees' => 'Print data',
	'impression_explication' => 'Only the dates with at least 1 basket are clickable and open a new window.',
	'impression_paniers_fonction_date' => 'Printing baskets per date :',
	'impression_paniers_fonction_mois' => 'Printing baskets per month :',
	'impression_responsables_fonction_mois' => 'Printing responsible people according to a month:',
	'information_amap' => 'CSA information',

	// L
	'les_livraisons' => 'Deliveries',
	'les_livraisons_effectuees' => 'Past deliveries',
	'les_paniers_dispo' => 'Available baskets of @nom@',
	'les_responsabilites' => 'The @nb@ responsibilities of',
	'liste_amapiens' => 'CSA members list',
	'liste_amapiens_enregistres' => 'registered CSA members list',
	'liste_livraisons' => 'Deliveries list',
	'liste_paniers' => 'Baskets list',
	'liste_paniers_distribuer_le' => 'Basket list to deliver on',
	'liste_paniers_vendu' => 'List of the baskets sold',
	'liste_responsables' => 'People in charge list',
	'livraison' => 'Delivery',
	'livraison_enregistre_explication' => 'You must have already distributed baskets to register via this content delivery form.',
	'livraison_explication' => 'On this page you will find the list of deliveries which you have already made. This table can be added to the site via an article by putting the code &lt;liste|livraison&gt;.',

	// M
	'manque_fpdf_imprimer' => 'The plugin "fpdf" is missing to be able to print your baskets lists.',
	'mettre_disposition' => 'Place at disposal',
	'mettre_disposition_explication' => 'On this page, you can see your baskets and can make them available for someone else if you are not available on a specific delivery date. Click on "Place at disposal" follow the process.',
	'mettre_disposition_interface' => 'A table is available to recover them, then the content can be seen on  <a href="@url@"><b>the next page</b></a>.',
	'mettre_disposition_mail' => 'An e-mail will be sent to all subscribers',
	'mini_doc' => 'Short documentation',

	// N
	'nom' => 'NAME',
	'non' => 'No',

	// O
	'oui' => 'Yes',

	// P
	'panier' => 'Basket',
	'panier_adherent' => 'The baskets of @nom@',
	'panier_deja_vendu' => 'You have sold already 1 basket',
	'panier_dispo' => 'Basket available the @date_distribution@',
	'panier_dispo_auteur' => 'Hello,
		<br />The basket of @date_distribution@ is available
		<br />@nom_adherent@',
	'panier_dispo_auteur_mail' => 'Hello,
The basket of @date_distribution@ is available, please follow this link @lien@ to get it
@panier_dispo_plus@
@nom_adherent@',
	'panier_dispo_interface' => 'Available basket',
	'panier_dispo_plus' => 'Extra information to give (It will be added in the mail sent before your name)',
	'panier_disposition' => 'There is 1 available basket',
	'panier_distribuer' => 'basket to deliver',
	'panier_explication' => 'On this page, you will find the list of the baskets that you have to deliver, You can modify or delete them. Once the date has passed, you will automatically no longer be able to see them.',
	'panier_explication_email' => 'Any change results in sending mass mail to all members and producers of the association.',
	'panier_explication_interface' => 'No email has been sent, it has been added to the availability interface.',
	'panier_liste' => 'Baskets list',
	'panier_livraison' => 'Basket content',
	'panier_recupere' => 'Transaction of the @date_distribution@ basket done',
	'panier_recupere_auteur' => 'I recover the @date_distribution@ basket, made available by @nom_adherent@',
	'panier_recupere_auteur_mail' => 'Hello,
I will get the basket of the @date_distribution@ produced by @nom_producteur@
@nom_adherent@',
	'panier_vous_bien_attribuer' => 'The basket has been assigned to you',
	'paniers_deja_vendu' => 'You have sold @nb@ basket(s)',
	'paniers_disponibles' => 'Available baskets',
	'paniers_disposition' => 'There are @nb@ baskets available',
	'paniers_distribuer' => 'baskets to deliver',
	'pas_connecte_ou_reconnu' => 'You are not logged in or your details have not been recognised',
	'pas_date_distributions' => 'No distribution date are filled in.',
	'pas_paniers' => 'You do not have any baskets for this season. You have no contracts currently in process.',
	'pas_paniers_disponible' => 'We have no baskets available to offer you at the moment.',
	'pas_producteur_amap' => 'ou do not have any producer in your CSA.',
	'pas_responsable_distribution' => 'You are not in charge for this season’s deliveries.',
	'pas_responsable_pour_vous' => 'You are not responsable for the distribution.',
	'pas_statuts_nom' => '@nom@ does not have any status at the moment. Please edit its profile to correct the status.',
	'petit' => 'Small',
	'pour_le' => 'for the',
	'producteur' => 'Producer',
	'producteurs' => 'Producers',

	// Q
	'qui_recupere_panier_disponible' => 'Who wants to get the available basket ?',

	// R
	'recupere_panier' => 'Recover the basket',
	'responsables' => 'People in charge',
	'responsables_distribution_paniers_mois' => 'In charge for deliveries from @date_debut@ to @date_fin@',
	'responsables_explication' => 'On this page, you will find the list of those in charge with the distribution dates to which they have been allocated.',
	'reste_panier_distribuer' => 'We still have 1 basket to deliver',
	'reste_panier_recuperer' => 'You still have 1 basket to get',
	'reste_paniers_distribuer' => 'We still have @nb@ baskets to deliver',
	'reste_paniers_recuperer' => 'You still have @nb@ baskets to get',
	'retour_auteur' => 'Back to the author page @nom@ ',

	// S
	'signature' => 'Signature',

	// T
	'table_vide_aucun_enregistrement' => 'This table is empty: It does not contain any registration.',
	'type_adherent' => 'Member type',
	'type_adherent_auteur' => 'Member type :',
	'type_panier' => 'Basket type',
	'type_panier_auteur' => 'Basket type :',

	// U
	'utiliser_entete_colone_tri' => 'Use column header to sort CSA Members (in black active sorting method and in green available ones).',

	// V
	'visiteur' => 'Visitor',
	'vos_paniers' => 'Your @nb@ baskets',
	'vos_paniers_vendu' => 'Your @nb@ sold baskets',
	'vos_responsabilites' => 'Your @nb@ responsibilities',
	'votre_compte_amap' => 'Your CSA account',
	'votre_panier' => 'Your basket',
	'votre_panier_vendu' => 'Your sold basket',
	'votre_responsabilite' => 'Your responsibility',
	'vous_etes_responsable_distribution' => 'You are in charge for the following distribution:',
	'vous_etes_responsable_distributions' => 'You are in charge for the following distributions:'
);

?>
