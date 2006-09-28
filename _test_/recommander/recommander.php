<?php

// Envoyer cet article par email
//

// TODO :
// - internationalisation
// - presentation + cache (squelette ?)
// - previsualisation et explications (on va envoyer ce message de votre part)

/*
>È Message envoyŽ !
Mensagem enviada!
>È Erreur lors de l'envoi du message.
Erro no envio da mensagem
*/

if (!defined("_ECRIRE_INC_VERSION")) return;



//
// Fonction de base de cet INCLURE
//
// Dans la variable $contexte_inclus on trouve les donnees de l'article
// dans _request() les donnees du visiteur
function main_recommander() {
	if (!$f = charger_fonction('fragment_recommander', 'action', true))
		die('erreur fragment_recommander absent');

	lang_select($GLOBALS['contexte_inclus']['lang']);

	echo "<div id='recommander' class='formulaire_spip'>\n"
	. "<h2 class='menu-titre'>"
	. _T("recommander:recommander")."</h2>"
	. "<div id='formulaire_recommander'>";

	echo $f();

	$searching_div = '<div style="float: '.$GLOBALS['spip_lang_right'].'; z-index:2;"><img src="'._DIR_IMG_PACK.'searching.gif'.'" /></div>';

	echo "</div>"
	. "</div>";

	echo '
<script type="text/javascript"><!--
if (typeof jQuery == "function") {
	var ajax_image_searching = "'.addslashes($searching_div).'";'
	.<<<EOS
	$("div#formulaire_recommander").hide();
	function recommander_js() {
		$("div#formulaire_recommander").css("height","");
		$("div#formulaire_recommander form")
		.prepend(
			"<input name='action' value='fragment_recommander' type='hidden' />"
		)
		.ajaxForm("#formulaire_recommander",
			recommander_js,
			function() {
				$("#formulaire_recommander").prepend(ajax_image_searching);
			}
		);
	}
	recommander_js();
	$("#recommander>h2").click(function(){
		$("div#formulaire_recommander:visible").slideUp("slow");
		$("div#formulaire_recommander:hidden").slideDown("slow");
	});
}
// --></script>
EOS;

	lang_dselect();

}

// main()
echo main_recommander();

?>
