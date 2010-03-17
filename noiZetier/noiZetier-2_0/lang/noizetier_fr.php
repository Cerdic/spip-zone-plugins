<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(
	'info_page' => 'PAGE&nbsp;:',
	'derniere_maj' => 'Derni&egrave;re mise &agrave; jour le',
	'installation_tables' => 'Tables du plugin noiZetier install&eacute;es.<br />',
	'choisir_noisette' => 'Choisissez la noisette que vous voulez ajouter&nbsp:',
	'noisettes_page' => 'Noisettes sp&eacute;cifiques &agrave; la page <i>@type@</i>&nbsp;:',
	'noisettes_composition' => 'Noisettes sp&eacute;cifiques &agrave; la composition <i>@type@-@composition@</i>&nbsp;:',
	'noisettes_toutes_pages' => 'Noisettes communes &agrave; toutes les pages&nbsp;:',
	'saisies_non_installe' => '<b>Plugin saisies&nbsp;:</b> ce plugin n\'est pas install&eacute; sur votre site. Il n\'est pas n&eacute;cessaire au fonctionnement du noizetier. Son utilisation est cependant recommand&eacute;e afin d\'offrir des formulaires de configuration plus ergonomiques.',
	'compositions_non_installe' => '<b>Plugin compositions&nbsp;:</b> ce plugin n\'est pas install&eacute;s sur votre site. Il n\'est pas n&eacute;cessaire au fonctionnement du noizetier. Cependant, s\'il est activ&eacute;, vous pourrez d&eacute;clarer des compositions directement dans le noizetier.',
	'yaml_non_installe' => '<b>Plugin YAML&nbsp;:</b> ce plugin n\'est pas install&eacute;s sur votre site. Il n\'est pas n&eacute;cessaire au fonctionnement du noizetier. Cependant, il permet d\'importer et exporter des configurations de noisettes.',
	
	'description_bloc_contenu' => 'Contenu principal de chaque page.',
	'description_bloc_navigation' => 'Informations de navigation propres à chaque page.',
	'description_bloc_extra' => 'Informations extra contextuelles pour chaque page.',
	
	'description_bloctexte' => 'Le titre est optionnel. Pour le texte, vous pouvez utiliser les raccourcis typographiques de SPIP.',
	
	'editer_noizetier_titre' => 'noiZetier',
	'editer_noizetier_explication' => 'Configurer ici les noisettes &agrave; ajouter aux pages de votre site.',
	'editer_configurer_page' => 'Configurer les noisettes de cette page',
	'editer_supprimer_noisettes' => 'Supprimer les noisettes d&eacute;finies pour cette page',
	'editer_exporter_configuration' => 'Exporter la configuration',
	'editer_compositions' => 'G&eacute;rer les compositions',
	'editer_importer_configuration' => 'Importer une config.',
	
	'erreur_aucune_noisette' => 'Aucune noisette trouv&eacute;e.',
	'erreur_doit_choisir_noisette' => 'Vous devez choisir une noisette.',
	'erreur_mise_a_jour' => 'Une erreur s\'est produite pendant la mise &agrave; jour de la base de donn&eacute;e.',
	
	'formulaire_ajouter_noisette' => 'Ajouter une noisette',
	'formulaire_modifier_noisette' => 'Modifier cette noisette',
	'formulaire_supprimer_noisette' => 'Supprimer cette noisette',
	'formulaire_supprimer_noisettes_page' => 'Supprimer les noisettes de cette page',
	'formulaire_deplacer_bas' => 'D&eacute;placer vers le bas',
	'formulaire_deplacer_haut' => 'D&eacute;placer vers le haut',
	'formulaire_configurer_page' => 'Configurer la page&nbsp;:',
	'formulaire_configurer_bloc' => 'Configurer le bloc&nbsp;:',
	'formulaire_obligatoire' => 'obligatoire',
	'formulaire_noisette_sans_parametre' => 'Cette noisette ne propose pas de param&egrave;tre.',
	'formulaire_explication_oui_non' => '(saisir <i>on</i> ou laisser vide)',
	'formulaire_explication_oui_ou_non' => '(saisir <i>oui</i> ou <i>non</i>)',
	
	'nom_bloc_contenu' => 'Contenu',
	'nom_bloc_navigation' => 'Navigation',
	'nom_bloc_extra' => 'Extra',
	
	'nom_bloctexte' => 'Bloc de texte libre',
	
	'label_titre' => 'Titre&nbsp;:',
	'label_texte' => 'Texte&nbsp;:',
	
	'warning_noisette_plus_disponible' => 'ATTENTION&nbsp: cette noisette n\'est plus disponible.',
	'warning_noisette_plus_disponible_details' => 'Le squelette de cette noisette (<i>@squelette@</i>) n\'est plus accessible. Il se peut qu\'il s\'agisse d\'une noisette fournie par un plugin que vous avez d&eacute;sactiv&eacute; ou d&eacute;sinstall&eacute;.',

);

?>
