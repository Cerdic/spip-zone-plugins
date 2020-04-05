<?php
/**
 * PhotoSPIP
 * Modification d'images dans SPIP
 *
 * Auteurs :
 * kent1 (kent1@arscenic.info -  http://www.kent1.info)
 *
 * © 2008-2012 - Distribue sous licence GNU/GPL
 * Pour plus de details voir le fichier COPYING.txt
 *
 */
 
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(
	// B
	'bouton_creer_vignette' => 'Créer une vignette à partir de ce document',
	'bouton_editer_image' => 'Éditer l\'image',
	'bouton_editer_vignette' => 'Éditer la vignette',
	'bouton_modifier_document' => 'Modifier les informations du document',
	'bouton_revenir_version' => 'Revenir à cette version',
	'bouton_supprimer_previsu' => 'Revenir à la version sans prévisualisation',
	'bouton_supprimer_version' => 'Supprimer cette version',
	'bouton_supprimer_vignette' => 'Supprimer cette vignette',
	'bouton_supprimer_vignette_document' => 'Supprimer la vignette de ce document',
	'bouton_tester' => 'Prévisualiser',
	'bouton_valider' => 'Appliquer',
	'bouton_valider_continuer' => 'Valider et continuer',
	'bouton_valider_fermer' => 'Valider et revenir à la page précédente',
	
	// E
	'erreur_auth_modifier' => 'Vous n\'êtes pas autoriser à modifier ce document.',
	'erreur_choisir_version' => 'Choisissez une version',
	'erreur_doc_numero' => 'Vous devez indiquer un identifiant de document existant.',
	'erreur_form_filtre' => 'Veuillez indiquer un filtre à appliquer.',
	'erreur_form_filtre_sstest' => 'Le filtre que vous avez essayé ne permet pas d\'&ecirc;tre testé. Vous ne pouvez que l\'appliquer.',
	'erreur_form_filtre_valeur_obligatoire' => 'Vous devez choisir une valeur.',
	'erreur_form_type_resultat' => 'Vous devez choisir le type de résultat',
	'erreur_image_process' => 'Le site n\'utilise pas GD2 pour gérer les images, veuillez l\'utiliser pour leur traitement.',
	'erreur_nb_versions_atteint' => 'Le nombre de versions différentes de l\'image a été atteint (@nb@). Vous ne pouvez que tester et non appliquer les modifications.',
	'erreur_previsu' => 'Si le résultat vous satisfait vous pouvez le valider en bas du formulaire, sinon vous pouvez tester d\'autres filtres.',
	'erreur_sansconf' => 'Vous n\'avez pas configuré votre filtre.',
	'erreur_sansfiltre' => 'Vous n\'avez pas sélectionné de filtre.',
	'erreur_sanstest' => 'Le filtre que vous avez essayé ne permet pas d\'&ecirc;tre testé. Vous ne pouvez que l\'appliquer.',
	'erreur_selectionner_au_moins_une_valeur' => 'Vous devez sélectionner au moins une valeur',
	'erreur_valeur_numerique' => 'Ce filtre nécessite une valeur numérique comme paramètre',
	'erreur_valeurs_numeriques' => 'Ce filtre nécessite des valeurs numériques',
	'explication_image_flip_horizontal' => 'Appliquer un effet de « miroir » selon un axe horizontal (bas<->haut). Aucun réglage nécessaire.',
	'explication_image_flip_vertical' => 'Appliquer un effet de « miroir » selon un axe vertical (gauche<->droite). Aucun réglage nécessaire.',	
	'explication_image_flou' => 'Le filtre image_flou rend l\'image... floue. On peut lui passer en paramètre un nombre compris entre 1 et 11, définissant l’intensité du flou (de 1 à 11 pixels de flou).',
	'explication_image_gamma' => 'Le filtre Gamma change la luminosité d\'une image.<br />Il rend une image plus claire ou plus foncée.<br />Son paramètre est compris entre -254 et 254. Les valeurs supérieures à zéro rendent l\'image plus claire (254 rend toute l\'image entièrement blanche); les valeurs négatives rendent l\'image plus foncée (-254 rend l\'image complètement noire).',
	'explication_image_nb' => 'Transforme l\'image en noir et blanc',
	'explication_image_niveau_de_gris_auto' => 'Correction automatique des niveaux de l\'image.<br />(Ne nécessite pas de paramètres).',
	'explication_image_passe_partout' => 'Ce filtre réduira la taille de l\'image au minimum la faisant entrer dans un cadre des largeurs et hauteurs fournies.',
	'explication_image_recadre' => 'Recadre l\'image en fonction de la sélection de l\'utilisateur.',
	'explication_image_reduire' => 'Ce filtre réduira la taille de l\'image proportionnellement en fonction de la hauteur et de la largeur fournie.',
	'explication_image_saturation_desaturation' => 'Ce filtre va saturer ou désaturer les couleurs d’une image.<br />La luminosité de l’image et le contraste sont inchangés<br />Dans le premier cas, la couleur est « affadie » ; l’effet, utilisé subtilement, donnera des tonalités de photographie vieillie...<br />Dans l’autre cas, à l’inverse, le même filtre pourra «doper» les couleurs.',
	'explication_image_sincity' => 'Ce filtre donne un aspect "Sin City" (Ne nécessite aucun réglage).<br />Il exécute une désaturation contrastée et une accentuation du rouge.',
	'explication_image_rotation' => 'Fait tourner l’image d’un angle égal au paramètre passé. Les valeurs positives sont dans le sens des aiguilles d’une montre et inversement.<br />Attention : ce filtre modifie les dimensions de l’image.',
	'explication_resultats' => 'Lors de la validation de la modification d\'images, trois types de résultats sont possibles.',
	'explication_resultats_defaut' => 'Valeur par défaut sélectionnée lors du chargement du formulaire.',
	'explication_tourner' => 'Appliquer une rotation de 90, 180 ou 270 degrés à l\'image<br />Ce filtre ne peut &ecirc;tre testé, il ne peut &ecirc;tre qu\'appliqué.',

	// I
	'info_modifier_image' => 'Éditer l\'image',
	'info_modifier_vignette' => 'Modifier la vignette du document #@id_document@',
	'info_nb_versions_une' => 'Une version',
	'info_nb_versions' => '@nb@ versions',
	
	// L
	'label_angle_rotation' => 'Angle de rotation&nbsp:',
	'label_choisir_filtres' => 'Choisissez les filtres à activer',
	'label_compression_rendu' => 'Qualité de compression du rendu (en %, 85 par défaut) :',
	'label_couleur_sepia' => 'Couleur&nbsp;:',
	'label_hauteur_previsu' => 'Hauteur maximale de la prévisualisation en px (450 par défaut) : ',
	'label_image_aplatir' => 'Aplatir l\'image',
	'label_image_flip_vertical' => 'Flip Vertical de l\'image',
	'label_image_flip_horizontal' => 'Flip Horizontal de l\'image',
	'label_image_flou' => 'Filtre Flou',
	'label_image_gamma' => 'Filtre Gamma',
	'label_image_nb' => 'Filtre Noir et Blanc',
	'label_image_niveau_de_gris_auto' => 'Niveaux automatiques',
	'label_image_passe_partout' => 'Réduire l\'image (passe-partout)',
	'label_image_recadre' => 'Recadrer l\'image',
	'label_image_reduire' => 'Réduire l\'image',
	'label_image_rotation' => 'Rotation manuelle de l\'image',
	'label_image_saturation_desaturation' => 'Filtre [Dé-]saturation',
	'label_image_sepia' => 'Filtre Sepia',
	'label_image_sincity' => 'Filtre Sin City',
	'label_largeur_previsu' => 'Largeur maximale de la prévisualisation en px (450 par défaut) : ',
	'label_limiter_version' => 'Limiter le nombre de versions possibles à :',
	'label_modif_creer_nouvelle_image' => 'Un nouveau document sera créé à partir de l\'image originale',
	'label_modif_creer_version_image' => 'L\'image originale sera enregistrée en tant que version du nouveau document qui la remplacera dans le site',
	'label_modif_remplacer_image' => 'L\'image originale sera simplement remplacée',
	'label_modif_vignette_creer_version_image' => 'La vignette originale sera enregistrée en tant que version de la nouvelle vignette qui la remplacera dans le site',
	'label_modif_vignette_remplacer_image' => 'La vignette originale sera simplement remplacée',
	'label_niveau_flou' => 'Niveau de flou&nbsp;:',
	'label_niveau_gamma' => 'Niveau Gamma&nbsp;:',
	'label_niveau_saturation_desaturation' => 'Niveau de saturation&nbsp;:',
	'label_ratio' => 'Ratio de la sélection&nbsp;:',
	'label_ratio_libre' => 'Libre',
	'label_reduire_height' => 'Hauteur (en px)&nbsp;:',
	'label_reduire_width' => 'Largeur (en px)&nbsp;:',
	'label_recadre_height' => 'Hauteur de la sélection (en px)&nbsp;:',
	'label_recadre_width' => 'Largeur de la sélection (en px)&nbsp;:',	
	'label_recadre_x1_y1' => 'Position (coin supérieur gauche)',	
	'label_recadre_x2_y2' => 'Position (coin inférieur droit)',
	'label_resultats' => 'Choix des résultats possibles par l\'utilisateur',
	'label_resultats_defaut' => 'Valeur par défaut présélectionnée',
	'label_tourner' => 'Rotation paramétrée',
	'label_tourner_90' => 'Un quart de tour vers la droite',
	'label_tourner_180' => 'Un demi tour',
	'label_tourner_270' => 'Un quart de tour vers la gauche',
	'label_type_modification' => 'Quel sera le résultat?',
	'label_type_retour' => 'Que faire après l\'application du filtre ?',
	'label_type_retour_continuer' => 'Continuer à modifier l\'image',
	'label_type_retour_retour' => 'Fermer la modification',
	'label_vider_version' => 'Intervale entre les vidages des versions intermédiaires (en nombre de jours, 0 = jamais) :',
	'legend_configuration' => 'Configuration du plugin',
	'legend_configuration_resultats' => 'Configuration des résultats',
	'legend_configuration_publique' => 'Configuration de la partie publique',
	'legend_filtres_a_disposition' => 'Filtres à disposition',
	'legende_filtres_de_couleur' => 'Filtres de coloration',
	'legende_filtres_format' => 'Modifier le format',
	'legende_filtres_supplementaires' => 'Filtres supplémentaires',
	'lien_editer_image' => 'Éditer cette image',
	'lien_editer_vignette' => 'Éditer la vignette',
	
	// M
	'message_image_taille_actuelle' => 'Taille actuelle de l\'image&nbsp;: @largeur@x@hauteur@px.',
	'message_limite_versions' => 'Le nombre de versions précédentes sont limitées à @limite@.',
	'message_nouvelle_image_creee' => 'Votre nouvelle image a été créée #@id_document@',
	'message_ok_version_retour' => 'Vous êtes revenu à la version #@version@',
	'message_ok_version_supprimee' => 'La version #@version@ a été supprimée',
	'message_pas_de_versions' => 'Ce document n\'est pas versionné.',
	'message_vignette_installe_succes' => 'La vignette a été chargée avec succès',

	// P
	'photospip' => 'PhotoSPIP',
	
	// T
	'title_version' => 'Version #@version@',
	'titre_informations_images' => 'Informations du document',
	'titre_page_image_edit' => 'Édition de l\'image',
	'titre_versions_precedentes' => 'Précédentes versions',
	
	// PUBLIC
	
	// Versions
	
	'id_document' => 'ID du document dans le site : ',
	'type_original' => 'Type du document : ',
	'date_doc' => 'Date de mise en ligne : ',
	'date_modif_doc' => 'Date de dernière modification : ',
	'taille_fichier' => 'Taille du fichier : ',
	'fichier_original' => 'Fichier original : ',
	'donnees_exif' => 'Données EXIF',
	'tester' => 'Tester',
	'revenir_version' => 'Revenir à la version&nbsp;:',
	'supprimer_version' => 'Supprimer la version&nbsp;:',
	'image_taille_actuelle' => 'Taille actuelle de l\'image&nbsp;:',
	
	//aide
	'texte_aide' =>'<p>Pour appliquer une modification à votre image, il vous suffit de choisir le type en le cochant à coté de son titre et de procéder à son réglage.</p>
	<p>Ensuite choisissez entre "tester" ou "appliquer", puis validez.</p>
	<p><strong>NB :</strong> Ces traitements d\'images sont lourds et peuvent prendre quelque temps avant de s\'effectuer. C\'est pourquoi nous vous recommandons de les "tester" avant (cela créera une prévisualisation). Chaque test repartira de la dernière version. Appliquer créera une nouvelle version disponible.</p>',
	'modification_pas_autorisee' => 'Vous ne disposez pas des droits nécessaires pour pouvoir modifier ce document',
	
);
?>