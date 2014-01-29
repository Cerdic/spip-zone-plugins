<?php
function exec_test_url()
{
	$commencer_page = charger_fonction('commencer_page','inc');
	echo $commencer_page(_T("testurl:tester_url_sites_syndiques"));

	echo gros_titre(_T("testurl:tester_url_sites_syndiques"),'', false);

	echo debut_gauche("",true);
	echo debut_boite_info(true);
		echo _T("testurl:descriptif");
	echo fin_boite_info(true);
	
	function message_erreur_curl($id_syndic, $nom_site, $code_erreur) {
		static $erreurs = false;
		if ($erreurs === false) {
			$erreurs = array(
				204 => _T("testurl:cette_page_contient_rien"),
				206 => _T("testurl:contenu_partiel_page"),
				400 => _T("testurl:erreur_requete_http"),
				401 => _T("testurl:authentification_requise"),
				402 => _T("testurl:acces_page_payant"),
				403 => _T("testurl:acces_page_refuser"),
				404 => _T("testurl:page_inexistante"),
				405 => _T("testurl:methode_requete_non_autorise"),
				500 => _T("testurl:erreur_interne_serveur"),
				502 => _T("testurl:erreur_cause_passerelle_serveur"),
			);
		}
		return '<a href=?exec=sites&id_syndic='.$id_syndic.'>'.$nom_site.'</a> <span style="color:red;">'
				._T("testurl:site_incorrect_code_erreur"). ' ' .$code_erreur.': '. $erreurs[$code_erreur] . '</span><br />';
	}

	function check_url($url_site, $id_syndic, $nom_site, $timeout = 10)
	{
		$ch = curl_init($url_site);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_NOBODY, TRUE);

		if (strpos($url_site, 'https://') === 0)
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		if (!curl_exec($ch))
			$ret = 404;

		$ret = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		if ($ret == 0) {
			$ret = 404;
		}

		switch ($ret) {
			case 204:
			case 206:
			case 400:
			case 401:
			case 402:
			case 403:
			case 404:
			case 405:
			case 500:
			case 502:
				$code = message_erreur_curl($id_syndic, $nom_site, $ret);
				break;
			case 200:
			case 301:
			case 302:
			default:
				$code='';
				break;
				
		}
		return $code;
	}

	echo debut_droite("", true);
	
	// Affichage des sites en defaux
	if ($id_rubrique = _request('id_rubrique')) {
		$result_url = sql_allfetsel('id_syndic, url_site, nom_site', 'spip_syndic', 'id_rubrique='.intval($id_rubrique));
	} else {
		$result_url = sql_allfetsel('id_syndic, url_site, nom_site', 'spip_syndic');
	}
	echo _T("testurl:voici_liste_sites_erreur").'<p />';
	if($result_url)
	{
		if ($id_rubrique = _request('id_rubrique')) {
			echo _T("testurl:analyse_de"). ' ' . count($result_url) . ' ' ._T("testurl:sites_dans_rubrique").' '.intval($id_rubrique).'.<br/>';
		} else {
			echo _T("testurl:analyse_de"). ' ' . count($result_url) . ' ' ._T("testurl:sites_tous_site").'.<br/>';
		}
		foreach($result_url as $result)
		{
			if($erreur = check_url($result['url_site'], $result['id_syndic'], $result['nom_site']))
			{
				echo $erreur;
				$last_id = $result['id'];
			}
		}
	}
	echo fin_gauche(true);
	echo fin_page(true);
}
?>
