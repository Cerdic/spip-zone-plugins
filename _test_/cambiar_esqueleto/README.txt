CAMBIAR ESQUELETO/VISUALIZACION DE LA WEB

Sistema de seleccion de esqueletos por menu desplegable.

Para poder utilizarlo solo tienes que ir colocando en una carpeta llamada "esqueletos" todos los esqueletos que quieras, cada uno en una subcarpeta con todos sus elementos necesarios: los html, estilos, imagenes propias,... y una baliza #CAMBIAR_ESQUELETO donde quieras que aparezca el desplegable.

Muestra en una lista los esqueletos que haya en las carpetas (si están en la raíz):
- esqueletos
- squelettes-test
- themes
y los "dist" y "squelettes".
INSTALACION

El plugin:

- Descomprime el archivo "plugin_cambiar_esqueleto.zip".

- Sube la carpeta "cambiar_esqueleto" a la carpeta "plugins" de tu SPIP. Estara en la raiz del sitio, y si no crea una y llamala "plugins".

- Entra en el espacio privado de tu sitio y en Configuracion del sitio > Gestion de los plugins, marca la casilla de "Cambiar esqueleto" para activarlo.

Los esqueletos:

- Crea en la raiz del sitio una carpeta llamada "esqueletos".

- Pon en esa carpeta los esqueletos que quieras, cada uno en una subcarpeta propia. IMPORTANTE: los nombres de las carpetas tienen que ser sin espacios en blanco y sin caracteres "raros" como acentos y dem‡«s signos propios del castellano. O sea, "mi_esqueleto_anejo" estaria bien pero "mi esqueleto a–ejo" daria problemas.

- A uno de ellos, el que quieras que aparezca por defecto cuando alguien entra por primera vez, llamalo "predeterminada".

- Coloca la baliza #CAMBIAR_ESQUELETO donde quieras que aparezca el desplegable (cabecera del sitio, menú de navegación,...) para seleccionar los esqueletos.
- HechoPERSONALIZAREn el archivo baliza_cambiar_esqueleto.php, a partir de "function balise_CAMBIAR_ESQUELETO($p)...", encontraras todos los elementos de tama–o de letra, color, subrayados, caja del desplegable,...

Si quieres que el esqueleto "predeterminada" se llame de otra manera abre el archivo cambiar_esqueleto_options.php y cambia el nombre "predeterminada", esta un par de veces, por el de la carpeta que quieras tener como primer esqueleto cuando entran de nuevo a tu sitio.

AGRADECIMIENTOSCuando casi tenia acabada esta contribucion encontre una propuesta parecida en el sitio 3615MARLENE (http://marlene.c3ew.com). He mantenido lo que llevaba hecho, pero he incorporado de su desarrollo la idea y el codigo para vaciar la cache al cambiar de esqueletos y que salgan solo los nuevos. Gracias.

====================================
CopyLeft 2006 joseluis@digital77.com
