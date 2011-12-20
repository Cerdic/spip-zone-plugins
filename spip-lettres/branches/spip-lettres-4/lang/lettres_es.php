<?php


	/**
	 * SPIP-Lettres
	 *
	 * Copyright (c) 2006-2009
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPLv3.
	 * Pour plus de details voir http://www.gnu.org/licenses/gpl-3.0.html
	 *  
	 **/

if (!defined("_ECRIRE_INC_VERSION")) return;

	$GLOBALS[$GLOBALS['idx_lang']] = array(

		// formulaire lettres
		'lettre_information' => "Boletín",
		'lettres_information' => "Boletines",
		'themes' => "Temas",
		'racine' => "Raíz del sitio",
		'tout_le_site' => "Todos los temas",
		'email' => "Correo",
		'nom' => "Nombre",
		'format' => "Formato",
		'format_html' => "HTML",
		'format_texte' => "Texto",
		'format_mixte' => "Mixto",
		'action' => "Acción",
		'abonnement' => "Suscripción",
		'desabonnement' => "Dar de baja",
		'changement_format' => "Cambio de formato",
		'valider' => "Validar",
		'email_ko' => "Formato invalido",
		'choix_ko' => "Selección obligatoria",
		'vous_devez_choisir_un_theme' => "Tienes que elegir un tema",
		'vous_n_etes_pas_abonnes' => "No eres suscriptor",

		// envoi
		'envoi_lettre_abonne' => 'Envio Boletín No @id_lettre@ al suscriptor No @id_abonne@ (Formato @format@)',
		'envoi_lettre_abonne_essai_n' => "[Intento No@n@] ",
		
		// formulaire lettres messages
		'validation_abonnements_succes' => "Tus suscripciones fueron validadas.",
		'validation_abonnements_erreur' => "Tus suscripciones no pudieron ser validadas.",
		'validation_desabonnements_succes' => "Tu pedido de baja fue validado.",
		'validation_desabonnements_erreur' => "Tu pedido de baja no pudó ser validado.",
		'validation_changement_format_succes' => "Tu cambio de formato fue tomado en cuenta.",
		'validation_changement_format_erreur' => "Tu cambio de formato no pudo ser tomado en cuenta.",
		'envoi_abonnements_succes' => "Vas a recibir un correo para confirmar tus sucripciones.",
		'envoi_abonnements_erreur' => "El correo de confirmación de tus sucripciones no pudo ser enviado.",
		'envoi_desabonnements_succes' => "Vas a recibir un correo para confirmar la baja de tus suscripciones.",
		'envoi_desabonnements_erreur' => "El correo de confirmación de la baja de tus suscripciones no pudó ser enviado.",
		'envoi_changement_format_succes' => "Vas a recibir un correo para confirmar tu cambio de formato.",
		'envoi_changement_format_erreur' => "El correo de confirmación de tu cambio de formato no pudó ser enviado.",
		'retour' => 'Retour',

		// lettres
		'probleme_lecture_lettre' => "Hacer clic aquí si el boletín no se puede visualizar correctamente.",
		'se_desabonner' => "Hacer clic aquí para dar de baja.",

		// notifications
		'validation_abonnements' => "Validación de tus suscripciones",
		'vous_avez_demande_a_vous_abonner' => "En ".$GLOBALS['meta']['nom_site'].", pediste la suscripción a los temas siguientes:",
		'validation_desabonnements' => "Validación de tus pedidos de baja",
		'vous_avez_demande_a_vous_desabonner' => "En ".$GLOBALS['meta']['nom_site'].", pediste dar de baja a tu suscripción par los temas siguientes:",
		'validation_changement_format' => "Validación de tu cambio de formato",
		'vous_avez_demande_a_changer_format' => "En ".$GLOBALS['meta']['nom_site'].", pediste cambiar el formato de los boletines que recibes.",
		'confirmation' => "Confirmación",
		'cliquez_ici_pour_confirmer' => "Hacer clic aquí para confirmar",
		'suppression_abonne' => "Dar de baja a un suscriptor",
		'a_ete_supprime' => "fue borrado de la base de datos. Por favor sincronizar con el archivo de clientes.",

		// Page d'abonnement
		'description_page-lettres' => 'Accesible vía spip.php?page=lettres, está página provee un formulario de suscripción a los boletines informativos',

		'Z' => 'ZZzZZzzz'

	);


?>
