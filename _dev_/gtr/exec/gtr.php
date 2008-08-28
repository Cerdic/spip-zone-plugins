<?php
	if (!defined("_ECRIRE_INC_VERSION"));
	include_spip('inc/presentation');
	include_spip('inc/config');
	include_spip("inc/meta");
	//fonction principal de la page
	function exec_gtr () {
	$commencer_page = charger_fonction('commencer_page', 'inc');
echo $commencer_page("gtr", "", "");
echo gros_titre('Google Translate','',false);
echo '<script type="text/javascript" src="http://www.google.com/jsapi"></script>';
$action = generer_url_ecrire('gtr');
	
    
	echo "<form action='$action' method='post'>";
	echo "Rentrez votre texte &agrave; traduire en anglais<br />";
  echo "<div id='text'><textarea name='texte' cols='50' rows='10'></textarea></div><br />";
  echo "<input type='submit' value='Traduire' />";
  echo "</form>";
	$bj = $_POST['texte'];
	echo $bj;
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
	if ($bj) {
    echo "<div id='translation' style='border: 2px solid #000000'></div>";
	}
}
?>