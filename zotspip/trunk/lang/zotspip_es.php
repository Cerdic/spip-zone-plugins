<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/zotspip?lang_cible=es
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'afficher_masquer_details' => 'Activar y ocultar los detalles',
	'ajouter_createur' => 'Añadir otro autor',
	'ajouter_tag' => 'Añadir otra palabra clave',
	'annee_non_precisee' => 'Année non précisée', # NEW
	'aucune_reference' => 'Aucune référence ne correspond.', # NEW

	// B
	'bibliographie_zotero' => 'una bibliografía Zotero',
	'bouton_forcer_maj_complete' => 'Forzar una actualización completa',
	'bouton_synchroniser' => 'Sincronizar',

	// C
	'configurer_zotspip' => 'Configurar ZotSpip',
	'confimer_remplacement' => 'Reemplazar <strong>@source@</strong> por <strong>@dest@</strong> ? ¡Cuidado!, esta acción no se puede deshacer. !',
	'confirmer' => 'Confirmar',
	'connexion_ok' => 'La connexion avec Zotero est opérationnelle.', # NEW
	'contributeurs' => 'Contribuidores',
	'createurs' => 'Autor(es)',

	// D
	'deselectionner_tout' => 'Deseleccionar todo',
	'droits_insuffisants' => 'Vous n\'avez pas les droits requis pour procéder à cette modification.', # NEW

	// E
	'erreur_connexion' => 'ZotSpip n\'a pas été capable de se connecter à Zotero. Veuillez vérifier vos paramètres de connexion. Si vous utilisez un proxy, veuillez vérifier qu\'il est correctement configuré dans Spip (Configuration > Fonctions avancées). À savoir, ZopSpip ne fonctionne pas toujours si un proxy est requis.', # NEW
	'erreur_dom' => 'Para  funcionar ZotSpip necesita la extensión PHP DOM. Habilitan/instalan esta extensión.',
	'erreur_openssl' => 'Para funccionar, ZotSpip necesita  la extensión PHP openSSL. habilitan/instalan esta extensión.',
	'erreur_simplexml' => 'Para funcionar, ZotSpip necesita  la extensión PHP SimpleXML. Habilitan/instalan esta  extensión.',
	'explication_api_key' => 'S\'obtient sur la <a href="https://www.zotero.org/settings/keys">page Zotero de gestion des clés personnelles</a>. Pensez à accorder des droits d\'accès suffisants à cette clé.', # NEW
	'explication_autoriser_modif_zotero' => 'Activer les options de modification de la librairie Zotero (par exemple, la fusion d\'auteurs) ? Si oui, qui a les droits suffisants pour valider ces modifications ? ATTENTION : vous devez également vérifier que vote <em>Clé API</em> a les droits en écriture.', # NEW
	'explication_corriger_date' => 'Zotero transmets les dates de publication telles qu\'elles ont été saisies. Dès lors, le processeur CSL n\'est pas toujours en capacité de décomposer correctement ces dernières en raison de la grande variété de formats différents. Si tel est le cas, la date de publication ne sera pas affichée une fois les références mises en forme. ZotSpip peut corriger en amont les dates de publications. Attention : seule l\'année sera alors transmise au processeur CSL, sauf si la date est de la forme aaaa-mm-jj ou aaaa-mm. Cette option n\'a par contre aucune répercussion sur la librairie Zotero elle-même.', # NEW
	'explication_depuis' => 'Sea un año (por ejemplo: <em>2009</em>), sea una duración en año seguida de la palabra <em>años</em> (por ejemplo: <em>3años</em>).', # MODIF
	'explication_id_librairie' => 'Pour une librairie personnelle, le <em>userID</em> est indiqué sur la <a href="https://www.zotero.org/settings/keys">page Zotero de gestion des clés personnelles</a>. Pour un groupe, le <em>groupID</em> se trouve dans l\'URL de configuration du groupe qui est de la forme <em>https://www.zotero.org/groups/&lt;groupID&gt;/settings</em>.', # NEW
	'explication_maj_zotspip' => 'ZotSpip se sincroniza con regularidad (aproximadamente cada cuatro horas) con el servidor Zotero. Solamente las últimas modificaciones (desde la as última sincronización) se tendrán en cuenta. En caso necesario, pueden forzar una actualización completa de la base de datos, entonces todas las referencias vuelven a descargarse (si su librería es grande, esta sincronización se realizara en múltiples pasos, solamente  se puede sincronizar simultáneamente 50 referencias).',
	'explication_ordre_types' => 'Vous pouvez personnaliser l\'ordre utilisé pour les tris par type de référence (changez l\'ordre par glisser/déposer).', # NEW
	'explication_username' => 'Pour une librairie personnelle, le nom d\'utilisateur est indiqué sur la <a href="https://www.zotero.org/settings/account">page de configuration du compte</a>. Pour un groupe partagé, le nom du groupe se situe à la fin de l\'URL de la page d\'accueil du groupe qui est de la forme <em>https://www.zotero.org/groups/&lt;nom_du_groupe&gt;</em> (dans certain cas, le nom du groupe correspondant à son identifiant numérique).', # NEW
	'exporter' => 'Exportar',
	'exporter_reference' => 'Exportar la referencia:',
	'exporter_selection' => 'Exportar la selección al formato',

	// F
	'filtrer' => 'Filtrar',

	// I
	'identifier_via_doi' => 'Identificar el recurso a  partir de su DOI',
	'identifier_via_isbn' => 'Identificar el recurso a  partir de su ISBN',
	'item_admin' => 'administradores no limitados',
	'item_admin_restreint' => 'tous les administrateurs (y compris restreints)', # NEW
	'item_aeres' => 'según la clasificación AERES',
	'item_annee' => 'por año',
	'item_annee_type' => 'por año después por tipo',
	'item_aucun' => 'ningún',
	'item_auteur' => 'por autor',
	'item_complet' => 'todos los campos',
	'item_date_ajout' => 'par date d\'ajout dans la base', # NEW
	'item_liste' => 'lista',
	'item_liste_simple' => 'lista sencilla',
	'item_numero' => 'por número',
	'item_personne' => 'nadie',
	'item_premier_auteur' => 'por primer autor',
	'item_recente' => 'publicaciones recientes',
	'item_redacteur' => 'administrateurs + rédacteurs', # NEW
	'item_resume_tags' => 'resumen + palabras clave',
	'item_type' => 'por tipo',
	'item_type_annee' => 'par type puis par année', # NEW
	'item_type_librairie_group' => 'grupo',
	'item_type_librairie_user' => 'usario',
	'item_volume' => 'por número de volumen',
	'item_webmestre' => 'únicamente los diseñadores de páginas web',
	'items_zotero' => 'Referencias Zotero',

	// L
	'label_annee' => 'Año',
	'label_api_key' => 'Clave API',
	'label_auteur' => 'Autor',
	'label_autoriser_modif_zotero' => 'Modifications de la librairie Zotero', # NEW
	'label_collection' => 'Colección

',
	'label_conference' => 'Cconferencia',
	'label_corriger_date' => 'Corriger les dates de publication', # NEW
	'label_csl' => 'Style CSL (mise en forme)', # NEW
	'label_csl_defaut' => 'Estilo predeterminado',
	'label_depuis' => 'Desde',
	'label_details' => 'Detalles',
	'label_editeur' => 'Maison d\'édition', # NEW
	'label_export' => 'Afficher les options d\'exportation ?', # NEW
	'label_id_librairie' => 'Identificador de la librería',
	'label_identifiants_zotero' => 'Identificadores Zotero',
	'label_liens' => 'Afficher les liens ?', # NEW
	'label_max' => 'Nombre maximum de références affichées', # NEW
	'label_options' => 'Opciones',
	'label_options_affichage' => 'Options d\'affichage', # NEW
	'label_ordre_types' => 'Tri par type de référence', # NEW
	'label_page_biblio' => 'Activer la page ‘biblio’ pour Zpip ?', # NEW
	'label_publication' => 'Publicación',
	'label_recherche_libre' => 'Búsqueda libre',
	'label_selection_references' => 'Sélection des références', # NEW
	'label_souligne' => '¿Subrayar el autor principal?',
	'label_tag' => 'Palabra clave',
	'label_tags' => 'Palabras clave',
	'label_titre_page_biblio' => 'Titre de la page ‘biblio’', # NEW
	'label_tri' => 'Tri', # NEW
	'label_type_doc' => 'Tipo de documento',
	'label_type_librairie' => 'Type de librairie Zotero', # NEW
	'label_type_ref' => 'Tipo de referencia',
	'label_username' => 'Nom d\'utilisateur ou du groupe', # NEW
	'label_variante' => 'Variante',
	'label_zcollection' => 'Colección Zotero',
	'lien_ressource' => 'Lien vers la ressource', # NEW
	'liste_createurs' => 'Liste des contributeurs', # NEW
	'liste_references' => 'Liste des références Zotero', # NEW
	'liste_tags' => 'Lista de palabras clave',

	// M
	'maj_zotspip' => 'Actualizar  ZotSpip',
	'message_erreur_style_csl' => 'Le style CSL @style@.csl n\'a pas été trouvé sur le serveur (fichier inexistant ou plugin désactivé).', # NEW
	'modifier_en_ligne' => 'Modificar en linea en zotero.org',

	// N
	'nom_prenom' => 'Nom, Prénom', # NEW

	// O
	'outil_explication_inserer_ref' => 'Identifiant Zotero de la référence. Dans le cas d\'une citation, un nombre de page ou un numéro de section peut être précisé après l\'identifiant, séparé par @. Plusieurs références peuvent être indiquées, séparées par une virgule. Exemple : 4JA2I4UC@page 16-17,FSCANX5W', # NEW
	'outil_explication_inserer_ref_exemple' => 'Exemple : 4JA2I4UC@page 16-17,FSCANX5W', # NEW
	'outil_inserer_ref' => 'Insertar una referencia bibliográfica [ref=XXX]',

	// P
	'plusieurs_references' => '@nb@ referencias',
	'probleme_survenu_lors_du_remplacement' => 'Un problème est survenu lors du remplacement (code HTTP @code@).', # NEW

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
	'sync_complete_demandee' => 'Une synchronisation complète de la base a été demandée.', # NEW
	'sync_en_cours' => 'La sincronización está siendo procesado pero no siempre esta terminada. haz clic en el lienzo <em>Sincronizar</em> de nuevo.',
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
