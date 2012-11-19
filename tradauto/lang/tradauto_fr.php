<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	//C
	'caractere_max' => 'ATTENTION : la longueur du champs dépasse le maximum de 10000 caractères.\nLa traduction sera effective pour les 10000 premiers caractères.\nVous devrez complèter la traduction pour les car. au délà.\nIl est également possible que cela provoque une erreur 414 (requête trop longue).',
	'configuration' => 'Configuration',
	'configurer_parametre' => 'Configurez quelques parametres',
	'connecter_creer_id' => 'Se connecter ou créer un identifiant Windows Live ID',
	'creer_application' => 'Créer votre application et récupérer vos identifiants',

	//E
	'erreur_pas_configure' => 'ERREUR Tradauto : Le plugin n\'est pas encore configuré.',
	'exclusions' => 'Exclusions',
	'exclusions_explication' => '
		Listez ci-dessous les chaines à exclure de la traduction.<br />
		Chaque occurence de chaine sera laissée telle que durant la traduction.<br />
		Chaque chaine est à séparer par un retour à la ligne.<br />
		Vous pouvez mettre des expressions régulières. Exemple :<br/>
		<strong>&lt;test&gt;.*?&lt;/test&gt;</strong> laisse tel que <strong>&lt;test&gt;ceci est un test&lt;/test&gt;</strong><br/>
		Voir les expressions régulières javascript pour plus d\'infos.<br />
		En fonction des langues sélectionnées, le traducteur peut insérer des espaces indésirables. C\'est notamment le cas pour les modèles. Faites des tests et excluez les modèles modifiés injustement.<br />
		Avec une expression régulière intelligente, vous pouvez exclure tout ou partie d\'un modèle. Par exemple pour exclure la balise du modèle mais laisser certains paramètres à la traduction.<br />',
	'exclusions_liste' => 'Liste des exclusions',

	//I
	'id_application_mp' => 'ID de l\'application Market place',
	'incrire_api_microsoft' => 'S\'inscrire à l\'API Microsoft Translator sur Azure Marketplace. Choisir un forfait de volume mensuel de traduction (GRATUIT pour 2 millions de caratères par mois)',

	//S
	'secret_application_mp' => 'Secret du client de l\'application',

	//T
	'titre_page_configurer' => 'Configurer Tradauto',
	'tradauto' => 'Tradauto',
	'traduction_effectuer' => 'Traduction effectuée',
	'traduction_en_cours' => 'Traduction en cours. Patientez...',
	'traduction_effectuer_succes' => 'Traduction effectuée avec succès.\nVous pouvez maintenant corriger la traduction et/ou enregistrer.',
);

?>
