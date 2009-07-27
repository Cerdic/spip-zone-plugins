<?php

include_spip('simpletest/autorun');
include_spip('inc/autoriser');

if (!autoriser('configurer')) 
	die('Administrateur requis !');

if (!in_array($_SERVER["REMOTE_ADDR"], array('127.0.0.1', '127.0.1.1')))
	die('Admin local requis pour executer les tests !');

/*
 * il faut remettre le chdir pour les fonctions de spip
 * comme find_in_path() ou include_spip()
 * a l'interieur de la classe.
 */
define('_CHDIR', getcwd());
define('_DEBUG_MAX_SQUELETTE_ERREURS', 100);

/**
 * Extension SpipTest
 * pour donner le bon repertoire de travail
 * et definir d'autres fonctions d'assertion
 */
class SpipTest extends UnitTestCase {  
	var $options_recuperer_code = array();
	var $adresse_dernier_fichier_pour_code = '';
	
	function SpipTest($name = false) {
		chdir(_CHDIR);
	    if (!$name) {
            $name = get_class($this);
        }
        $this->UnitTestCase($name);
		// creer un repertoire pour les tests
		// sert a la compilation
		include_spip('inc/flock');
		sous_repertoire(_DIR_CACHE, 'simpleTests');
		define('_DIR_CODE',_DIR_CACHE . 'simpleTests/');
    }
	
	/**
	 * Retourne l'adresse du site SPIP
	 */
	function me(){
		return $GLOBALS['meta']['url_site_spip'];
	}
	
	/**
	 * Retourne une url pour tester une noisette
	 * 
	 * 
	 * @param string $noisette : noisette a tester
	 * @param array $params : tableau de params a transmettre
	 */
	function generer_url_test($noisette, $params=array(), $var_mode_auto=true){
		$appel = parametre_url(generer_url_public('simpletests'),'test',$noisette,'&');
		foreach ($params as $p=>$v)
			$appel =  parametre_url($appel,$p,$v,'&');
		if ($var_mode_auto) {
			if ($mode = $GLOBALS['var_mode'] AND in_array($mode, array('calcul','recalcul')))
				$appel =  parametre_url($appel,'var_mode',$mode,'&');
		}
		return $appel;
	}
	
	/**
	 * Retourne une url pour tester une code
	 * 
	 * Voir la fonction recuperer_fond pour les parametres
	 * @param string $code : code du squelette
	 * @param array $contexte : contexte de calcul du squelette
	 * @param array $opt : options ?
	 * @param string $connect : nom de la connexion a la bdd
	 * 
	 * @return url d'appel
	 */	
	function urlTestCode($code, $contexte=array(), $options = array(), $connect='') {
		$infos = $this->recuperer_infos_code($code, $contexte, $options, $connect);
		return $this->generer_url_test($infos['fond']);
	}
	
	/**
	 * Determine si une chaine est de type NA (non applicable)
	 * @param string $chaine 	Chaine a tester
	 * @return bool	est-ce Non Applicable ?
	 */
	function isNa($chaine) {
		return substr(strtolower(trim($chaine)),0,2)=='na';
	}
	
	/**
	 * Cree une exception si l'on est de type NA
	 * Retourne true si exception, false sinon.
	 * 
	 * @param string $chaine 	Chaine a tester
	 * @return bool		Est-ce Non Applicable ?
	 */
	function exceptionSiNa($chaine) {
		if ($this->isNa($chaine)) {
			throw new SpipNaException($chaine);
			return true;
		}
		return false;
	}	
	
	/**
	 * Assertion qui verifie si le retour est la chaine 'ok' (casse indifferente)
	 * 
	 * @param mixed $value valeur a tester si ok
	 * @param string $message : message pour une eventuelle erreur
	 *  
	 */
	function assertOk($value, $message = "%s") {
        $dumper = &new SimpleDumper();
        $message = sprintf(
                $message,
                '[' . $dumper->describeValue($value) . '] should be string \'ok\'');
		if ($this->exceptionSiNa($value)) {
			return false;
		}
        return $this->assertTrue((strtolower($value)=='ok'), $message);
	}
	
	/**
	 * Assertion qui verifie que le retour n'est pas la chaine 'ok' (casse indifferente)
	 * 
	 * @param mixed $value valeur a tester si pas ok
	 * @param string $message : message pour une eventuelle erreur
	 * 
	 */
	function assertNotOk($value, $message = "%s") {
        $dumper = &new SimpleDumper();
        $message = sprintf(
                $message,
                '[' . $dumper->describeValue($value) . '] shouldn\'t be string \'ok\'');
        return $this->assertFalse((strtolower($value)=='ok'), $message);
	}
	
	/**
	 * Assertion qui verifie si le retour ne vaut pas 'ok' (casse indifferente)
	 * la fonction appelle recuperer_code avec les arguments.
	 * 
	 * L'appel
	 * 		$this->assertNotOkCode('[(#CONFIG{pasla}|oui)ok]');
	 * est equivalent de : 
	 * 		$this->assertNotOk($this->recuperer_code('[(#CONFIG{pasla}|oui)ok]'));
	 * 
	 * Voir la fonction recuperer_fond pour les parametres
	 * @param string $code : code du squelette
	 * @param array $contexte : contexte de calcul du squelette
	 * @param array $opt : options ?
	 * @param string $connect : nom de la connexion a la bdd
	 * @param string $message : message pour une eventuelle erreur
	 *  
	 * @return true/false
	 */
	function assertNotOkCode($code, $contexte=array(), $options = array(), $connect='', $message = "%s") {
		return $this->assertNotOk($this->recuperer_code($code, $contexte, $options, $connect), $message);
	}
	
	
	/**
	 * Assertion qui verifie si le retour vaut 'ok' (casse indifferente)
	 * la fonction appelle recuperer_code avec les arguments.
	 * 
	 * L'appel
	 * 		$this->assertOkCode('[(#CONFIG{pasla}|non)ok]');
	 * est equivalent de : 
	 * 		$this->assertOk($this->recuperer_code('[(#CONFIG{pasla}|non)ok]'));
	 * 
	 * Voir la fonction recuperer_fond pour les parametres
	 * @param string $code : code du squelette
	 * @param array $contexte : contexte de calcul du squelette
	 * @param array $opt : options ?
	 * @param string $connect : nom de la connexion a la bdd
	 * @param string $message : message pour une eventuelle erreur
	 *  
	 * @return true/false
	 */
	function assertOkCode($code, $contexte=array(), $options = array(), $connect='', $message = "%s") {
		return $this->assertOk($this->recuperer_code($code, $contexte, $options, $connect), $message);
	}
	
	/**
	 * Assertion qui verifie si le retour vaut $value 
	 * la fonction appelle recuperer_code avec les arguments.
	 * 
	 * L'appel
	 * 		$this->assertEqualCode('ok','[(#CONFIG{pasla}|non)ok]');
	 * est equivalent de : 
	 * 		$this->assertEqual('ok',$this->recuperer_code('[(#CONFIG{pasla}|non)ok]'));
	 * 
	 * Voir la fonction recuperer_fond pour les parametres
	 * @param string $value : chaine a comparer au resultat du code
	 * @param string $code : code du squelette
	 * @param array $contexte : contexte de calcul du squelette
	 * @param array $opt : options ?
	 * @param string $connect : nom de la connexion a la bdd
	 * @param string $message : message pour une eventuelle erreur
	 * 
	 * @return true/false
	 */
	function assertEqualCode($value, $code, $contexte=array(), $options = array(), $connect='', $message = "%s") {
		return $this->assertEqual($value, $this->recuperer_code($code, $contexte, $options, $connect), $message);
	}
	
	
	/**
	 * Assertion qui verifie si le retour ne vaut pas $value 
	 * la fonction appelle recuperer_code avec les arguments.
	 * 
	 * L'appel
	 * 		$this->assertEqualCode('ok','[(#CONFIG{pasla}|non)ok]');
	 * est equivalent de : 
	 * 		$this->assertEqual('ok',$this->recuperer_code('[(#CONFIG{pasla}|non)ok]'));
	 * 
	 * Voir la fonction recuperer_fond pour les parametres
	 * @param string $value : chaine a comparer au resultat du code
	 * @param string $code : code du squelette
	 * @param array $contexte : contexte de calcul du squelette
	 * @param array $opt : options ?
	 * @param string $connect : nom de la connexion a la bdd
	 * @param string $message : message pour une eventuelle erreur
	 * 
	 * @return true/false
	 */
	function assertNotEqualCode($value, $code, $contexte=array(), $options = array(), $connect='', $message = "%s") {
		return $this->assertNotEqual($value, $this->recuperer_code($code, $contexte, $options, $connect), $message);
	}
	
	/**
	 * Assertion qui verifie si le retour verifie le pattern $pattern 
	 * la fonction appelle recuperer_code avec les arguments.
	 * 
	 * L'appel
	 * 		$this->assertPatternCode('/^ok$/i','[(#CONFIG{pasla}|non)ok]');
	 * est equivalent de : 
	 * 		$this->assertPattern('/^ok$/i',$this->recuperer_code('[(#CONFIG{pasla}|non)ok]'));
	 * 
	 * Voir la fonction recuperer_fond pour les parametres
	 * @param string $pattern : pattern a comparer au resultat du code
	 * @param string $code : code du squelette
	 * @param array $contexte : contexte de calcul du squelette
	 * @param array $opt : options ?
	 * @param string $connect : nom de la connexion a la bdd
	 * @param string $message : message pour une eventuelle erreur
	 * 
	 * @return true/false
	 */
	function assertPatternCode($pattern, $code, $contexte=array(), $options = array(), $connect='', $message = "%s") {
		return $this->assertPattern($pattern, $this->recuperer_code($code, $contexte, $options, $connect), $message);
	}
	
			
	/**
	 * recupere le resultat du calcul d'une compilation de code de squelette
	 * $coucou = $this->recuperer_code('[(#AUTORISER{ok}|oui)coucou]');
	 * 
	 * Voir la fonction recuperer_fond pour les parametres
	 * @param string $code : code du squelette
	 * @param array $contexte : contexte de calcul du squelette
	 * @param array $opt : options ?
	 * @param string $connect : nom de la connexion a la bdd
	 * 
	 * @return string/array : page compilee et calculee
	 */
	function recuperer_code($code, $contexte=array(), $options = array(), $connect=''){
		$opt = $this->options_recuperer_code;
		if (isset($opt['avant_code']))
			$code = $opt['avant_code'] . $code;
		if (isset($opt['apres_code']))
			$code .= $opt['apres_code'];
		
		$fond = _DIR_CODE . md5($code.serialize($opt));
		$this->ecrire_fichier($fond . '.html', $code);
		
		if (isset($opt['fonctions']) and $opt['fonctions']) {
			// un fichier unique pour ces fonctions
			$func = _DIR_CODE . "func_" . md5($opt['fonctions']) . ".php";
			$this->ecrire_fichier($func, $this->php($opt['fonctions']));
			// une inclusion unique de ces fichiers
			$this->ecrire_fichier($fond.'_fonctions.php', $this->php("include_once('$func');"));
		}
		
		$fond = str_replace('../', '', $fond); // pas de ../ si dans ecrire !
		return recuperer_fond($fond, $contexte, $options, $connect);
	}
	
	
	/**
	 * Appele recuperer_fond avec l'option raw pour obtenir un tableau d'informations
	 * que l'on complete avec le nom du fond et les erreurs de compilations generees
	 */
	function recuperer_infos_code($code, $contexte=array(), $options = array(), $connect=''){
		$options['raw'] = true;
		// vider les erreurs
		$this->init_compilation_errors();
		$infos = $this->recuperer_code($code, $contexte, $options, $connect);
		
		// ca ne devrait pas arriver
		if (!is_array($infos)) return $infos;
		
		// on ajoute des infos supplementaires a celles retournees
		$path = pathinfo($infos['source']);
		$infos['fond'] = $path['dirname'].'/'.$path['filename']; // = $fond;
		$infos['erreurs'] = $this->get_compilation_errors();
		return $infos;
	}
	
	
	
	/**
	 * S'utilise avec recuperer_code() :
	 * 
	 * stocke des options :
	 * - fonctions : pour ajouter un fichier de fonction au squelette cree (on passe le contenu du fichier)
	 * - avant_code : pour inserer du contenu avant le code
	 * - apres_code : pour inserer du contenu apres le code
	 * 
	 * @param array $options : param->valeur des options
	 * @param bool $merge  : les options se cumulent aux autres ? (ou viennent en remplacement)
	 * @return null;
	 */
	function options_recuperer_code($options = array(), $merge=false) {
		if ($merge) {
			$this->options_recuperer_code = array_merge($this->options_recuperer_code,$options);
		} else {
			$this->options_recuperer_code = $options;
		}
	}

	/**
	 * Recupere un array des erreurs de compilation
	 * @return array 	Erreurs de compilations
	 */
	function get_compilation_errors(){
		$erreurs = $GLOBALS['tableau_des_erreurs'];
		$GLOBALS['tableau_des_erreurs'] = array();
		return $erreurs;
	}
	
	/**
	 * Raz les erreurs de compilation
	 * @return null
	 */
	function init_compilation_errors(){
		// les erreurs s'ecrivent dans une jolie globale
		$GLOBALS['tableau_des_erreurs'] = array();
	}
	
	/**
	 * Retourne "<?php $code ?>"
	 * @param string $code	Code php
	 * @return string	Code php complet
	 */
	function php($code){
		return "<"."?php\n" . $code . "\n?".">";
	}
	
	/**
	 * Ecrire un fichier a l'endroit indique
	 * Si le fichier existe, il n'est pas recree
	 * sauf en cas de var_mode=recalcul
	 * 
	 * @param string $adresse	Adresse du fichier a ecrire
	 * @param string $contenu	Contenu du fichier
	 * @return null
	 */
	function ecrire_fichier($adresse, $contenu){
		if (!file_exists($adresse)
		OR  $GLOBALS['var_mode']=='recalcul') {
			ecrire_fichier($adresse, $contenu);
		}
	}
	
}


/**
 * Gestion des exceptions
 * 
 * Exception de type NA (Par exemple en generant des squelettes
 * 
 */
class SpipNaException extends Exception { 
  // chaîne personnalisé représentant l'objet
  public function __toString() {
    return "{$this->message}\n";
  }		
}

/**
 * Gestion des exceptions
 * 
 * Exceptions prevues dans les tests
 * 
 */
class SpipTestException extends Exception { 
  // chaîne personnalisé représentant l'objet
  public function __toString() {
    return "{$this->message}\n";
  }		
}


/**
 * Extension de TestSuite
 * pour donner le bon repertoire de travail
 * et ajouter des fonctions specifiques a SPIP
 */
class SpipTestSuite extends TestSuite {
	function SpipTestSuite($name = false){
		chdir(_CHDIR);
	    if (!$name) {
            $name = get_class($this);
        }
		$this->TestSuite($name);
	}
	
	/**
	 * Ajoute tous les fichiers de tests
	 * d'un repertoire donne (ou du repertoire d'un fichier donne)
	 * 
	 * @param string $dir : fichier ou repertoire qui sera scanne
	 * @param bool $recurs : inclure recursivement les dossiers ?
	 * @return null
	 */
	function addDir($dir, $recurs = false){
		if (is_file($dir))
			$dir = dirname($dir);
		include_spip('inc/flock');
		$a = preg_files($dir);
		foreach ($a as $f) {
			$info = pathinfo($f);
			if (($info['extension']=='php') 
			AND !strpos($info['basename'], '_fonctions.php')
			AND !in_array($info['basename'], array(
				'lanceur_spip.php',
				'all_tests.php',
			))) {
				$this->addFile($f);	
			}
		}
	}
}



/**
 * Extension de HTMLReporter
 * pour donner le bon repertoire de travail
 * et ajouter des fonctions specifiques a SPIP
 */
class SpipHtmlReporter extends HtmlReporter {
	var $_na;
	
	function SpipHtmlReporter($charset='UTF-8') {
		chdir(_CHDIR);
        $this->HtmlReporter($charset);	
		$this->_na = 0;
	}
	
	/** 
	 * retourne un code css de deco
	 * 
	 */
	function _getCss() {
		$css = parent::_getCss();
		return $css . "\n.na{background-color: inherit; color: #fa0;}"
					. "\n.complements{background-color: inherit; color: #999;}";
	}
	
    /**
     *    Paints the top of the web page setting the
     *    title to the name of the starting test.
     *    @param string $test_name      Name class of test.
     *    @access public
     */
    function paintHeader($test_name) {
		chdir(_CHDIR); // va savoir Charles... des fois il le perd en route ?
		include_spip('inc/filtres_mini');
        $this->sendNoCacheHeaders();
        print "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.01 Transitional//EN\" \"http://www.w3.org/TR/html4/loose.dtd\">";
        print "<html>\n<head>\n<title>$test_name</title>\n";
        print "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=" .
                $this->_character_set . "\">\n";
        print "<style type=\"text/css\">\n";
        print $this->_getCss() . "\n";
        print "</style>\n";
		print "<link rel='stylesheet' href='" . url_absolue(find_in_path('css/tests.css')) . "' type='text/css' />";
        print "</head>\n<body>\n";
		
		print "<h1>Tests SPIP " . $this->version_spip() . "</h1>\n";
        print "<h2>$test_name</h2>\n";
        flush();	
	}
	
    /**
     *    Paints the end of the test with a summary of
     *    the passes and failures.
     *    @param string $test_name        Name class of test.
     *    @access public
     */
    function paintFooter($test_name) {
        $colour = ($this->getFailCount() + $this->getExceptionCount() > 0 ? "red" : ($this->getNaCount()>0 ? "#ffaa00" : "green"));
		
        print "<div style=\"";
        print "padding: 8px; margin-top: 1em; background-color: $colour; color: white;";
        print "\">";
        print $this->getTestCaseProgress() . "/" . $this->getTestCaseCount();
        print " test complete:\n";
        print "<strong>" . $this->getPassCount() . "</strong> passes, ";
        print "<strong>" . $this->getFailCount() . "</strong> fails, ";
        print "<strong>" . $this->getExceptionCount() . "</strong> exceptions and ";
        print "<strong>" . $this->getNaCount() . "</strong> non applicable.";
        print "</div>\n";
        print "</body>\n</html>\n";
    }
	
	/** 
	 * retourne le nombre de tests non applicables
	 * @return int	Nombre de tests non applicables
	 */
	function getNaCount(){
		return $this->_na;
	}
	
    /**
     *    Paints a PHP exception.
     *    @param Exception $exception        Exception to display.
     *    @access public
     */
    function paintException($exception) {
		switch(get_class($exception)) {
			case 'SpipNaException':
				$this->paintNA($exception);
				break;
			case 'SpipTestException':
				$this->paintTestException($exception);
				break;
			default:
				parent::paintException($exception);
				break;
		}
    }
	
	/**
	 * Paints a Non Applicable Test
	 * 
     * @param Exception $exception    The actual exception thrown.
     * @access public
     */
	function paintNa($exception) {
		$this->_na++;
		
		print "<span class=\"na\">Non applicable</span>: ";
		$breadcrumb = $this->getTestList();
		array_shift($breadcrumb);
		print implode(" -&gt; ", $breadcrumb);
		$message = $exception->getMessage();
		print " -&gt; <strong>" . $this->_htmlEntities($message) . "</strong><br />\n";				
	}
	
	/**
	 * Paints a Spip Test Exception
	 * 
     * @param Exception $exception    The actual exception thrown.
     * @access public
     */
	function paintTestException($exception) {
		$this->_exceptions++;
		
		print "<span class=\"fail\">Exception</span>: ";
		$breadcrumb = $this->getTestList();
		array_shift($breadcrumb);
		print implode(" -&gt; ", $breadcrumb);
		$message = $exception->getMessage();
		print " -&gt; <strong>" . $this->_htmlEntities($message) . "</strong><br />\n";				
	}
	
	
	function paintGroupStart($test_name, $size){
		$test_name = str_replace(realpath(SpipTest::me()).'/','',$test_name);
		parent::paintGroupStart($test_name, $size);
		#echo "<ul><li><h3>$test_name</h3>\n";
	}
/*	
	function paintGroupEnd($test_name){
		parent::paintGroupEnd($test_name);
		echo "</li></ul>\n";
	}
	
	function paintCaseStart($test_name) {
		parent::paintCaseStart($test_name);
		echo "<ul><h3>$test_name</h3>\n";
    }
	function paintCaseEnd($test_name) {
		parent::paintCaseEnd($test_name);
		echo "</ul>\n";
    }
	
	function paintMethodStart($test_name) {
		parent::paintMethodStart($test_name);
		echo "<li>$test_name</li>\n";
    }
	function paintMethodEnd($test_name) {
		parent::paintMethodEnd($test_name);
		parent::paintFooter($test_name);
    }	
*/		
	/**
	 * Donne le nom de la version SPIP en cours
	 */
	function version_spip() {
		include_spip('inc/minipres');
		$version = $GLOBALS['spip_version_affichee'];
		if ($svn_revision = version_svn_courante(_DIR_RACINE)) {
			$version .= ' ' . (($svn_revision < 0) ? 'SVN ':'')
			. "[<a href='http://trac.rezo.net/trac/spip/changeset/"
			. abs($svn_revision) . "' onclick=\"window.open(this.href); return false;\">"
			. abs($svn_revision) . "</a>]";
		}
		return $version;
	}
}


/**
 * Extension de HTMLReporter
 * pour donner le bon repertoire de travail
 * et ajouter des fonctions specifiques a SPIP
 */
class SpipMiniHtmlReporter extends SpipHtmlReporter {
	function SpipMiniHtmlReporter($charset='UTF-8') {
		chdir(_CHDIR);
        $this->SpipHtmlReporter($charset);
	}
	

    /**
     *    Paints the top of the web page setting the
     *    title to the name of the starting test.
     *    @param string $test_name      Name class of test.
     *    @access public
     */
    function paintHeader($test_name) {
		include_spip('inc/filtres_mini');
        $this->sendNoCacheHeaders();
        flush();	
	}
	
	
   /**
     *    Paints the end of the test with a summary of
     *    the passes and failures.
     *    @param string $test_name        Name class of test.
     *    @access public
     */
    function paintFooter($test_name) {
        if ($this->getFailCount() + $this->getExceptionCount() == 0) {
			if ($this->getNaCount()) {
	            print "OK <em>(".$this->getPassCount().")</em> but some NA <em>(".$this->getNaCount().")</em>\n";
			} else {
				print "OK <em>(".$this->getPassCount().")</em>\n";
			}
        } else {
            print "BOUM !!!\n";
			print "<span class='complements'>- Passes: " . $this->getPassCount() .
					", Failures: " . $this->getFailCount() .
					", Exceptions: " . $this->getExceptionCount() .
					", Non Applicable: " . $this->getNaCount() . "</span>\n";
        }
    }
	
	
	/**
	 * Donne le nom de la version SPIP en cours
	 */
	function version_spip() {
		return SpipHtmlReporter::version_spip();
	}
	
}

/**
 * Extension de TestReporter
 * pour donner le bon repertoire de travail
 * et ajouter des fonctions specifiques a SPIP
 */
class SpipTextReporter extends TextReporter {
	function SpipTextReporter() {
		chdir(_CHDIR);
        $this->TextReporter();	
	}
	
   /**
     *    Paints the end of the test with a summary of
     *    the passes and failures.
     *    @param string $test_name        Name class of test.
     *    @access public
     */
    function paintFooter($test_name) {
        if ($this->getFailCount() + $this->getExceptionCount() == 0) {
			if ($this->getNaCount()) {
	            print "OK ($this->getPassCount()) but some NA ($this->getNaCount())\n";
			} else {
				print "OK ($this->getPassCount())\n";
			}
        } else {
            print "FAILURES!!!\n";
			print "Test cases run: " . $this->getTestCaseProgress() .
					"/" . $this->getTestCaseCount() .
					", Passes: " . $this->getPassCount() .
					", Failures: " . $this->getFailCount() .
					", Exceptions: " . $this->getExceptionCount() .
					", Non Applicable: " . $this->getNaCount() . "\n";
        }
    }
}


class SqueletteTest{
	var $title = "";
	var $head = "";
	var $body = "";
	
	/**
	 * Constructeur
	 * @param string $title		Donne un titre a la page
	 */
	function SqueletteTest($title = ""){
		$this->setTitle($title ? $title : "Squelette de test");
	}
	
	/**
	 * Change le title
	 * @param string $title		Donne un titre a la page
	 * @return null
	 */
	function setTitle($title){
		$this->title = $title;
	}
	
	/**
	 * Ajoute insert Head
	 * @return null
	 */
	function addInsertHead(){
		$this->head = "\n#INSERT_HEAD\n" . $this->head;
	}
	
	/**
	 * Ajoute dans head
	 * @return null
	 */
	function addToHead($content){
		$this->head .= "\n" . $content;
	}	
	
	/**
	 * Ajoute dans body
	 * @return null
	 */
	function addToBody($content){
		$this->body .= "\n" . $content;
	}
		
	/**
	 * Retourne le code du squelette
	 */
	function code(){
		$code = '
			<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
			<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="#LANG" lang="#LANG" dir="#LANG_DIR">
			<head>
			<title>'. $this->title . '</title>
			' . $this->head . '
			</head>
			<body class="page_test">
			' . $this->body . '
			</body>
			</html>		
		';
		return $code;
	}
}




// si provient de la base des tests de spip,
// on affiche simplement un 'OK : ..."

if (_request('mode') == 'test_general') {
	SimpleTest::prefer(new SpipTextReporter());
	SimpleTest::prefer(new SpipMiniHtmlReporter());
} else {
	SimpleTest::prefer(new SpipHtmlReporter());
}

?>
