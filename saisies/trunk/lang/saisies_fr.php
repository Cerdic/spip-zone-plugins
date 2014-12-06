<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// Fichier source, a modifier dans svn://zone.spip.org/spip-zone/_plugins_/saisies/lang/
if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// B
	'bouton_parcourir_docs_article' => 'Parcourir l&#8217;article',
	'bouton_parcourir_docs_breve' => 'Parcourir la br&#232;ve',
	'bouton_parcourir_docs_rubrique' => 'Parcourir la rubrique',
	'bouton_parcourir_mediatheque' => 'Parcourir la m&#233;diath&#232;que',

	// C
	'construire_action_annuler' => 'Annuler',
	'construire_action_configurer' => 'Configurer',
	'construire_action_deplacer' => 'D&#233;placer',
	'construire_action_dupliquer' => 'Dupliquer',
	'construire_action_dupliquer_copie' => '(copie)',
	'construire_action_supprimer' => 'Supprimer',
	'construire_ajouter_champ' => 'Ajouter un champ',
	'construire_attention_enregistrer' => 'N&#8217;oubliez pas d&#8217;enregistrer vos modifications&#160;!',
	'construire_attention_modifie' => 'Le formulaire ci-dessous est diff&#233;rent du formulaire initial. Vous avez la possibilit&#233; de le r&#233;initialiser &#224; son &#233;tat avant vos modifications.',
	'construire_attention_supprime' => 'Vos modifications comportent des suppressions de champs. Veuillez confirmer l&#8217;enregistrement de cette nouvelle version du formulaire.',
	'construire_aucun_champs' => 'Il n&#8217;y a pour l&#8217;instant aucun champ dans ce formulaire.',
	'construire_confirmer_supprimer_champ' => 'Voulez-vous vraiment supprimer ce champ&#160;?',
	'construire_info_nb_champs_masques' => '@nb@ champ(s) masqu&#233;(s) le temps de configurer le groupe.',
	'construire_position_explication' => 'Indiquez devant quel autre champ sera plac&#233; celui-ci.',
	'construire_position_fin_formulaire' => '&#192; la fin du formulaire',
	'construire_position_fin_groupe' => '&#192; la fin du groupe @groupe@',
	'construire_position_label' => 'Position du champ',
	'construire_reinitialiser' => 'R&#233;initialiser le formulaire',
	'construire_reinitialiser_confirmer' => 'Vous allez perdre toutes vos modifications. &#202;tes-vous s&#251;r de vouloir revenir au formulaire initial&#160;?',
	'construire_verifications_aucune' => 'Aucune',
	'construire_verifications_label' => 'Type de v&#233;rification &#224; effectuer',

	// E
	'erreur_generique' => 'Il y a des erreurs dans les champs ci-dessous, veuillez v&#233;rifier vos saisies',
	'erreur_option_nom_unique' => 'Ce nom est d&#233;j&#224; utilis&#233; par un autre champ et il doit &#234;tre unique dans ce formulaire.',

	// I
	'info_configurer_saisies' => 'Page de test des Saisies',

	// L
	'label_annee' => 'Ann&#233;e',
	'label_jour' => 'Jour',
	'label_mois' => 'Mois',

	// O
	'option_aff_art_interface_explication' => 'Afficher uniquement les articles de la langue de l&#8217;utilisateur',
	'option_aff_art_interface_label' => 'Affichage multilingue',
	'option_aff_langue_explication' => 'Affiche la langue de l&#8217;article ou rubrique s&#233;lectionn&#233; devant le titre',
	'option_aff_langue_label' => 'Afficher la langue',
	'option_aff_rub_interface_explication' => 'Afficher uniquement les rubriques de la langue de l&#8217;utilisateur',
	'option_aff_rub_interface_label' => 'Affichage multilingue',
	'option_afficher_si_explication' => 'Indiquez les conditions pour afficher le champ en fonction de la valeur des autres champs. L&#8217;identifiant des autres champs doit &#234;tre mis entre <code>@</code>. <br />Exemple <code>@selection_1@=="Toto"</code> conditionne l&#8217;affichage du champ &#224; ce que le champ <code>selection_1</code> ait pour valeur <code>Toto</code>.',
	'option_afficher_si_label' => 'Affichage conditionnel',
	'option_afficher_si_remplissage_explication' => 'Contrairement &#224; la pr&#233;c&#233;dente option, celle-ci ne conditionne l&#8217;affichage que lors du remplissage du formulaire, pas lors de l&#8217;affichage des r&#233;sultats. Sa syntaxe est la m&#234;me.',
	'option_afficher_si_remplissage_label' => 'Affichage conditionnel lors du remplissage',
	'option_attention_explication' => 'Un message plus important que l&#8217;explication.',
	'option_attention_label' => 'Avertissement',
	'option_autocomplete_defaut' => 'Laisser par d&#233;faut',
	'option_autocomplete_explication' => 'Au chargement de la page, votre navigateur peut pr&#233;-remplir le champ en fonction de son historique',
	'option_autocomplete_label' => 'Pr&#233;-remplissage du champ',
	'option_autocomplete_off' => 'D&#233;sactiver',
	'option_autocomplete_on' => 'Activer',
	'option_cacher_option_intro_label' => 'Cacher le premier choix vide',
	'option_choix_alternatif_label' => 'Permettre de proposer un choix alternatif',
	'option_choix_alternatif_label_defaut' => 'Autre choix',
	'option_choix_alternatif_label_label' => 'Label de ce choix alternatif',
	'option_choix_destinataires_explication' => 'Un ou plusieurs auteurs parmis lesquels l&#8217;utilisateur pourra faire son choix. Si rien n&#8217;est s&#233;lectionn&#233;, c&#8217;est l&#8217;auteur qui a install&#233; le site qui sera choisi.',
	'option_choix_destinataires_label' => 'Destinataires possibles',
	'option_class_label' => 'Classes CSS suppl&#233;mentaires',
	'option_cols_explication' => 'Largeur du bloc en nombre de caract&#232;res. Cette option n&#8217;est pas toujours appliqu&#233;e car les styles CSS de votre site peuvent l&#8217;annuler.',
	'option_cols_label' => 'Largeur',
	'option_datas_explication' => 'Vous devez indiquez un choix par ligne sous la forme "cle|Label du choix"',
	'option_datas_label' => 'Liste des choix possibles',
	'option_datas_sous_groupe_explication' => 'Vous devez indiquez un choix par ligne sous la forme "cle|Label" du choix. <br />Vous pouvez indiquer le d&#233;but d&#8217;un sous-groupe sous la forme "*Titre du sous-groupe". Pour finir un sous-groupe vous pouvez en entamez un autre, ou bien mettre une ligne contenant unique "/*".',
	'option_defaut_label' => 'Valeur par d&#233;faut',
	'option_disable_avec_post_explication' => 'Identique &#224; l&#8217;option pr&#233;c&#233;dente mais poste quand m&#234;me la valeur dans un champ cach&#233;.',
	'option_disable_avec_post_label' => 'D&#233;sactiver mais poster',
	'option_disable_explication' => 'Le champ ne peut plus obtenir le focus.',
	'option_disable_label' => 'D&#233;sactiver le champ',
	'option_erreur_obligatoire_explication' => 'Vous pouvez personnaliser le message d&#8217;erreur affich&#233; pour indiquer l&#8217;obligation (sinon laisser vide).',
	'option_erreur_obligatoire_label' => 'Message d&#8217;obligation',
	'option_explication_explication' => 'Si besoin, une courte phrase d&#233;crivant l&#8217;objet du champ.',
	'option_explication_label' => 'Explication',
	'option_groupe_affichage' => 'Affichage',
	'option_groupe_description' => 'Description',
	'option_groupe_utilisation' => 'Utilisation',
	'option_groupe_validation' => 'Validation',
	'option_heure_pas_explication' => 'Lorsque vous utilisez l&#8217;horaire, un menu s&#8217;affiche pour aider &#224; saisir heures et minutes. Vous pouvez ici choisir l&#8217;intervalle de temps entre chaque choix (par d&#233;faut 30min).',
	'option_heure_pas_label' => 'Intervalle des minutes dans le menu d&#8217;aide &#224; la saisie',
	'option_horaire_label' => 'Horaire',
	'option_horaire_label_case' => 'Permettre de saisir aussi l&#8217;horaire',
	'option_id_groupe_label' => 'Groupe de mots',
	'option_info_obligatoire_explication' => 'Vous pouvez modifier l&#8217;indication d&#8217;obligation par d&#233;faut&#160;: <i>[Obligatoire]</i>.',
	'option_info_obligatoire_label' => 'Indication d&#8217;obligation',
	'option_inserer_barre_choix_edition' => 'barre d&#8217;&#233;dition compl&#232;te',
	'option_inserer_barre_choix_forum' => 'barre des forums',
	'option_inserer_barre_explication' => 'Ins&#232;re une barre d&#8217;outils du porte-plume si ce dernier est activ&#233;.',
	'option_inserer_barre_label' => 'Ins&#233;rer une barre d&#8217;outils',
	'option_label_case_label' => 'Label plac&#233; &#224; c&#244;t&#233; de la case',
	'option_label_explication' => 'Le titre qui sera affich&#233;.',
	'option_label_label' => 'Label',
	'option_maxlength_explication' => 'L&#8217;utilisateur ne pourra pas taper plus de caract&#232;res que ce nombre.',
	'option_maxlength_label' => 'Nombre de caract&#232;res maximum',
	'option_multiple_explication' => 'L&#8217;utilisateur pourra s&#233;lectionner plusieurs valeurs',
	'option_multiple_label' => 'S&#233;lection multiple',
	'option_nom_explication' => 'Un nom informatique qui identifiera le champ. Il ne doit contenir que des caract&#232;res alpha-num&#233;riques minuscules ou le caract&#232;re "_".',
	'option_nom_label' => 'Nom du champ',
	'option_obligatoire_label' => 'Champ obligatoire',
	'option_option_destinataire_intro_label' => 'Label du premier choix vide (lorsque sous forme de liste)',
	'option_option_intro_label' => 'Label du premier choix vide',
	'option_option_statut_label' => 'Afficher les statuts',
	'option_pliable_label' => 'Pliable',
	'option_pliable_label_case' => 'Le groupe de champs pourra &#234;tre repli&#233;.',
	'option_plie_label' => 'D&#233;j&#224; pli&#233;',
	'option_plie_label_case' => 'Si le groupe de champs est pliable, il sera d&#233;j&#224; pli&#233; &#224; l&#8217;affichage du formulaire.',
	'option_previsualisation_explication' => 'Si le porte-plume est activ&#233;, ajoute un onglet pour pr&#233;visualiser le rendu du texte saisi.',
	'option_previsualisation_label' => 'Activer la pr&#233;visualisation',
	'option_readonly_explication' => 'Le champ peut &#234;tre lu, s&#233;lectionn&#233;, mais pas modifi&#233;.',
	'option_readonly_label' => 'Lecture seule',
	'option_rows_explication' => 'Hauteur du bloc en nombre de ligne. Cette option n&#8217;est pas toujours appliqu&#233;e car les styles CSS de votre site peuvent l&#8217;annuler.',
	'option_rows_label' => 'Nombre de lignes',
	'option_size_explication' => 'Largeur du champ en nombre de caract&#232;res. Cette option n&#8217;est pas toujours appliqu&#233;e car les styles CSS de votre site peuvent l&#8217;annuler.',
	'option_size_label' => 'Taille du champ',
	'option_type_choix_plusieurs' => 'Permettre &#224; l&#8217;utilisateur de choisir <strong>plusieurs</strong> destinataires.',
	'option_type_choix_tous' => 'Mettre <strong>tous</strong> ces auteurs en destinataires. L&#8217;utilisateur n&#8217;aura aucun choix.',
	'option_type_choix_un' => 'Permettre &#224; l&#8217;utilisateur de choisir <strong>un seul</strong> destinataire (sous forme de liste d&#233;roulante).',
	'option_type_choix_un_radio' => 'Permettre &#224; l&#8217;utilisateur de choisir <strong>un seul</strong> destinataire (sous forme de liste &#224; puce).',
	'option_type_explication' => 'En mode "masqu&#233;", le contenu du champ ne sera pas visible.',
	'option_type_label' => 'Type du champ',
	'option_type_password' => 'Texte masqu&#233; lors de la saisie (ex&#160;: mot de passe)',
	'option_type_text' => 'Normal',

	// S
	'saisie_auteurs_explication' => 'Permet de s&#233;lectionner un ou plusieurs auteurs',
	'saisie_auteurs_titre' => 'Auteurs',
	'saisie_case_explication' => 'Permet d&#8217;activer ou de d&#233;sactiver quelque chose.',
	'saisie_case_titre' => 'Case unique',
	'saisie_checkbox_explication' => 'Permet de choisir plusieurs options avec des cases.',
	'saisie_checkbox_titre' => 'Cases &#224; cocher',
	'saisie_date_explication' => 'Permet de saisir une date&#160;? l&#8217;aide d&#8217;un calendrier',
	'saisie_date_titre' => 'Date',
	'saisie_destinataires_explication' => 'Permet de choisir un ou plusieurs destinataires parmis des auteurs pr&#233;-s&#233;lectionn&#233;.',
	'saisie_destinataires_titre' => 'Destinataires',
	'saisie_explication_explication' => 'Un texte explicatif g&#233;n&#233;ral.',
	'saisie_explication_titre' => 'Explication',
	'saisie_fieldset_explication' => 'Un cadre qui pourra englober plusieurs champs.',
	'saisie_fieldset_titre' => 'Groupe de champs',
	'saisie_file_explication' => 'Envoi d&#8217;un fichier',
	'saisie_file_titre' => 'Fichier',
	'saisie_hidden_explication' => 'Un champ pr&#233;-rempli que l&#8217;utilisateur ne pourra pas voir.',
	'saisie_hidden_titre' => 'Champ cach&#233;',
	'saisie_input_explication' => 'Une simple ligne de texte, pouvant &#234;tre visible ou masqu&#233;e (mot de passe).',
	'saisie_input_titre' => 'Ligne de texte',
	'saisie_mot_explication' => 'Un ou plusieurs mots-cl&#233;s d&#8217;un groupe de mot',
	'saisie_mot_titre' => 'Mot-cl&#233;',
	'saisie_oui_non_explication' => 'Oui ou non, c&#8217;est clair&#160;?&#160;:)',
	'saisie_oui_non_titre' => 'Oui ou non',
	'saisie_radio_defaut_choix1' => 'Un',
	'saisie_radio_defaut_choix2' => 'Deux',
	'saisie_radio_defaut_choix3' => 'Trois',
	'saisie_radio_explication' => 'Permet de choisir une option parmis plusieurs disponibles.',
	'saisie_radio_titre' => 'Boutons radios',
	'saisie_selecteur_article' => 'Affiche un navigateur de s&#233;lection d&#8217;article',
	'saisie_selecteur_article_titre' => 'S&#233;lecteur d&#8217;article',
	'saisie_selecteur_rubrique' => 'Affiche un navigateur de s&#233;lection de rubrique',
	'saisie_selecteur_rubrique_article' => 'Affiche un navigateur de s&#233;lection d&#8217;article ou de rubrique',
	'saisie_selecteur_rubrique_article_titre' => 'S&#233;lecteur d&#8217;article ou rubrique',
	'saisie_selecteur_rubrique_titre' => 'S&#233;lecteur de rubrique',
	'saisie_selection_explication' => 'Choisir une option dans une liste d&#233;roulante.',
	'saisie_selection_multiple_explication' => 'Permet de choisir plusieurs options avec une liste.',
	'saisie_selection_multiple_titre' => 'S&#233;lection multiple',
	'saisie_selection_titre' => 'Liste d&#233;roulante',
	'saisie_textarea_explication' => 'Un champ de texte sur plusieurs lignes.',
	'saisie_textarea_titre' => 'Bloc de texte',

	// T
	'tous_visiteurs' => 'Tous les visiteurs (m&#234;me non enregistr&#233;s)',
	'tout_selectionner' => 'Tout s&#233;lectionner',

	// V
	'vue_sans_reponse' => '<i>Sans r&#233;ponse</i>',

	// Z
	'z' => 'zzz'
);

?>
