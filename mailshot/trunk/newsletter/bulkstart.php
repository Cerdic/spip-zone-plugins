<?php
/**
 * Plugin MailShot
 * (c) 2012 Cedric Morin
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;
include_spip("inc/config");

/**
 * Demarrer un envoi en nombre d'une infolettre vers une ou plusieurs listes
 *
 * @param string|array $corps
 *   string id de la newsletter a envoyer en lot
 *ou array contenu a envoyer
 *     string sujet
 *     string html
 *     string texte
 * @param array $listes
 *   listes a qui on envoie (1 ou ++)
 * @param array $options
 *   string statut : statut par defaut
 *   string date_start : date de debut d'envoi (dans le futur)
 *   bool graceful : ne pas envoyer aux destinataires qui on déjà reçu ce contenu
 * @return int
 *   0 si echec ou id de l'envoi sinon
 */
function newsletter_bulkstart_dist($corps,$listes = array(),$options=array()){
	// TODO : recuperer la limite de rate d'apres la config
	$now = date('Y-m-d H:i:s');
	$defaut = array('statut'=>'processing','date_start'=>$now, 'graceful'=>0);
	if (isset($options['date_start'])){
		if (strtotime($options['date_start'])>time()) {
			$defaut['statut'] = 'init';
		}
		else {
			unset($options['date_start']);
		}
	}
	$options = array_merge($defaut ,$options);

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

	// from fourni par la newsletter ou par une des listes de diffusion ?
	$from_name = (isset($corps['from_name'])?$corps['from_name']:'');
	$from_email = (isset($corps['from_email'])?$corps['from_email']:'');
	if (!$from_name and !$from_email) {
		if (count($listes)){
			$lists = charger_fonction("lists","newsletter");
			$desc_lists = $lists();
			foreach($listes as $i) {
				if (isset($desc_lists[$i]['from_email']) and $desc_lists[$i]['from_email']){
					$from_email = $desc_lists[$i]['from_email'];
					$from_name = $desc_lists[$i]['from_name'];
					break;
				}
			}
		}
	}

	$bulk = array(
		'id' => $id,
		'sujet' => $corps['sujet'],
		'html' => $corps['html'],
		'texte' => $corps['texte'],
		'listes' => implode(',',$listes),
		'graceful' => $options['graceful'],
		'from_name' => $from_name,
		'from_email' => $from_email,
		'total' => $count,
		'current' => 0,
		'failed' => 0,
		'date' => $now,
		'date_start' => $options['date_start'],
		'statut' => $options['statut'],
	);

	$id_mailshot = sql_insertq("spip_mailshots",$bulk);

	if ($id_mailshot){
		// mettre a jour la meta en priorite car l'init du maileur peut faire timeout
		// et dans ce cas on ne declenche jamais vraiment l'envoi
		include_spip('inc/mailshot');
		mailshot_update_meta_processing($options['statut']=='processing');

		if ($options['statut']!=='init'){
			// initialiser le mailer si necessaire
			// On cree l'objet Mailer (PHPMailer) pour le manipuler ensuite
			if ($mailer = lire_config("mailshot/mailer")
				AND charger_fonction($mailer,'bulkmailer',true)
				AND $init = charger_fonction($mailer."_init",'bulkmailer',true)){
				$init($id_mailshot);
			}
		}
	}

	return $id_mailshot;
}
