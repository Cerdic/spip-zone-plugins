<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/plugonet?lang_cible=es
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// B
	'bouton_lancer' => 'Lanzar',
	'bouton_plugonet' => 'PlugOnet',
	'bouton_tout_cocher' => 'Marcar todo',
	'bouton_tout_decocher' => 'Desmarcar todo',

	// D
	'details_generation_paquetxml_erreur' => 'El paquet.xml du plugin no ha sido generado porque se han producido errores durante el proceso. Consulte por favor la información siguiente para aportar las correcciones necesarias.',
	'details_generation_paquetxml_erreur_pluriel' => 'Los paquet.xml des @nb@ plugins no han sido generados porque se han producido errores durante el proceso. Consulte por favor la información siguiente para aportar las correcciones necesarias.',
	'details_generation_paquetxml_notice' => 'El paquet.xml du plugin se ha generado correctamente pero su plugin.xml fuente contiene errores. Verifique por favor el plugin.xml y los archivos resultantes (paquet.xml, archivos de idioma) para determinar si deben aportarse correcciones.',
	'details_generation_paquetxml_notice_pluriel' => 'Los paquet.xml des @nb@ plugins se han generado correctamente pero sus plugin.xml fuente contienen errores. Verifique por favor los plugin.xml y los archivos resultantes (paquet.xml, archivos de idioma) para determinar si deben aportarse correcciones.',
	'details_generation_paquetxml_succes' => 'El paquet.xml del plugin se ha generado correctamente.',
	'details_generation_paquetxml_succes_pluriel' => 'Los paquet.xml de los @nb@ plugins se han generado correctamente.',
	'details_validation_paquetxml_erreur' => 'La validación formal del paquet.xml ha revelado errores. Consulte por favor la información siguiente para aportar correcciones.',
	'details_validation_paquetxml_erreur_pluriel' => 'La validación formal de los @nb@ paquet.xml ha revelado errores. Consulte por favor la información siguiente para aportar correcciones.',
	'details_validation_paquetxml_succes' => 'La validación formal del paquet.xml no ha revelado ningún error.',
	'details_validation_paquetxml_succes_pluriel' => 'La validacion formal de los @nb@ paquet.xml no ha revelado ningún error.',
	'details_verification_pluginxml_erreur' => 'La verificación del plugin.xml ha revelado errores. Consulte por favor la siguiente información para aportar correcciones si fuera necesario (todos los errores ligados a la utilización de etiqueta a, código, br... no son susceptibles de considerarse).',
	'details_verification_pluginxml_erreur_pluriel' => 'La verificación de los @nb@ plugin.xml ha revelado errores. Consulte por faor la información siguiente para aportar correcciones si fuera necesario (todos los errores ligados a la utilización de etiqueta a, código br... en la descripción no son susceptibles de considerarse).',
	'details_verification_pluginxml_succes' => 'La verificación del plugin.xml no ha revelado ningún error.',
	'details_verification_pluginxml_succes_pluriel' => 'La verificación de los @nb@ plugin.xml no ha revelado ningún error.',

	// I
	'index_aide_paqxmlaut' => 'Etiqueta <code>auteur</code>',
	'index_aide_paqxmlbout' => 'Etiquetas <code>menu</code> y <code>onglet</code>',
	'index_aide_paqxmlcopy' => 'Etiqueta <code>copyright</code>',
	'index_aide_paqxmlcred' => 'Etiqueta <code>credit</code>',
	'index_aide_paqxmldesc' => 'Eslogan y descripción',
	'index_aide_paqxmlexe' => 'Ejemplos de paquet.xml',
	'index_aide_paqxmlfoi' => 'Funciones, opciones y administración',
	'index_aide_paqxmlgen' => 'Archivo de descripción de un plugin: <code>paquet.xml</code>',
	'index_aide_paqxmllib' => 'Etiqueta <code>lib</code>',
	'index_aide_paqxmllic' => 'Etiqueta <code>licence</code>',
	'index_aide_paqxmlnec' => 'Etiquetas <code>necessite</code> y <code>utilise</code>',
	'index_aide_paqxmlnom' => 'Etiqueta <code>nom</code>',
	'index_aide_paqxmlpaquet' => 'Etiqueta <code>paquet</code>',
	'index_aide_paqxmlpath' => 'Etiqueta <code>chemin</code>',
	'index_aide_paqxmlpipe' => 'Etiqueta <code>pipeline</code>',
	'index_aide_paqxmlproc' => 'Etiqueta <code>procure</code>',
	'index_aide_paqxmlspip' => 'Etiqueta <code>spip</code>',
	'index_aide_paqxmltrad' => 'Etiqueta <code>traduire</code>',
	'info_choisir_paquetxml_valider' => 'Elija los archivos paquet.xml que desea validar. También puede hacer clic sobre el nombre de un paquet.xml para lanzar automáticamente su validación formal.',
	'info_choisir_pluginxml_generer' => 'Elija los archivos que desea convertir entre los presentes en la carpeta <code>plugins/</code> de este sitio. También puede hacer clic sobre el nombre de un plugin.xml para lanzar directamente la generación forzada de su paquet.xml en la carpeta temporal del sitio.',
	'info_choisir_pluginxml_verifier' => 'Elija los archivos plugin.xml que desea verificar. También puede hacer clic sobre el nombre de un plugin.xml para lanzar directamente su verificación.',
	'info_forcer_paquetxml' => 'Por omisión, el archivo paquet.xml solo esta escrito si su contenido esta validado según la nueva DTD. Sin embargo, puede forzar su escritura sin importar el resultado de la validación.',
	'info_generer' => 'Esta opción le permite generar el nuevo archivo paquet.xml de descripción de un plugin a partir de un archivo plugin.xml existente.<br />A parte del archivo paquet.xml, los archivos de idioma de los items eslogan y descripción del plugin, además de un archivo de comandos Unix, son creados en carpetas propias a cada plugin.',
	'info_paquet_existant' => 'paquet.xml existente',
	'info_simuler_paquetxml' => 'Por omisión, los archivos de resultado son creados en la carpeta de instalación de cada plugin. Sin embargo, puede elegir de crearlos en una carpeta temporal del sitio.',
	'info_valider' => 'Esta opción le permite validar formalmente el archivo paquet.xml de descripción de un plugin según su DTD. Este formulario propone la lista de archivos paquet.xml presentes en todas las carpetas de este sitio.',
	'info_verifier' => 'Esta opción le permite verificar el archivo plugin.xml de descripción de un plugin de manera a anticipar problemas en la generación del archivo paquet.xml. Este formulario propone la lista de archivos plugin.xml presentes en todas las carpetas de este sitio.',

	// L
	'label_choisir_xml' => '@dtd@.xml disponibles',
	'label_forcer_non' => 'No, respetar los resultados de la validación',
	'label_forcer_oui' => 'Sí, forzar la escritura',
	'label_generer_paquetxml' => 'Archivos resultado',
	'label_simuler_non' => 'No, escribir en el directorio plugins/ del sitio',
	'label_simuler_oui' => 'Sí, escribir en el directorio temporal tmp/plugonet/',
	'legende_resultats' => 'Resultados detallados por plugin',

	// M
	'message_nok_aucun_xml' => 'Ningún @dtd@.xml encontrado en los directorio de los plugins de este sitio.',
	'message_nok_information_pluginxml' => '@nb@ plugin.xml ilegible',
	'message_nok_information_pluginxml_pluriel' => '@nb@ plugin.xml ilegibles',
	'message_nok_lecture_pluginxml' => '@nb@ plugin.xml inaccesibles en lectura',
	'message_nok_lecture_pluginxml_pluriel' => '@nb@ plugin.xml inaccesibles en lectura',
	'message_nok_validation_paquetxml' => '@nb@ paquet.xml no conforme a la DTD',
	'message_nok_validation_paquetxml_pluriel' => '@nb@ paquet.xml no conformes a la DTD',
	'message_nok_validation_pluginxml' => '@nb@ plugin.xml no conforme a la DTD',
	'message_nok_validation_pluginxml_pluriel' => '@nb@ plugin.xml non conformes a la DTD',
	'message_notice_validation_pluginxml' => 'dentro de los cuales @nb@ vienen de un plugin.xml incorrecto',
	'message_notice_validation_pluginxml_pluriel' => 'dentro de los cuales @nb@ vienen de plugin.xml incorrectos',
	'message_ok_generation_paquetxml' => '@nb@ paquet.xml correctamente generado',
	'message_ok_generation_paquetxml_pluriel' => '@nb@ paquet.xml correctamente generados',
	'message_ok_validation_paquetxml' => '@nb@ paquet.xml correcto',
	'message_ok_validation_paquetxml_pluriel' => '@nb@ paquet.xml correctos',
	'message_ok_verification_pluginxml' => '@nb@ plugin.xml correcto',
	'message_ok_verification_pluginxml_pluriel' => '@nb@ plugin.xml correctos',

	// O
	'onglet_generer' => 'Generar paquet.xml',
	'onglet_valider' => 'Validar paquet.xml',
	'onglet_verifier' => 'Verificar plugin.xml',

	// R
	'resume_generation_paquetxml' => '@nb@ plugin.xml tratado (@duree@s): @details@.<br />Consulte por favor los resultados detallados a continuación.',
	'resume_generation_paquetxml_pluriel' => '@nb@ plugin.xml tratados (@duree@s): @details@.<br />Consulte por favor los resultados detallados a continuación.',
	'resume_validation_paquetxml' => '@nb@ paquet.xml validado (@duree@s): @details@.<br />Consulte por favor los resultados detallados a continuación.',
	'resume_validation_paquetxml_pluriel' => '@nb@ paquet.xml validados (@duree@s): @details@.<br />A continuación puede consultar el detalle de los resultados.',
	'resume_verification_pluginxml' => '@nb@ plugin.xml verificado (@duree@s): @details@.<br />A continuación puede consultar el detalle de los resultados.',
	'resume_verification_pluginxml_pluriel' => '@nb@ plugin.xml verificados (@duree@s): @details@.<br />A continuación puede consultar el detalle de los resultados.',

	// T
	'titre_boite_aide_paquetxml' => 'Ayuda acerca de paquet.xml',
	'titre_form_generer' => 'Generación de los archivos paquet.xml',
	'titre_form_valider' => 'Validación formal de los archivos paquet.xml',
	'titre_form_verifier' => 'Verificación de los archivos plugin.xml',
	'titre_page' => 'PlugOnet',
	'titre_page_navigateur' => 'PlugOnet'
);

?>
