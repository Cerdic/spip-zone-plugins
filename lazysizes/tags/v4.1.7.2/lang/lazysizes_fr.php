<?php

// Ceci est un fichier langue de SPIP -- This is a SPIP language file


if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(


	// C
	'cfg_titre_page'	=> 'Configurer LazySizes',
	'cfg_explication_css'	=> 'Inssérer les styles dans l\'entête des pages',
	'cfg_label_css'	=> 'Insertion des css',
	// E
	// Art direction
	'explication_artdirect'=>'{{Art Direction :}}
		L\'extension artdirect vous permet de contrôler totalement
	 la direction artistique via votre CSS.
	 [Documentation->https://github.com/aFarkas/lazysizes/tree/gh-pages/plugins/artdirect] ',
	'explication_aspectratio'=>'{{lazysizes aspectratio extension :}}',
	'explication_attrchange'=>'{{lazysizes attribute change / re-initialization extension :}}',
	// BgSet
	'explication_bgset'=>'{{bgset - responsive background images :}}
	Permet de définir plusieurs images d’arrière-plan avec un descripteur de largeur ou une rêgle de l\'extension customMedia, similaire à la manière dont <code>img[srcset]</code> fonctionne.
Également pour des images avec direction artistique utilisant des requêtes multimédias.
Combinable a customMedia, parentfit.

	[Documentation->https://github.com/aFarkas/lazysizes/blob/master/plugins/bgset]',
	// BlurUp
	'explication_blur-up'=>'{{The lazysizes Blur Up/effect plugin plugin :}}',
	// CustoMedia
	'explication_custommedia'=>'{{CustomMedia :}}
Permet de synchronniser les breakpoints définis dans la configuration entre vos css, le js et l\'attribut media des images responsive (picture,img).
Peut être combiné avec bgset, pour des backgrounds responsives.

	[Documentation->https://github.com/aFarkas/lazysizes/tree/gh-pages/plugins/custommedia]',
	'explication_fix-io-sizes'=>'{{Fix io Sizes :}}',
	'explication_include'=>'{{lazysizes include plugin :}}',
	'explication_noscript'=>'{{lazysizes noscript/progressive enhancement extension :}}',
	'explication_object-fit'=>'{{lazySizes object fit extension :}}',
	'explication_optimumx'=>'{{lazysizes optimumx plugin :}}',
	// ParentFit
	'explication_parent-fit'=>'{{Parent Fit}}
	étend la fonctionnalité <code>data-values = "auto"</code> afin de calculer également les bonnes tailles pour <code>object-fit: constain | cover </code>en hauteur (et en largeur) en général.
Peut être combiné avec bgset.
	[Documentation->https://github.com/aFarkas/lazysizes/blob/gh-pages/plugins/parent-fit/]',
	'explication_print'=>'',
	'explication_progressive'=>'',
	'explication_respimg'=>'{{Resp Img polyfill}}
  Polyfill pour la prise en charge dans les navigateurs du chargement d\'images suivant la taille de l\'écran. Peut être utilisé conjointement avec <code>bgset</code> et <code>custom-media</code>
  [Documentation->https://github.com/aFarkas/lazysizes/tree/gh-pages/plugins/respimg]',
	'explication_rias'=>'',
	'explication_static-gecko-picture'=>'',
	'explication_twitter'=>'',
	'explication_unload'=>'',
	'explication_unveilhooks'=>'',
	'explication_video-embed'=>'',
	// L
	'lazysizes_titre'	=> 'LazySizes',


);
