<?php
/**
 * Plugin Pages pour mobiles
 * (c) 2012 C. Imberti, B. Marne, JM. Labat
 * Licence Creative commons BY-NC-SA
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * La fonction renvoie web s'il faut afficher le site classique,
 * sinon elle renvoie le type de mobile.
 * La détection passe par plusieurs phases décrites
 * ici: http://contrib.spip.net/IMG/pdf/description_du_plugin_cimobile_110726.pdf
 * 
 * À noter que le cookie est renommé "pages_mobiles"
 *  comme le paramètre d'URL à passer "&pages_mobiles=le_nom_du_type_de_mobile"
 * 
 *
 * @return string
 *     "web" ou le type de mobile, ou "autre_mobile".
**/
// 

	function pages_mobiles_detecter_mobile() {

	// Par défaut c'est vide...
	$pages_mobiles = '';


	// Le parametre d'URL pages_mobiles est-il present ?
	// On peut forcer l'affichage du site web classique et inversement retourner a la vue non classique
	$pages_mobiles = isset($_GET['pages_mobiles']) ? $_GET['pages_mobiles'] : '';


	
	// Sinon un cookie de squelette est-il present ?
	if (!$pages_mobiles) {
		$pages_mobiles = isset($_COOKIE['pages_mobiles']) ? $_COOKIE['pages_mobiles'] : '';
	}
	// détection du mobile
	if (!$pages_mobiles OR $pages_mobiles == "reinit_mobile") {

		// si on ne demande pas de re-détecter le mobile
		if (!$pages_mobiles) {
			$user_agent = isset($_SERVER['HTTP_USER_AGENT'])?strtolower($_SERVER['HTTP_USER_AGENT']):'';
			
			// Cas d'un desktop (pour eviter des tests inutiles)
			if (strpos($user_agent,'firefox')!==false AND strpos($user_agent,'fennec')===false) {
				// firefox (sauf version mobile)
				$pages_mobiles = 'web';
			} elseif (strpos($user_agent,'msie')!==false AND strpos($user_agent,'windows ce')===false AND strpos($user_agent,'iemobile')===false) {
				// internet explorer (sauf version mobile)
				$pages_mobiles = 'web';
			}
		}
		// Tableau des mobiles individualises (smartphones et tablettes)
		// expression reguliere sur le user agent => nom du mobile
		$mobiles = pages_mobiles_types_mobiles();
				
		foreach($mobiles as $regexp=>$val){
			if (preg_match($regexp,$user_agent)) {
			$pages_mobiles = $val;
			break;
			}
		}


		

		// Les autres cas
		if (!$pages_mobiles) {
			$httpaccept = isset($_SERVER['HTTP_ACCEPT'])?strtolower($_SERVER['HTTP_ACCEPT']):'';
			$user_agent_4car = substr($user_agent,0,4);
			
			if (preg_match('/(mini 9.5|vx1000|lge |m800|e860|u940|ux840|compal|wireless| mobi|ahong|lg380|lgku|lgu900|lg210|lg47|lg920|lg840|lg370|sam-r|mg50|s55|g83|t66|vx400|mk99|d615|d763|el370|sl900|mp500|samu3|samu4|vx10|xda_|samu5|samu6|samu7|samu9|a615|b832|m881|s920|n210|s700|c-810|_h797|mob-x|sk16d|848b|mowser|s580|r800|471x|v120|rim8|c500foma:|160x|x160|480x|x640|t503|w839|i250|sprint|w398samr810|m5252|c7100|mt126|x225|s5330|s820|htil-g1|fly v71|s302|-x113|novarra|k610i|-three|8325rc|8352rc|sanyo|vx54|c888|nx250|n120|mtk |c5588|s710|t880|c5005|i;458x|p404i|s210|c5100|teleca|s940|c500|s590|foma|samsu|vx8|vx9|a1000|_mms|myx|a700|gu1100|bc831|e300|ems100|me701|me702m-three|sd588|s800|8325rc|ac831|mw200|brew |d88|htc\/|htc_touch|355x|m50|km100|d736|p-9521|telco|sl74|ktouch|m4u\/|me702|8325rc|kddi|phone|lg |sonyericsson|samsung|240x|x320|vx10|nokia|sony cmd|motorola|up.browser|up.link|mmp|symbian|smartphone|midp|wap|vodafone|o2|pocket|kindle|mobile|psp|treo)/i',$user_agent))
			$pages_mobiles = 'autre_mobile';
					
		elseif ((strpos($httpaccept,'text/vnd.wap.wml')>0)||(strpos($httpaccept,'application/vnd.wap.xhtml+xml')>0))
			$pages_mobiles = 'autre_mobile';
			
		elseif (isset($_SERVER['HTTP_X_WAP_PROFILE'])||isset($_SERVER['HTTP_PROFILE']))
			$pages_mobiles = 'autre_mobile';
		
		elseif (in_array($user_agent_4car, array('1207','3gso','4thp','501i','502i','503i','504i','505i','506i','6310','6590','770s','802s','a wa','acer','acs-','airn','alav','asus','attw','au-m','aur ','aus ','abac','acoo','aiko','alco','alca','amoi','anex','anny','anyw','aptu','arch','argo','bell','bird','bw-n','bw-u','beck','benq','bilb','blac','c55/','cdm-','chtm','capi','cond','craw','dall','dbte','dc-s','dica','ds-d','ds12','dait','devi','dmob','doco','dopo','el49','erk0','esl8','ez40','ez60','ez70','ezos','ezze','elai','emul','eric','ezwa','fake','fly-','fly_','g-mo','g1 u','g560','gf-5','grun','gene','go.w','good','grad','hcit','hd-m','hd-p','hd-t','hei-','hp i','hpip','hs-c','htc ','htc-','htca','htcg','htcp','htcs','htct','htc_','haie','hita','huaw','hutc','i-20','i-go','i-ma','i230','iac','iac-','iac/','ig01','im1k','inno','iris','jata','java','kddi','kgt','kgt/','kpt ','kwc-','klon','lexi','lg g','lg-a','lg-b','lg-c','lg-d','lg-f','lg-g','lg-k','lg-l','lg-m','lg-o','lg-p','lg-s','lg-t','lg-u','lg-w','lg/k','lg/l','lg/u','lg50','lg54','lge-','lge/','lynx','leno','m1-w','m3ga','m50/','maui','mc01','mc21','mcca','medi','meri','mio8','mioa','mo01','mo02','mode','modo','mot ','mot-','mt50','mtp1','mtv ','mate','maxo','merc','mits','mobi','motv','mozz','n100','n101','n102','n202','n203','n300','n302','n500','n502','n505','n700','n701','n710','nec-','nem-','newg','neon','netf','noki','nzph','o2 x','o2-x','opwv','owg1','opti','oran','p800','pand','pg-1','pg-2','pg-3','pg-6','pg-8','pg-c','pg13','phil','pn-2','pt-g','palm','pana','pire','pock','pose','psio','qa-a','qc-2','qc-3','qc-5','qc-7','qc07','qc12','qc21','qc32','qc60','qci-','qwap','qtek','r380','r600','raks','rim9','rove','s55/','sage','sams','sc01','sch-','scp-','sdk/','se47','sec-','sec0','sec1','semc','sgh-','shar','sie-','sk-0','sl45','slid','smb3','smt5','sp01','sph-','spv ','spv-','sy01','samm','sany','sava','scoo','send','siem','smar','smit','soft','sony','t-mo','t218','t250','t600','t610','t618','tcl-','tdg-','telm','tim-','ts70','tsm-','tsm3','tsm5','tx-9','tagt','talk','teli','topl','hiba','up.b','upg1','utst','v400','v750','veri','vk-v','vk40','vk50','vk52','vk53','vm40','vx98','virg','vite','voda','vulc','w3c ','w3c-','wapj','wapp','wapu','wapm','wig ','wapi','wapr','wapv','wapy','wapa','waps','wapt','winc','winw','wonu','x700','xda2','xdag','yas-','your','zte-','zeto','acs-','alav','alca','amoi','aste','audi','avan','benq','bird','blac','blaz','brew','brvw','bumb','ccwa','cell','cldc','cmd-','dang','doco','eml2','eric','fetc','hipt','http','ibro','idea','ikom','inno','ipaq','jbro','jemu','java','jigs','kddi','keji','kyoc','kyok','leno','lg-c','lg-d','lg-g','lge-','libw','m-cr','maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','mywa','nec-','newt','nok6','noki','o2im','opwv','palm','pana','pant','pdxg','phil','play','pluc','port','prox','qtek','qwap','rozo','sage','sama','sams','sany','sch-','sec-','send','seri','sgh-','shar','sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-','tosh','treo','tsm-','upg1','upsi','vk-v','voda','vx52','vx53','vx60','vx61','vx70','vx80','vx81','vx83','vx85','wap-','wapa','wapi','wapp','wapr','webc','whit','winw','wmlb','xda-'),true))	    
			$pages_mobiles = "autre_mobile";
			}

	}
	if ($pages_mobiles) {
		// Securite
		if (preg_match(',^[0-9a-z_]*$,i', $pages_mobiles)) {
		
			// Poser un cookie s'il n'existe pas ou si son contenu doit changer
			if (!isset($_COOKIE['pages_mobiles']) OR (isset($_COOKIE['pages_mobiles']) AND $_COOKIE['pages_mobiles']!=$pages_mobiles)){
				include_spip('inc/cookie');
				spip_setcookie('pages_mobiles', $pages_mobiles);
			}

		}
	}

// si ce n'était pas un mobile (ou n'avait pas le cookie ou l'URL), c'est web...
	return ($pages_mobiles ? $pages_mobiles : 'web');

}

/**
 * Tableau des mobiles individualises (smartphones et tablettes)
 * (les anciens mobiles sont deja pris en compte avec 'autre_mobile')
 * 
 * @return array (expression reguliere sur le user agent => nom du mobile) 
 */
function pages_mobiles_types_mobiles() {
	return array(
		',iphone,i'=>'iphone',
		',ipod,i'=>'ipod',
		',ipad,i'=>'ipad',
		',xoom,i'=>'androidtablette',
		',android,i'=>'android',
		',blackberry,i'=>'blackberry',
		',Windows Phone OS 7,i'=>'windowsphone7',
		'/(iris|3g_t|windows ce|opera mobi|windows ce; smartphone;|windows ce; iemobile)/i'=>'windowsmobile',
		',opera mini,i'=>'opera',
		'/(series60|series 60)/i'=>'S60',
		'/(symbian|series60|series70|series80|series90)/i'=>'symbianos',
		',webos,i'=>'palmwebos',
		'/(pre\/|palm os|palm|hiptop|avantgo|plucker|xiino|blazer|elaine)/i'=>'palmos',
		'/nuvifone/i'=>'nuvifone',
		'/(qt embedded|com2)/i'=>'sonymylo',
		'/maemo/i'=>'nokiatablette',
		'/playbook/i'=>'blackberrytablette',
		'/archos/i'=>'archos'
		);
}	

/**
 * Tableau des groupes de mobiles
 * 
 * @return array (groupe de mobiles => tableau des mobiles correspondants) 
 */
function pages_mobiles_groupes_mobiles() {
	return array(
		'ios_phones' => array('iphone','ipod'),
		'smartphones' => array('android','blackberry','windowsphone7','windowsmobile','opera','S60','symbianos','palmwebos','palmos','nuvifone','sonymylo'),
		'tablettes' => array('ipad','androidtablette','nokiatablette','blackberrytablette','archos'),
		'autres_mobiles' => array('autre_mobile')
	);
}


// À chaque hit on regarde sur quelle page on est.
// Si on est dans la liste des pages développées en mobile, alors
// on redirige vers la page mobile

// le define est necessaire quand on appelle urls_decoder_url(), voir:
// http://thread.gmane.org/gmane.comp.web.spip.zone/29186/focus=29214
if (!defined('_SPIP_SCRIPT')) define('_SPIP_SCRIPT', 'spip.php');
include_spip('inc/urls');
$url_decodee = urls_decoder_url($GLOBALS['REQUEST_URI']);

// urls_decoder_url() retourne le type de page et le contexte
$contexte = $url_decodee[1];
$type_page = $url_decodee[0];
// soit le type de page est déterminé par l'objet demandé, soit par page
$type_page = $type_page ? $type_page : $_GET[page];

// si on est pas déjà en train de rediriger vers les pages mobiles
if ($type_page!="pages_mobiles" AND !test_espace_prive()) {

	// Pas de type de page alors c'est le sommaire
	// Attention depuis SPIP 3.0.7, urls_decoder_url retourne 404 comme type page
	// quand "page" n'est pas défini !
	if (!$type_page OR ($type_page == "404" AND $_GET[page] == false)) $type_page = "sommaire";

	// on identifie le type de navigation
	$type_mobile = pages_mobiles_detecter_mobile();

	// Si ce n'est pas web
	if ($type_mobile !== 'web') {

		// Par défaut on cherche le squelette mobile de l'objet
		// dans un sous repertoire nommé comme type de mobile renvoyé par la detection
		// ex. iphone/article.html
		// 
		// Voir la fonction pages_mobiles_detecter_mobile() pour les différents types.
		// Idem pour le groupe de mobile (voir pages_mobiles_groupes_mobiles())
		//
		// S'il n'y a pas le squelette de l'objet dans le repertoire specifique de ce type ou du groupe,
		// c'est le repertoire "mobile" qui est choisi
		// ex. mobile/rubrique.html

		$chemin_mobile = $type_mobile."/".$type_page;

		// si on ne trouve pas la page pour le mobile on cherche pour le groupe de mobile
		if (!find_in_path($chemin_mobile.".html"))
				$chemin_mobile = pages_mobiles_groupes_mobiles($type_mobile)."/".$type_page;
		
		// si on ne trouve pas la page pour le groupe de mobile on cherche dans le répertoire générique
		if (!find_in_path($chemin_mobile.".html")) $chemin_mobile = "mobile/".$type_page;

		// si le squelette existe, on redirige vers une page pivot qui à son tour include la page mobile
		// C'est necessaire car seul l'admin peut accéder à des pages placées dans un sous répertoire de
		// l'arborescence.
		if (find_in_path($chemin_mobile.".html")) {
			foreach ($contexte as $key => $value) {
				$liste_params .= "&".$key."=".$value;
			}
			// ??? étrange, le $contexte est parfois vide, il faut alors aller rechercher les paramètres dans la query_string
			if (!$contexte) {
				foreach(explode("&",$_SERVER['QUERY_STRING']) as $value) {
					if (substr($value,0,5) != "page=" AND substr($value,0,13) != "page_mobiles=")
						$liste_params .= "&".$value;
				}
			}
			include_spip('inc/headers');
			redirige_par_entete("spip.php?page=pages_mobiles&squelette_mobile=$chemin_mobile".$liste_params);
		}

	}
}
// sinon, on ne change rien à l'affichage (affichage par défaut)

?>