<?php
	if (!defined("_ECRIRE_INC_VERSION"));
	include_spip('inc/presentation');
	include_spip('inc/config');
	include_spip("inc/meta");
	//fonction principal de la page
	function exec_gtr () {
	$commencer_page = charger_fonction('commencer_page', 'inc');
echo $commencer_page("Google Translate", "", "");
echo gros_titre('Google Translate','',false);
echo '<script type="text/javascript" src="http://www.google.com/jsapi"></script>';
$action = generer_url_ecrire('traducteur');
	
    
	echo "<form action='$action' method='post'>";
	echo "Rentrez votre texte &agrave; traduire en anglais<br />";
  echo "<textarea name='texte' cols='50' rows='10'></textarea><br />";
  echo "<input type='submit' value='Traduire' />";
  echo "</form>";
	$for_trad = $_POST['texte'];
	echo "<div id='text'>$for_trad</div>";
	?><script type="text/javascript">


    
    google.load("language", "1");

    function initialize() {
      var text = document.getElementById("text").innerHTML;
      google.language.detect(text, function(result) {
        if (!result.error && result.language) {
          google.language.translate(text, result.language, "en",
                                    function(result) {
            var translated = document.getElementById("translation");
            if (result.translation) {
              translated.innerHTML = result.translation;
            }
          });
        }
      });
    }
    google.setOnLoadCallback(initialize);


    </script><?php
	if ($for_trad) {
    echo "<div id='translation' style='border: 2px solid #000000'></div>";
	}
}
?>