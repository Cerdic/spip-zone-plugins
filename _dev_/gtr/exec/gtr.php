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
  echo "<textarea name='texte' cols='50' rows='10'></textarea><br />";
  echo "<input type='submit' value='Traduire' />";
  echo "</form>";
	$bj = $_POST['texte'];
	echo $bj;
	?><script type="text/javascript">

    google.load("language", "1");

    function initialize() {
      google.language.translate("<?php echo $bj; ?>", "fr", "en", function(result) {
        if (!result.error) {
          var container = document.getElementById("translation");
          container.innerHTML = result.translation;
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