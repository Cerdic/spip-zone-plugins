<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/zotspip?lang_cible=es
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'afficher_masquer_details' => 'Activar/ocultar los detalles',
	'ajouter_createur' => 'Añadir otro autor',
	'ajouter_tag' => 'Añadir otra palabra clave',
	'annee_non_precisee' => 'Año no preciso',
	'aucune_reference' => 'No coincide ninguna referencia.',

	// B
	'bibliographie_zotero' => 'una bibliografía Zotero',
	'bouton_forcer_maj_complete' => 'Forzar una actualización completa',
	'bouton_synchroniser' => 'Sincronizar',

	// C
	'configurer_zotspip' => 'Configurar ZotSpip',
	'confimer_remplacement' => 'Reemplazar <strong>@source@</strong> por <strong>@dest@</strong>? ¡Cuidado!, esta acción no se puede deshacer',
	'confirmer' => 'Confirmar',
	'connexion_ok' => 'La conexión con Zotero es operativa.',
	'contributeurs' => 'Contribuidores',
	'createurs' => 'Autor(es)',

	// D
	'deselectionner_tout' => 'Desmarcar todo',
	'droits_insuffisants' => 'No tienen los derechos exigidos para  realizar esta modificación.',

	// E
	'erreur_connexion' => 'ZotSpip no fue capaz de conectarse a Zotero. Por favor, compruebe su configuración de conexión. Si utilizan un proxy, asegúrese de que está correctamente configurado en Spip (Configuración > Opciones avanzadas). A saber, ZopSpip no siempre funciona si se requiere un proxy.
',
	'erreur_dom' => 'Para  funcionar ZotSpip necesita la extensión PHP DOM. Habilitan/instalan esta extensión.',
	'erreur_openssl' => 'Para funccionar, ZotSpip necesita  la extensión PHP openSSL. Habilitan/instalan esta extensión.',
	'erreur_simplexml' => 'Para funcionar, ZotSpip necesita  la extensión PHP SimpleXML. Habilitan/instalan esta  extensión.',
	'explication_api_key' => 'Se obtiene en la <a href="https://www.zotero.org/settings/keys">página Zotero de gestión de claves personales</a>. Piense en conceder derechos de acceso suficientes para esta clave.',
	'explication_autoriser_modif_zotero' => '¿Habilitar las opciones de modificación de la librería Zotero (por ejemplo, la unión de autores)? Si es así, ¿quien tiene los derechos suficientes para verificar estas modificaciones? ¡CUIDADO! deben también  comprobar que su <em>Clé API</em> tiene los derechos para escribir.',
	'explication_corriger_date' => 'Zotero transmite las fechas de publicación tales como han sido introducidas. Desde entonces, el procesador CSL no está siempre en capacidad de descomponerlas correctamente debido a la gran variedad de formatos diferentes. En este caso, la fecha de publicación  no aparecerá una vez las referencias  formateadas. ZotSpip puede corregir más arriba las fechas de publicación. ¡Cuidado! sólo el año se transmitirá entonces al procesador CSL, salvo si la fecha esta de forma aaaa-mm-jj o aaaa-mm. En cambio, este opción no tiene ninguna repercusión sobre la librería Zotero si mismo.',
	'explication_depuis' => 'Sea un año (por ejemplo: <em>2009</em>), sea una duración en año seguida de la palabra francesa<em>ans</em> (por ejemplo: <em>3ans</em>) o de la palabra inglesa<em>years</em> (por ejemplo: <em>3years</em>) o de la palabra española<em>años</em> (por ejemplo: <em>3años</em>).',
	'explication_id_librairie' => 'Para una librería personal, la identificación <em>userID</em> se indica en la <a href="https://www.zotero.org/settings/keys"> pagina Zotero de  gestión de las  claves personales</a>. Para un grupo, la identificación  <em>groupID</em> se encuentra en el URL de configuración del grupo que aparece como  <em>https://www.zotero.org/groups/&lt;groupID&gt;/settings</em>.',
	'explication_maj_zotspip' => 'ZotSpip se sincroniza con regularidad (aproximadamente cada cuatro horas) con el servidor Zotero. Solamente se toman en cuenta las últimas modificaciones (desde la última sincronización) En caso necesario, pueden forzar una actualización completa de la base de datos, entonces todas las referencias vuelven a descargarse (si su librería es grande, esta sincronización se realizara en múltiples pasos, solamente  se puede sincronizar simultáneamente 50 referencias).',
	'explication_ordre_types' => 'Se puede personalizar el orden utilizado para las clasificaciones por tipo de referencia (cambie el orden por deslizarse/depositar).
',
	'explication_username' => 'Para una librería personal, el nombre de usuario es indicado sobre la <a href="https://www.zotero.org/settings/account"> página de configuración de la cuenta </a>. Para un grupo compartido, el nombre del grupo se sitúa al final del URL de la página inicial del grupo que aparece como <em>https://www.zotero.org/groups/<nom_du_groupe></em>  (en cierto caso, el nombre del grupo correspondiente a su identificador numérico).',
	'exporter' => 'Exportar',
	'exporter_reference' => 'Exportar la referencia:',
	'exporter_selection' => 'Exportar la selección al formato',

	// F
	'filtrer' => 'Filtrar',

	// I
	'identifier_via_doi' => 'Identificar el recurso a  partir de su DOI',
	'identifier_via_isbn' => 'Identificar el recurso a  partir de su ISBN',
	'item_admin' => 'administradores no limitados',
	'item_admin_restreint' => 'todos los administradores (incluido limitados)',
	'item_aeres' => 'según la clasificación AERES',
	'item_annee' => 'por año',
	'item_annee_type' => 'por año después por tipo',
	'item_aucun' => 'ningún',
	'item_auteur' => 'por autor',
	'item_complet' => 'todos los campos',
	'item_date_ajout' => 'por fecha de añadido en la base',
	'item_liste' => 'lista',
	'item_liste_simple' => 'lista sencilla',
	'item_numero' => 'por número',
	'item_personne' => 'nadie',
	'item_premier_auteur' => 'por primer autor',
	'item_recente' => 'publicaciones recientes',
	'item_redacteur' => 'administradores + rédactores',
	'item_resume_tags' => 'resumen + palabras clave',
	'item_type' => 'por tipo', # MODIF
	'item_type_annee' => 'por tipo y por año',
	'item_type_librairie_group' => 'grupo',
	'item_type_librairie_user' => 'usario',
	'item_volume' => 'por número de volumen',
	'item_webmestre' => 'únicamente los diseñadores de páginas web',
	'items_zotero' => 'Referencias Zotero',

	// L
	'label_annee' => 'Año',
	'label_api_key' => 'Clave API',
	'label_auteur' => 'Autor',
	'label_autoriser_modif_zotero' => 'Modificaciones de la librería Zotero',
	'label_collection' => 'Colección

',
	'label_conference' => 'Conferencia',
	'label_corriger_date' => 'Corregir las fechas de publicación',
	'label_csl' => 'Estilo CSL (formato)',
	'label_csl_defaut' => 'Estilo predeterminado',
	'label_depuis' => 'Desde',
	'label_details' => 'Detalles',
	'label_editeur' => 'Casa editorial',
	'label_export' => '¿Mostrar las opciones de exportación?',
	'label_id_librairie' => 'Identificador de la librería',
	'label_identifiants_zotero' => 'Identificadores Zotero',
	'label_liens' => '¿Mostrar los vínculos?',
	'label_max' => 'Número máximo de referencias mostradas',
	'label_options' => 'Opciones',
	'label_options_affichage' => 'Opciones de vizualización',
	'label_ordre_types' => 'Ordenar por tipo de referencia',
	'label_page_biblio' => '¿Activar la pagina ‘biblio’ para Zpip?',
	'label_publication' => 'Publicación',
	'label_recherche_libre' => 'Búsqueda libre',
	'label_selection_references' => 'Selección de referencias ',
	'label_souligne' => '¿Subrayar el autor principal?',
	'label_tag' => 'Palabra clave',
	'label_tags' => 'Palabras clave',
	'label_titre_page_biblio' => 'Titulo de la pagina ‘biblio’',
	'label_tri' => 'Clasificación
',
	'label_type_doc' => 'Tipo de documento',
	'label_type_librairie' => 'Tipo de librería Zotero',
	'label_type_ref' => 'Tipo de referencia',
	'label_username' => 'Nombre del usuario o del grupo',
	'label_variante' => 'Variante',
	'label_zcollection' => 'Colección Zotero',
	'lien_ressource' => 'Vínculo hacia este recurso ',
	'liste_createurs' => 'Lista des contribuidores',
	'liste_references' => 'Lista des referencias Zotero',
	'liste_tags' => 'Lista de palabras clave',

	// M
	'maj_zotspip' => 'Actualizar  ZotSpip',
	'message_erreur_style_csl' => 'No se encontró el estilo CSL @style@.csl en el servidor (archivo inexistente o plugin desactivado).',
	'modifier_en_ligne' => 'Modificar en linea en zotero.org',

	// N
	'nom_prenom' => 'Apellido, Nombre',

	// O
	'outil_explication_inserer_ref' => 'Identificador Zotero de la referencia.En el caso de una citación, se puede especificar un número de página o número de sección después del identificador, separado por @. Se pueden enumerar varias referencias, separadas por una coma.',
	'outil_explication_inserer_ref_exemple' => 'Ejemplo: 4JA2I4UC@page 16-17,FSCANX5W',
	'outil_inserer_ref' => 'Insertar una referencia bibliográfica [ref=XXX]',

	// P
	'plusieurs_references' => '@nb@ referencias',
	'probleme_survenu_lors_du_remplacement' => 'Ocurrió un problema durante de la sustitución (codigo HTTP @code@).',

	// R
	'reference_num' => 'Referencia n°',
	'remplacer_par' => 'Reemplazar por',
	'resume' => 'Resumen:',

	// S
	'sans_auteur' => 'Sin autor',
	'selectionner_tout' => 'Seleccionar todo',
	'source' => 'fuente',
	'supprimer_createur' => 'Borrar este autor',
	'supprimer_tag' => 'Borrar esta palabra clave',
	'sync_complete_demandee' => 'Se ha solicitado una sincronización completa de la base.',
	'sync_en_cours' => 'La sincronización está siendo procesado pero no siempre esta terminada. Haz clic en <em>Sincronizar</em> de nuevo.',
	'synchronisation_effectuee' => 'Sincronización realizada',

	// T
	'tags' => 'Palabras clave:',
	'titre_page_biblio' => 'Referencias bibliográficas',

	// U
	'une_reference' => '1 referencia',

	// V
	'voir_publis_auteur' => 'Ver las publicaciones de @auteur@.',
	'voir_sur_zotero' => 'Consultar esta referencia en zotero.org',

	// Z
	'zotspip' => 'ZotSpip'
);

?>
