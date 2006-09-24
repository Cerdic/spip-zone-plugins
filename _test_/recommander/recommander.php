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
	. "<span class='recommander_titre'>"
	. _T("recommander:recommander")."</span>"
	. "<div id='formulaire_recommander'>";

	echo $f();

	echo "</div>"
	. "</div>\n"
	. <<<EOS
<script type="text/javascript"><!--
if (typeof jQuery == "function") {
	$("div#formulaire_recommander").hide();
	function recommander_js() {
		$("div#formulaire_recommander").css("height","");
		$("div#formulaire_recommander form")
		.prepend(
			"<input name='action' value='fragment_recommander' type='hidden' />"
		)
		.submit(function(){
			$(this)
			.ajaxSubmit("#formulaire_recommander", recommander_js);
			return false;
		});
	}
	recommander_js();
	$("span.recommander_titre").oneclick(function(){
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
