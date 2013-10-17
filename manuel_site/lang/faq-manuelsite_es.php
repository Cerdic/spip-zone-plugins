<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/faq-manuelsite?lang_cible=es
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// F
	'forum' => 'Los foros se activan por defecto en sus artículos @complement@; pueden desactivarse caso por caso... Los visitantes pueden por tanto reaccionar en sus artículos... Será advertido por mail cada vez que un mensaje sea publicado en uno de sus artículos. Pequeña desventaja: a veces tendrá que administrar manualmente aquellos spams que no resulten evidentes como para rechazarlos. Para tratar un mensaje de foro (eliminarlo si no le agrada o señalarlo como spam si resulta uno):
-* En el sitio público, en la página del artículo, si se encuentra identificado, existen dos botones "Eliminar este mensaje" o "SPAM"
-* En el espacio privado, vía el menú Actividad / Seguir los Foros', # MODIF
	'forum_q' => '¿Cómo administrar los foros?',

	// I
	'img' => 'No hay un "buen" tamaño para mostrar una imagen en un artículo. En todo caso, es inútil enviar una imagen de 3000 pixels de anchura, ¡ninguna pantalla podrá mostrarla en su integridad! Salvo si el documento está destinado a impresión.
-* Si la imagen ha de estar integrada en el texto de un artículo, todo depende de su contenido: si se trata de un retrato, una altura de 200px cabría hacer atención a las arrugas; si se trata de un bello paisaje, puede irse hasta {{@largeur_max@}} pixels máximo de anchura.
-* Si la imagen está prevista para la cartera de un artículo, no sobrepasar los 1000 pixels de ancho o los 600 pixels de alto.

{Atención, el peso máximo a sobrepasar es de {{@poids_max@}}Mo sin los que la descarga será rechazada}.',
	'img_nombre' => 'Es posible enviar en un clic varias fotos en un artículo:
-* Copiar las fotos elegidas en una carpeta de su disco duro
-* Redimensionarlas en el buen tamaño
-* Insertarlas en un archivo zip
-* Añadir este archivo zip al artículo. Tras la descarga, se le pide que cree el fichero, puede por ejemplo desposar todas las fotos en la cartera.',
	'img_nombre_q' => '¿Cómo llenar fácilmente una cartera?',
	'img_ou_doc' => 'Se utiliza sobre todo la etiqueta <code><imgXX|center></code> para insertar una imagen en un texto del artículo. Pero si se quiere además mostrar el título o la descripción bajo la imagen, se ha de utilizar <code><docXX|center></code>.',
	'img_ou_doc_q' => '¿<code><imgXX> o <docXX></code> ?',
	'img_q' => '¿Qué tamaño debe tener mi foto?',

	// S
	'son_audacity_q' => '¿Cómo preparar un sonido?',
	'son_q' => '¿Cómo añadir un sonido a un artículo?',

	// T
	'thumbsites' => 'Hacer clic sobre «Referenciar un sitio» en la sección {{@rubrique@}}. Informar de la url del sitio, y validar, el sistema tratará de recuperar el título, la descripción y una miniatura del sitio en línea. Corregir el título y la descripción si es necesario. Si la miniatura no se genera automáticamente, hacer una captura de pantalla e insertarla como logo del sitio en 120x90 pixels.',
	'thumbsites_q' => '¿Cómo referenciar un sitio en la página de enlaces?',
	'trier' => 'Los numéros delante de los títulos de los artículos / las secciones / los documentos, permiten administrar su orden de visualización. La sintaxis es un número seguido de un punto y de un espacio',
	'trier_q' => '¿Cómo administrar la orden de visualización de los artículos / las secciones / los documentos?',

	// V
	'video_320x240_q' => '¿Cómo añadir un vídeo a un artículo?',
	'video_dist' => 'Si su viídeo está alojado en DailyMotion, YouTube o Viméo, en una nueva pestaña de su navegador, ir a la página de visualización del vídeo, y copiar la url. En la página de edición de su artículo haga clic sobre "Añadir un vídeo" y pegar la url. Insertar entonces en el área de texto del artículo <code><videoXX|center></code>',
	'video_dist_q' => '¿Cómo añadir un vídeo dailymotin (youtube...) a un artículo?'
);

?>
