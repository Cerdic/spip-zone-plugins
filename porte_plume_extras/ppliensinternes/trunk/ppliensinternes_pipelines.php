<?php

function ppliensinternes_porte_plume_barre_pre_charger($barres){

	$barre = &$barres['edition'];
		
	// Liens internes
	$barre->set('link', array(
		"dropMenu" => array(
			// lorem ipsum 3 paragraphes
			array(
				"id"          => 'ppliensinternes',
				"name"        => _T('ppliensinternes:barre_ppliensinternes'),
				"className"   => "ppliensinternes", 
				"replaceWith" => '
					function(markitup) {
						zone_selection = markitup.selection;
						jQuery.fn.mediabox({
							href:"'.generer_url_ecrire('ppliensinternes','var_zajax=contenu').'",
							onCleanup:function(){
								var textArea = $("#text_area");
								var len = textArea.val().length;
								var start = textArea[0].selectionStart;
								var end = textArea[0].selectionEnd;
								var tabId = $("input[name^=\'ppliensinternesarticle\']").val();
								if(tabId){
									var idArt = tabId.replace("|", "");
									var replacement= "[" 
										+ zone_selection 
										+ "->" 
										+ idArt
										+ "]";
									textArea.val(textArea.val().substring(0, start) + replacement + textArea.val().substring(end, len));
								}
							}
						});
					}
				',
				"display"     => true,
				"selectionType" => "word",
			),
		),
	));
	

	return $barres;
}

function ppliensinternes_porte_plume_lien_classe_vers_icone($flux){
	return array_merge($flux, array(	
		'ppliensinternes' => 'ppliensinternes.png',
	));
}
?>
