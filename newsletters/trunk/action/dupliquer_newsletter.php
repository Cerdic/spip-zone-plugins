<?php
/**
 * Plugin Newsletters
 * (c) 2012 Cedric Morin
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;
include_spip("inc/newsletters");

function action_dupliquer_newsletter_dist($id_newsletter = null){
	if (is_null($id_newsletter)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$id_newsletter = $securiser_action();
	}

	include_spip('inc/autoriser');
	if (autoriser('creer', 'newsletter')
	  AND $row = sql_fetsel('*', 'spip_newsletters', 'id_newsletter=' . intval($id_newsletter))){

		$new = $row;
		unset($new['id_newsletter']);
		unset($new['maj']);
		unset($new['date_redac']);
		unset($new['date']);
		$new['baked'] = 0;
		$new['statut'] = 'prepa';

		include_spip("action/editer_objet");
		if ($id_new = objet_inserer("newsletter")){
			objet_modifier("newsletter",$id_new,$new);

			$chercher_logo = charger_fonction('chercher_logo','inc');
			foreach(array('on','off') as $mode) {
				if($logo = $chercher_logo($id_newsletter,'id_newsletter',$mode)){
					list($f, $dir, $nom, $format, $timestamp) = $logo;
					$fnew = $dir . type_du_logo('id_newsletter').$mode.intval($id_new).'.'.$format;
					@copy($f,$fnew);
				}
			}
			
			// tous les objets lies
			include_spip('action/editer_liens');
			objet_dupliquer_liens('newsletter',$id_newsletter,$id_new);

			$GLOBALS['redirect'] = generer_url_entite($id_new,"newsletter");
		}

	}
}
