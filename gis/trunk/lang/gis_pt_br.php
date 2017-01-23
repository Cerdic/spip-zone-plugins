<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/gis?lang_cible=pt_br
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'aucun_gis' => 'Nenhum ponto',
	'aucun_objet' => 'Nenhum objeto',

	// B
	'bouton_annuler_title' => 'Anular a edição, descartará todas as modificações.',
	'bouton_enregistrer_title' => 'Grave suas alterações.',
	'bouton_lier' => 'Vincular este ponto',
	'bouton_supprimer_gis' => 'Excluir definitivamente este ponto',
	'bouton_supprimer_lien' => 'Excluir este vínculo',

	// C
	'cfg_descr_gis' => 'Sistema de Informações Geográficas.<br /><a href="http://contrib.spip.net/4189" class="spip_out">Acessar a documentação</a>.',
	'cfg_inf_adresse' => 'Exibir os campos suplementares de localização (país, cidade, região, endereço...)',
	'cfg_inf_bing' => 'A camada Bing Aerial precisa de uma chave a ser criada no <a href=\'@url@\' class="spip_out">site do Bing</a>.',
	'cfg_inf_geocoder' => 'Ativar as funcões do geocoder (busca a partir de um endereço, recperação do endereço a partir das coordenadas).',
	'cfg_inf_geolocaliser_user_html5' => 'Se o nevagador do usuario permitir, a sua localização geográfica aproximadaé recuperada para dar a posição ao se criar um ponto.',
	'cfg_inf_google' => 'As camadas Google precisam de uma chave a ser criada no <a href=\'@url@\' class="spip_out">site do GoogleMaps</a>.',
	'cfg_inf_styles' => 'Exibe os campos supplementares de estilo (cor, opacidade, espessura...)',
	'cfg_lbl_activer_objets' => 'Ativar a geolocalização nos conteúdos:',
	'cfg_lbl_adresse' => 'Exibir os campos de endereço',
	'cfg_lbl_api' => 'API de cartografia',
	'cfg_lbl_api_key_bing' => 'Chave Bing',
	'cfg_lbl_api_key_google' => 'Chave GoogleMaps',
	'cfg_lbl_api_microsoft' => 'Microsoft Bing',
	'cfg_lbl_geocoder' => 'Geocoder',
	'cfg_lbl_geolocaliser_user_html5' => 'Centrar o mapa na localização do usuário, na criação',
	'cfg_lbl_layer_defaut' => 'Camada padrão',
	'cfg_lbl_layers' => 'Camadas propostas',
	'cfg_lbl_maptype' => 'Fundo cartográfico',
	'cfg_lbl_styles' => 'Exibir os campos de estilos',
	'cfg_titre_gis' => 'Configuração de GIS',

	// E
	'editer_gis_editer' => 'Alterar este ponto',
	'editer_gis_nouveau' => 'Criar um novo ponto',
	'editer_gis_titre' => 'Os pontos geolocalizados',
	'erreur_geocoder' => 'Nenhum resultado para a sua busca',
	'erreur_recherche_pas_resultats' => 'Nenhum ponto corresponde à busca.',
	'erreur_xmlrpc_lat_lon' => 'A latitude e a longitude devem ser passadas em argumento',
	'explication_api_forcee' => 'A API é imposta por um outro plugin ou gabarito.',
	'explication_color' => 'Cor do traço no formato CSS (valor padrão: #0033FF)',
	'explication_fillcolor' => 'Cor de fundo no formato CSS (valor padrão: herdado da cor do traço)',
	'explication_fillopacity' => 'Opacidade do fundo de 0 à 1 (valor padrão: 0.2)',
	'explication_import' => 'Importar um arquivo no formato GPX ou KML.',
	'explication_layer_forcee' => 'A camada é imposta por um outro plugin ou gabarito.',
	'explication_maptype_force' => 'O fundo cartográfico é imposto por um outro plugin ou gabarito.',
	'explication_opacity' => 'Opacidade do traço de 0 à 1 (valor padrão: 0.5)',
	'explication_weight' => 'Espessura do traço (valor padrão: 5)',

	// F
	'formulaire_creer_gis' => 'Criar um ponto geolocalizado:',
	'formulaire_modifier_gis' => 'Alterar o ponto geolocalizado:',

	// G
	'gis_pluriel' => 'Pontos geolocalizados',
	'gis_singulier' => 'Ponto geolocalizado',

	// I
	'icone_gis_tous' => 'Pontos geolocalizados',
	'info_1_gis' => 'Um ponto geolocalizado',
	'info_1_objet_gis' => 'Um objeto vinculado a este ponto',
	'info_aucun_gis' => 'Nenhum ponto geolocalizado',
	'info_aucun_objet_gis' => 'Nenhum objeto vinculado a este ponto',
	'info_geolocalisation' => 'Géolocalização',
	'info_id_objet' => 'N°',
	'info_liste_gis' => 'Pontos geolocalizados',
	'info_nb_gis' => '@nb@ pontos geolocalizados',
	'info_nb_objets_gis' => '@nb@ objetos vinculados a este ponto',
	'info_numero_gis' => 'Ponto número',
	'info_objet' => 'Objeto',
	'info_recherche_gis_zero' => 'Nenhum resultado para «@cherche_gis@».',
	'info_supprimer_lien' => 'Desvincular',
	'info_supprimer_liens' => 'Desvincular todos os pontos',
	'info_voir_fiche_objet' => 'Ver a ficha',

	// L
	'label_adress' => 'Endereço',
	'label_code_pays' => 'Código do país',
	'label_code_postal' => 'CEP',
	'label_color' => 'Cor',
	'label_departement' => 'Estado',
	'label_fillcolor' => 'Cor de fundo',
	'label_fillopacity' => 'Opacidade de fundo',
	'label_import' => 'Importar',
	'label_inserer_modele_articles' => 'vinculados às matérias',
	'label_inserer_modele_articles_sites' => 'vinculados às matérias + sites',
	'label_inserer_modele_auteurs' => 'vinculados aos autores',
	'label_inserer_modele_centrer_auto' => 'Sem centralização automática',
	'label_inserer_modele_centrer_fichier' => 'Não centralizar o mapa nos arquivos KLM/GPX',
	'label_inserer_modele_controle' => 'Ocultar os controles',
	'label_inserer_modele_controle_type' => 'Ocultar os tipos',
	'label_inserer_modele_description' => 'Descrição',
	'label_inserer_modele_documents' => 'vinculados aos documentos',
	'label_inserer_modele_echelle' => 'Escala',
	'label_inserer_modele_fullscreen' => 'Botão tela cheia',
	'label_inserer_modele_gpx' => 'Arquivo GPX a sobrepor',
	'label_inserer_modele_hauteur_carte' => 'Altura do mapa',
	'label_inserer_modele_identifiant' => 'Identificador',
	'label_inserer_modele_identifiant_opt' => 'Identificador (opcional)',
	'label_inserer_modele_identifiant_placeholder' => 'id_gis',
	'label_inserer_modele_kml' => 'Arquivo KML a sobrepor',
	'label_inserer_modele_kml_gpx' => 'id_document ou url',
	'label_inserer_modele_largeur_carte' => 'Largura do mapa',
	'label_inserer_modele_limite' => 'Número máximo de pontos',
	'label_inserer_modele_localiser_visiteur' => 'Centralizar no visitante',
	'label_inserer_modele_mini_carte' => 'Mini mapa de situação',
	'label_inserer_modele_molette' => 'Desativar a roda do mouse',
	'label_inserer_modele_mots' => 'vinculados às palavras',
	'label_inserer_modele_objets' => 'Tipo de ponto(s)',
	'label_inserer_modele_point_gis' => 'ponto único gravado',
	'label_inserer_modele_point_libre' => 'ponto único livre',
	'label_inserer_modele_points' => 'Cachear os pontos',
	'label_inserer_modele_rubriques' => 'vinculados às seções',
	'label_inserer_modele_sites' => 'vinculados aos sites',
	'label_inserer_modele_titre_carte' => 'Título do mapa',
	'label_opacity' => 'Opacidade',
	'label_pays' => 'País',
	'label_rechercher_address' => 'Buscar um endereço',
	'label_rechercher_point' => 'Buscar um ponto',
	'label_region' => 'Região',
	'label_ville' => 'Cidade',
	'label_weight' => 'Espessura',
	'lat' => 'Latitude',
	'libelle_logo_gis' => 'LOGO DO PONTO',
	'lien_ajouter_gis' => 'Incluir este ponto',
	'lon' => 'Longitude',

	// M
	'message_limite_atteinte' => 'Você dispõe de mais pontos geolocalizados do que o limite de exibição atual.<br /> Se você deseja exibir todos, clique <a href="@url@">neste link</a>.',

	// O
	'onglet_carte' => 'Mapa',
	'onglet_liste' => 'Lista',

	// P
	'placeholder_geocoder' => 'Um endereço, uma cidade, um país, um local turístico...',

	// T
	'telecharger_gis' => 'Transferir no formato @format@',
	'texte_ajouter_gis' => 'Incluir um ponto geolocalizado',
	'texte_creer_associer_gis' => 'Criar e vincular um ponto geolocalizado',
	'texte_creer_gis' => 'Criar um ponto geolocalizado',
	'texte_modifier_gis' => 'Alterar o ponto geolocalizado',
	'texte_voir_gis' => 'Ver o ponto geolocalizado',
	'titre_bloc_creer_point' => 'Vincular um novo ponto',
	'titre_bloc_points_lies' => 'Pontos vinculados',
	'titre_bloc_rechercher_point' => 'Buscar um ponto',
	'titre_limite_atteinte' => 'Limite do número de pontos atingido (@limite@)',
	'titre_nombre_utilisation' => 'Uma utilização',
	'titre_nombre_utilisations' => '@nb@ utilizações',
	'titre_nouveau_point' => 'Novo ponto',
	'titre_objet' => 'Título',
	'toolbar_actions_title' => 'Anular o traçado',
	'toolbar_buttons_circle' => 'Traçar um círculo',
	'toolbar_buttons_marker' => 'Traçar um ponto',
	'toolbar_buttons_polygon' => 'Traçar um polígono',
	'toolbar_buttons_polyline' => 'Traçar uma linha',
	'toolbar_buttons_rectangle' => 'Traçar um retângulo',
	'toolbar_edit_buttons_edit' => 'Alterar o objeto',
	'toolbar_edit_buttons_editdisabled' => 'Nenhum objeto alterável',
	'toolbar_edit_buttons_remove' => 'Excuir um objeto',
	'toolbar_edit_buttons_removedisabled' => 'Nenhum objeto a excluir',
	'toolbar_edit_handlers_edit_tooltip_subtext' => 'Clique em Cancelar para suprimir as modificações',
	'toolbar_edit_handlers_edit_tooltip_text' => 'Desloque as pegas ou o marcador para alterar o objeto.',
	'toolbar_edit_handlers_remove_tooltip_text' => 'Clique num objeto para o excluir',
	'toolbar_handlers_marker_tooltip_start' => 'Clique para posicionar o marcador',
	'toolbar_handlers_polygon_tooltip_cont' => 'Clique para continuar a traçar o polígono',
	'toolbar_handlers_polygon_tooltip_end' => 'Clique sobre o primeiro ponto para fechar o polígnono',
	'toolbar_handlers_polygon_tooltip_start' => 'Clique para começar a traçar o polígono',
	'toolbar_handlers_polyline_tooltip_cont' => 'Clique para continua a traçar a linha',
	'toolbar_handlers_polyline_tooltip_end' => 'Clique sobre o último ponto para finalizar a linha',
	'toolbar_handlers_polyline_tooltip_start' => 'Clique para começar a traçar a linha',
	'toolbar_handlers_rectangle_tooltip_start' => 'Clique e arraste para traçar um retângulo',
	'toolbar_handlers_simpleshape_tooltip_end' => 'Solte o mouse para concluir o desenho',
	'toolbar_undo_text' => 'Excluir o último ponto',
	'toolbar_undo_title' => 'Excluir o último ponto traçado',

	// Z
	'zoom' => 'Zoom'
);
