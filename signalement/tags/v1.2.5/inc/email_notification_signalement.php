<?php
/**
 * Plugin Signalement
 *
 * Auteurs :
 * kent1 (http://www.kent1.info - kent1@arscenic.info)
 *
 * © 2012 - Distribue sous licence GNU/GPL
 *
 * Fonction de construction de notification
 *
 **/
 
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Construitre l'email personalise de notification d'un signalement
 *
 * @param array $t
 * @param string $email
 * @param array $contexte
 * @return string
 */
function inc_email_notification_signalement_dist ($t, $email, $contexte=array()) {
	static $contextes_store = array();

	if(!isset($contextes_store[$t['id_signalement']])){
		$url = '';
		$id_signalement = $t['id_signalement'];

		if ($t['statut'] == 'publie') # signelement publie
		{
			$url = generer_url_entite($t['id_objet'], $t['objet']);
		}
		else #  signalement modere, spam, poubelle direct ....
		{
			$url = generer_url_ecrire('controler_signalement', "debut_id_signalement=".$id_signalement);
		}

		if (!$url) {
			spip_log("signalement $id_signalement sans referent",'notifications');
			$url = './';
		}
		if ($t['id_objet']) {
			include_spip('inc/filtres');
			$t['titre_source'] = generer_info_entite($t['id_objet'], $t['objet'], 'titre');
		}

		$t['url'] = $url;

		// detecter les url des liens du signalement
		// pour la moderation (permet de reperer les SPAMS avec des liens caches)
		// il faut appliquer le traitement de raccourci car sinon on rate des liens sous forme [->..] utilises par les spammeurs !
		include_spip("public/interfaces");
		$table_objet = "signalement";

		$links = array();
		foreach ($t as $champ=>$v){
			$champ = strtoupper($champ);
			$traitement = (isset($GLOBALS['table_des_traitements'][$champ])?$GLOBALS['table_des_traitements'][$champ]:null);
			if (is_array($traitement)
			  AND (isset($traitement[$table_objet]) OR isset($traitement[0]))){
				$traitement = $traitement[isset($traitement[$table_objet]) ? $table_objet : 0];
				$traitement = str_replace('%s', "'".texte_script($v)."'", $traitement);
				eval("\$v = $traitement;");
			}

			$links = $links + extraire_balises($v,'a');
		}
		$links = extraire_attribut($links,'href');
		$links = implode("\n",$links);
		$t['liens'] = $links;

		$contextes_store[$t['id_signalement']] = $t;
	}

	$fond = "notifications/signalement_poste";
	if (isset($contexte['fond'])){
		$fond = $contexte['fond'];
		unset($contexte['fond']);
	}
	$t = array_merge($contextes_store[$t['id_signalement']],$contexte);
		// Rechercher eventuellement la langue du destinataire
	if (NULL !== ($l = sql_getfetsel('lang', 'spip_auteurs', "email=" . sql_quote($email))))
		$l = lang_select($l);

	$parauteur = (strlen($t['auteur']) <= 2) ? '' :
		(" " ._T('forum_par_auteur', array(
			'auteur' => $t['auteur'])
		) .
		 ($t['email_auteur'] ? ' <' . $t['email_auteur'] . '>' : ''));

	$titre = textebrut(typo($t['titre_source']));
	$signalement_poste_par = ($t['id_article']
		? _T('forum:forum_poste_par', array(
			'parauteur' => $parauteur, 'titre' => $titre))
		: $parauteur . ' (' . $titre . ')');

	$t['par_auteur'] = $signalement_poste_par;

	$envoyer_mail = charger_fonction('envoyer_mail','inc'); // pour nettoyer_titre_email
	$corps = recuperer_fond($fond,$t);

	if ($l)
		lang_select();

	return $corps;
}
