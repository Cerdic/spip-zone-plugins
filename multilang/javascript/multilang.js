/**
 * multilang
 *
 * Copyright (c) 2006-2010 Renato Formato (rformato@gmail.com)
 * Licensed under the GPL License:
 *   http://www.gnu.org/licenses/gpl.html
 *
 * Modif Yffic mars 2010
 * - Correction bug : Fonctionnement incoherent si plusieurs menu dans la meme page (ex : portfolio dans page de
 *   presentation d'un article)
 * - On ne rajoute pas le menu s'il existe deja (cas d'un retour Ajax)
 * - On ne rajoute pas le menu dans forms d'upload (par ex vignette d'un doc)
 */


var multilang_containers={}, //menu containers
    multilang_forms_fields={},
    multilang_forms, //forms to be processed (jQuery object)
    multilang_menu_lang; //template of the menu (jQuery object)
/*
(?:\[([a-z_]+)\]|^[\s\n]*)
[lang] or white space

((?:.|\n)*?)
all chars not greedy

(?=\[[a-z_]+\]|$)
[lang] or end string
*/

/**
 * Initialisation de différentes variables :
 *
 */
var multilang_match_multi = /(?:\[([a-z_]+)\]|^[\s\n]*)((?:.|\n|\s)*?)(?=\[[a-z_]+\]|$)/ig;
var multilang_css_link,
    multilang_css_cur_link={},
    multilang_root, //root of the search (jQuery object)
    multilang_root_opt,
    multilang_fields_selector,
    multilang_menu_selector,
    multilang_containers,
    multilang_forms_selector; //selector of the forms to be processed (string)
multilang_css_link = {};
$.extend(multilang_css_cur_link,multilang_css_link,{fontWeight:"bold"});

/**
 * options is a hash having the following values:
 * - fields (mandatory): a jQuery selector to set the fields that have to be internationalized.
 * - page (optional): a string to be searched in the current url. if found the plugin is applied.
 * - root (optional): the root element of all processing. Default value is 'document'. To speed up search
 * - forms (optional): a jQuery selector to set the forms that have to be internationalized. Default value is 'form'.
 * - main_menu (optional): a jQuery selector to set the container for the main menu to control all the selected forms.
 * - form_menu (optional): a jQuery selector to set the container for the form menus.
 */
function multilang_init_lang(options) {
	//Detect if we're on the right page and if multilinguism is activated. If not return.
	if((options.page && window.location.search.indexOf(options.page)==-1) || multilang_avail_langs.length<=1) return;

	//set the root element of all processing

	var root = options.root || document;
	multilang_root = $(root+','+root_opt);
	multilang_root_opt = $(root_opt);
	//Add Yffic : S'il existe deja un menu lang sous multilang_root, return (cas Ajax) Ca, c'est pas terrible
	// Car dans Mediatheque, le menu ne s'affichera pas
	//if(multilang_root.find('.menu_lang').length > 0) return;

	//set the main menu element
	multilang_containers = options.main_menu ? $(options.main_menu,multilang_root) : $([]);

	//create menu lang template
	multilang_menu_lang = $("<div class='langues'>");
	$.each(multilang_avail_langs,function() {
		multilang_menu_lang.append($("<a class='change_lang'>").html("["+this+"]").css(this==multilang_def_lang?multilang_css_cur_link:multilang_css_link)[0]);
	});
	// Pour pouvoir desactiver le multilang
	multilang_menu_lang.append($("<a class='recover_lang' href='#'>").html("["+multilang_lang.lien_desactiver+"]").css(this==multilang_def_lang?multilang_css_cur_link:multilang_css_link)[0]);

	//init fields
	multilang_fields_selector = options.fields;
	//store all the internationalized forms
	// Modif Yffic : on exclue aussi les form d'upload (Pour les vignettes de docs, logos...)
	multilang_forms_selector = options.forms || "form[class!='form_upload'][class!='form_upload_icon']";
	multilang_forms = $(multilang_forms_selector,multilang_root);

	//create menu lang for the global form
	if(multilang_containers.size())
		multilang_make_menu_lang(multilang_containers);
	multilang_menu_selector = options.form_menu;

	// Modif Yffic : On va pas plus s'il n'y a pas de form
	if(multilang_forms.size()) multilang_init_multi();
}

function multilang_init_multi(options) {
	var target = options ? options.target : null;
	var init_forms;
	//Update the list of form if this is an update
	if(target) {
		//Verify the target is really a form to be internationalized (in case of an ajax request fired by onAjaxLoad)
		if(target==document) return;
		init_forms = $(target).find('form').in_set($(multilang_forms_selector,multilang_root));
		if(!init_forms.length) return;
		multilang_forms.add(init_forms.each(multilang_attach_submit).get());
	} else {
		//attach multi processing to submit event
		init_forms = multilang_forms;
		multilang_forms.each(multilang_attach_submit);
	}
	multilang_forms_fields = {};
	multilang_forms_fields["undefined"] = $(multilang_fields_selector,multilang_forms);
	//init the value of the field to current lang
	//add a container for the language menu inside the form
	init_forms.each(function() {
		this.form_lang = multilang_def_lang;
		var container = multilang_menu_selector ? $(multilang_menu_selector,this) : $(this);
		// Pas de rajout s'il y en deja un
		if(!container.find('.menu_lang').size())
			container.prepend("<div class='menu_lang'>");
	});
	/**
	 * Initialisation de chaque input ou textarea
	 * On vérifie si on est dans un formulaire optionnel (dans ce cas on ne prend que
	 * les éléments qui on la class optionnelle) sinon on prend tous les champs qui
	 * matchent
	 */
	$(multilang_fields_selector,init_forms).each(function(){
	    var me = $(this);
	    if($(multilang_fields_selector).parents(root_opt).size()>0){
	        if(me.is(forms_selector_opt)){
	        	multilang_init_field(this,this.form.form_lang);
	        }
	    }else{
			multilang_init_field(this,this.form.form_lang);
		}
	});
	//create menu for each form. The menu is just before the form
	$("div.menu_lang",init_forms).empty().each(function() {
		//store all form containers to allow menu lang update on each container
		//when it is triggered by global menu
		multilang_containers.add(this);
		multilang_make_menu_lang($(this),$(this).parents("form"));
	});
}

/**
 * Création du menu de langue
 * Liste les langues disponibles et ajoute un lien pour désactiver multilang
 *
 * @param container
 * @param target
 * @return
 */
function multilang_make_menu_lang(container,target) {
	target = target || multilang_forms;
	$(multilang_menu_lang).clone().find("a").click(function() {
		if($(this).is('.change_lang') && !$(this).is('.on')){
			$(this).parents('form > .menu_lang').find('a.on').removeClass('on');
			$(this).addClass('on');
			multilang_change_lang(this,container,target);
		}else if(!$(this).is('.on') && $(this).is('.recover_lang')){
			$(this).parents('form > .menu_lang').find('a.on').removeClass('on');
			$(this).addClass('on');
			multilang_multi_recover(this,container,target,'test');
		}
		return false;
	}).appendTo(container);
}

/**
 * Action au click sur une langue du menu de langue
 *
 * @param el Le lien cliqué sur le menu
 * @param container Le conteneur du formulaire
 * @param target Le formulaire lui même
 *
 */
function multilang_change_lang(el,container,target) {

	var target_id = target != multilang_forms ? jQuery.data(target[0]) : "undefined";
	// Modif Yffic : pas trop bien compris, mais avec le test, ca ne fonctionne pas bien dans le cas ou il y a plusieurs
	// menu_lang dans la meme page, par ex des docs dans la page de presentation d'article
	//if(!multilang_forms_fields[target_id])
	multilang_forms_fields[target_id] = $(multilang_fields_selector,target);

	var lang = el.innerHTML;

	/**
	 * Gestion des styles des liens
	 * - on met en gras la langue sélectionnée
	 * - on remet en normal les autres éléments du meny
	 */
	container.find("a").each(function(){
		$(this).css("fontWeight",lang==this.innerHTML?"bold":"normal");
	}).end();
	lang = lang.slice(1,-1);

	if(target.isfull){
		target.isfull = false;
		multilang_forms_fields[target_id].each(function(){
			multilang_save_lang(this,this.form.form_lang);
			multilang_init_field(this,lang,true);
		});
	}

	//store the fields inputs for later use (usefull for select)
	//save the current values
	multilang_forms_fields[target_id].each(function(){
		multilang_save_lang(this,this.form.form_lang);
	});
	//change current lang
	target.each(function(){this.form_lang = lang});

	$(el).parents('form > .menu_lang').find('.message').detach();
	$(el).parents('form').find('li.editer_titre_numero').show();

	//reinit fields to current lang
	multilang_forms_fields[target_id].each(function(){multilang_set_lang(this,lang)});
}

/**
 * Affiche le contenu complet du champ
 * Utilisé lors de la désactivation de multilang et de la validation des formulaires
 *
 * @param el Le lien de désactivation
 * @param container Le container du formulaire
 * @param target Le formulaire
 * @return
 */
function multilang_multi_recover(el,container,target,event){

	$(el).parents('form > .menu_lang').append('<div class="message"><p>'+multilang_lang.champs_readonly+'<\/p><\/div>');
	$(el).parents('form').find('li.editer_titre_numero').hide();

	if(target.isfull){
		return true;
	}
	if(!target.isfull || (event == 'submit' || 'test')){
		target.isfull = true;
		if(typeof(el) == "object"){
			var text = $(el).html();
			container.find("a").each(function(){
				$(this).css("fontWeight",text==$(this).html()?"bold":"normal");
			}).end();
		}
		lang = 'full';
		var target_id = target != multilang_forms ? jQuery.data(target[0]) : "undefined";
		multilang_forms_fields[target_id] = $(multilang_fields_selector,target);
		multilang_forms_fields[target_id].each(function(){
			multilang_field_set_background(this,'full');
			//Add Yffic 30/03/2010
			if(!this.totreat) return ;
			//End Add Yffic
			//save data before submit
			multilang_save_lang(this,this.form.form_lang);
			//build the string value
			if(this.form.form_lang != 'full'){
				var def_value = this.field_lang[multilang_def_lang];
				if(!this.multi)
					// Modif Yffic : Don't add the field_pre_lang now
					this.value = (def_value==undefined?"":def_value);
				else {
					var value="",count=0;
					$.each(this.field_lang,function(name){
						if(name != 'full'){
							//save default lang value and other lang values if different from
							//the default one
							if(this!=def_value || name==multilang_def_lang) {
								value += "["+name+"]"+this;
								count++;
							}
						}
					});
					// Modif Yffic : Don't add the field_pre_lang now
					this.value = (count!=1?"<multi>"+value+"</multi>":value.replace(/^\[[a-z_]+\]/,''));
				}
			}
			// Add Yffic 30/03/2010
			// Add the title number to the final value
			multilang_save_lang(this,'full');
			if((this.id=='titre' || this.id.match(/^titre_document[0-9]+/)) && ($('#'+this.id+'_numero').val() != ''))
				this.value= $('#'+this.id+'_numero').val().replace(/\.\s+/,'') + ". " + this.value;

			if((event == undefined) || (event != 'submit')){
				target.each(function(){
					this.form_lang = lang;
				});
			}
			// End Add Yffic

		});
		return true;
	}
}

/**
 * On attache nos évènements pour la validation du formulaire
 * - sur le submit pour tous les formulaires
 * - sur l'évènement 'form-pre-serialize' d'ajaxForms au cas où nous sommes dans
 * un formulaire ajax
 *
 * @return
 */
function multilang_attach_submit() {
	if($(this).parents('.ajax').size() && $(this).find('input[name=var_ajax]')){
		$(this).bind('form-pre-serialize',multilang_multi_submit);
	}else{
		var oldsubmit = this.onsubmit;
		this.onsubmit = "";
		if(oldsubmit && oldsubmit != "")
			$(this).submit(function(){multilang_multi_submit.apply(this);return oldsubmit.apply(this);})
		else if(oldsubmit != "")
				$(this).submit(multilang_multi_submit);
	}
}

/**
 * Initialisation du script sur un champ
 *
 * Récupère les données suivantes
 * - Le contenu de l'élément du champ
 * - Le booléen (true/false) multi (est ce un champs déjà multi)
 * - Les différentes chaines de langue dans un objet :
 * {fr="texte fr",en="texte en",full="<multi>[fr]texte fr[en]texte en</multi>"}
 * Si le champ est déjà initialisé, fait un simple return
 *
 * @param el Le champ a initialiser
 * @param lang La langue
 *
 */
function multilang_init_field(el,lang,force) {
	if(el.field_lang && !force) return;
	var langs;
	if(force){
		el.value = (el.field_lang['full'] == undefined ? '' : el.field_lang['full']);
	}
	// Modif Yffic : ne pas considerer comme multi les champs qui contiennent du texte
	// en dehors des multi sauf un numero (\d+\.\s+)
	el.value = el.value.replace(/^\s+/g,'').replace(/\s+$/g,'');
	var m = el.value.match(/(\d+\.\s+)?<multi>((?:.|\n|\s)*?)<\/multi>(.*)/);
	el.field_lang = {};
	el.field_pre_lang = ""; //this is the 01. part of the string, the will be put outside the <multi>
	el.titre_el = $("#titre_"+el.id);
	// Add Yffic 30/03/2010
	// Init the elements to treat
	if(m!=null) {
		if( m.index || (m[3]!=undefined && m[3]!=""))
			el.totreat=false;
		else
			el.totreat=true;
		if(el.totreat) {
			el.field_pre_lang = m[1] || "";
			// Modif Yffic : suppress point and spaces
			el.field_pre_lang = el.field_pre_lang.replace(/\.\s+/,'') ;
			el.multi = true;
			multilang_match_multi.lastIndex=0;
			el.field_lang['full'] = el.value;
			while((langs=multilang_match_multi.exec(m[2]))!=null) {
				var text = langs[2].match(/^(\d+\.\s+)((?:.|\n|\s)*)/), value;
				if(text!=null) {
					value = text[2];
					// Modif Yffic : suppress point and spaces
					el.field_pre_lang = text[1].replace(/\.\s+/,'') || "";
				} else {
					value = langs[2];
				}
				el.field_lang[langs[1]||multilang_def_lang] = value;
			}
		}
	} else {
		el.multi = false;
		el.totreat=true;

		var n = el.value.match(/(\d+\.\s+)?(.*)/);
		el.field_pre_lang = n[1] || "";
		el.field_pre_lang = el.field_pre_lang.replace(/\.\s+/,'') ;
		el.field_lang[lang] = n[2];
	}

	//Put the current lang string only in the field
	multilang_set_lang(el,lang);
	multilang_field_set_background(el,lang) ;

	/**
	 * Si le champ est un titre, on ajoute un champ facultatif "numéro" au formulaire permettant
	 * de traiter le cas où l'on utilise les numéros pour trier les objets
	 * Ajout d'Yffic le 30/03/2010
	 */
	if(!force && (el.id=='titre' || el.id.match(/^titre_document[0-9]+/))){
		numid=el.id+'_numero';
		$(el).parent()
				.before('<li class="editer_'+numid+'"><label for="titre_numero">'+multilang_lang.numero+'</label><input id="'+numid+'" name="titre_numero" type="text" value="'+el.field_pre_lang+'" class="text nomulti"></input></li>');
		$('#'+numid).totreat = false;
	}
}

/**
 * Change l'image de fond d'un champ pour indiquer la présence ou non de multis
 *
 * @param el
 * @param lang
 * @return
 */
function multilang_field_set_background(el,lang) {
	if(lang != 'full'){
		if(el.totreat){
			$(el).removeAttr('readonly');
			$(el).css({"background-image":"url("+multilang_dir_plugin+"/images/multi_"+(el.multi?lang:undefined)+".png)","backgroundRepeat":"no-repeat","backgroundPosition":"top right"});
		}
		else
			$(el).css({"background":"url("+multilang_dir_plugin+"/images/multi_forbidden.png) no-repeat right top"});
	}else{
		$(el).attr('readonly','readonly');
		$(el).css({"backgroundImage":""});
	}
}

/**
 * Action au changement de la langue
 * Lorsque l'on clique sur une le menu de langue
 * On affiche pour le champ "el" son contenu dans la langue "lang"
 *
 * @param el Un champ du formulaire (input ou textarea)
 * @param lang La langue correspondante souhaitée
 * @return
 */
function multilang_set_lang(el,lang) {
	//Add Yffic 30/03/2010
	if(!el.totreat) return ;
	//End Add Yffic

	//if current lang is not setted use default lang value
	if(el.field_lang[lang]==undefined)
		el.field_lang[lang] = el.field_lang[multilang_def_lang];
	// Modif Yffic : Don't add the field_pre_lang
	el.value = (el.field_lang[lang] == undefined ? "" : el.field_lang[lang]); //show the common part (01. ) before the value
	el.titre_el.html(el.value);

	multilang_field_set_background(el,lang) ;
}

/**
 * Garde en mémoire dans l'array de langues du champs la valeur de la langue
 * sélectionnée
 *
 * @param el Le champ du formulaire concerné
 * @param lang La langue actuelle
 * @return
 */
function multilang_save_lang(el,lang) {
	//Add Yffic 30/03/2010
	if(!el.totreat) return ;
	//End Add Yffic

	//if the lang value is equal to the def lang do nothing
	//else save value but if the field is not empty, delete lang value
	var m = el.value.match(/^(\d+\.\s+)((?:.|\n|\s)*)/);

	if(m!=null) {
		// Modif Yffic : suppress point and spaces
		el.field_pre_lang = m[1].replace(/\.\s+/,'');
		el.value = m[2];
	}
	if(el.field_lang[multilang_def_lang]!=$(el).val()) {
		if(!$(el).val()) {
			delete el.field_lang[lang];
			return;
		}
		el.multi = true;
		el.field_lang[lang] = el.value;
		console.log(el.value);
	}
}

//This func receives the form that is going to be submitted
function multilang_multi_submit(params) {
	if(multilang_avail_langs.length<=1) return;
	var form = this;
	//remove the current form from the list of forms
	multilang_forms.not(this);
	//remove the current menu lang container from the list
	multilang_containers.not("div.menu_lang",$(this));
	//build the input values
	multilang_multi_recover('','',form,'submit');
	//save back the params
	if(params) $.extend(params,$(form).formToArray(false));
}

jQuery.fn.in_set = function(set) {
	var elements = this.get();
	var result = $.grep(set,function(i){
		var found = false;
		$.each(elements,function(){
			if(this==i) found=true;
		})
		return found;
	});
	return jQuery(result);
}
