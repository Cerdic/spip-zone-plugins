<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/courtcircuit?lang_cible=es
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'aide_en_ligne' => 'Ayuda en línea',

	// C
	'configurer_courtcircuit' => 'Configurar las reglas de cortocircuito de las secciones',
	'courtcircuit' => 'Cortocircuito',

	// E
	'explication_liens_rubriques' => '¿Modificar la URL de las secciones redirigidas directamente en los esqueletos?',
	'explication_regles' => 'Las diferentes reglas a continuación son probadas en este orden. Si ninguna regla define una redirección, entonces la sección será mostrada normalmente. ',
	'explication_restreindre_langue' => 'Si esta opción está activada, sólo los artículos del idioma activo serán considerados para el cálculo de la redirección. Esta opción no es útil salvo si sus secciones contienen artículos de diferentes idiomas. No utilizar si su sitio está organizado en sectores de idioma o si utiliza los campos multi.',
	'explication_sousrubrique' => 'Parcourir la première sous-rubrique (tri par numéro du titre et date) ? Les règles de redirection seront testées à nouveau dans cette sous-rubrique.', # NEW
	'explication_variantes_squelettes' => 'Exemple : squelettes de la forme rubrique-2.html ou rubrique=3.html.', # NEW

	// I
	'item_appliquer_redirections' => 'Aplicar las reglas de redirección',
	'item_jamais_rediriger' => 'No redirigir nunca',
	'item_ne_pas_rediriger' => 'No redirigir',
	'item_rediriger_sur_article' => 'Redirigir a este artículo',

	// L
	'label_article_accueil' => 'Artículo de Inicio de la sección',
	'label_composition_rubrique' => 'Sección con composición',
	'label_exceptions' => 'Excepciones',
	'label_liens' => 'URL de las secciones',
	'label_liens_rubriques' => 'Agir sur la balise #URL_RUBRIQUE ?', # NEW
	'label_plus_recent' => 'Artículo más reciente de la sección',
	'label_plus_recent_branche' => 'Artículo de la rama más reciente',
	'label_rang_un' => 'Primer artículo (artículos numerados)',
	'label_regles' => 'Reglas de redirección de las secciones',
	'label_restreindre_langue' => 'Ne prendre en compte que les articles de la langue ?', # NEW
	'label_sousrubrique' => 'Sous-rubriques', # NEW
	'label_un_article' => 'Seul article de la rubrique', # NEW
	'label_variantes_squelettes' => 'Rubrique avec variante de squelettes' # NEW
);

?>
