<?php

// This is a SPIP module file  --  Ceci est un fichier module de SPIP

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

// A
	'acceder_repertoire' => '', /* missing (Accéder au répertoire @repertoire@.) */
	'acces_interdit' => '', /* missing (Accès interdit.) */
	'affiche_les_informations_de_developpement' => '', /* missing (Afficher les informations de développement) */
	'aide_class_css_formulaire' => '', /* missing (Si vous voulez utilisez CKEditor avec vos formulaires, il faut que vos {{<textarea>}}s aient une classe CSS spécifique que vous devez renseigner ici. Par défaut, CKEditor utilisera la la classe CSS : {{inserer_barre_edition}} qui correspond aux {{<textarea>}}s auxquels le {{porteplume}} se greffe.) */
	'aide_contextes' => '', /* missing (Indiquez ici une liste d'identifiants CSS ({{#identifiant}}) ou de classes CSS ({{.class}}) qui pourront être appliquées au {{<body>}} de l'éditeur. Vous pouvez préciser une description du contexte en entrant : {{#contexte|description}} ou {{.contexte|description}}.
{{Exemple :}} {#colonne_un|Colonne principale; .colonne_gauche|Colonne de gauche ; .colonne_droite|Colonne de droite ; #extrait|Comme extrait de texte}) */
	'aide_couleurs' => '', /* missing (Entrez ici, une liste de couleurs au format rvb ou rrvvbb<br/>(exemple <code>999 333 939 993 399 339 933 393</code>).<br/>Ceci permet, par exemple, de prédéfinir les couleurs de la CSS du site et ainsi de maintenir une certaine unité au site.<br/>Toute entrée invalide est ignorée.) */
	'aide_css_site' => '&#1571;&#1583;&#1582;&#1604; &#1607;&#1606;&#1575; &#1602;&#1575;&#1574;&#1605;&#1577; &#40;&#1605;&#1601;&#1589;&#1608;&#1604;&#1577; &#1576;&#1601;&#1608;&#1575;&#1589;&#1604;&#41; &#1571;&#1608;&#1585;&#1575;&#1602; &#1575;&#1604;&#1571;&#1606;&#1605;&#1575;&#1591; &#1604;&#1604;&#1605;&#1608;&#1602;&#1593; &#1604;&#1604;&#1575;&#1587;&#1578;&#1582;&#1583;&#1575;&#1605; &#1601;&#1610; CKEditor.',
	'aide_echappe_car' => '', /* missing (Lorsque vous entrez du texte dans un modèles à balises fermantes, lors de la réédition, il est interprété par SPIP. Pour éviter cela, il faut protéger certains caractêres. A priori, il suffit de protéger <code>{}[]</code>.) */
	'aide_fontkit' => '', /* missing (Cochez cette option pour pouvoir utiliser les kits de police de <a href="http://www.fontsquirrel.com/">Font Squirrel</a> que vous aurez préalablement décompressées dans un répertoire par kit dans _DIR_IMG/FontKits/.) */
	'aide_fontsizes' => '', /* missing (Liste de tailles pour les polices de caractêres. Cette liste doit suivre la syntaxe suivante<br/><code>intitulé 1/taille 1;intitulé 2/taille 2 ...</code>.<br/>Par exemple<br/><code>12 Pixels/12px;Gros/2.3em;30 Pourcents de plus/130%;Plus gros/larger</code>) */
	'aide_formats' => '&#1605;&#1579;&#1575;&#1604;: <code style="color: green;">div;pre;h3</code>',
	'aide_html2spip_non_trouvee' => '', /* missing (La librairie <code>html2spip</code> n'est pas installée. Vous ne pouvez bénéficier de la traduction automatique du HTML vers SPIP. Veuillez d'abord installer [la librairie html2spip->http://ftp.espci.fr/pub/html2spip/html2spip-0.6.zip] dans le répertoire <code>lib/</code>. ) */
	'aide_identifiant_parametre_1' => '', /* missing (Vous pouvez laisser vide l'identifiant pour que le paramètre soit entré sous la forme &lt;modele|valeur&gt; plutôt que &lt;modele|identifiant=valeur&gt;) */
	'aide_identifiant_parametre_2' => '', /* missing (Ici, vous devez entrer l'identifiant du paramètre, commme par exemple left, player etc...) */
	'aide_images_modele' => '', /* missing (Si vous voulez modifier les images proposées, vous pouvez créer un répertoire <code>images/templates/</code> dans votre répertoire <code>squelettes</code>.) */
	'aide_inserer_la_css' => '', /* missing (Si vous cochez ces options, c'est le plugin qui va se charger d'insérer le code HTML chargeant les CSS des polices, sinon, vous devrez modifier les squelettes de vos pages pour les insérer dans celles que vous désirez.) */
	'aide_plugin' => '', /* missing (Si vous voulez installez des [plugins->http://ckeditor.com/Forums/Plugins-0] pour CKEditor, il faut créer un dossier <code>plugins/ckeditor</code> accessible depuis la commande <code>#CHEMIN</code> de SPIP (par exemple dans le dossier <code>squelettes</code>). Puis installer chaque plugin dans un répertoire de ce dossier.) */
	'aide_selecteurs' => '', /* missing (Entrez ici une liste de sélecteurs (un par ligne), suivis éventuellement de <code>|basique</code> pour n'utiliser qu'une barre d'outils basique.
Exemple : 
<cadre class=css>
.inserer_barre_outils
#formulaire_forum textarea|basique
</cadre>) */
	'aide_styles' => '&#1580;&#1605;&#1610;&#1593; &#1575;&#1604;&#1571;&#1606;&#1605;&#1575;&#1591;  &#1575;&#1604;&#1605;&#1603;&#1578;&#1608;&#1576;&#1577; &#1576;&#1591;&#1585;&#1610;&#1602;&#1577; &#1587;&#1604;&#1610;&#1605;&#1577; &#1587;&#1608;&#1601; &#1578;&#1592;&#1607;&#1585; &#1607;&#1606;&#1575; &#13;&#10;  &#1601;&#1610; "&#1587;&#1585;&#1583; &#1575;&#1604;&#1571;&#1606;&#1605;&#1575;&#1591;" CKEditor.<br>&#1608;&#1610;&#1580;&#1576; &#1571;&#1606; &#1578;&#1578;&#1576;&#1593; &#1571;&#1587;&#1604;&#1608;&#1576; &#1576;&#1606;&#1575;&#1569; &#1575;&#1604;&#1580;&#1605;&#1604;&#1577;:<code style="color: green;">NOM : element.class { css }</code>&#1571;&#1610;&#1606;, <ul><li><strong>NOM</strong> &#1607;&#1608; &#58; &#1571;&#1610; &#1587;&#1604;&#1587;&#1604;&#1577; &#1605;&#1606; &#1575;&#1604;&#1571;&#1581;&#1585;&#1601;&#1548; &#1607;&#1608; &#1575;&#1604;&#1575;&#1587;&#1605; &#1575;&#1604;&#1584;&#1610; &#1587;&#1608;&#1601; &#1610;&#1592;&#1607;&#1585; &#1601;&#1610; CKEditor,</li><li><strong>element</strong> &#1607;&#1608; &#58; &#1593;&#1606;&#1589;&#1585; HTML (strong, em, span, et c...), &#1575;&#1604;&#1575;&#1582;&#1578;&#1610;&#1575;&#1585; &#1593;&#1606;&#1583; &#1578;&#1591;&#1576;&#1610;&#1602; &#1607;&#1584;&#1575; &#1575;&#1604;&#1571;&#1587;&#1604;&#1608;&#1576; &#1587;&#1608;&#1601; &#1610;&#1603;&#1608;&#1606; &#1601;&#1610; &#1603;&#1578;&#1604;&#1577; HTML &#1605;&#1606; &#1606;&#1608;&#1593; &#1575;&#1604;&#1593;&#1606;&#1589;&#1585;,</li><li><strong>class</strong> (&#40;&#1575;&#1582;&#1578;&#1610;&#1575;&#1585;&#1610;&#41; &#1607;&#1608; &#58; &#1575;&#1587;&#1605; &#1575;&#1604;&#1591;&#1576;&#1602;&#1577; CSS &#1604;&#1578;&#1593;&#1585;&#1610;&#1601; &#1601;&#1610; &#1608;&#1585;&#1602;&#1577; &#1575;&#1604;&#1571;&#1606;&#1605;&#1575;&#1591;,</li><li><strong>css</strong> &#40;&#1575;&#1582;&#1578;&#1610;&#1575;&#1585;&#1610;&#41; &#1607;&#1608; &#58; &#1585;&#1605;&#1586; CSS &#1587;&#1575;&#1585;&#1610;&#1577; &#1575;&#1604;&#1605;&#1601;&#1593;&#1608;&#1604; &#1548; &#1605;&#1579;&#1604; <code style="color: green;">color: blue;</code> (&#1604;&#1605; &#1610;&#1578;&#1605; &#1575;&#1582;&#1578;&#1576;&#1575;&#1585; &#1582;&#1591;&#1571; &#1610;&#1605;&#1603;&#1606; &#1571;&#1606; &#1610;&#1578;&#1604;&#1601; javascript &#1575;&#1604;&#1605;&#1588;&#1603;&#1604; &#1608;&#1605;&#1606;&#1593; &#1575;&#1587;&#1578;&#1602;&#1576;&#1575;&#1604; &#1575;&#1604;&#1605;&#1581;&#1585;&#1585;).</li></ul><strong>&#1575;&#1604;&#1575;&#1607;&#1605;:</strong> &#1575;&#1606;&#1578; &#1576;&#1581;&#1575;&#1580;&#1577; &#1575;&#1604;&#1609; &#1605;&#1593;&#1585;&#1601;&#1577; &#1575;&#1604;&#1578;&#1604;&#1575;&#1593;&#1576; CSS &#1605;&#1606; &#1571;&#1580;&#1604; &#1578;&#1593;&#1583;&#1610;&#1604; &#1605;&#1590;&#1605;&#1608;&#1606; &#1607;&#1584;&#1607; &#1575;&#1604;&#1589;&#1601;&#1581;&#1577;.',
	'aide_syntaxe_modele' => '', /* missing (Les paramêtres des modèles spip sont normalement passés avec la syntaxe (spip) : <code><modele|param1=val1|param2=val2...></code>, si vous voulez que votre modèle utilise la syntaxe (html) : <code><modele param1=ˮval1ˮ param2=ˮval2ˮ...></code>, sélectionnez l'option <code>html</code>.) */
	'aide_vignette' => '', /* missing (Entrez ici la taille maximale des vignettes utilisées par CKEditor. Si vous laissez cette zone vide, les images seront affichées avec leur taille normale par CKEditor.) */
	'aide_webfonts' => '', /* missing (Entrez ici, une liste (séparée par des virgules) de noms de police <a target="_blank" onclick="window.open(this.href,'_blank','height=500,width=500,location=no,scrollbar=yes');return false;" href="http://code.google.com/webfonts">Google Web Fonts</a>. Les noms doivent être exactement ceux proposés dans le répertoire de polices de google.) */
	'apres' => '', /* missing (après) */
	'article' => '&#1605;&#1602;&#1575;&#1604;',
	'aucune_conversion' => '', /* missing (aucune conversion) */
	'aucun_document' => '', /* missing (Aucun document) */
	'aucun_document_descriptif' => '', /* missing (Vous n'avez encore téléchargé aucun document.) */
	'aucun_parametre' => '', /* missing (Ce modèle n'a aucun paramêtre.) */
	'aucun_plugin' => '', /* missing (SPIP ne détecte aucun plugin dans le dossier des plugins (<code>plugins/ckeditor/</code>), veuillez y décompresser les ZIP contenant des plugins pour CKEditor. Chaque plugin doit être dans un dossier individuel comme s'il était installé dans le dossier <code>plugins</code> de CKEditor.) */
	'autorisations_telechargement' => '', /* missing (Autorisations de téléchargement :) */
	'autoriser_autres_couleurs' => '', /* missing (autoriser d'autres couleurs) */
	'autoriser_insertion_documents' => '&nbsp;&#1575;&#1604;&#1587;&#1605;&#1575;&#1581; &#1576;&#1573;&#1583;&#1582;&#1575;&#1604; &#1608;&#1579;&#1575;&#1574;&#1602; &#1605;&#1606; &#1571;&#1610; &#1605;&#1602;&#1575;&#1604;.',
	'autoriser_liens_spip' => '&nbsp;&#1578;&#1587;&#1605;&#1581; &#1575;&#1604;&#1585;&#1608;&#1575;&#1576;&#1591; / &#1575;&#1604;&#1605;&#1585;&#1575;&#1587;&#1610; &#1606;&#1608;&#1593; SPIP.',
	'autoriser_mkdir' => '', /* missing (Autoriser à créer des répertoires@plus@.) */
	'autoriser_parcours_dossier_spip' => '', /* missing (Autoriser à parcourir le dossier de SPIP pour insérer des images.) */
	'autoriser_redacteurs' => '', /* missing (autoriser aussi les rédacteurs@plus@.) */
	'autoriser_telechargement_dossier_spip' => '', /* missing (Autoriser à télécharger des images dans le dossier de SPIP.) */
	'avant' => '', /* missing (avant) */
	'avertissement_css' => '', /* missing ({{⚠ Attention :}} vous devez savoir manipuler le CSS pour pouvoir modifier le contenu de cette page.) */

// B
	'balises_spip_autoriser' => ' &#1575;&#1604;&#1593;&#1604;&#1575;&#1605;&#1575;&#1578; &#1575;&#1604;&#1605;&#1587;&#1605;&#1608;&#1581; &#1576;&#1607;&#1575; &#1601;&#1610; CKEditor:',
	'balise_fermante' => '', /* missing (balise fermante) */

// C
	'changer_de_contexte' => '', /* missing (Contexte) */
	'choisir_correction_ortho' => '', /* missing (Si la langue n'est pas connue, choisir :) */
	'choisir_skin' => '&#1575;&#1582;&#1578;&#1610;&#1575;&#1585; &#1605;&#1592;&#1607;&#1585;:',
	'cisf' => '', /* missing (saisie facile (fonctionnalité expérimentale)) */
	'ckeditor_defaut' => 'CKEditor &#1575;&#1601;&#1578;&#1585;&#1575;&#1590;&#1610;&#1575;',
	'ckeditor_exclu' => 'CKEditor &#1601;&#1602;&#1591;',
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
	'ck_delete' => '', /* missing (Configuration réinitialisée.) */
	'ck_ko' => '', /* missing (Erreur dans les paramètres.) */
	'ck_ok' => '', /* missing (Configuration enregistrée.) */
	'class_des_formats' => '', /* missing (Classe CSS des éléments insérés par le menu déroulants formats :) */
	'class_partie_privee' => '', /* missing (classe CSS :) */
	'class_partie_publique' => '', /* missing (classe CSS :) */
	'configuration_des_couleurs' => '', /* missing (Configuration des couleurs :) */
	'configuration_des_modeles_spip' => '', /* missing (Liste de modèles) */
	'configuration_des_polices' => '', /* missing (Configuration des polices :) */
	'configuration_formats' => '', /* missing (Configuration des « formats » CKEditor) */
	'configuration_modeles' => '', /* missing (Gestion des modèles :) */
	'confirmer_supprimer_modele' => '', /* missing (Êtes vous sur de vouloir supprimer ce modèle ?) */
	'confirme_reinitialiser_plugin' => '', /* missing (Êtes vous sur de vouloir réinitialiser le plugin ? (Cela supprimera toutes vos préférences)) */
	'contenu_du_modele' => '', /* missing (Contenu du modèle :) */
	'conversion_partielle_vers_spip' => '', /* missing (conversion partielle en typographie SPIP) */
	'copie_impossible' => '', /* missing (<p>Impossible de copier <code>@fichier@</code></p><blockquote>@errmsg@</blockquote>) */
	'copie_reussie' => '', /* missing (Le fichier <code>@fichier@</code> a été correctement copié.) */
	'css_site' => '&#1571;&#1608;&#1585;&#1575;&#1602; &#1575;&#1604;&#1571;&#1606;&#1605;&#1575;&#1591; &#1604;&#1604;&#1605;&#1608;&#1602;&#1593; (CSSs):',

// D
	'demarrer_correction_ortho' => '&#1576;&#1583;&#1569; &#1575;&#1604;&#1578;&#1583;&#1602;&#1610;&#1602; &#1575;&#1604;&#1573;&#1605;&#1604;&#1575;&#1574;&#1610;:',
	'desactive_car_zappe_par_html2spip' => '', /* missing (Désactivé car zappé par HTML2SPIP.) */
	'description' => '', /* missing (Description :) */
	'documents_article' => '&#1575;&#1604;&#1605;&#1587;&#1578;&#1606;&#1583;&#1575;&#1578; &#1605;&#1606; &#1607;&#1584;&#1575; &#1575; &#1575;&#1604;&#1605;&#1602;&#1575;&#1604; &#1602;&#1610;&#1583; &#1575;&#1604;&#1578;&#1581;&#1585;&#1610;&#1585;.',
	'documents_rubrique' => '', /* missing (Afficher uniquement les documents de la rubrique en cours d'édition.) */

// E
	'echappe_caracteres' => '', /* missing (Caractères protégés dans ce modèle :) */
	'editer_modele' => '', /* missing (Éditer le modèle) */
	'edite_modele' => '', /* missing (Édite) */
	'edition_du_modele' => '', /* missing (Édition du modèle : [ @MODELE@ ]) */
	'enregistrer_modele' => '', /* missing (Enregister le modèle) */
	'entermode' => 'Enter &#1610;&#1589;&#1576;&#1581;:',
	'enter_br' => '&#1582;&#1591; &#1601;&#1575;&#1589;&#1604; (br)',
	'enter_div' => '&#1601;&#1602;&#1585;&#1577; (div)',
	'enter_p' => '&#1601;&#1602;&#1585;&#1577; (p)',
	'erreur_droits' => '', /* missing (erreur de droits sur le répertoire de destination) */
	'erreur_transmission' => '', /* missing (erreur de transmission) */
	'err_conversion' => '', /* missing (<p>Problème de converions. Aucune conversion.</p>) */
	'err_filesdir_pas_de_sousrep' => '', /* missing (Fichiers Entrez un nom de sous répertoire sans /) */
	'err_files_extensions_autorisees' => '', /* missing (Pour les extensions autorisées pour les fichiers, respectez le format <code>ext<sub>1</sub> ; ext<sub>2</sub> ; ... ; ext<sub>n</sub></code>.) */
	'err_flashdir_pas_de_sousrep' => '', /* missing (Flash Entrez un nom de sous répertoire sans /) */
	'err_flash_extensions_autorisees' => '', /* missing (Pour les extensions autorisées pour les documents Flash, respectez le format <code>ext<sub>1</sub> ; ext<sub>2</sub> ; ... ; ext<sub>n</sub></code>.) */
	'err_images_extensions_autorisees' => '', /* missing (Pour les extensions autorisées pour les images, respectez le format <code>ext<sub>1</sub> ; ext<sub>2</sub> ; ... ; ext<sub>n</sub></code>.) */
	'err_imgdir_pas_de_sousrep' => '', /* missing (Images Entrez un nom de sous répertoire sans /) */
	'err_la_largeur_ecran_etroit_doit_etre_numerique' => '', /* missing (La largeur de l'écran (étroit) doit être un nombre.) */
	'err_la_largeur_ecran_large_doit_etre_numerique' => '', /* missing (La largeur de l'écran (large) doit être un nombre.) */
	'err_mauvaise_class_pour_formats' => '', /* missing (Vous n'avez pas entré un nom de classe CSS valide.) */
	'err_mauvaise_liste_de_formats' => '', /* missing (Respectez la syntaxe des formats.) */
	'err_mauvaise_syntaxe_de_tag' => '', /* missing (Respectez la syntaxe des <code>tags</code> SPIP à autoriser.) */
	'err_mauvais_langage' => '', /* missing (Le langage doit être un code à 2 lettres.) */
	'err_mauvais_mode_entree' => '', /* missing (Le mode <code>ENTREE</code> doit respecter une syntaxe précise.) */
	'err_mauvais_mode_shiftentree' => '', /* missing (Le mode <code>SHIFT+ENTREE</code> doit respecter une syntaxe précise.) */
	'err_repertoire_parent_interdit' => '', /* missing (Il est interdit de rémonter dans l'arboressence pour le répertoire parent.) */
	'etroit' => '&#1575;&#1604;&#1588;&#1575;&#1588;&#1577; &#1575;&#1604;&#1590;&#1610;&#1602;&#1577;: ',
	'explique_div' => '<em>div</em> : &#1601;&#1610; &#1607;&#1584;&#1575; &#1575;&#1604;&#1608;&#1590;&#1593; &#1548; &#1575;&#1604;&#1590;&#1594;&#1591; &#1593;&#1604;&#1609; [Enter] &#1610;&#1578;&#1587;&#1576;&#1576; &#1601;&#1610; &#1575;&#1583;&#1585;&#1575;&#1580; &#1603;&#1578;&#1604;&#1577; html &lt;div&gt;&#1548; &#1575;&#1604;&#1605;&#1610;&#1586;&#1577;&#58; &#1571;&#1606;&#1607; &#1610;&#1571;&#1582;&#1584; &#1601;&#1610; &#1575;&#1604;&#1575;&#1593;&#1578;&#1576;&#1575;&#1585; &#1605;&#1593;&#1604;&#1608;&#1605;&#1575;&#1578; &#1575;&#1604;&#1578;&#1582;&#1591;&#1610;&#1591; &#40;&#1575;&#1604;&#1605;&#1581;&#1575;&#1584;&#1575;&#1577; &#1548; &#1575;&#1604;&#1582;&#46;&#46;&#46;&#41; &#1605;&#1606; &#1607;&#1584;&#1607; &#1575;&#1604;&#1603;&#1578;&#1604; &#1548; &#1575;&#1604;&#1593;&#1610;&#1576; &#58;&#1604;&#1575; &#1610;&#1578;&#1576;&#1593; &#1578;&#1589;&#1605;&#1610;&#1605; &#1605;&#1608;&#1581;&#1583; &#1605;&#1606; SPIP &#1548; &#1608;&#1604;&#1603;&#1606; &#1605;&#1606; &#1575;&#1604;&#1605;&#1605;&#1603;&#1606; &#1575;&#1604;&#1602;&#1610;&#1575;&#1605; &#1576;&#1584;&#1604;&#1603; &#1605;&#1606; &#1582;&#1604;&#1575;&#1604; &#1578;&#1581;&#1583;&#1610;&#1583; &#1608;&#1585;&#1602;&#1577; &#1575;&#1604;&#1571;&#1606;&#1605;&#1575;&#1591; &#1575;&#1604;&#1587;&#1604;&#1610;&#1605;&#1577;.',
	'explique_p' => '<em>p</em> : &#1601;&#1610; &#1607;&#1584;&#1575; &#1575;&#1604;&#1608;&#1590;&#1593; &#1548; &#1575;&#1604;&#1590;&#1594;&#1591; &#1593;&#1604;&#1609; [Enter] &#1610;&#1578;&#1587;&#1576;&#1576; &#1601;&#1610; &#1575;&#1583;&#1585;&#1575;&#1580; &#1603;&#1578;&#1604;&#1577; html &lt;p&gt;&#1548; &#1575;&#1604;&#1605;&#1610;&#1586;&#1577;&#58; &#58; &#1571;&#1606;&#1607;&#1575; &#1578;&#1581;&#1578;&#1585;&#1605; &#1578;&#1582;&#1591;&#1610;&#1591; SPIP&#1548; &#1575;&#1604;&#1593;&#1610;&#1576; &#58; SPIP &#1604;&#1575; &#1578;&#1578;&#1590;&#1605;&#1606; &#1605;&#1593;&#1604;&#1608;&#1605;&#1575;&#1578; &#1578;&#1582;&#1591;&#1610;&#1591; &#40;&#1575;&#1604;&#1605;&#1581;&#1575;&#1584;&#1575;&#1577; &#1548; &#1575;&#1604;&#1582;&#46;&#46;&#46;&#41; &#1605;&#1606; &#1607;&#1584;&#1607; &#1575;&#1604;&#1603;&#1578;&#1604;.',
	'explorateur_titre' => '', /* missing (Explorateur de fichier pour CKeditor-SPIP-plugin) */
	'extensions_autorisees_descriptif' => '', /* missing (Entrez les extensions séparées par des « ; ».) */

// F
	'files_extensions_autorisees' => '', /* missing (Liste des extensions autorisées :) */
	'fin' => '', /* missing (fin) */
	'flash_extensions_autorisees' => '', /* missing (Liste des extensions autorisées :) */
	'forcer_copie_comme_texte' => '', /* missing (Forcer la copie « comme texte » en provenance du presse papier.) */
	'formats' => '&#1602;&#1575;&#1574;&#1605;&#1577; &#1575;&#1604;&#1593;&#1604;&#1575;&#1605;&#1575;&#1578; HTML &#1575;&#1604;&#1605;&#1608;&#1580;&#1608;&#1583;&#1577; &#1601;&#1610; "&#1587;&#1585;&#1583; &#1575;&#1604;&#1571;&#1606;&#1605;&#1575;&#1591;":',

// H
	'hauteur_editeur' => '&#1575;&#1585;&#1578;&#1601;&#1575;&#1593; &#1605;&#1581;&#1585;&#1585;:',
	'html2spip_detecte' => '', /* missing (HTML2SPIP est présent.) */
	'html2spip_identite' => '', /* missing (Balises HTML que HTML2SPIP doit laisser intouchées :) */
	'html2spip_limite' => '', /* missing (N'utiliser que les options de CKEditor compatibles avec les raccourcis typographiques SPIP) */

// I
	'identifiant_du_parametre' => '', /* missing (Identifiant du paramètre :) */
	'ignore_mauvaise_version' => '', /* missing (Ne pas tenir compte de la version de CKeditor installée) */
	'images_extensions_autorisees' => '', /* missing (Liste des extensions autorisées :) */
	'image_du_modele' => '', /* missing (Image du modèle :) */
	'info_bulle' => '', /* missing (Description du modèle :) */
	'inserer_la_css_privee' => '', /* missing (Insérer les CSS dans la partie privée.) */
	'inserer_la_css_public' => '', /* missing (Insérer les CSS dans la partie publique.) */
	'intitule_du_modele' => '', /* missing (Intitulé dans la liste déroulante :) */

// K
	'kcfinder_ignore' => '', /* missing ( (option ignorée par KCFinder)) */

// L
	'label_article' => '', /* missing (Article) */
	'label_auteur' => '', /* missing (Auteur) */
	'label_breve' => '', /* missing (Brêve) */
	'label_du_parametre' => '', /* missing (Label du paramètre :) */
	'label_groupe_mots' => '', /* missing (Groupe de mots clés) */
	'label_mot' => '', /* missing (Mot clé) */
	'label_section' => '', /* missing (Rubrique) */
	'langue_ckeditor' => '&#1604;&#1594;&#1577; CKEditor:',
	'large' => '&#1575;&#1604;&#1588;&#1575;&#1588;&#1577; &#1575;&#1604;&#1593;&#1585;&#1610;&#1590;&#1577;',
	'les_crayons' => '', /* missing (les crayons (fonctionnalité expérimentale)) */
	'les_forums' => '', /* missing (les forums (fonctionnalité expérimentale)) */
	'listes_des_couleurs_presentees' => '', /* missing (Liste des couleurs présentées :) */
	'liste_des_parametres' => '', /* missing (Liste des paramètres) */
	'liste_de_contextes' => '', /* missing (Liste de contextes :) */
	'liste_google_webfonts' => '', /* missing (Liste des polices Google Web Fonts à inclure :) */
	'liste_plugins_ckeditor' => '', /* missing (Liste des plugins pour CKEditor) */
	'liste_telechargements_autorises' => '', /* missing (<p>Les types de fichiers autorisés sont :</p><blockquote>@liste@</blockquote>) */
	'loading' => '', /* missing (Chargement) */

// M
	'modeles_spip_autorisees' => '', /* missing (Nom du modèle :) */
	'modele_a_droite' => '', /* missing (à droite) */
	'modele_a_gauche' => '', /* missing (à gauche) */
	'modele_centre' => '', /* missing (centré) */
	'modele_commence_pas_par_slash' => '', /* missing (Un nom de modèle ne doit pas commencer par un slash &laquo;/&raquo;) */
	'modele_cree' => '', /* missing (Le modèle <strong>@modele@</strong> est créé.) */
	'modele_document' => '', /* missing (Document) */
	'modele_enregistre' => '', /* missing (Le modèle <strong>@modele@</strong> est enregistré.) */
	'modele_image' => '', /* missing (Image) */
	'modele_incorpore' => '', /* missing (Incorporé) */
	'modele_supprime' => '', /* missing (Modèle <strong>@modele@</strong> supprimé.) */
	'mode_edition_defaut' => '&#1608;&#1590;&#1593; &#1575;&#1604;&#1578;&#1581;&#1585;&#1610;&#1585; &#1575;&#1601;&#1578;&#1585;&#1575;&#1590;&#1610;&#1575;:',
	'modification_de_modele' => '', /* missing (Modification du modèle SPIP.) */

// N
	'nom_du_bouton' => '', /* missing (Nom du bouton :) */
	'nom_du_nouveau_modele' => '', /* missing (Entrez le nom du nouveau modèle :) */
	'nom_nouveau_modele' => '', /* missing (Nom du modèle :) */
	'nom_repertoire_creer' => '', /* missing (Nom du répertoire à créer) */
	'normalement_detectee' => '&#1593;&#1575;&#1583;&#1577; &#1575;&#1604;&#1603;&#1588;&#1601; &#1593;&#1606; &#1607;&#1584;&#1607;  &#1575;&#1604;&#1602;&#1610;&#1605;&#1577; &#1610;&#1588;&#1594;&#1604; &#1571;&#1608;&#1578;&#1608;&#1605;&#1575;&#1578;&#1610;&#1603;&#1610;&#1575; .',
	'nouveau' => '', /* missing (Nouveau) */
	'nouveau_modele' => '', /* missing (Nouveau modèle) */
	'nouveau_modele_sans_nom' => '', /* missing (Nouveau modèle sans nom.) */
	'nouveau_parametre' => '', /* missing (Nouveau paramètre) */
	'numerique_facultatif' => '', /* missing (numérique (facultatif)) */
	'numerique_obligatoire' => '', /* missing (numérique) */
	'num_article' => '', /* missing (un article) */
	'num_auteur' => '', /* missing (un auteur) */
	'num_breve' => '', /* missing (une brêve) */
	'num_document' => '', /* missing (un document) */
	'num_groupe_mots' => '', /* missing (un groupe de mots) */
	'num_indefini' => '', /* missing (autre) */
	'num_mot' => '', /* missing (un mot) */
	'num_rubrique' => '', /* missing (une rubrique) */
	'num_site' => '', /* missing (un site) */

// O
	'objet_en_edition' => '', /* missing (l'objet en cours d'édition) */
	'ok' => '', /* missing (Ok) */
	'options_cisf' => '', /* missing (Options Saisie Facile) */
	'options_conversion' => '', /* missing (Options de conversion) */
	'options_crayons' => '', /* missing (Options pour les crayons) */
	'options_css' => '', /* missing (Options CSS :) */
	'options_developpeur' => '', /* missing (Options développeur) */
	'options_editeur' => '', /* missing (Option de l'éditeur) */
	'options_forums' => '', /* missing (Options pour les forums) */
	'options_gui' => '', /* missing (Option de l'interface) */
	'options_html2spip' => '', /* missing (Mode de conversion :) */
	'options_orthographe' => '', /* missing (Options de correction orthographique) */
	'options_privee' => '', /* missing (Options pour la partie privée) */
	'options_publique' => '', /* missing (Options pour la partie publique) */
	'options_spip' => '&#1582;&#1610;&#1575;&#1585;&#1575;&#1578; SPIP:',
	'options_vignettes' => '', /* missing (Options pour les vignettes) */
	'ordre_du_bouton' => '', /* missing (Ordre du bouton :) */

// P
	'parametre_nomme' => '', /* missing (Paramètre : @PARAMETRE@) */
	'partie_privee' => '', /* missing (les formulaires en partie privée) */
	'partie_publique' => '', /* missing (les formulaires en partie publique) */
	'pas_numerique' => '', /* missing (non numérique) */
	'plugins_barre_position' => '', /* missing (Position des boutons :) */
	'plugin_active' => '', /* missing (activer le plugin) */
	'plugin_bouton' => '', /* missing (activer le bouton) */

// R
	'reinitialiser' => '', /* missing (Réinitialiser) */
	'reinitialiser_le_plugin' => '', /* missing (Réinitialiser le plugin) */
	'repertoires_telechargement' => '', /* missing (Répertoires de téléchargement :) */
	'repertoire_des_fichiers' => '', /* missing (Répertoire des fichiers:) */
	'repertoire_des_flash' => '', /* missing (Répertoire des documents Flash®:) */
	'repertoire_des_images' => '', /* missing (Répertoire des images :) */
	'repertoire_de_base' => '', /* missing (Répertoire de base des téléchargements :) */
	'repertoire_parent' => '', /* missing (Accéder au répertoire parent.) */
	'reset_toolbars' => '', /* missing (Réinitialise les barres d'outils) */
	'retour' => '', /* missing (Retour) */
	'rubrique' => '', /* missing (rubrique) */

// S
	'sans_contexte' => '', /* missing (sans contexte) */
	'selecteurs_espace_prive' => '', /* missing (Sélecteurs jQuery pour l'espace privé :) */
	'selecteurs_espace_public' => '', /* missing (Sélecteurs jQuery pour l'espace publique :) */
	'selection_aucun' => '&#1604;&#1575; &#1575;&#1582;&#1578;&#1610;&#1575;&#1585;',
	'selection_document_spip' => '&#1575;&#1582;&#1578;&#1610;&#1575;&#1585; &#1605;&#1587;&#1578;&#1606;&#1583; SPIP',
	'selection_inverse' => '&#1575;&#1582;&#1578;&#1610;&#1575;&#1585; &#1605;&#1593;&#1603;&#1608;&#1587;',
	'selection_tout' => '&#1575;&#1582;&#1578;&#1610;&#1575;&#1585; &#1603;&#1604; &#1605;&#1606;',
	'shiftentermode' => 'Shift + Enter &#1610;&#1589;&#1576;&#1581;:',
	'spipification' => '&#1581;&#1602;&#1608;&#1602; &#1575;&#1604;&#1578;&#1571;&#1604;&#1610;&#1601; &#1608;&#1575;&#1604;&#1606;&#1588;&#1585; &copy; 2009 <a <a style="text-decoration:underline;color:blue;cursor:pointer;" href="http://code.google.com/p/ckeditor-spip-plugin/">Plugin SPIP</a> - Fr&#233;d&#233;ric Bonnaud, Mehdi Cherifi, Emmanuel Dreyfus',
	'spip_defaut' => 'SPIP &#1575;&#1601;&#1578;&#1585;&#1575;&#1590;&#1610;&#1575;',
	'styles' => '&#1575;&#1604;&#1571;&#1606;&#1605;&#1575;&#1591;:',
	'supprimer' => '', /* missing (Supprimer) */
	'supprimer_ce_modele' => '', /* missing (Supprimer ce modèle) */
	'supprimer_ce_parametre' => '', /* missing (Supprimer ce paramètre ?) */
	'supprimer_modele' => '', /* missing (Supprimer le modèle) */
	'supprime_ce_parametre' => '', /* missing (Supprime ce paramètre) */
	'supprime_modele' => '', /* missing (Supprime) */
	'syntaxe' => '', /* missing (Syntaxe des paramètres :) */

// T
	'tailles_de_police' => '', /* missing (Taille des polices :) */
	'taille_maximale_telechargements' => '', /* missing (<p>La taille maximale autorisée d'un fichier est de @taille@Mo.</p>) */
	'tb_basic' => '', /* missing (basique) */
	'tb_full' => '', /* missing (complète) */
	'telecharger' => '', /* missing (Télécharger) */
	'telecharger_document' => '', /* missing (Télécharger un document) */
	'toolbar' => '', /* missing (barre d'outils :) */
	'tool_About' => '', /* missing (&#192; propos) */
	'tool_Anchor' => '', /* missing (Ancre) */
	'tool_BGColor' => '', /* missing (Couleur du fond) */
	'tool_Blockquote' => '', /* missing (Citation) */
	'tool_Bold' => '', /* missing (Gras) */
	'tool_BulletedList' => '', /* missing (Liste à puces) */
	'tool_Button' => '', /* missing (Bouton) */
	'tool_Checkbox' => '', /* missing (Boite à cocher) */
	'tool_Copy' => '', /* missing (Copier) */
	'tool_Cut' => '', /* missing (Couper) */
	'tool_Find' => '', /* missing (Chercher) */
	'tool_Flash' => '', /* missing (Flash) */
	'tool_Font' => '', /* missing (Polices) */
	'tool_FontSize' => '', /* missing (Taille de police) */
	'tool_Form' => '', /* missing (Formulaire) */
	'tool_Format' => '', /* missing (Formats) */
	'tool_HiddenField' => '', /* missing (Champ caché) */
	'tool_HorizontalRule' => '', /* missing (Trait horizontal) */
	'tool_Iframe' => '', /* missing (IFrame) */
	'tool_Image' => '', /* missing (Image) */
	'tool_ImageButton' => '', /* missing (Bouton avec image) */
	'tool_Indent' => '', /* missing (Indenter) */
	'tool_Italic' => '', /* missing (Italique) */
	'tool_JustifyBlock' => '', /* missing (Justifier) */
	'tool_JustifyCenter' => '', /* missing (Centrer) */
	'tool_JustifyLeft' => '', /* missing (Aligner à gauche) */
	'tool_JustifyRight' => '', /* missing (Aligner à droite) */
	'tool_Link' => '', /* missing (Lien) */
	'tool_Maximize' => '', /* missing (Maximiser) */
	'tool_NewPage' => '', /* missing (Nouvelle page) */
	'tool_NumberedList' => '', /* missing (Liste numérotée) */
	'tool_Outdent' => '', /* missing (Désindenter) */
	'tool_PageBreak' => '', /* missing (Saut de page) */
	'tool_Paste' => '', /* missing (Coller) */
	'tool_PasteFromWord' => '', /* missing (Coller depuis Word) */
	'tool_PasteText' => '', /* missing (Coller comme texte) */
	'tool_Print' => '', /* missing (Imprimer) */
	'tool_Radio' => '', /* missing (Bouton radio) */
	'tool_Redo' => '', /* missing (Refaire) */
	'tool_RemoveFormat' => '', /* missing (Enlever les formats) */
	'tool_Replace' => '', /* missing (Remplacer) */
	'tool_Scayt' => '', /* missing (Correction durant la frappe) */
	'tool_Select' => '', /* missing (Boite à sélectionner) */
	'tool_SelectAll' => '', /* missing (Tout sélectionner) */
	'tool_ShowBlocks' => '', /* missing (Montrer les blocks) */
	'tool_Smiley' => '', /* missing (Émoticône) */
	'tool_Source' => '', /* missing (Source) */
	'tool_SpecialChar' => '', /* missing (Caractère spécial) */
	'tool_SpellChecker' => '', /* missing (Correcteur orthographique) */
	'tool_Spip' => '', /* missing (Lien Spip) */
	'tool_SpipDoc' => '', /* missing (Document Spip) */
	'tool_SpipModeles' => '', /* missing (Modèle Spip) */
	'tool_SpipSave' => '', /* missing (Sauver) */
	'tool_Strike' => '', /* missing (Barré) */
	'tool_Styles' => '', /* missing (Styles) */
	'tool_Subscript' => '', /* missing (Indice) */
	'tool_Superscript' => '', /* missing (Exposant) */
	'tool_Table' => '', /* missing (Tableau) */
	'tool_Templates' => '', /* missing (Modèles) */
	'tool_Textarea' => '', /* missing (Zone texte) */
	'tool_TextColor' => '', /* missing (Couleur du texte) */
	'tool_TextField' => '', /* missing (Champ texte) */
	'tool_Underline' => '', /* missing (Souligné) */
	'tool_Undo' => '', /* missing (Défaire) */
	'tool_Unlink' => '', /* missing (Supprimer le lien) */
	'tool_ZpipPreview' => '', /* missing (Aperçu) */
	'tous' => '&#1575;&#1604;&#1603;&#1604;',
	'tous_documents' => '&#1580;&#1605;&#1610;&#1593; &#1575;&#1604;&#1605;&#1587;&#1578;&#1606;&#1583;&#1575;&#1578; &#1575;&#1604;&#1605;&#1578;&#1608;&#1601;&#1585;&#1577; &#1601;&#1610; SPIP.',
	'type_article' => '', /* missing (Article) */
	'type_auteur' => '', /* missing (Auteur) */
	'type_breve' => '', /* missing (Brêve) */
	'type_de_modele' => '', /* missing (Type de modèle :) */
	'type_fichier_interdit' => '', /* missing (type de fichier interdit) */
	'type_groupemot' => '', /* missing (Groupe de mots clés) */
	'type_mot' => '', /* missing (Mot clé) */
	'type_nombre' => '', /* missing (La valeur numérique représente :) */
	'type_section' => '', /* missing (Rubrique) */

// U
	'url_site' => '&#1593;&#1606;&#1608;&#1575;&#1606; &#1575;&#1604;&#1605;&#1608;&#1602;&#1593;:',
	'use_ckeditor' => '&#1575;&#1587;&#1578;&#1582;&#1583;&#1575;&#1605; CKEditor',
	'use_spip_editor' => '&#1575;&#1587;&#1578;&#1582;&#1583;&#1575;&#1605; &#1605;&#1581;&#1585;&#1585; SPIP',
	'utiliser_ckeditor_avec' => '', /* missing (Utiliser CKeditor avec :) */
	'utiliser_fichier' => '', /* missing (Utiliser le fichier @fichier@.) */
	'utiliser_html2spip' => '', /* missing (conversion complète en typographie SPIP) */
	'utiliser_html2spip_descriptif' => '', /* missing (Il existe 3 modes de conversion :
-* {{aucune}} : aucune conversion n'est faite, le code produit par CKEditor est stocké; tel quel dans la base de données,
-* {{partielle}} : les tags HTML qui peuvent être traduits directement, ainsi que les images sont traduits en typographie SPIP,
-* {{complête}} : aucun tag HTML n'est préservé. Tout est traduit en typographie SPIP. Cette option utilise la librairie HTML2SPIP pour reconvertir le HTML en typographie SPIP. En activant cette option, vous pouvez utiliser ckeditor tout en préservant l'usage de la typographie SPIP dans vos articles.) */
	'utiliser_une_vignette_pour_les_images' => '', /* missing (Utiliser des vignettes de :) */
	'utilise_fontkit' => '', /* missing (Utiliser les kits de polices Font Squirrel.) */
	'utilise_upload' => '', /* missing (Autoriser le téléchargement depuis les dialogues de CKEDITOR.) */

// V
	'valeurs_du_parametre' => '', /* missing (Valeurs possibles pour ce paramètre :) */
	'version_preferee' => 'CKEditor &#1575;&#1604;&#1606;&#1587;&#1582;&#1577; %1 &#1605;&#1579;&#1576;&#1577;&#1548; &#1607;&#1584;&#1575; &#1575;&#1604;&#1576;&#1585;&#1606;&#1575;&#1605;&#1580; &#1575;&#1604;&#1605;&#1587;&#1575;&#1593;&#1583; &#1610;&#1601;&#1590;&#1604; &#1575;&#1604;&#1606;&#1587;&#1582;&#1577; %2 &#1610;&#1585;&#1580;&#1609; &#1573;&#1604;&#1594;&#1575;&#1569; &#1607;&#1584;&#1607; &#1575;&#1604;&#1606;&#1587;&#1582;&#1577; &#1571;&#1608;&#1604;&#1575; .',

// Chaines probablement pas utilisée :
/* On les garde au cas où ...
*/
);
?>
