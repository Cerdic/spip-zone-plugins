<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/faq-manuelsite?lang_cible=es
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// F
	'forum' => 'Los foros se activan por defecto en sus artículos @complement@; pueden desactivarse caso por caso... Los visitantes pueden por tanto reaccionar en sus artículos... Será advertido por correo electrónico cada vez que un mensaje sea publicado en uno de sus artículos. Pequeña desventaja: a veces tendrá que administrar manualmente aquellos spams que no sean tan evidentes como para rechazarlos. Para tratar un mensaje de foro (eliminarlo si no le agrada o señalarlo como spam si se trata de uno):
-* En el sitio público, en la página del artículo, si se encuentra identificado, existen dos botones "Eliminar este mensaje" o "SPAM"
-* En el espacio privado, vía el menú Actividad / Seguir los Foros',
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
	'son' => 'Preparar su sonido en formato mp3 en mono con una frecuencia de 11 o 22 kHz y un bitrate (tasa de comprensión) de 64kbps (o más si desea una calidad superior).
	
Asociar el archivo mp3 a su artículo como para una imagen y darle un título y eventualmente una descripción y un crédito.
Finalmente colocar en el cuerpo de su artículo en el lugar deseado <code><docXX|center|player></code>. Un lector flash aparecerá en su sitio público para permitir al visitante lanzar el sonido.
_ {Atención, el tamaño máximo de un archivo es de 150M, o sea, alrededor de una duración de 225 minutos}',
	'son_audacity' => 'Para trabajar un archivo audio, puede utilizar el software Audacity (Mac, Windows, Linux) descargable aquí
[->http://audacity.sourceforge.net/]. Algunos trucos:
-* Tras instalar el software, necesitará la biblioteca lame para la codificación mp3 [->http://audacity.sourceforge.net/help/faq?s=install&item=lame-mp3].
-* Para pasar el archivo a mono: Menú {Pistas/Pista stéréo hacia mono}
-* Para crear el archivo mp3: Menú {Archivo/Exportar}
-* Para regular bitrate: Menú {Archivo/Exportar/Opciones/Calidad}',
	'son_audacity_q' => '¿Cómo preparar un sonido?',
	'son_q' => '¿Cómo añadir un sonido a un artículo?',

	// T
	'thumbsites' => 'Hacer clic sobre «Referenciar un sitio» en la sección {{@rubrique@}}. Informar de la url del sitio, y validar, el sistema tratará de recuperar el título, la descripción y una miniatura del sitio en línea. Corregir el título y la descripción si es necesario. Si la miniatura no se genera automáticamente, hacer una captura de pantalla e insertarla como logo del sitio en 120x90 pixels.',
	'thumbsites_q' => '¿Cómo referenciar un sitio en la página de enlaces?',
	'trier' => 'Los numéros delante de los títulos de los artículos / las secciones / los documentos, permiten administrar su orden de visualización. La sintaxis es un número seguido de un punto y de un espacio',
	'trier_q' => '¿Cómo administrar la orden de visualización de los artículos / las secciones / los documentos?',

	// V
	'video_320x240' => 'Preparar su vídeo en formato flv (streaming flash) en 320x240 pixels con un bitrate (tasa de comprensión) de 400kbps y un sonido en mono/64kbps. Para convertir un archivo vídeo, puede utilizar el software avidemux (Mac, Windows, Linux) descargable por aquí [->http://www.avidemux.org/]. 

Asociar el archivo creado en vuestro artículo como un documento adjunto, darle un título, eventualmente una descripción y un crédito, y un tamaño (anchura 320, altura 240). Para colocar dentro del cuerpo de su artículo en el lugar deseado <code><docXX|center|video></code>. Un lector flash aparecerá en su sitio público para permitir al visitante lanzar el vídeo.
_ {Atención, el tamaño máximo de un archivo es de 150M, o sea, en torno a una duración de 37.5 minutos}',
	'video_320x240_q' => '¿Cómo añadir un vídeo a un artículo?',
	'video_dist' => 'Si su viídeo está alojado en DailyMotion, YouTube o Viméo, en una nueva pestaña de su navegador, ir a la página de visualización del vídeo, y copiar la url. En la página de edición de su artículo haga clic sobre "Añadir un vídeo" y pegar la url. Insertar entonces en el área de texto del artículo <code><videoXX|center></code>',
	'video_dist_q' => '¿Cómo añadir un vídeo dailymotin (youtube...) a un artículo?'
);

?>
