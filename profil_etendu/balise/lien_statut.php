<?php

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

include_spip('base/abstract_sql');

function balise_LIEN_STATUT ($p) {
  return calculer_balise_dynamique($p,'LIEN_STATUT',array());
}

function balise_LIEN_STATUT_stat($args, $filtres) {
	return $args;
}
function balise_LIEN_STATUT_dyn($url_public='',$lien_public='',$url_6forum='',$lien_6forum='',$url_1comite='',$lien_1comite='',$url_0minirezo='',$lien_0minirezo='') {
	$cont=array('lien' => $lien_public?$lien_public:$url_public,
						'url' => $url_public);	  	
	
	if (($GLOBALS['auteur_session']['statut']=="0minirezo")
		  &&($url_0minirezo!=''))
		$cont=array('lien' => $lien_0minirezo?$lien_0minirezo:$url_0minirezo,
						'url' => $url_0minirezo);
	elseif ((($GLOBALS['auteur_session']['statut']=="0minirezo")
			||($GLOBALS['auteur_session']['statut']=="1comite"))
		  &&($url_1comite!=''))
		$cont=array('lien' => $lien_1comite?$lien_1comite:$url_1comite,
						'url' => $url_1comite);
	elseif ((($GLOBALS['auteur_session']['statut']=="0minirezo")
			||($GLOBALS['auteur_session']['statut']=="1comite")
			||($GLOBALS['auteur_session']['statut']=="6forum"))
		  &&($url_6forum!=''))
		$cont=array('lien' => $lien_6forum?$lien_6forum:$url_6forum,
						'url' => $url_6forum);
	
	return array("formulaires/lien_statut", $GLOBALS['delais'],
					$cont);
}
?>