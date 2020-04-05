<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/174?lang_cible=pt_br
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// E
	'erreur_cache_taille_mini' => 'O cache não pode ter um tamanho inferior a 10MB',
	'erreur_dossier_squelette_invalide' => 'A pasta squelette não pode ser um caminho absoluto nem conter a referência <tt>../</tt>',
	'explication_dossier_squelettes' => 'Você pode indicar várias pastas separadas por ’ :’, que serão considerados na ordem. A pasta intitulada "<tt>squelettes</tt>" é sempre considerada por último, caso exista.',
	'explication_image_seuil_document' => 'As imagens transferidas podem ser processadas automaticamente em modo documento a partir de uma largura predefinida.',
	'explication_introduction_suite' => 'As reticências são incluídas pela tag <tt>#INTRODUCTION</tt> quando ela cortar um texto . Por padrão,  <tt> (...)</tt>',

	// L
	'label_cache_duree' => 'Validade do cache (s)',
	'label_cache_duree_recherche' => 'Validade do cache de busca (s)',
	'label_cache_strategie' => 'Estratégia do cache',
	'label_cache_strategie_jamais' => 'Sem cache (esta opção será cancelada após 24h)',
	'label_cache_strategie_normale' => 'Cache de validade limitada',
	'label_cache_strategie_permanent' => 'Cache de validade ilimitada',
	'label_cache_taille' => 'Tamanho do cache (MB)',
	'label_compacte_head_ecrire' => 'Sempre comprimir CSS e javascript',
	'label_derniere_modif_invalide' => 'Atualizar o cache a cada nova publicação',
	'label_docs_seuils' => 'Limitar o tamanho dos documentos na transferência',
	'label_dossier_squelettes' => 'Pasta <tt>squelettes</tt>',
	'label_forcer_lang' => 'Forçar o idioma do url ou do visitante(<tt>$forcer_lang</tt>)',
	'label_image_seuil_document' => 'Largura das imagens em modo documento',
	'label_imgs_seuils' => 'Limitar o tamanho das imagensna transferência',
	'label_inhiber_javascript_ecrire' => 'Desativar o javascript nas matérias',
	'label_introduction_suite' => 'Pontos de continuação',
	'label_logo_seuils' => 'Limitar o tamanho dos logos na transferência',
	'label_longueur_login_mini' => 'Tamanho mínimo dos logins',
	'label_max_height' => 'Altura máxima (pixels)',
	'label_max_size' => 'Peso máximo (KB)',
	'label_max_width' => 'Largura máxima (pixels)',
	'label_nb_objets_tranches' => 'Número de objetos nas listas',
	'label_no_autobr' => 'Desconsiderar as alíneas (quebra de linha simples) no texto',
	'label_no_set_html_base' => 'Não incluir automaticamente <tt>&lt;base href="..."&gt;</tt>',
	'label_options_ecrire_perfo' => 'Desempenho',
	'label_options_ecrire_secu' => 'Segurança',
	'label_options_skel' => 'Cálculo das páginas',
	'label_options_typo' => 'Tratamento dos textos',
	'label_supprimer_numero' => 'Suprimir automaticamente os números dos títulos',
	'label_toujours_paragrapher' => 'Emcapsular todos os parágrafos em <tt>&lt;p&gt;</tt> (mesmo os textos constituídos de um único parágrafo)',
	'legend_cache_controle' => 'Controle do cache',
	'legend_espace_prive' => 'Área restrita',
	'legend_image_documents' => 'Imagens e documentos',
	'legend_site_public' => 'Site público',

	// M
	'message_ok' => 'As suas configurações foram processadas e gravadas no arquivo <tt>@file@</tt>. Elas estão ativas a partir de agora.',

	// T
	'texte_boite_info' => 'Esta página permite configurar facilmente as opções ocultas do SPIP.

Se você forçar determinadas opções no seu arquivo <tt>config/mes_options.php</tt>, este formulário deixará de ter efeito sobre elas.

Quando você tiver terminado a configuração do seu site, você poderá, se o desejar, copiar e colar o conteúdo do arquivo <tt>tmp/ck_options.php</tt> no arquivo <tt>config/mes_options.php</tt> antes de desinstalar este plugin que deixará de ser útil.',
	'titre_page_couteau' => 'Canivete KISS'
);
