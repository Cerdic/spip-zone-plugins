<?php

function spipicious_affichage_final($page){

    // sinon regarder rapidement si la page a des classes crayon
    if (!strpos($page, 'form_spipicious_ajax'))
        return $page;

    // voire un peu plus precisement lesquelles
    include_spip('inc/spipicious');
    $spipiciouscfg = spipiciouscfgform();

	$page = spipicious_preparer_page($page, array('*'), $spipiciouscfg);
    return $page;
}

function spipicious_preparer_page($page, $droits, $wdgcfg = array()) {

	$interface = find_in_path('javascript/interface.js');
	$iautocompleter = find_in_path('javascript/iautocompleter.js');
	$jqueryautocomplete = generer_url_public('jquery.autocomplete.js');
	$autocompletecss = find_in_path('jquery.autocomplete.css');
	$id_article = _request('id_article');
	$urlselecteur = parametre_url(generer_url_ecrire('selecteur_generique',
		'quoi=mot'),
		id_article, $id_article, '\\x26');;

    $incHead = <<<EOS
<script type='text/javascript' src='$interface'></script>
<script type='text/javascript' src='$iautocompleter'></script>
<script type='text/javascript' src='$jqueryautocomplete'></script>
<link rel="stylesheet" href="$autocompletecss" type="text/css" media="all" />
<script  type="text/javascript"><!--
	var appliquer_selecteur_cherche_mot = function() {

	// chercher l'input de saisie
	var inp = jQuery('input[@name=tags]', this);

	// ne pas reappliquer si on vient seulement de charger les suggestions
	if (!inp[0] || inp[0].autoCFG) return;

	// attacher l'autocompleter
	inp.each(function() {
		var me = this;
		var id_groupe = $("#select_groupe").val();
		var id_article = $("#spipicious_id").val();
		jQuery(this)
		.Autocomplete({
			'source': '$urlselecteur'+'\x26id_article='+id_article+'\x26id_groupe='+id_groupe,
			'delay': 300,
			'autofill': false,
			'helperClass': "autocompleter",
			'selectClass': "selectAutocompleter",
			'minchars': 1,
			'mustMatch': true,
			'cacheLength': 20,
			'onSelect': 
			function(li) {
			if (li.id > 0) {
					jQuery(me)
					.end();
				}
			}
		});
	});
	}
	jQuery(document).ready(appliquer_selecteur_cherche_mot);
	onAjaxLoad(function(){setTimeout(appliquer_selecteur_cherche_mot, 200);});
// --></script>
EOS;
	return substr_replace($page, $incHead, strpos($page, '</head>'), 0);

}

?>