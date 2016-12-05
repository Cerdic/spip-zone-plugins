<?php
/**
 * Plugin Newsletters
 * (c) 2012 Cedric Morin
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;
include_spip("inc/mailsubscribinglists");

function action_dupliquer_mailsubscribinglist_dist($id_mailsubscribinglist = null){
	if (is_null($id_mailsubscribinglist)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$id_mailsubscribinglist = $securiser_action();
	}

	include_spip('inc/autoriser');
	if (autoriser('creer', 'mailsubscribinglist')
	  AND $row = sql_fetsel('*', 'spip_mailsubscribinglists', 'id_mailsubscribinglist=' . intval($id_mailsubscribinglist))){

		$new = $row;
		unset($new['id_mailsubscribinglist']);
		unset($new['maj']);
		unset($new['date']);
		$new['statut'] = 'fermee';
		$new['titre'] = _T('mailsubscribinglist:titre_copie') . $new['titre'];
		$new['identifiant'] = trim(preg_replace(',\W+,','_',strtolower(_T('mailsubscribinglist:titre_copie'))),'_') . '_' . $new['identifiant'];

		include_spip("action/editer_objet");
		if ($id_new = objet_inserer("mailsubscribinglist")){
			$new['identifiant'] .= "_".$id_new;
			objet_modifier("mailsubscribinglist",$id_new,$new);

			$chercher_logo = charger_fonction('chercher_logo','inc');
			foreach(array('on','off') as $mode) {
				if($logo = $chercher_logo($id_mailsubscribinglist,'id_mailsubscribinglist',$mode)){
					list($f, $dir, $nom, $format, $timestamp) = $logo;
					$fnew = $dir . type_du_logo('id_mailsubscribinglist').$mode.intval($id_new).'.'.$format;
					@copy($f,$fnew);
				}
			}
			
			// tous les objets lies
			include_spip('action/editer_liens');
			objet_dupliquer_liens('mailsubscribinglist',$id_mailsubscribinglist,$id_new);

			// tous les inscrits, en une requete
			$trouver_table = charger_fonction('trouver_table','base');
			$desc = $trouver_table('spip_mailsubscriptions');
			if ($desc and isset($desc['field'])) {
				$field = array_keys($desc['field']);
				$field = array_diff($field, array('id_mailsubscribinglist','maj'));
				$select = implode(',',$field);
				$req = sql_get_select($id_new.','.$select,'spip_mailsubscriptions','id_mailsubscribinglist='.intval($id_mailsubscribinglist));
				spip_query($s = 'INSERT INTO spip_mailsubscriptions (id_mailsubscribinglist,'.$select.') '.$req);
				//spip_log($s,'dbg');
			}
			else {
				spip_log('description table spip_mailsubscriptions non trouvee','mailsubscribers'._LOG_ERREUR);
			}

			$GLOBALS['redirect'] = generer_url_entite($id_new,"mailsubscribinglist");
		}

	}
}
