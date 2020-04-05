<?php

// This is a SPIP module file  --  Ceci est un fichier module de SPIP

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

// A
	'acceder_repertoire' => 'Access the directory: @repertoire@.',
	'acces_interdit' => 'Access denied.',
	'affiche_les_informations_de_developpement' => '', /* missing (Afficher les informations de développement) */
	'aide_class_css_formulaire' => '', /* missing (Si vous voulez utilisez CKEditor avec vos formulaires, il faut que vos {{<textarea>}}s aient une classe CSS spécifique que vous devez renseigner ici. Par défaut, CKEditor utilisera la la classe CSS : {{inserer_barre_edition}} qui correspond aux {{<textarea>}}s auxquels le {{porteplume}} se greffe.) */
	'aide_contextes' => 'Indicate here a list of identifiers CSS (<code># identifier</code>) or CSS classes (<code>.class</code>) that can be applied to the &lt; body &gt; of the editor. You can specify a description of the context by entering: <code># contexte|description</code> or <code>. contexte|description</code>.<br/> <strong>Example:</strong> <code># colonne_un|Main column;. colonne_gauche|Left column;. colonne_droite|Right column; # extrait|As extract from text</code>',
	'aide_couleurs' => 'Enter here, a list of colors in the format: RGB or RRGGBB (example: 999 333 939 993 399 339 933 393). <br/>This allows, for example, predefine the colors in the CSS of the site and thus to maintain a certain unity site. <br/>Any invalid entry is ignored.',
	'aide_css_site' => 'Enter here a list (separated by commas) style sheets of site within CKEditor.',
	'aide_echappe_car' => '', /* missing (Lorsque vous entrez du texte dans un modèles à balises fermantes, lors de la réédition, il est interprété par SPIP. Pour éviter cela, il faut protéger certains caractêres. A priori, il suffit de protéger <code>{}[]</code>.) */
	'aide_fontkit' => 'Mark this option to use the font kits for <a href="http://www.fontsquirrel.com/">Font Squirrel</a> that you have previously uncompressed directory by kit in _DIR_IMG/FontKits/.',
	'aide_fontsizes' => 'List of sizes for fonts. This list should follow the following syntax: <code>title 1/size 1;title 2/size 2 ...</code>',
	'aide_formats' => 'Sample : <code style="color: green;">div;pre;h3</code>',
	'aide_html2spip_non_trouvee' => 'The library <code>html2spip</code> is not installed. You can benefit from the automatic translation from HTML to SPIP. Please first install <a href="http://ftp.espci.fr/pub/html2spip/html2spip-0.6.zip"> library <code> html2spip </code></a> in the <code>lib/</code>.',
	'aide_identifiant_parametre_1' => '', /* missing (Vous pouvez laisser vide l'identifiant pour que le paramètre soit entré sous la forme &lt;modele|valeur&gt; plutôt que &lt;modele|identifiant=valeur&gt;) */
	'aide_identifiant_parametre_2' => '', /* missing (Ici, vous devez entrer l'identifiant du paramètre, commme par exemple left, player etc...) */
	'aide_images_modele' => 'If you want to change the proposed images, you can create a directory <code>images/templates/</code> in your directory <code>squelettes</code>.',
	'aide_inserer_la_css' => 'If you check these options, the plugin will take care to insert the HTML CSS loading fonts, otherwise you will need to modify the skeleton of your pages and insert them into the ones you want.',
	'aide_plugin' => 'If you want to install plugins for CKEditor, create a folder <code>plugins/ckeditor</code> accessible from the command SPIP <code>#CHEMIN</code> (for example in the folder <code>squelettes</code>)',
	'aide_selecteurs' => '', /* missing (Entrez ici une liste de sélecteurs (un par ligne), suivis éventuellement de <code>|basique</code> pour n'utiliser qu'une barre d'outils basique.
Exemple : 
<cadre class=css>
.inserer_barre_outils
#formulaire_forum textarea|basique
</cadre>) */
	'aide_styles' => 'Any style correctly defined here will appear in the "combo styles" of CKEditor.<br>A style must follow the syntax:<pre style="color: green;">NOM : element.class { css }</pre>In which, <ul><li><strong>NOM</strong> is: 	any string of characters long, it is the name that will appear in CKEditor,</li><li><strong>element</strong> is: an HTML element (strong, em, span, et c...), the selection you apply this style will be in an HTML block of that element type,</li><li><strong>class</strong> (optional) is: a class name to define CSS in your stylesheet,</li><li><strong>css</strong> (optional) is: valid CSS code, such as <code style="color: green;">color: blue;</code> (no testing is done, one error can crash the javascript generated and preventing the editor display).</li></ul><strong>Attention :</strong> you need to know CSS to manipulate\to change the content of this page.',
	'aide_syntaxe_modele' => '', /* missing (Les paramêtres des modèles spip sont normalement passés avec la syntaxe (spip) : <code><modele|param1=val1|param2=val2...></code>, si vous voulez que votre modèle utilise la syntaxe (html) : <code><modele param1=ˮval1ˮ param2=ˮval2ˮ...></code>, sélectionnez l'option <code>html</code>.) */
	'aide_vignette' => 'Enter the maximum size of the thumbnails used by CKEditor. If you leave this box blank, the images will be displayed with their normal size by CKEditor.',
	'aide_webfonts' => 'Here, enter a list (separated by commas) of font names <a target="_blank" onclick="window.open(this.href,&#39;_blank&#39;,&#39;height=500,width=500,location=no,scrollbar=yes&#39;);return false;" href="http://code.google.com/webfonts">Google Web Fonts</a>. Names must be exactly those proposed in the google font directory',
	'apres' => 'after',
	'article' => 'article',
	'aucune_conversion' => '', /* missing (aucune conversion) */
	'aucun_document' => 'No document',
	'aucun_document_descriptif' => 'You have not uploaded any documents.',
	'aucun_parametre' => 'This model has no parameters.',
	'aucun_plugin' => 'SPIP does not detect the plugin in the plugins folder, please unpack the ZIP containing plugins for CKEditor. Each plugin must be in an individual folder as if it were installed in the <code>plugins</code> CKEDITOR folder.',
	'autorisations_telechargement' => '', /* missing (Autorisations de téléchargement :) */
	'autoriser_autres_couleurs' => 'allow other colors',
	'autoriser_insertion_documents' => '&nbsp;Allow insertion of documents from any article.',
	'autoriser_liens_spip' => '&nbsp;Allow links/anchors in SPIP style.',
	'autoriser_mkdir' => 'Allow to create directories@plus@.',
	'autoriser_parcours_dossier_spip' => 'Allow to browse the folder SPIP to insert images.',
	'autoriser_redacteurs' => 'also authorize the writers@plus@.',
	'autoriser_telechargement_dossier_spip' => 'Allow to upload images to the SPIP folder.',
	'avant' => 'before',
	'avertissement_css' => '', /* missing ({{⚠ Attention :}} vous devez savoir manipuler le CSS pour pouvoir modifier le contenu de cette page.) */

// B
	'balises_spip_autoriser' => 'SPIP tags to allow in CKEditor:',
	'balise_fermante' => 'closing tag',

// C
	'changer_de_contexte' => 'Context:',
	'choisir_correction_ortho' => '', /* missing (Si la langue n'est pas connue, choisir :) */
	'choisir_skin' => 'Choose a skin:',
	'cisf' => '', /* missing (saisie facile (fonctionnalité expérimentale)) */
	'ckeditor_defaut' => 'CKEditor by default',
	'ckeditor_exclu' => 'CKEditor only',
	'ckeditor_p1' => '', /* missing (Basique) */
	'ckeditor_p1_titre' => '', /* missing (Configuration de base) */
	'ckeditor_p2' => '', /* missing (Barres d'outils) */
	'ckeditor_p2_titre' => '', /* missing (Configuration des barres d'outils) */
	'ckeditor_p3' => '', /* missing (Avancée) */
	'ckeditor_p3_titre' => '', /* missing (Configuration avancée) */
	'ckeditor_p4' => '', /* missing (Images et documents) */
	'ckeditor_p4_titre' => '', /* missing (Images et documents) */
	'ckeditor_p5' => '', /* missing (Styles) */
	'ckeditor_p5_titre' => '', /* missing (Configuration des styles) */
	'ckeditor_p6' => '', /* missing (Modèles de pages) */
	'ckeditor_p7' => '', /* missing (Plugins) */
	'ckeditor_p7_titre' => '', /* missing (Configuration des Add-ons) */
	'ckeditor_p8' => '', /* missing (Modèles SPIP) */
	'ckeditor_p8_titre' => '', /* missing (Modèles SPIP) */
	'ckeditor_titre' => '', /* missing (Configurer CKeditor) */
	'ck_delete' => 'Reset configuration',
	'ck_ko' => 'Error in parameters.',
	'ck_ok' => 'Configuration saved.',
	'class_des_formats' => 'CSS class of items inserted by drop-down menu <em>formats</em>:',
	'class_partie_privee' => '', /* missing (classe CSS :) */
	'class_partie_publique' => '', /* missing (classe CSS :) */
	'configuration_des_couleurs' => 'Color configuration:',
	'configuration_des_modeles_spip' => 'SPIP Models',
	'configuration_des_polices' => 'Fonts configuration:',
	'configuration_formats' => '', /* missing (Configuration des « formats » CKEditor) */
	'configuration_modeles' => 'Templates management',
	'confirmer_supprimer_modele' => 'Are you sure you want to delete this template?',
	'confirme_reinitialiser_plugin' => 'Are you sure you want to reset the plugin? (This will remove all your preferences)',
	'contenu_du_modele' => 'Template content:',
	'conversion_partielle_vers_spip' => '', /* missing (conversion partielle en typographie SPIP) */
	'copie_impossible' => '<p>Can not copy <code>@fichier@</code></p><blockquote>@errmsg@</blockquote>',
	'copie_reussie' => 'The file: <code>@fichier@</code> has been correctly copied.',
	'css_site' => 'Stylesheets Site (CSSs):',

// D
	'demarrer_correction_ortho' => 'Start spell checking:',
	'desactive_car_zappe_par_html2spip' => 'Disabled because ignored by HTML2SPIP.',
	'description' => 'Description:',
	'documents_article' => 'Show only documents of the article being edited.',
	'documents_rubrique' => 'Only show documents in the section being edited.',

// E
	'echappe_caracteres' => '', /* missing (Caractères protégés dans ce modèle :) */
	'editer_modele' => 'Edit the template',
	'edite_modele' => 'Edit',
	'edition_du_modele' => 'Editing the template',
	'enregistrer_modele' => 'Save the template',
	'entermode' => 'Enter gives:',
	'enter_br' => 'line break (br)',
	'enter_div' => 'paragraph (div)',
	'enter_p' => 'paragraph (p)',
	'erreur_droits' => 'rights error on the destination directory',
	'erreur_transmission' => 'transmission error',
	'err_conversion' => '<p>Problem of converions. No conversion.</p>',
	'err_filesdir_pas_de_sousrep' => 'Files: Enter the name of a subdirectory without /',
	'err_files_extensions_autorisees' => 'For the allowed extensions for the files, follow the format: <code>ext<sub>1</sub> ; ext<sub>2</sub> ; ... ; ext<sub>n</sub></code>.',
	'err_flashdir_pas_de_sousrep' => 'Flash: Enter the name of a subdirectory without /',
	'err_flash_extensions_autorisees' => 'For the allowed extensions for the Flash docuemnts, follow the format: <code>ext<sub>1</sub> ; ext<sub>2</sub> ; ... ; ext<sub>n</sub></code>.',
	'err_images_extensions_autorisees' => 'For the allowed extensions for the images, follow the format: <code>ext<sub>1</sub> ; ext<sub>2</sub> ; ... ; ext<sub>n</sub></code>.',
	'err_imgdir_pas_de_sousrep' => 'Images: Enter the name of a subdirectory without /',
	'err_la_largeur_ecran_etroit_doit_etre_numerique' => 'The width of the screen (slim) must be a number.',
	'err_la_largeur_ecran_large_doit_etre_numerique' => 'The width of the screen (wide) must be a number.',
	'err_mauvaise_class_pour_formats' => 'You have not entered a valid CSS class name.',
	'err_mauvaise_liste_de_formats' => 'Follow the syntax of the formats.',
	'err_mauvaise_syntaxe_de_tag' => 'Follow the syntax of the SPIP <code>tags</code> to authorize.',
	'err_mauvais_langage' => 'The language should be a 2-letter code.',
	'err_mauvais_mode_entree' => '<code>ENTER</code> mode must follow a specific syntax.',
	'err_mauvais_mode_shiftentree' => '<code>SHIFT + input</code> mode must follow a specific syntax.',
	'err_repertoire_parent_interdit' => 'It is forbidden to go back in the tree to the parent directory.',
	'etroit' => 'Narrow screen: ',
	'explique_div' => '<em>div</em> : in this mode pressing the [Enter] causes the insertion of a block html &lt;div&gt;, advantage: it takes account of information layout (alignment, etc ...) of these blocks, disadvantage: it does not follow the standard layout of SPIP, but it is possible to do so by selecting the proper style sheet.',
	'explique_p' => '<em>p</em> : in this mode, pressing the [Enter] causes the insertion of a block html &lt;p&gt;, advantage: that respects the SPIP layout, disadvantage: SPIP does not include information layout (alignment, etc ...) of these blocks.',
	'explorateur_titre' => 'File Explorer for CKeditor-SPIP-plugin',
	'extensions_autorisees_descriptif' => 'Enter the extensions separated by ";".',

// F
	'files_extensions_autorisees' => 'List of allowed extensions:',
	'fin' => 'end',
	'flash_extensions_autorisees' => 'List of allowed extensions:',
	'forcer_copie_comme_texte' => '', /* missing (Forcer la copie « comme texte » en provenance du presse papier.) */
	'formats' => '	List of HTML-tags present in the "combo format":',

// H
	'hauteur_editeur' => 'Height Editor: ',
	'html2spip_detecte' => '', /* missing (HTML2SPIP est présent.) */
	'html2spip_identite' => 'HTML tags that HTML2SPIP must leave untouched',
	'html2spip_limite' => 'Use the options CKEditor only compatible with SPIP',

// I
	'identifiant_du_parametre' => 'Id parameter:',
	'ignore_mauvaise_version' => '', /* missing (Ne pas tenir compte de la version de CKeditor installée) */
	'images_extensions_autorisees' => 'List of allowed extensions:',
	'image_du_modele' => '', /* missing (Image du modèle :) */
	'info_bulle' => 'Model description:',
	'inserer_la_css_privee' => '', /* missing (Insérer les CSS dans la partie privée.) */
	'inserer_la_css_public' => 'Insert CSS in the public part.',
	'intitule_du_modele' => 'Title in the list box:',

// K
	'kcfinder_ignore' => ' (Option ignored by KCFinder)',

// L
	'label_article' => 'Article:',
	'label_auteur' => 'Author:',
	'label_breve' => 'News:',
	'label_du_parametre' => 'Label parameter:',
	'label_groupe_mots' => 'Group of keywords',
	'label_mot' => 'Keyword:',
	'label_section' => 'Section:',
	'langue_ckeditor' => 'CKEditor language:',
	'large' => 'Wide screen:',
	'les_crayons' => 'pencils (experimental feature)',
	'les_forums' => '', /* missing (les forums (fonctionnalité expérimentale)) */
	'listes_des_couleurs_presentees' => 'List of colors displayed:',
	'liste_des_parametres' => 'Parameter list:',
	'liste_de_contextes' => 'List of contexts:',
	'liste_google_webfonts' => 'Font List <a href="http://code.google.com/webfonts">Google Web Fonts</a> to include:',
	'liste_plugins_ckeditor' => '', /* missing (Liste des plugins pour CKEditor) */
	'liste_telechargements_autorises' => '<p>Allowed file types are: <blockquote>@liste@</blockquote>',
	'loading' => 'Loading',

// M
	'modeles_spip_autorisees' => 'SPIP Model',
	'modele_a_droite' => '', /* missing (à droite) */
	'modele_a_gauche' => '', /* missing (à gauche) */
	'modele_centre' => '', /* missing (centré) */
	'modele_commence_pas_par_slash' => 'A model name must not start with a slash &laquo;/&raquo;',
	'modele_cree' => 'The model <strong>@modele@</strong> is created.',
	'modele_document' => '', /* missing (Document) */
	'modele_enregistre' => 'The model <strong>@modele@</strong> is recorded.',
	'modele_image' => 'Image model:',
	'modele_incorpore' => '', /* missing (Incorporé) */
	'modele_supprime' => 'Model <strong>@modele@</strong> deleted.',
	'mode_edition_defaut' => 'Edit mode by default: ',
	'modification_de_modele' => 'SPIP Model Editing.',

// N
	'nom_du_bouton' => 'Button name:',
	'nom_du_nouveau_modele' => 'Enter the name of the new model:',
	'nom_nouveau_modele' => 'Model name:',
	'nom_repertoire_creer' => 'Directory name:',
	'normalement_detectee' => 'Normally this is autodetected value.',
	'nouveau' => 'New',
	'nouveau_modele' => 'New model',
	'nouveau_modele_sans_nom' => 'Unnamed new model.',
	'nouveau_parametre' => '', /* missing (Nouveau paramètre) */
	'numerique_facultatif' => 'numeric (optional)',
	'numerique_obligatoire' => 'numeric',
	'num_article' => 'an article',
	'num_auteur' => 'an author',
	'num_breve' => 'a news',
	'num_document' => 'a document',
	'num_groupe_mots' => 'a group of words',
	'num_indefini' => 'other',
	'num_mot' => 'a word',
	'num_rubrique' => 'a section',
	'num_site' => 'a site',

// O
	'objet_en_edition' => '', /* missing (l'objet en cours d'édition) */
	'ok' => 'Ok',
	'options_cisf' => '', /* missing (Options Saisie Facile) */
	'options_conversion' => '', /* missing (Options de conversion) */
	'options_crayons' => '', /* missing (Options pour les crayons) */
	'options_css' => 'CSS options:',
	'options_developpeur' => '', /* missing (Options développeur) */
	'options_editeur' => '', /* missing (Option de l'éditeur) */
	'options_forums' => '', /* missing (Options pour les forums) */
	'options_gui' => '', /* missing (Option de l'interface) */
	'options_html2spip' => 'HTML2SPIP Options:',
	'options_orthographe' => '', /* missing (Options de correction orthographique) */
	'options_privee' => '', /* missing (Options pour la partie privée) */
	'options_publique' => '', /* missing (Options pour la partie publique) */
	'options_spip' => 'SPIP Options:',
	'options_vignettes' => '', /* missing (Options pour les vignettes) */
	'ordre_du_bouton' => '', /* missing (Ordre du bouton :) */

// P
	'parametre_nomme' => '', /* missing (Paramètre : @PARAMETRE@) */
	'partie_privee' => '', /* missing (les formulaires en partie privée) */
	'partie_publique' => '', /* missing (les formulaires en partie publique) */
	'pas_numerique' => 'This is not a number.',
	'plugins_barre_position' => 'Buttons position:',
	'plugin_active' => 'Activate the plugin',
	'plugin_bouton' => '', /* missing (activer le bouton) */

// R
	'reinitialiser' => '', /* missing (Réinitialiser) */
	'reinitialiser_le_plugin' => 'Reset plugin',
	'repertoires_telechargement' => '', /* missing (Répertoires de téléchargement :) */
	'repertoire_des_fichiers' => 'Files directory',
	'repertoire_des_flash' => 'Flash® directory:',
	'repertoire_des_images' => 'Images directory:',
	'repertoire_de_base' => 'Uploads directory',
	'repertoire_parent' => 'Access parent directory.',
	'reset_toolbars' => 'Reset toolbars',
	'retour' => 'Back',
	'rubrique' => 'Section',

// S
	'sans_contexte' => 'without context',
	'selecteurs_espace_prive' => '', /* missing (Sélecteurs jQuery pour l'espace privé :) */
	'selecteurs_espace_public' => '', /* missing (Sélecteurs jQuery pour l'espace publique :) */
	'selection_aucun' => 'Deselect all',
	'selection_document_spip' => 'Selecting a SPIP document',
	'selection_inverse' => 'Inverse selection',
	'selection_tout' => 'Select all',
	'shiftentermode' => 'Shift + Enter gives:',
	'spipification' => 'Copyright &copy; 2009 <a <a style="text-decoration:underline;color:blue;cursor:pointer;" href="http://code.google.com/p/ckeditor-spip-plugin/">Plugin SPIP</a> - Fr&#233;d&#233;ric Bonnaud, Mehdi Cherifi, Emmanuel Dreyfus',
	'spip_defaut' => 'SPIP by default',
	'styles' => 'Styles:',
	'supprimer' => 'Delete',
	'supprimer_ce_modele' => 'Deleting this model:',
	'supprimer_ce_parametre' => 'Deleting this parameter:',
	'supprimer_modele' => 'Delete the model',
	'supprime_ce_parametre' => 'Delete this parameter',
	'supprime_modele' => 'Delete',
	'syntaxe' => '', /* missing (Syntaxe des paramètres :) */

// T
	'tailles_de_police' => 'Font size:',
	'taille_maximale_telechargements' => '<p>The maximum allowed size of a file is @taille@Mo.</p>',
	'tb_basic' => '', /* missing (basique) */
	'tb_full' => '', /* missing (complète) */
	'telecharger' => 'Upload',
	'telecharger_document' => 'Upload a document',
	'toolbar' => '', /* missing (barre d'outils :) */
	'tool_About' => 'About',
	'tool_Anchor' => 'Anchor',
	'tool_BGColor' => 'Background color',
	'tool_Blockquote' => 'Blockquote',
	'tool_Bold' => 'Bold',
	'tool_BulletedList' => 'Bulleted list',
	'tool_Button' => 'Button',
	'tool_Checkbox' => 'Checkbox',
	'tool_Copy' => 'Copy',
	'tool_Cut' => 'Cut',
	'tool_Find' => 'Find',
	'tool_Flash' => 'Flash',
	'tool_Font' => 'Font',
	'tool_FontSize' => 'FontSize',
	'tool_Form' => 'Form',
	'tool_Format' => 'Format',
	'tool_HiddenField' => 'Hidden field',
	'tool_HorizontalRule' => 'Horizontal rule',
	'tool_Iframe' => 'Iframe',
	'tool_Image' => 'Image',
	'tool_ImageButton' => 'Image button',
	'tool_Indent' => 'Indent',
	'tool_Italic' => 'Italic',
	'tool_JustifyBlock' => 'Justify block',
	'tool_JustifyCenter' => 'Justify center',
	'tool_JustifyLeft' => 'Justify left',
	'tool_JustifyRight' => 'Justify right',
	'tool_Link' => 'Link',
	'tool_Maximize' => 'Maximize',
	'tool_NewPage' => 'New page',
	'tool_NumberedList' => 'Numbered list',
	'tool_Outdent' => 'Outdent',
	'tool_PageBreak' => 'Page break',
	'tool_Paste' => 'Paste',
	'tool_PasteFromWord' => 'Paste from Word',
	'tool_PasteText' => 'Paste text',
	'tool_Print' => 'Print',
	'tool_Radio' => 'Radio',
	'tool_Redo' => 'Redo',
	'tool_RemoveFormat' => 'Remove format',
	'tool_Replace' => 'Replace',
	'tool_Scayt' => 'Correction during the typing',
	'tool_Select' => 'Select',
	'tool_SelectAll' => 'Select all',
	'tool_ShowBlocks' => 'Show blocks',
	'tool_Smiley' => 'Smiley',
	'tool_Source' => 'Source',
	'tool_SpecialChar' => 'Special character',
	'tool_SpellChecker' => 'Spell checker',
	'tool_Spip' => 'Spip link',
	'tool_SpipDoc' => 'Spip document',
	'tool_SpipModeles' => 'SPIP model',
	'tool_SpipSave' => 'Save',
	'tool_Strike' => 'Strike',
	'tool_Styles' => 'Styles',
	'tool_Subscript' => 'Subscript',
	'tool_Superscript' => 'Superscript',
	'tool_Table' => 'Table',
	'tool_Templates' => 'Templates',
	'tool_Textarea' => 'Text area',
	'tool_TextColor' => 'Text color',
	'tool_TextField' => 'Text field',
	'tool_Underline' => 'Underline',
	'tool_Undo' => 'Undo',
	'tool_Unlink' => 'Delete the link',
	'tool_ZpipPreview' => 'Overview',
	'tous' => 'all',
	'tous_documents' => 'Show all documents made available by SPIP.',
	'type_article' => 'Article',
	'type_auteur' => 'Author',
	'type_breve' => 'News',
	'type_de_modele' => 'Model type:',
	'type_fichier_interdit' => 'prohibited file type',
	'type_groupemot' => 'Group of keywords',
	'type_mot' => 'Keyword',
	'type_nombre' => 'The numerical value represents: ',
	'type_section' => 'Section',

// U
	'url_site' => 'Site URL:',
	'use_ckeditor' => 'Use CKEditor',
	'use_spip_editor' => 'Use SPIP editor',
	'utiliser_ckeditor_avec' => 'Use CKeditor with:',
	'utiliser_fichier' => 'Use the file: @fichier@.',
	'utiliser_html2spip' => 'Reconvert HTML into SPIP typo',
	'utiliser_html2spip_descriptif' => 'Use the HTML2SPIP library to reconvert HTML into SPIP typo. Using this option, you can use ckeditor while preserving the SPIP typo in your articles.',
	'utiliser_une_vignette_pour_les_images' => 'Use thumbnails from:',
	'utilise_fontkit' => 'Use the fonts from Font Squirrel kits.',
	'utilise_upload' => 'Allow the download from the CKEDITOR dialogues.',

// V
	'valeurs_du_parametre' => 'Parameter values​​:',
	'version_preferee' => 'CKEditor version %1 is installed, this plugin would prefer the version %2. Please first uninstall this version.',

// Chaines probablement pas utilisée :
/* On les garde au cas où ...
*/
);
?>
