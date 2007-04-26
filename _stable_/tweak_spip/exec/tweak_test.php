 <?php
#-----------------------------------------------------#
#  Plugin  : Tweak SPIP - Licence : GPL               #
#  Auteur  : Patrice Vanneufville, 2006               #
#  Contact : patrice¡.!vanneufville¡@!laposte¡.!net   #
#  Infos : http://www.spip-contrib.net/?article1554   #
#-----------------------------------------------------#

/*
	Cette page test certaines fonctions presentes dans le plugin
	Pour rajouter des tests, rdv tout en bas de cette page !
*/

include_spip('inc/texte');
include_spip('inc/layer');
include_spip("inc/presentation");

// compatibilite spip 1.9
if ($GLOBALS['spip_version_code']<1.92) { function fin_gauche(){return false;} }

function exec_tweak_test() {
cout_log("Début : exec_tweak_test()");
	global $connect_statut, $connect_toutes_rubriques;
	
	if ($connect_statut != '0minirezo' OR !$connect_toutes_rubriques) {
		debut_page(_T('icone_admin_plugin'), "configuration", "plugin");
		echo _T('avis_non_acces_page');
		fin_page();
		exit;
	}
	
	// initialisation generale forcee : recuperation de $outils;
	tweak_initialisation(true);

	if ($GLOBALS['spip_version_code']<1.92) 
  		debut_page(_T('cout:titre_tests'), 'configuration', 'tweak_spip');
  	else {
		$commencer_page = charger_fonction('commencer_page', 'inc');
		echo $commencer_page(_T('cout:titre_tests'), "configuration", "tweak_spip");
	}
	
	echo "<br /><br /><br />";
	gros_titre(_T('cout:titre_tests'));
	echo '<div style="width:98%; text-align:left; margin:0 auto">';
	// et hop, on lance les tests !
	tweak_les_tests();
	echo '</div>';

	echo fin_page();
cout_log("Fin   : exec_tweak_test()");
}

// renvoie un tableau contenant le texte original et sa transfrmation par la fonction $fonction
// $textes est un tableau de chaines
function tweak_test_fun(&$textes, $fonction) {
	$a = array();
	if (!function_exists($fonction)) return array('erreur' => "$fonction() introuvable, outil non activé !");
	foreach ($textes as $i=>$t) {
		$b = $fonction($t);
		$a["\$texte[$i]"] = htmlentities($t);
//		$a["\$resultat[$i]"] = htmlentities($b);
		$a["\$previsu[$i]"] = str_replace("\n",'\n', $b);
	}
	return $a;
}

// affiche un cadre de titre $titre base sur les donnees de $array
function tweak_array($array, $titre) {
	static $i;
	debut_cadre_trait_couleur('administration-24.gif','','',++$i.". $titre");
	foreach($array as $s=>$v) if(is_array($v))
			foreach($v as $s2=>$v2) echo "\n<strong>{$s}[$s2]</strong> = ".trim($v2)."<br />";
		else echo "\n<strong>$s</strong> = ".trim($v)."<br />";
	fin_cadre_trait_couleur();
}

// affiche un text en rouge
function tweak_red($s){ return "<span style='color:red;'>$s</span>"; }

// effectue tous les tests !
function tweak_les_tests() {
	tweak_array($_SERVER, 'Echo de : $_SERVER[]');
	tweak_array($_ENV, 'Echo de : $_ENV[]');
	global $HTTP_ENV_VARS;
	tweak_array($HTTP_ENV_VARS, 'Echo de : $HTTP_ENV_VARS');
	$a = array('DOCUMENT_ROOT'=>getenv('DOCUMENT_ROOT'), 
			'REQUEST_URI'=>getenv('REQUEST_URI'), 
			'SCRIPT_NAME'=>getenv('SCRIPT_NAME'),
			'PHP_SELF'=>getenv('PHP_SELF'),
		);
	tweak_array($a, 'Echo de : getenv()');
	
	// lecture des variables stockees en meta
	include_spip('inc/meta');
	lire_metas();
	$metas_tweaks = isset($GLOBALS['meta']['tweaks_actifs'])?unserialize($GLOBALS['meta']['tweaks_actifs']):array();
	$metas_vars = isset($GLOBALS['meta']['tweaks_variables'])?unserialize($GLOBALS['meta']['tweaks_variables']):array();
	tweak_array($metas_tweaks, 'Tweaks actifs : $metas_tweaks[]');
	tweak_array($metas_vars, 'Contenu des variables : $metas_vars[]');


	// test de tweak_htmlpath()
	$relative_path = dirname(find_in_path('img/smileys/test'));
	$realpath = str_replace("\\", "/", realpath($relative_path));
	$root = preg_replace(',/$,', '', $_SERVER['DOCUMENT_ROOT']);
	$test_result=substr($realpath, strlen($root));
	$dir = dirname(!empty($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] :
			(!empty($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : 
			(!empty($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : str_replace('\\','/',__FILE__)
		)));
	$a = array('DOCUMENT_ROOT'=>$_SERVER['DOCUMENT_ROOT'], 
			'REQUEST_URI'=>$_SERVER['REQUEST_URI'], 
			'SCRIPT_NAME'=>$_SERVER['SCRIPT_NAME'],
			'PHP_SELF'=>$_SERVER['PHP_SELF'],
			'__FILE__'=>__FILE__,
			'$root'=>$root,
			"find_in_path('img/smileys/test')"=>find_in_path('img/smileys/test'),
			"dirname(find_in_path('img/smileys/test'))"=>$relative_path,
			"str_replace('\\', '/', realpath('$relative_path'))"=>$realpath,
			"substr('$realpath', strlen('$root'))"=>tweak_red($test_result),
			"return?"=>(strlen($root) && strpos($realpath, $root)===0)?'oui':'non',
			"tweak_htmlpath('$relative_path')"=>tweak_htmlpath($relative_path),
			'$dir'=>$dir,
			"tweak_canonicalize('$dir'.'/'.'$relative_path')"=>tweak_red(tweak_canonicalize($dir.'/'.$relative_path)),
		);
	tweak_array($a, 'Test sur : tweak_htmlpath()');

	// test de tweak_canonicalize()
	$dir = $dir.'/'.$relative_path;
	$address = str_replace("//", "/", $dir);
	$address1 = $address2 = explode('/', $address);
	$keys = array_keys($address2, '..');
	foreach($keys as $keypos => $key) array_splice($address2, $key - ($keypos * 2 + 1), 2);
	$address3 = preg_replace(',([^.])\./,', '\1', implode('/', $address2));
	$a = array('$dir'=>$dir,
			'$address'=>$address,
			"explode('/', '$address')"=>$address1, 
			'array_keys($dessus, "..")'=>$keys,
			'array_spliced()'=>$address2, 
			'$resultat'=>tweak_red($address3), 
		);
	tweak_array($a, 'Test sur : tweak_canonicalize()');

	// test de typo_exposants()
	$textes = array(
		"Pr Paul, Dr Jules, Prs Pierre &amp; Paul, Drs Pierre &amp; Paul, Pr&eacute;-St-Gervais ou Dr&eacute;",
		"Ste Lucie, St-Lucien, St.Patrick, St Patrick, st-jules, Sts Pierre &amp; Paul, STe Lucie",
		"Bse Lucie, Bx-Lucien, Bx.Patrick, Bx Patrick, bx-jules, Bses Jeanne &amp; Julie",
		"Iier, Iiers, Ière, 1ière, 1ères, 1ières",
		"Ie IIème IIIe IVe Ve VIe VIIe VIIIe IXe Xe XIe XVe XXe XLe Lème LIe",
		"Erreurs 2me, 3ème, 4ième, 5mes, 6èmes, 7ièmes",
		"1er 1ers, 2e 2es, IIIe IIIes, ",
		"3 ou 4 m², 3 ou 4 m2 et 2 m3.",
		"Mlle, Mlles, Mme, Mmes et erreurs Melle, Melles",
	);
	tweak_array(tweak_test_fun($textes, 'typo_exposants'), 'Test sur : typo_exposants()');

	// test de typo_guillemets()
	$textes = array(
		'avant <i class="style">le</i> "test"!',
		'avant <code class="code">toto</code>. apres le "test"!',
		'avant '.echappe_html('<script>toto</script>', 'TEST', true).'apres le "test"!',
		'avant '.echappe_html('<code class="code">toto</code>', 'TEST', true).'apres le "test"!',
	);
	tweak_array(tweak_test_fun($textes, 'typo_guillemets'), 'Test sur : typo_guillemets()');
	
	// test des smileys
	$textes = array(
		"Doubles : :-(( :-)) :)) :'-)) :’-))",
		"Simples : :-> :-&gt; :-( :-D :-) |-) :'-) :’-) :'-D :’-D :'-( :’-( :-( :o) B-) ;-) :-p :-P' :-| :-/ :-o :-O",
		"les courts (reconnus s'il y a un espace avant) : :) :( ;) :| |) :/ :(",
	);
	tweak_array(tweak_test_fun($textes, 'tweak_smileys_pre_typo'), 'Test sur : tweak_smileys_pre_typo()');

	// test des filets
	$textes = array(
		"__degrade.png__\n__ornement.png__",
		"\n__6__\n__5__\n__4__\n__3__\n__2__\n__1__\n__0__\n",
	);
	tweak_array(tweak_test_fun($textes, 'filets_sep'), 'Test sur : filets_sep()');

	// test des liens orphelins
	$GLOBALS["liens_orphelins_etendu"]=true;
	$textes = array(
		"http://google.fr et <html>http://google.fr</html> et <code>http://google.fr</code> et <cite>http://google.fr</cite>",
		"Voici : http://google.fr. Voici :http://www.google.fr. Voici http://www.google.fr",
		"voici : https://mabanque.fr ou encore ftp://mabanque.fr!",
		"www.google.fr ou bien : www.google.fr",
		"http://user:password@www.commentcamarche.net:80/glossair/glossair.php3 et http://serveur:port/repertoire/fichier.html",
		"ftp://serveur/repertoire/fichier.qqchose, ou encore ftp.stockage.fr/tropdelaballe...",
		"file:///disque|/repertoire/fichier.qqchose et : file:///c|/tmp/fichier.txt",
		"mailto:nom@organisation.domaine et : mailto:Fabien.Gandon@sophia.inria.fr",
		"telnet://bbs.monsite.com/ et telnet://Nom:Password@serveur.ici:port",
		"telnet://gandonf:abcde@gopa.insa.fr:23",
		"gopher://serveur.ici:port/repertoire/fichier#marqueur et gopher://gopher.monsite.com/",
		"newsrc://serveur:port/repertoire/nom.de.la.news",
		"wais://host.ici:port/database et wais://wais.monsite.com/",
		"news:fr.comp.lang.c++ et pkoi pas : <div toto='ici.rien'></div>http://google.fr",
		"moi+moi@world.com, mailto:moi-moi@world.com, mailto:nom@provider.com?subject=renseignement",
		'une image ? <img src="http://mailer.e-flux.com/mail_images/toto.jpg" alt="" />',
		'[<img src="http://mailer.e-flux.com/mail_images/toto.jpg" alt="" />->http://www.americas-society.org/] ',
	);
//	tweak_array(tweak_test_fun($textes, 'typo'), 'Test sur : echappements');
	tweak_array(tweak_test_fun($textes, 'liens_orphelins'), 'Test sur : liens_orphelins()');
}
?>