<?php
/**
 * Plugin MailShot
 * (c) 2012 Cedric Morin
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;
include_spip("inc/config");

/**
 * @param string|array $corps
 *   string si id de la newsletter a envoyer en lot
 *   array si contenu a envoyer
 *     string sujet
 *     string html
 *     string texte
 * @param array $listes
 *   listes a qui on envoie (1 ou ++)
 * @param array $options
 *   string statut : statut par defaut
 * @return bool
 */
function newsletter_bulkstart_dist($corps,$listes = array(),$options=array()){
	// TODO : recuperer la limite de rate d'apres la config
	$options = array_merge(
		array('statut'=>'processing'),$options);

	if (!is_array($corps)){
		$id = $corps;
		$content = charger_fonction("content","newsletter");
		$corps = $content($id);
	}
	else {
		$id = md5(serialize($corps));
	}

	// nombre d'abonnes
	$subscribers = charger_fonction("subscribers","newsletter");
	$count = $subscribers($listes, array('count'=>true));

	$now = date('Y-m-d H:i:s');
	$bulk = array(
		'id' => $id,
		'sujet' => $corps['sujet'],
		'html' => $corps['html'],
		'texte' => $corps['texte'],
		'listes' => implode(',',$listes),
		'total' => $count,
		'current' => 0,
		'failed' => 0,
		'date' => $now,
		'date_start' => $now,
		'statut' => $options['statut'],
	);

	$id_mailshot = sql_insertq("spip_mailshots",$bulk);

	if ($id_mailshot){
		// initialiser le mailer si necessaire
		// On cree l'objet Mailer (PHPMailer) pour le manipuler ensuite
		if ($mailer = lire_config("mailshot/mailer")
			AND charger_fonction($mailer,'bulkmailer',true)
			AND $init = charger_fonction($mailer."_init",'bulkmailer',true)){
			$init($id_mailshot);
		}

		include_spip('inc/mailshot');
		mailshot_update_meta_processing($options['statut']=='processing');
	}

	return $id_mailshot;
}
