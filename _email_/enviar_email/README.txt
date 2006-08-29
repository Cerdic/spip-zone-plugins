PLUGIN ENVIAR EMAIL PARA SPIP 1.9

El plugin "Enviar email" para SPIP 1.9 crea un enlace que permite enviar la referencia del art�culo o breve por email a una o varias direcciones.

Al cliquear en el enlace aparece una ventana pop-up para recoger los datos necesarios y realizar el env�o.

Utiliza la clase phpmailer 1.73 lo que permite realizar el env�o por SMTP evitando los problemas que surgen al utilizar la funci�n mail de PHP.

INSTALACI�N

- Descomprime el archivo "plugin_enviar_email_spip_1_9.zip".
- Pon la carpeta enviar_email dentro de la carpeta plugins de tu instalaci�n de SPIP. Si no existe crea una y ll�mala "plugins".- En el espacio privado cliquea en Configuraci�n del sitio > Gesti�n de los plugins.- Marca la casilla de Enviar email para activarlo.- Donde quieras que aparezca (en los esqueletos article.html o breve.html) escribe #ENVIAR_EMAIL** (con los dos asteriscos)- Hecho

PERSONALIZAR

Para personalizar la tipograf�a del enlace puedes colocar la baliza #ENVIAR_EMAIL** entre <span style="font-size:65%">...</span>, por ejemplo, y en style puedes poner tipo de letra, tama�o, color,... o puedes crear una clase en tu hoja de estilos y pon�rsela,...

Para modificar el icono del sobre pon el que tu quieras (a poder ser en formato gif) en la carpeta enviar_email y ll�malo "sobre.gif"

Para otras modificaciones abre el archivo baliza_enviar_email.php y retoca lo que quieras. Por ejemplo, el '400' y el '430' son el ancho y el alto de la ventana que se abre.

Para modificar el esqueleto que se env�a modifica enviar_email_articulo.html o enviar_email_breve.html

=========================================================================CopyLeft 2006 joseluis@digital77.com
