<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de https://trad.spip.net/tradlang_module/fabrique?lang_cible=pt_br
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'action_incomprise' => 'Ação @f_action@ não suportada!',
	'aide_creation_peupler_table' => 'Ajuda na criação de preenchimento de uma tabela',
	'aide_creation_squelette_fabrique' => 'Ajuda para a criação de templates Fábrica',
	'autorisation_administrateur' => '≥ Administrador completo',
	'autorisation_administrateur_explication' => 'Ser, no mínimo, administrador completo.',
	'autorisation_administrateur_restreint' => '≥ Administrador restrito',
	'autorisation_administrateur_restreint_explication' => 'Ser, no mínimo, administrador restrito (seja qual for a seção).',
	'autorisation_administrateur_restreint_objet' => '≥ Administrador restrito + seção',
	'autorisation_administrateur_restreint_objet_explication' => 'Ser, no mínimo, administrador restrito da seção (requer um campo id_rubrique).',
	'autorisation_auteur_objet' => '≥ Autor do objeto',
	'autorisation_auteur_objet_explication' => 'Ser autor do objeto ou, pelo menos administrador completo (ou restrito, se o objeto tiver o campo id_rubrique).’',
	'autorisation_auteur_objet_statut' => '≥ Autor do objeto, exceto se publicado',
	'autorisation_auteur_objet_statut_explication' => 'Ser autor do objeto, exceto se o objeto estiver publicado (requer a gestão de status) ou, pelo menos, ser administrador completo (ou restrito, se o objeto tiver o campo id_rubrique).',
	'autorisation_defaut' => 'Padrão (@defaut@)',
	'autorisation_jamais' => 'Nunca',
	'autorisation_jamais_explication' => 'Sempre retorna não!',
	'autorisation_redacteur' => '≥ Redator',
	'autorisation_redacteur_explication' => 'Ser, pelo menos, redator (quer seja ou não o autor do objeto).',
	'autorisation_toujours' => 'Sempre',
	'autorisation_toujours_explication' => 'Retorna sempre sim!',
	'autorisation_webmestre' => 'Webmaster',
	'autorisation_webmestre_explication' => 'Ser o webmaster do site.',
	'avertissement_champs' => 'Não inserir aqui a chave primária (@id_objet@), nem nenhum dos campos especiais (id_rubrique, lang etc.) propostos na parte «Campos Especiais» ou nas partes «Ligações».',

	// B
	'bouton_ajouter_champ' => 'Incluir um campo',
	'bouton_ajouter_objet' => 'Incluir um objeto editorial',
	'bouton_calculer' => 'Calcular',
	'bouton_charger' => 'Carregar a cópia de segurança',
	'bouton_charger_sauvegarde_attention' => 'Carregar uma cópia de segurança apaga as informações do plugin que está sendo criado neste momento!',
	'bouton_creer' => 'Criar o plugin',
	'bouton_exporter' => 'Exportar',
	'bouton_menu_edition' => 'Menu edição',
	'bouton_outils_rapides' => 'FErramentas rápidas',
	'bouton_reinitialiser_autorisations' => 'Reinicializar as autorizações',
	'bouton_reinitialiser_chaines' => 'Reinicializar as cadeias de idioma deste objeto',
	'bouton_renseigner_objet' => 'Preencher este objeto',
	'bouton_reset' => 'Reinicializar o formulário',
	'bouton_supprimer_champ' => 'Excluir o campo',
	'bouton_supprimer_logo' => 'Excluir este logo',
	'bouton_supprimer_objet' => 'Excluir este objeto editorial',

	// C
	'c_fabrique_dans_plugins' => 'Facilite os testes!',
	'c_fabrique_dans_plugins_texte' => 'Ao criar um diretório <code>@dir@</code> acessível para escrita no seu diretório de plugins, a Fábrica poderá confeccionar o plugin (seus arquivos e estrutura de pastas) diretamente nele. Você poderá, assim que o plugin for criado, ativá-lo de imediato na área de administração dos plugins e testá-lo.<br><br>
Atenção, de outra forma, o plugin será criado em <code>tmp/cache/@dir_cache@</code>;  esta pasta é excluída quando se esvazia o cache.',
	'c_fabrique_info' => 'Criação de um plugin',
	'c_fabrique_info_texte' => 'Esta ferramenta permite criar facilmente o código básico para um plugin. Embora o código produzido seja funcional, provavelmente não será exatamente o que você deseja, e não é esse o objetivo! A Fábrica cria os arquivos e os códigos básicos mas provavelmente será necessário modificá-los em seguida para o que você realmente deseja.<br><br>
É aconselhável que você compreenda previamente o funcionamento dos plugins, do SPIP e dos seus templates, e se desejar criar objetos editoriais, o funcionamento dos pipelines, autorizações, formulários. Este plugin pode, contudo, servir para estudar o código gerado em função das opções que você selecionar.',
	'c_fabrique_zone' => 'Wow, é muito fácil!',
	'c_fabrique_zone_texte' => 'Você certamente apreciará poder produzir um plugin que  
		gerencie um ou diversos objetos editoriais. Bom!<br><br>
		Atenção, no entanto! Se criar um plugin é fácil, mantê-lo ao longo do tempo, 
		gerenciar a sua documentação, sua vida, é bem mais difícil. O melhor meio para 
		manter um plugin implica geralmente em duas condições: que ele seja 
		útil e que ele seja partilhado; quando partilhado entre outros 
		desenvolvedores e colaboradores, estes podem participar e melhorá-lo. No SPIP, 
		os plugins partilhados com código livre, podem ser hospedados no espaço de 
		colaboração SPIP Zone.<br><br>
		Antes de se lançar na criação de um novo plugin, verifique se não existe já no 
		espaço de colaboração um plugin equivalente ao qual você possa aplicar as suas 
		melhorias, a sua documentação. è mais interessante para todos que haja um 
		mínimo de duplicados e sim plugins funcionais e duradouros!
	',
	'calcul_effectue' => 'Cálculo efetuado',
	'chaine_ajouter_lien_objet' => 'Incluir este @type@',
	'chaine_ajouter_lien_objet_feminin' => 'Incluir esta @type@',
	'chaine_confirmer_supprimer_objet' => 'Você confirma a exclusão deste @type@?',
	'chaine_confirmer_supprimer_objet_feminin' => 'Você confirma a exclusão desta @type@?',
	'chaine_icone_creer_objet' => 'Criar um @type@',
	'chaine_icone_creer_objet_feminin' => 'Criar uma @type@',
	'chaine_icone_modifier_objet' => 'Alterar este @type@',
	'chaine_icone_modifier_objet_feminin' => 'Alterar este @type@',
	'chaine_info_1_objet' => 'Um @type@',
	'chaine_info_1_objet_feminin' => 'Uma @type@',
	'chaine_info_aucun_objet' => 'Nenhum @type@',
	'chaine_info_aucun_objet_feminin' => 'Nenhuma @type@',
	'chaine_info_nb_objets' => '@nb@ @objets@',
	'chaine_info_nb_objets_feminin' => '@nb@ @objets@',
	'chaine_info_objets_auteur' => 'Os @objets@ deste autor',
	'chaine_info_objets_auteur_feminin' => 'As @objets@ deste autor',
	'chaine_retirer_lien_objet' => 'Retirar este @type@',
	'chaine_retirer_lien_objet_feminin' => 'Retirar esta @type@',
	'chaine_retirer_tous_liens_objets' => 'Retirar todos os @objets@',
	'chaine_retirer_tous_liens_objets_feminin' => 'Retirar todas as @objets@',
	'chaine_supprimer_objet' => 'Excluir este @type@',
	'chaine_supprimer_objet_feminin' => 'Escluir esta @type@',
	'chaine_texte_ajouter_objet' => 'Incluir um @type@',
	'chaine_texte_ajouter_objet_feminin' => 'Incluir uma @type@',
	'chaine_texte_changer_statut_objet' => 'Este @type@ está:',
	'chaine_texte_changer_statut_objet_feminin' => 'Esta @type@ está:',
	'chaine_texte_creer_associer_objet' => 'Criar e associar um @type@',
	'chaine_texte_creer_associer_objet_feminin' => 'Criar e associar uma @type@',
	'chaine_texte_definir_comme_traduction_objet' => 'Este @type@ é uma tradução do @type@ número:',
	'chaine_texte_definir_comme_traduction_objet_feminin' => 'Esta @type@ é uma tradução da @type@ número:',
	'chaine_titre_langue_objet' => 'Idioma deste @type@',
	'chaine_titre_langue_objet_feminin' => 'Idioma desta @type@',
	'chaine_titre_logo_objet' => 'Logo deste @type@',
	'chaine_titre_logo_objet_feminin' => 'Logo desta @type@',
	'chaine_titre_objet' => '@mtype@',
	'chaine_titre_objet_feminin' => '@mtype@',
	'chaine_titre_objets' => '@mobjets@',
	'chaine_titre_objets_feminin' => '@mobjets@',
	'chaine_titre_objets_lies_objet' => 'Ligados a este @type@',
	'chaine_titre_objets_lies_objet_feminin' => 'Ligados a esta @type@',
	'chaine_titre_objets_rubrique' => '@mobjets@ da seção',
	'chaine_titre_objets_rubrique_feminin' => '@mobjets@ da seção',
	'champ_ajoute' => 'Um campo foi incluído',
	'champ_auto_rempli' => 'O campo será automaticamente preenchido se você deixar vazio.',
	'champ_deplace' => 'O campo foi movido',
	'champ_supprime' => 'O cazmpo foi excluído',
	'chargement_effectue' => 'Carregamntgo efetuado',
	'config_exemple' => 'Exemplo',
	'config_exemple_explication' => 'Explicação deste exemplo',
	'config_titre_parametrages' => 'Configurações',

	// D
	'datalist_aide' => 'Alguns navegadores podem sugerir autocompletar pressionando a tecla de seta para baixo ou fazendo clique duplo no campo.',

	// E
	'echappement_accolades' => '{ }',
	'echappement_crochets' => '[ ]',
	'echappement_diese' => '#',
	'echappement_idiome' => '&lt; :',
	'echappement_inclure' => '&lt;INCLURE',
	'echappement_parentheses' => '( )',
	'echappement_php' => '&lt; ?php',
	'echappement_tag_boucle' => '&lt; de boucle',
	'erreur_chargement_fichier' => 'O arquivo enviado não pode ser compreendido. O restauro não ocorreu.',
	'erreur_copie_sauvegarde' => 'A cópia de segurança de @dir@ não pode ser realizada. O plugin não pode ser regenerado, por segurança. A causa é, provavelmente, por direitos insuficientes do diretório origem para o servidor.',
	'erreur_envoi_fichier' => 'Erro no envio do arquivo.',
	'erreur_suppression_sauvegarde' => 'A cópia de segurança antereior (@dir@) não pode ser excluída. O plugin não foi regenerado, por precaução. A causa provável é a criação, por você mesmo, de arquivos suplementares no plugin que não possuem direitos suficientes para serem manipulados pelo servidor.',
	'erreurs' => 'Existem erros!',
	'experimental_explication' => '<strong>Parte experimental!</strong><br>
A perenidade das entradas não é garantida.
Esta parte pode evoluir ou desaparecer em futuras versões.',
	'explication_fichiers' => 'Mesmo se você não os ativar aqui, alguns desses arquivos serão mesmo assim criados, em função de outras opções que você tenha selecionado em outro lugar, especialmente se você ativou um objeto editorial.',
	'explication_fichiers_echafaudes' => 'O SPIP gera automaticamente em cache estes arquivos, se eles estiverem ausentes. Você pode no entanto criar alguns para modificá-los de modo a alterar o comportamento padrão proposto pelo SPIP. Além disso, esses arquivos têm adições mínimas de funcionalidades, tornando-os indicados.',
	'explication_fichiers_explicites' => 'Estes arquivos não existem por padrão no SPIP mas podem ser gerados para o seu conforto, se necessário.',
	'explication_reinitialiser' => 'Esta ação apaga as informações do plugin correntemente sendo criado. Você recomeçará do zero!',
	'explication_roles' => 'De modo experimental, os papéis podem ser gerenciados nas ligações usando o plugin «Papéis».',
	'explication_sauvegarde' => 'A Fábrica cria um arquivo de segurança (<code>fabrique_{prefixo}.php</code>) dentro de cada plugin que ela cria. Você pode restaurar este arquivo enviando-o para o servidor ou usar um dos arquivos já presentes.',
	'explication_tables_hors_normes' => 'Uma tabela respeita as normas padrão do SPIP sendo nomeada com plural em «s» (como <code>spip_coisas</code>) sendo a sua chave primária baseada no nome da tabela no singular (como <code>id_coisa</code>). Nos outros casos, você deverá completar algumas informações abaixo.',

	// F
	'fabrique_dev_intro' => 'Esta ferramenta ajuda na criação de templates pela Fábrica',
	'fabrique_dev_titre' => 'Desenvolvimento da Fábrica',
	'fabrique_intro' => 'Ferramenta de fabricação de plugin',
	'fabrique_outils' => 'Ferramentas',
	'fabrique_peuple_intro' => 'Esta ferramenta auxilia a criação de um arquivo e de uma função de popular uma tabela, no momento de instalação do plugin',
	'fabrique_peuple_titre' => 'Popular um objeto',
	'fabrique_plugin' => 'Fábrica de @plugin@',
	'fabrique_restaurer_titre' => 'Restaurar ou reinicializar uma fábrica',
	'fabrique_titre' => 'A Fábrica',
	'fichier_echafaudage_prive/objets/infos/objet.html' => 'Incluir o link de visualização',
	'fichier_echafaudage_prive/squelettes/contenu/objets.html' => 'Incluir um campo de busca',
	'fichier_explicite_action/supprimer_objet.php' => 'Ação de exclusão do objeto (este arquivo é criado automaticamente se o objeto não gerencia o status).',
	'fichier_importation_cree_dans' => 'Arquivo de importação criado na pasta <code>@dir@</code>, arquivo <code>@import@</code> com @lignes@ linhas, de um total de @taille@',
	'fichiers_importations_compresses_cree_dans' => 'Arquivo de importação criado no diretório <code>@dir@</code>, arquivos <code>@import@</code> e <code>@donnees_compressees@</code>, com @lignes@ linhas de um total de @taille@',

	// I
	'image_supprimee' => 'A imagem foi excluída',
	'insertion_code_explication' => 'Esta parte permite inserir código em em certos pontos previstos pela Fábrica. Atenção, no entanto, para que esse código seja válido!',

	// L
	'label_afficher_liens' => 'Exibir as diferentes ligações na visão do seu objeto?',
	'label_afficher_liens_explication' => 'Você pode, na visão do seu objeto, listar os objetos (selecionados abaixo) que estão ligados a ele.
Nota: é possível que essas listas não funcionem perfeitamente, exibindo o conjunto de objetos, em vez de apenas os ligados ao seu;
Será necessário carregar o arquivo de lista usado (privado/objetos/lista/xxx.html) para incluir um critério `{xxx_liens.id_xxx ?}` adicional.',
	'label_auteur' => 'Nome do autor',
	'label_auteur_lien' => 'URL do autor',
	'label_auteurs_liens' => 'Vincular os autores?',
	'label_auteurs_liens_explication' => 'Permite incluir o formulário de vinculação de autores neste objeto.',
	'label_boutons' => 'Botões',
	'label_boutons_explication' => 'Inserir botões nesses vínculos:',
	'label_caracteristiques' => 'Características',
	'label_categorie' => 'Categoria',
	'label_champ_date_publication' => 'Campo SQL de data',
	'label_champ_date_publication_explication' => 'Para gerenciar uma data de publicação, indique o campo, como  «date» ou «date_publication»',
	'label_champ_est_editable' => 'Pode ser editado',
	'label_champ_est_obligatoire' => 'É obrigatório',
	'label_champ_est_versionne' => 'Pode ser versionado',
	'label_champ_id_rubrique' => 'Criar o campo <strong>id_rubrique</strong>',
	'label_champ_id_secteur' => 'Criar o campo <strong>id_secteur</strong>',
	'label_champ_id_trad' => 'Campo <strong>id_trad</strong>',
	'label_champ_lang_et_langue_choisie' => 'Campos <strong>lang</strong> e <strong>langue_choisie</strong>',
	'label_champ_langues' => 'Gestão de idiomas',
	'label_champ_langues_explication' => 'Incluir os campos para gerenciamento de idiomas do objeto (lang e langue_choisie) e as traduções (id_trad)?',
	'label_champ_plan_rubrique' => 'Listar o objeto no mapa do site?',
	'label_champ_rubriques' => 'Este objeto é um subordinado direto de uma seção?',
	'label_champ_rubriques_explication' => 'Afetar este objeto numa seção',
	'label_champ_statut' => 'Campo <strong>statut</strong>',
	'label_champ_statut_explication' => 'Permite usar os status de publicação (proposto para publicação, publicado, na lixeira…)',
	'label_champ_statut_rubrique' => 'Afetar o status das seções se este elemento estiver presente',
	'label_champ_titre' => 'Calcular os títulos',
	'label_champ_titre_explication' => 'Usar um dos campos SQL que você declarou para o seu objeto',
	'label_champ_vue_rubrique' => 'Exibir a lista na seção',
	'label_charger_depuis_table_sql' => 'Definir a partir de uma tabela SQL',
	'label_charger_depuis_table_sql_attention' => 'Isto apagará uma parte das informações que você entrou para este objeto.',
	'label_charger_depuis_table_sql_explication' => 'Você pode popular o seu objeto usando uma tabela SQL existente conhecida do SPIP',
	'label_cle_primaire' => 'Chave primária',
	'label_cle_primaire_attention' => 'É recomendável inserir o nome da tabela no singular, prefixada por id_. Este prefixo é importante. Na sua ausência, algums relacionamentos com critérios como 
<code>{id_mot ?}</code> ou <code>{id_auteur ?}</code>
num laço deste objeto apresentarão um erro de template.',
	'label_cle_primaire_explication' => 'Exemplo «id_chose»',
	'label_cle_primaire_sql' => 'Definição SQL para a chave primária',
	'label_cle_primaire_sql_attention' => 'É recomendável indicar uma chave primária numérica (<code>bigint(21) NOT NULL</code>). Quando o tipo de campo não é um integral, é impossível ao SPIP criar um novo elemento nesse objeto visto que a chave primária não poderá ser afetada por um «incremento auto».
Além disso, se a sua tabela contém já linhas com dados não inteiros na chave primária, ou com zeros à esquerda (0123), esses dados não poderão ser lidos pelo SPIP porque ele aplica a função intval (força um valor a ser um número inteiro) automaticamente a todo o campo prefixado por id_ e à chave primária de um objeto editorial.',
	'label_cle_primaire_sql_explication' => 'Definição SQL para a chave primária',
	'label_code_resultat' => 'Cósdigo transformado',
	'label_code_squelette' => 'Código do template fonte',
	'label_colonne_sql' => 'Coluna SQL',
	'label_colonne_sql_explication' => 'Um nome de campo para SQL. Exemplo «post_scriptum»',
	'label_compatibilite' => 'Compatibilidade',
	'label_credits_logo_texte' => 'Créditos do logo',
	'label_credits_logo_url' => 'URL para os créditos',
	'label_definition_sql' => 'Definição SQL',
	'label_description' => 'Descrição',
	'label_documentation_url' => 'Documentação (url)',
	'label_echappements' => 'O que escapar?',
	'label_etat' => 'Estado',
	'label_exemples' => 'Inserir exemplos',
	'label_exemples_explication' => 'Incluir exemplos de código e textos de ajuda em comentários nos arquivos do plugin?',
	'label_explication' => 'Frase de explicação para o campo',
	'label_fichier_administrations' => 'Arquivo de administração?',
	'label_fichier_administrations_explication' => 'Criar o arquivo de instalação/desinstalação?',
	'label_fichier_autorisations' => 'Autorizações',
	'label_fichier_fonctions' => 'Funções',
	'label_fichier_options' => 'Opções',
	'label_fichier_pipelines' => 'Pipelines',
	'label_fichier_sauvegarde' => 'Arquivo de segurança',
	'label_fichier_sauvegarde_ordinateur' => 'No seu computador',
	'label_fichier_sauvegarde_serveur' => 'No servidor',
	'label_fichiers' => 'Arquivos',
	'label_fichiers_echafaudes' => 'Arquivos de andaime',
	'label_fichiers_explicites' => 'Arquivos específicos',
	'label_formulaire_configuration' => 'Formulário de configuração?',
	'label_formulaire_configuration_titre' => 'Título da página de configuração',
	'label_genre' => 'Gênero',
	'label_genre_explication' => 'Serve para o pré-cálculo do texto das cadeias de idiomas.',
	'label_genre_feminin' => 'Feminino',
	'label_genre_masculin' => 'Masculino',
	'label_inserer_administrations_desinstallation' => 'Completar a desinstalação na função <code>vider_table()</code>',
	'label_inserer_administrations_fin' => 'No final do arquivo para inserir novas funções',
	'label_inserer_administrations_maj' => 'Completar <code>$maj</code> na função <code>upgrade()</code>',
	'label_inserer_base_tables_fin' => 'No final do arquivo para inserir novas funções',
	'label_inserer_paquet' => 'No nível das dependências',
	'label_liaison_directe' => 'Este objeto é um subordinado direto de um outro objeto?',
	'label_liaison_directe_explication' => 'Permite afetar este objeto num outro objeto superior.
Oste objeto incluirá na sua tabela a chave primária do objeto superior selecionado aqui.
Estes objetos serão listados na ficha do objeto superior.
Uma saída deve existir para o objeto superior selecionado aqui.',
	'label_libelle' => 'Texto',
	'label_libelle_champ_explication' => 'Um nome de campos para humanos. Exemplo «Post-Scriptum»',
	'label_licence' => 'Licença',
	'label_logo' => 'Logo',
	'label_logo_taille' => 'Logo de @taille@px',
	'label_logo_variantes' => 'Variantes de logos?',
	'label_logo_variantes_explication' => 'Criar todas as variantes (new, edit, del, add) de logo (tamanhos maiores ou iguais 16 pixels).',
	'label_nom' => 'Nome',
	'label_nom_pluriel' => 'Nome plural',
	'label_nom_pluriel_explication' => 'Exemplo «Coisas»',
	'label_nom_singulier' => 'Nome simgular',
	'label_nom_singulier_explication' => 'Exemplo «Coisa»',
	'label_prefixe' => 'Prefixo',
	'label_recherche' => 'Busca',
	'label_recherche_explication' => 'Ponderação da busca neste campo. Valores compreendidos entre 1 e 10 
		indicará que o SPIP pode pesquisar neste campo durante uma busca no objeto.
		Deixe em branco para não pesquisar no campo.',
	'label_roles' => 'Lista dos papéis',
	'label_roles_explication' => 'Cada linha descreve um papel: <code>código do papel,Título do papel</code>.
		O primeiro papel é considerado como o papel a ser aplicado por padrão. Exemplo:
<code>tradutor,Tradutor</code>',
	'label_saisie' => 'Tipo de entrada',
	'label_saisie_explication' => 'Se necessário (para exibir este campo no formulário), indique o tipo de entrada (do plugin entradas) desejado.',
	'label_saisie_options' => 'Opções de entrada',
	'label_saisie_options_explication' => 'Opções do código da tag #SAISIE.<br />
		Exemplo para uma textarea:<br />
		<code>conteneur_class=pleine_largeur, class=inserer_barre_edition, rows=4</code><br />
		Exemplo para um seletor / checkbox / radio :<br />
		<code>datas=[(#ARRAY{chave1,valor1,chave2,valor2})]</code>',
	'label_saisies' => 'Entradas',
	'label_saisies_explication' => 'Criar as entradas e os seus valores visíveis',
	'label_schema' => 'Esquema',
	'label_schema_explication' => 'Versão da estrutura dos dados',
	'label_scripts_post_creation' => '<code>post_creation</code>',
	'label_scripts_post_creation_explication' => 'Após a criação dos arquivos do seu plugin em <code>@destination_plugin@</code>',
	'label_scripts_pre_copie' => '<code>pre_copie</code>',
	'label_scripts_pre_copie_explication' => 'Antes de fazer a cópia de segurança do plugin corrente em <code>@destination_ancien_plugin@</code>',
	'label_slogan' => 'Slogan',
	'label_table' => 'Nome da tabela SQL',
	'label_table_a_exporter' => 'Tabela SQL a exportar',
	'label_table_attention' => 'Éaconselhável nomear a sua tabela no plural, com um s no final.
		Desse modo, o SPIP e a Fábrica saberão gerenciar os outros casos.',
	'label_table_compresser_donnees' => 'Comprimir os dados?',
	'label_table_compresser_donnees_explication' => 'Útil se a tabela for volumosa!',
	'label_table_destination' => 'Tabela SQL de destino',
	'label_table_destination_explication' => 'Nome da tabela onde serão importados os dados.
		Por padrão, o mesmo nome que a tabela fonte.',
	'label_table_explication' => 'Por exemplo «spip_coisas»',
	'label_table_liens' => 'Criar uma tabela de links?',
	'label_table_type' => 'Tipo do objeto',
	'label_table_type_attention' => 'É aconselhável inserir o nome da chave primária, sem o seu prefixo.',
	'label_table_type_explication' => 'Exemplo «coisa»',
	'label_transformer_objet' => 'Transformar os textos deste objeto',
	'label_transformer_objet_explication' => 'mudará, na melhor hipóetese o que for relativo a um objeto (articles, #ID_ARTICLE...) usando a sintaxe prevista pela Fábrica',
	'label_version' => 'Versão',
	'label_vue_auteurs_liens' => 'A lista na visão de um autor?',
	'label_vue_auteurs_liens_explication' => 'Permite exibir a lista dos elementos deste objeto ligados a um autor, na página do autor.',
	'label_vue_liens' => 'Permitir entrar os links neste objetos?',
	'label_vue_liens_explication' => 'Inclui um formulário de edição de links nos obetos:',
	'legend_autorisations' => 'Autorizações',
	'legend_autorisations_explication' => 'Permite definir para certas ações sobre o objeto editorial, 
		tal como a sua modificação, os testes de autorização que serão realizados. Eles podem depender
		do status do autor, da seção a que pertence o objeto (se ele conta com o campo id_rubrique),
		ou ainda do status do próprio objeto (se já está publicado ou não). 
		A autorização de <code>criar</code> não tem todas as opções.',
	'legend_chaines_langues' => 'Cadeias de idioma',
	'legend_champs' => 'Campos',
	'legend_champs_speciaux' => 'Campoos especiais',
	'legend_champs_sql' => 'Campos SQL usados para:',
	'legend_configuration' => 'Configuração',
	'legend_date_publication' => 'Data de publicação',
	'legend_description' => 'Descrição',
	'legend_fichiers' => 'Arquivos',
	'legend_fichiers_supplementaires' => 'Arquivos suplementares',
	'legend_inserer_administrations' => 'Em <code>@prefixe@_administrations.php</code>',
	'legend_inserer_base_tables' => 'Em <code>base/@prefixe@.php</code>',
	'legend_inserer_paquet' => 'Em <code>paquet.xml</code>',
	'legend_insertion_code' => 'Inserção de código',
	'legend_installation' => 'Instalação',
	'legend_langues_et_traductions' => 'Idiomas e traduções',
	'legend_liaison_directe_autre_objet' => 'Num outro objeto editorial',
	'legend_liaisons_auteurs_liens' => 'spip_auteurs_liens',
	'legend_liaisons_directes' => 'Vínculos diretos',
	'legend_liaisons_indirectes' => 'Vínculos indiretos',
	'legend_liaisons_objet_liens' => 'spip_@objet@_liens',
	'legend_logo' => 'Ícones',
	'legend_logo_specifiques' => 'Ícones específicos',
	'legend_logo_specifiques_explication' => 'Você pode igualmente fornecer logos específicos
		para certos tamanhos. Estas imagens serão ou calculadas pelo SPIP
		a partir do tamanho acima mais próximo, ou a partir do logo de base do objeto.',
	'legend_options' => 'Opções',
	'legend_paquet' => 'Pacote',
	'legend_pre_construire' => 'Pré construir',
	'legend_resultat' => 'Resultado',
	'legend_roles' => 'Papeis',
	'legend_rubriques' => 'Seções',
	'legend_saisie' => 'Entrada',
	'legend_scripts' => 'Scripts a executar',
	'legend_statut' => 'Status',
	'legend_suppression' => 'Exclusão',
	'legend_table' => 'Tabela',
	'legend_tables_hors_normes' => 'Especificidades de tabelas fora das normas',

	// M
	'message_diff' => 'Diferenças com a criação anterior',
	'message_diff_explication' => 'Este «diff» também é armazenado no arquivo <code>fabrique_diff.diff</code>
		do plugin gerado.',
	'message_diff_suppressions' => 'Os arquivos foram excluídos quando desta nova criação.',

	// O
	'objet_ajoute' => 'Um novo objeto editorial foi incluído',
	'objet_autorisations_reinitialisees' => 'As autorizações do objeto foram reinicializadas.',
	'objet_chaines_reinitialisees' => 'As cadeias de idiomas do objeto foram reinicializadas.',
	'objet_deplace' => 'O objeto foi movido',
	'objet_renseigne' => 'O objeto editorial foi populado com a tabela SQL indicada',
	'objet_supprime' => 'O objeto editorial foi excluído',
	'onglet_fabrique' => 'Fábrica de plugins',
	'onglet_fabrique_outils' => 'Ferramentas',
	'onglet_fabrique_restaurer' => 'Restauração, Reinicialização',
	'onglet_objet' => 'Objeto',
	'onglet_objet_n' => 'Objeto #@nb@',
	'onglet_plugin' => 'Plugin',

	// P
	'plugin_cree_succes' => 'O plugin foi criado corretamente',
	'plugin_cree_succes_dans' => 'O plugin foi criado corretamente em <br /><code>@dir@</code>',

	// R
	'reinitialisation_effectuee' => 'Reinicialização realizada',
	'reititialiser' => 'Reinicializar',
	'repertoire_plugin_fabrique' => 'Para facilitar os testes, você pode criar uma pasta <code>@dir@</code> acessível para leitura no seu
		diretório de plugins. Desse modo, os plugins criados ficarão imediatamente disponĩveis
		na administração dos plugins e ativáveis.',
	'restaurer' => 'Restaurar',

	// S
	'saisies_objets' => 'Entrada <code>@saisie@</code>: seletor de objeto simples para tabelas pouco populadas.',
	'scripts_explication' => 'Código PHP válido pode ser executado
		em certos momentos do procedimento da criação do plugin. Isso permite tratar as
		ações não previstas pela Fábrica, como remeter os arquivos que você tenha incluído,
		deslocando-os do plugin antigo para o novo.
		Um certo número de variáveis estão à sua disposição
		no momento da execução desses scripts, como <code>$destination_plugin</code>
		(o caminho até o futuro plugin), <code>$destination_ancien_plugin</code> (a
		cópia do plugin antigo - se ele existir antes!), <code>$destination</code> (o
		caminho superior dos últimos)',
	'scripts_securite_webmestres' => 'Por questões de segurança, apenas os webmasters
		deste site podem executar os scripts escritos nesta parte.',

	// T
	'titre_plugin' => 'Plugin « @plugin@ »',

	// V
	'valider_nom_objet_avant' => 'Para informar as cadeias de idiomas, por favor, valide
		o formulário depois de preencher o nome do objeto. Isto permite completar uma parte 
		das cadeias de idiomas, que você precisará apenas verificar.'
);
