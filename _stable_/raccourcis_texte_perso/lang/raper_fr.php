<?php

// lang/raper_fr.php

// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

$GLOBALS['i18n_'._RAPER_PREFIX.'_fr'] = array(

	  'raper' => "RaPer"
	, 'edition_des_raccourcis' => "Edition des raccourcis texte"
	, 'raccourcis_perso' => "Raccourcis texte personnalisés"
	, 'raccourcis_texte' => "Raccourcis texte"

	///////////////////
	// configure
	, 'parametrer_le_raper' => "Paramétrer le RaPer"
	, 'raper_description' => "<strong>RaPer</strong> vous permet de gérer les raccourcis du site.
		<br />
		Pour plus d'informations sur l'utilisation du <strong>RaPer</strong>,
		merci de consulter l'aide en ligne (le bouton à droite de l'écran).
		"
	, 'delegation_' => "Délégation : "
	, 'deleguer_description' => "Par défaut, seul le webmestre du site peut gérer les raccourcis.
		Vous pouvez déléguer cette gestion aux administrateurs restreints."
	, 'deleguer_restreints_' => "Permettre aux administrateurs restreints de gérer les raccourcis."
	, 'perimetre_' => "Périmètre : "
	, 'perimetre_description' => "Par défaut, vous avez accès aux raccourcis <em>publics</em>.
		En général, les raccourcis <em>publics</em> sont utilisés dans les squelettes de la distribution.
		<br />
		Pour ajouter des raccourcis, vous devez compléter votre fichier <em>lang/local_fr.php</em> 
		ou <em>local_fr.php</em> 
		ou <em>lang/local.php</em> 
		ou <em>local.php</em> qui doit se trouver 
		à la racine de votre site, ou dans votre dossier <em>squelettes</em>.
		"
	, 'editer_tout' => "Gérer tous les raccourcis disponibles."
	, 'editer_public' => "Gérer les raccourcis publics de SPIP."
	, 'editer_ecrire' => "Gérer les raccourcis du module <em>ecrire</em> de SPIP."
	, 'editer_spip' => "Gérer les raccourcis du module <em>spip</em> de SPIP."
	, 'editer_local' => "Gérer les raccourcis locaux (<em>squelettes/</em> ou racine)."
	, 'langues_' => "Langues : "
	, 'langues_description' => "Votre site a été configuré pour utiliser plusieurs langues.
		<br />
		Vous pouvez choisir dès maintenant de gérer les différentes traductions
		des raccourcis pour les langues souhaitées, ou uniquement pour les langues utilisées.
		"
	, 'gerer_langues_utilisees_' => "Ne gérer que les langues effectivement utilisées sur le site."
	, 'enregistrement_raccourcis_' => "Enregistrement des raccourcis : "
	, 'enregistrement_description' => "Les raccourcis personnalisés peuvent être enregistrés
		dans le champ meta de spip (par défaut) ou plus classiquement dans les fichiers de langues
		(lang/raper_nn.php).
		<br />
		S'il y a peu de raccourcis personnalisés, choisissez la première option.
		"

	///////////////////
	// edit
	, 'aucun_raccourci_perso_sur_i' => "Aucun raccourci texte personnalisé sur @i@."
	, 'un_raccourci_perso_sur_i' => "1 raccourci texte personnalisé sur @i@."
	, 'n_raccourcis_perso_sur_i' => "@n@ raccourcis texte personnalisés sur @i@."
	, 'perso_edit' => "Personnaliser ce raccourci"
	, 'perso_drop' => "Supprimer la personnalisation de ce raccourci"
	, 'selectionnez_langue' => "Sélectionnez une langue"
	, 'annuler' => "Annuler"
	, 'valider' => "Valider"
	
	///////////////////
	// raccourcis
	, 'aide_en_ligne' => "Aide en ligne"
	, 'voir_journal' => "Voir le journal"
	, 'titre_page_voir_journal' => "Journal du RaPer"

	// message jQuery
	, 'activez_javascript' => "Javascript n'est pas activé sur votre navigateur.
		<br />
		Activer Javascript vous permet une utilisation plus souple du RaPer."
	, 'jquery_ancienne_version' => "Vous utilisez une ancienne version de jQuery. 
		<br /> 
		Pour une meilleure fluidité lors de modifications des raccourcis, 
		il est conseillé d'installer - au minimum - la version 1.2.6 de jQuery."

); //

?>