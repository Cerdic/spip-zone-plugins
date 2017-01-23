<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/iextras?lang_cible=pt_br
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'action_associer' => 'gerenciar este campo',
	'action_associer_title' => 'Gerenciar a exibição deste campo extra',
	'action_desassocier' => 'desvincular',
	'action_desassocier_title' => 'Não mais gerenciar a exibição deste campo extra',
	'action_descendre' => 'descer',
	'action_descendre_title' => 'Deslocar o campo uma posição para baixo',
	'action_modifier' => 'alterar',
	'action_modifier_title' => 'Alterar os parâmetros do campo extra',
	'action_monter' => 'subir',
	'action_monter_title' => 'Deslocar o campo uma posição para cima',
	'action_supprimer' => 'excluir',
	'action_supprimer_title' => 'Excluir totalmente o campo da base de dados',

	// B
	'bouton_importer' => 'Importar',

	// C
	'caracteres_autorises_champ' => 'Caracteres possíveis: letras sem acentos, números, - et _',
	'caracteres_interdits' => 'Certos caracteres usados não são adequados para este campo.',
	'champ_deja_existant' => 'Um campo com o mesmo nome já existe para esta tabela.',
	'champ_sauvegarde' => 'Campo extra preservado!',
	'champs_extras' => 'Campos Extras',
	'champs_extras_de' => 'Campos Extras de: @objet@',

	// E
	'erreur_action' => 'Ação @action@ desconhecida.',
	'erreur_enregistrement_champ' => 'Problema na criação do campo extra.',
	'erreur_format_export' => 'Formato de exportação @format@ desconhecido.',
	'erreur_nom_champ_mysql_keyword' => 'O nome deste campo é uma palavra chave reservada pelo SQL e não pode ser usado.',
	'erreur_nom_champ_utilise' => 'O nome deste campo já está sendo usado pelo SPIP ou por um plugin ativo.',
	'exporter_objet' => 'Exportar todos os campos extras de: @objet@',
	'exporter_objet_champ' => 'Exportar os campos extras: @objet@ / @nom@',
	'exporter_tous' => 'Exportar todos os campos extras',
	'exporter_tous_explication' => 'Exportar todos os campos extras no formato YAML para uso nos formulários de importação',
	'exporter_tous_php' => 'Exportar PHP',
	'exporter_tous_php_explication' => 'Exportar no formato PHP para reutilização num plugin dependente unicamente do Campos Extras Core.',

	// I
	'icone_creer_champ_extra' => 'Criar um novo campo extra',
	'importer_explications' => 'O fato de importar os campos extras neste site completará todos os campos extras já existentes com os novos declarados no arquivo de importação. Os novos campos serão adicionados em seguida aos campos extras já existentes.',
	'importer_fichier' => 'Arquivo a ser importado',
	'importer_fichier_explication' => 'Arquivo de exportação no formato YAML',
	'importer_fusionner' => 'Alterar os campos já existentes',
	'importer_fusionner_explication' => 'Se campos extras a serem importados já existirem no site, o processo de importação irá ignorá-los (por padrão). Você pode, no entanto definir que todas as informações desses campos sejam substituídas pelas dos campos do arquivo de importação.',
	'importer_fusionner_non' => 'Não alterar os campos já existentes no site',
	'importer_fusionner_oui' => 'Alterar os campos extra em comum com os da importação',
	'info_description_champ_extra' => 'Esta página permite gerenciar campos extras, ou seja, campos suplementares nas tabelas do SPIP, existentes nos formulários de edição.',
	'info_description_champ_extra_creer' => 'Você pode criar novos campos que serão exibidos  nesta página, no box «Lista de objetos editoriais», bem como nos formulários.',
	'info_description_champ_extra_presents' => 'Finalmente, se os campos já existirem na sua base de dados, mas não estão declarados (por um plugin ou um conjunto de templates), você poderá especificar que este plugin os gerencie. Esses campos, se existirem, aparecerão num box «Lista de campos existentes não gerenciados».',
	'info_modifier_champ_extra' => 'Alterar campo extra',
	'info_nouveau_champ_extra' => 'Novo campo extra',
	'info_saisie' => 'Entrada de dados:',

	// L
	'label_attention' => 'Esclarecimentos muito importantes',
	'label_champ' => 'Nome do campo',
	'label_class' => 'Classes CSS',
	'label_conteneur_class' => 'Classes CSS do contentor superior',
	'label_datas' => 'Lista de valores',
	'label_explication' => 'Esclarecimentos da entrada de dados',
	'label_label' => 'Label da entrada de dados',
	'label_obligatoire' => 'Campo obrigatório?',
	'label_rechercher' => 'Busca',
	'label_rechercher_ponderation' => 'Ponderação da busca',
	'label_restrictions_auteur' => 'Por autor',
	'label_restrictions_branches' => 'Por ramo',
	'label_restrictions_groupes' => 'Por grupo',
	'label_restrictions_secteurs' => 'Por setor',
	'label_saisie' => 'Tipo de entrada de dados',
	'label_sql' => 'Definição SQL',
	'label_table' => 'Objeto',
	'label_traitements' => 'Tratamentos automáticos',
	'label_versionner' => 'Versionar o conteúdo do campo',
	'legend_declaration' => 'Declaração',
	'legend_options_saisies' => 'Opções da entrada de dados',
	'legend_options_techniques' => 'Técnica',
	'legend_restriction' => 'Restrição',
	'legend_restrictions_modifier' => 'Alterar a entrada de dados',
	'legend_restrictions_voir' => 'Visualizar a entrada de dados',
	'liste_des_extras' => 'Lista dos campos extras',
	'liste_des_extras_possibles' => 'Lista de campos existentes não gerenciados',
	'liste_objets_applicables' => 'Lista de objetos editoriais',

	// N
	'nb_element' => '1 elemento',
	'nb_elements' => '@nb@ elementos',

	// P
	'precisions_pour_attention' => 'Para qualquer coisa muito importante a indicar. Para ser usado com muita moderação! Pode ser uma cadeia de idioma «plugin:cadeia».',
	'precisions_pour_class' => 'Incluir classes CSS no elemento, separadas por um espaço. Exemplo: "inserer_barre_edition" para um bloco com o plugin Porte Plume',
	'precisions_pour_conteneur_class' => 'Incluir classes CSS no contentor superior, separadas por um espaço. Exemplo: "pleine_largeur" para usar toda a largura no formulário',
	'precisions_pour_datas' => 'Certos tipos de campos demandam uma lista de valores aceitos: indique os valores, um por linha, seuido de uma vírgula e de uma descrição. Uma linha em branco para o valor por padrão. A descrição pode ser uma cadeia de idioma.',
	'precisions_pour_explication' => 'Você pode dar informações adicionais sobre a entrada de dados. Pode ser uma cadeia de idioma «plugin:cadeia».',
	'precisions_pour_label' => 'Pode ser uma cadeia de idioma «plugin:cadeia».',
	'precisions_pour_nouvelle_saisie' => 'Permite alterar o tipo de entrada de dados usado por este campo',
	'precisions_pour_nouvelle_saisie_attention' => 'Atenção, no entanto: uma alteração de tipo de entrada de dados perde as opções de configuração da entrada de dados atual que não sejam comuns com a nova entrada de dados selecionada!',
	'precisions_pour_rechercher' => 'Incluir este campo no motor de busca?',
	'precisions_pour_rechercher_ponderation' => 'O SPIP pondera uma busca numa coluna por um coeficiênte de ponderação. Isto permite priorizar as colunas mais pertinentes (título, por exemplo), em relação a outras menos relevantes. O coeficiênte aplicado nos campos extras é, por padrão, 2. Para dar uma ideia, note que o SPIP usa 8 para o título, 1 para o texto.',
	'precisions_pour_restrictions_branches' => 'IDs dos ramos a restringir (separador «:»)',
	'precisions_pour_restrictions_groupes' => 'IDs de grupos a restringir (separador «:»)',
	'precisions_pour_restrictions_secteurs' => 'IDs de setores a restringir (separador «:»)',
	'precisions_pour_saisie' => 'Exibir uma entrada de dados do tipo:',
	'precisions_pour_traitements' => 'Aplicar automaticamente um tratamento para a tag #NOM_DU_CHAMP resultante:',
	'precisions_pour_versionner' => 'O versionamento se aplicará unicamente se o plugin «revisões» estiver ativo e se o objeto editorial do campo extra estiver ele mesmo versionado',

	// R
	'radio_restrictions_auteur_admin' => 'Apenas os administradores (mesmo restritos)',
	'radio_restrictions_auteur_admin_complet' => 'Apenas os adminsitradores completos',
	'radio_restrictions_auteur_aucune' => 'Todos podem',
	'radio_restrictions_auteur_webmestre' => 'Apenas os webmasters',
	'radio_traitements_aucun' => 'Ninguém',
	'radio_traitements_raccourcis' => 'Tratamento de atalhos SPIP (limpo)',
	'radio_traitements_typo' => 'Tratamento da tipografia, unicamente (typo)',

	// S
	'saisies_champs_extras' => 'De «Campos Extras»',
	'saisies_saisies' => 'De «Entradas de dados»',
	'supprimer_reelement' => 'Excluir este campo?',

	// T
	'titre_iextras' => 'Campos Extras',
	'titre_iextras_exporter' => 'Exportar Campos Extras',
	'titre_iextras_exporter_importer' => 'Exportar ou importar Campos Extras',
	'titre_iextras_importer' => 'Importar Campos Extras',
	'titre_page_iextras' => 'Campos Extras',

	// V
	'veuillez_renseigner_ce_champ' => 'Por favor, preencha este campo!'
);
