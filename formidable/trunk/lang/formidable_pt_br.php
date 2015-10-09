<?php
// This is a SPIP language file  --  Ceci est un fichier langue de SPIP
// extrait automatiquement de http://trad.spip.net/tradlang_module/formidable?lang_cible=pt_br
// ** ne pas modifier le fichier **

if (!defined('_ECRIRE_INC_VERSION')) return;

$GLOBALS[$GLOBALS['idx_lang']] = array(

	// A
	'activer_pages_explication' => 'Por default, as páginas publicadas dos formulários não são autorizadas',
	'activer_pages_label' => 'Autorizar a criação de páginas publicadas por formulários',
	'admin_reponses_auteur' => 'Autorizar os autores dos formulários a modificar as respostas',
	'admin_reponses_auteur_explication' => 'Somente os administradores podem modificar as respostas enviadas por um formulário (Na lixeira, publicado, proposto). Essa opção permite a um autor de formulário de modificar o status (com risco de criar eventuais erros estatísticos).',
	'analyse_avec_reponse' => 'Respostas não vazias',
	'analyse_exclure_champs_explication' => 'Liste os nomes de campos a serem excluídos da análise, separados por <code>|</code>. Não inserir <code>@</code>.',
	'analyse_exclure_champs_label' => 'Campos a excluir',
	'analyse_exporter' => 'Exportar análise',
	'analyse_longueur_moyenne' => 'Comprimento médio em número de palavras',
	'analyse_nb_reponses_total' => '@nb@ pessoas preencheram esse formulário.',
	'analyse_sans_reponse' => 'Deixado em branco',
	'analyse_une_reponse_total' => 'Uma pessoa preencheu esse formulário.',
	'analyse_zero_reponse_total' => 'Ninguém ainda preencheu esse formulário.',
	'aucun_traitement' => 'Nenhuma interação',
	'autoriser_admin_restreint' => 'Autorizar administradores restritos a criar e modificar os formulários',
	'autoriser_admin_restreint_explication' => 'Por default, somente administradores têm acesso à criação e modificação dos formulários',

	// B
	'bouton_formulaires' => 'Formulários',
	'bouton_revert_formulaire' => 'Reverter à última versão gravada',

	// C
	'cfg_analyse_classe_explication' => 'Indique as classes CSS que serão adicionadas ao conteúdo de cada gráfico, como <code>gray</code>,<code>blue</code>,<code>orange</code>,<code>green</code> ou como preferir!',
	'cfg_analyse_classe_label' => 'Classe CSS da barra de progresso',
	'cfg_titre_page_configurer_formidable' => 'Configurar Formidable',
	'cfg_titre_parametrages_analyse' => 'Parâmetros de análise dos preenchimentos',
	'champs' => 'Campos',
	'changer_statut' => 'Este formulário está:',

	// E
	'echanger_formulaire_forms_importer' => 'Forms & Tables (.xml)',
	'echanger_formulaire_wcs_importer' => 'W.C.S. (.wcs)',
	'echanger_formulaire_yaml_importer' => 'Formidable (.yaml)',
	'editer_apres_choix_formulaire' => 'Retornar ao formulário',
	'editer_apres_choix_redirige' => 'Redirecionar a outro endereço',
	'editer_apres_choix_rien' => '
Nada',
	'editer_apres_choix_stats' => 'Estatísticas de preenchimentos',
	'editer_apres_choix_valeurs' => 'Valores preenchidos',
	'editer_apres_explication' => 'Após validação, exibir no lugar do formulário:',
	'editer_apres_label' => 'Exibir em seguida',
	'editer_descriptif' => 'Descrição',
	'editer_descriptif_explication' => 'Explicação do formulário destinada ao espaço privado.',
	'editer_identifiant' => 'Identificador',
	'editer_identifiant_explication' => 'Insira um identificador único que com o qual se acessará o formulário.',
	'editer_menu_auteurs' => 'Configurar autores',
	'editer_menu_champs' => 'Configurar campos',
	'editer_menu_formulaire' => 'Configurar o formulário',
	'editer_menu_traitements' => 'Configurar interações',
	'editer_message_ok' => 'Mensagem de retorno',
	'editer_message_ok_explication' => 'Personalize a mensagem que será exibida ao usuário após o envio de um formulário válido.',
	'editer_modifier_formulaire' => 'Modificar formulário',
	'editer_nouveau' => 'Novo formulário',
	'editer_redirige_url' => 'Endereço de redirecionamento após validação',
	'editer_redirige_url_explication' => 'Deixar em branco se quiser que fique na mesma página',
	'editer_resume_reponse' => 'Exibe resumo do preenchimento',
	'editer_resume_reponse_explication' => 'Esta cadeia será utilizada para exibir um resumo de cada preenchimento nas listas. Os campos como <tt>@input_1@</tt> serão substituídos conforme indicado na Colinha ao lado.', # RELIRE
	'editer_titre' => 'Título',
	'erreur_autorisation' => 'Você não tem permissão para editar os formulários do site.',
	'erreur_base' => 'Aconteceu um erro técnico durante a gravação.',
	'erreur_generique' => 'Há erro(s) no preenchimento do(s) campo(s) indicado(s) abaixo.',
	'erreur_identifiant' => 'Esse identificador já está em uso.',
	'erreur_identifiant_format' => 'O identificador só pode conter letras e o caracter "_"', # RELIRE
	'erreur_importer_forms' => 'Erro na importação do formulário Forms&Tables',
	'erreur_importer_wcs' => 'Erro na importação do formulário W.C.S',
	'erreur_importer_yaml' => 'Erro na importação do arquivo YAML',
	'erreur_inexistant' => 'Formulário inexistente.',
	'exporter_formulaire_format_label' => 'Formato do arquivo',
	'exporter_formulaire_statut_label' => 'Preenchimentos',

	// F
	'formulaire_anonyme_explication' => 'Este formulário é anônimo, ou seja, a identificação de usuário não será gravada.',
	'formulaires_aucun' => 'No momento não há nenhum formulário.',
	'formulaires_aucun_champ' => 'No momento não há nenhum campo de entrada para esse formulário.',
	'formulaires_dupliquer' => 'Duplicar formulário',
	'formulaires_dupliquer_copie' => '(cópia)',
	'formulaires_introduction' => 'Crie e configure aqui os formulários do site.',
	'formulaires_nouveau' => 'Criar um novo formulário',
	'formulaires_supprimer' => 'Apagar formulário',
	'formulaires_supprimer_confirmation' => 'Atenção, isto apagará também todos os preenchimentos. Tem certeza de que quer apagar este formulário?',
	'formulaires_tous' => 'Todos os formulários',

	// I
	'identification_par_cookie' => 'Por cookie',
	'identification_par_id_auteur' => 'Por identificação (id_auteur) de usuário autenticado',
	'importer_formulaire' => 'Importar um formulário',
	'importer_formulaire_fichier_label' => 'Arquivo a importar',
	'importer_formulaire_format_label' => 'Formato do arquivo',
	'info_1_formulaire' => '1 formulário',
	'info_1_reponse' => '1 preenchimento',
	'info_aucun_formulaire' => 'Nenhum formulário',
	'info_aucune_reponse' => 'Nenhum preenchimento',
	'info_formulaire_refuse' => 'Arquivado',
	'info_formulaire_utilise_par' => 'Formulário utilizado por:',
	'info_nb_formulaires' => '@nb@ formulários',
	'info_nb_reponses' => '@nb@ preenchimentos',
	'info_reponse_proposee' => 'A moderar',
	'info_reponse_proposees' => 'A moderar',
	'info_reponse_publiee' => 'Validado',
	'info_reponse_publiees' => 'Validados',
	'info_reponse_supprimee' => 'Apagado',
	'info_reponse_supprimees' => 'Apagados',
	'info_reponse_toutes' => 'Todos',
	'info_utilise_1_formulaire' => 'Formulário utilizado:',
	'info_utilise_nb_formulaires' => 'Formulários utilizados:',

	// M
	'modele_label_formulaire_formidable' => 'Qual formulário?',
	'modele_nom_formulaire' => 'um formulário',

	// N
	'noisette_label_afficher_titre_formulaire' => 'Exibir título do formulário?',
	'noisette_label_identifiant' => 'Formulário a exibir:',
	'noisette_nom_noisette_formulaire' => 'Formulário',

	// R
	'reponse_aucune' => 'Nenhum preenchimento',
	'reponse_intro' => '@auteur@ preencheu o formulário @formulaire@',
	'reponse_numero' => 'Preenchimento número:',
	'reponse_statut' => 'Este preenchimento está:',
	'reponse_supprimer' => 'Apagar este preenchimento',
	'reponse_supprimer_confirmation' => 'Tem certeza de que quer apagar este preenchimento?',
	'reponse_une' => '1 preenchimento',
	'reponses_analyse' => 'Análise dos preenchimentos',
	'reponses_anonyme' => 'Anônimo',
	'reponses_auteur' => 'Usuário',
	'reponses_exporter' => 'Exportar preenchimentos',
	'reponses_exporter_format_csv' => 'Tableur .CSV',
	'reponses_exporter_format_xls' => 'Excel .XLS',
	'reponses_exporter_statut_publie' => 'Publicados',
	'reponses_exporter_statut_tout' => 'Todos',
	'reponses_exporter_telecharger' => 'Download',
	'reponses_ip' => 'Endereço IP',
	'reponses_liste' => 'Lista de preenchimentos',
	'reponses_liste_prop' => 'Preenchimentos aguardando validação',
	'reponses_liste_publie' => 'Todos os preenchimentos validados',
	'reponses_nb' => '@nb@ preenchimentos',
	'reponses_supprimer' => 'Apagar todos os preenchimentos deste formulário',
	'reponses_supprimer_confirmation' => 'Tem certeza de que quer apagar todos os preenchimentos deste formulário?',
	'reponses_voir_detail' => 'Ver preenchimento',
	'retour_aucun_traitement' => 'Seu preenchimento foi enviado com sucesso, mas não há interação prevista para esse formulário.',

	// S
	'sans_reponses' => 'Não preenchido',

	// T
	'texte_statut_poubelle' => 'apagado',
	'texte_statut_propose_evaluation' => 'proposto',
	'texte_statut_publie' => 'validado',
	'texte_statut_refuse' => 'arquivado',
	'titre_cadre_raccourcis' => 'Atalhos',
	'titre_formulaires_archives' => 'Arquivos',
	'titre_reponses' => 'Preenchimentos',
	'traitements_actives' => 'Interações ativadas',
	'traitements_aide_memoire' => 'Colinha:',
	'traitements_avertissement_creation' => 'As modificações nos campos do formulário foram gravadas com sucesso. Defina agora quais interações serão efetuadas na utilização do formulário.',
	'traitements_avertissement_modification' => 'As modificações nos campos do formulário foram gravadas com sucesso. <strong>Talvez seja necessário reconfigurar algumas interações.</strong>',
	'traitements_champ_aucun' => 'Nenhum',
	'traiter_email_description' => 'Postar o resultado do formulário por correio eletrônico a uma lista de destinatários.',
	'traiter_email_horodatage' => 'Formulário "@formulaire@" postado em @date@ às @heure@.',
	'traiter_email_message_erreur' => 'Aconteceu um erro no envio do correio eletrônico.',
	'traiter_email_message_ok' => 'Sua mensagem foi enviada por correio eletrônico.',
	'traiter_email_option_activer_accuse_label' => 'Acusar recebimento',
	'traiter_email_option_activer_accuse_label_case' => 'Enviar também um correio eletrônico para o remetente com uma mensagem de confirmação.',
	'traiter_email_option_courriel_envoyeur_accuse_explication' => 'Indique o correio eletrônico utilizado para enviar a acusação de recebimento. Por default, o destinatário será o remetente.',
	'traiter_email_option_courriel_envoyeur_accuse_label' => 'Correio eletrônico de acusação de recebimento',
	'traiter_email_option_destinataires_champ_form_explication' => 'Se um dos campos é um endereço de email, selecione-o se quiser enviar-lhe o formulário.',
	'traiter_email_option_destinataires_champ_form_label' => 'Destinatário presente em um dos campos dos formulários',
	'traiter_email_option_destinataires_explication' => 'Escolha o campo que corresponde aos destinatários da mensagem.',
	'traiter_email_option_destinataires_label' => 'Destinatários',
	'traiter_email_option_destinataires_plus_explication' => 'Uma lista de endereços eletrônicos separados por vírgulas.',
	'traiter_email_option_destinataires_plus_label' => 'Destinatários suplementares',
	'traiter_email_option_envoyeur_courriel_explication' => 'Escolha o campo que contém o correio eletrônico do remetente.',
	'traiter_email_option_envoyeur_courriel_label' => 'Correio eletrônico do remetente',
	'traiter_email_option_envoyeur_nom_explication' => 'Construa o nome com ajuda dos @raccourcis@ (cf. colinha). Deixando em branco, será o nome do site.',
	'traiter_email_option_envoyeur_nom_label' => 'Nome do remetente',
	'traiter_email_option_nom_envoyeur_accuse_explication' => 'Indique o nome do remetente utilizado para enviar a acusação de recebimento. Por default, o destinatário será o remetente.',
	'traiter_email_option_nom_envoyeur_accuse_label' => 'Nome do remetente da acusação de recebimento',
	'traiter_email_option_sujet_accuse_label' => 'Assunto da acusação de recebimento',
	'traiter_email_option_sujet_explication' => 'Construa o assunto com ajuda dos @raccourcis@. Deixando em branco, o assunto será automatizado.',
	'traiter_email_option_sujet_label' => 'Assunto da mensagem',
	'traiter_email_option_vrai_envoyeur_explication' => 'Alguns servidores SMTP não permitem a utilização de correio eletrônico arbitrário para o campo "From". Por isso, Formidable insere por default o correio eletrônico do remetente no campo "Reply-To". Verifique aqui para inserir o correio eletrônico no campo "From".',
	'traiter_email_option_vrai_envoyeur_label' => 'Inserir o correio eletrônico do remetente no campo "From"',
	'traiter_email_page' => '<a href="@url@">nesta página</a>.',
	'traiter_email_sujet' => '@nom@  enviou mensagem',
	'traiter_email_sujet_accuse' => 'Obrigado por preencher.',
	'traiter_email_titre' => 'Enviar por correio eletrônico',
	'traiter_email_url_enregistrement' => 'Gerenciamento de preenchimentos <a href="@url@">aqui</a>.',
	'traiter_enregistrement_description' => 'Gravar preenchimentos dos formulários na base de dados',
	'traiter_enregistrement_erreur_base' => 'Aconteceu um erro técnico durante a gravação na base de dados',
	'traiter_enregistrement_erreur_deja_repondu' => 'Você já preencheu este formulário.',
	'traiter_enregistrement_erreur_edition_reponse_inexistante' => 'O preenchimento a editar não foi encontrado.',
	'traiter_enregistrement_message_ok' => 'Sucesso. Seu preenchimento foi registrado',
	'traiter_enregistrement_option_anonymiser_explication' => 'Preenchimentos anônimos (não guardar identificação das pessoas que responderam).',
	'traiter_enregistrement_option_anonymiser_label' => 'Formulário anônimo',
	'traiter_enregistrement_option_anonymiser_variable_explication' => 'Variável utilizada para calcular um valor único para cada autor sem revelar sua identidade.',
	'traiter_enregistrement_option_anonymiser_variable_label' => 'Variável a partir da qual anonimar o formulário',
	'traiter_enregistrement_option_auteur' => 'Utilizar autores para os formulários',
	'traiter_enregistrement_option_auteur_explication' => 'Atribuir um ou vários autores a um formulário. Se esta opção estiver ativa, somente os autores do formulário podem acessar esses dados.',
	'traiter_enregistrement_option_choix_select_label' => 'Escolher uma variável entre as opções',
	'traiter_enregistrement_option_identification_explication' => 'Se os preenchimentos são modificáveis, qual o procedimento preferencialmente utilizado para saber qual preenchimento modificar?',
	'traiter_enregistrement_option_identification_label' => 'Identificação',
	'traiter_enregistrement_option_ip_label' => 'Gravar IPs (masquÃ©es aprÃ¨s un dÃ©lai de garde)',
	'traiter_enregistrement_option_moderation_label' => 'Moderação',
	'traiter_enregistrement_option_modifiable_explication' => 'Modificável: visitantes podem modificar seus preenchimentos realizados.',
	'traiter_enregistrement_option_modifiable_label' => 'Preenchimentos modificáveis',
	'traiter_enregistrement_option_multiple_explication' => 'Múltiplos: Uma mesma pessoa pode preencher várias vezes.',
	'traiter_enregistrement_option_multiple_label' => 'Preenchimentos múltiplos',
	'traiter_enregistrement_titre' => 'Gravar resultados',

	// V
	'voir_exporter' => 'Exportar formulário',
	'voir_numero' => 'Formulário número:',
	'voir_reponses' => 'Ver preenchimentos',
	'voir_traitements' => 'Interações'
);

?>
