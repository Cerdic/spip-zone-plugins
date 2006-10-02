var forms_containers={},forms_fields={},forms_forms,forms_menu_lang;
var match_multi = /\[([a-z_]+)\](.*?)(?=\[[a-z_]+\]|$)/mig;
//var forms_avail_langs = ['it','fr','en'];
//var forms_def_lang = "it";
var forms_cur_lang,css_link,css_cur_link={};
css_link = {cursor:"pointer",margin:"2px 5px",float:"left"};
$.extend(css_cur_link,css_link)
$.extend(css_cur_link,{fontWeight:"bold"});

function forms_init_lang() {
	//Should detect is multilinguism is activated. If not return.
	if(forms_avail_langs.length<=1) return;
	forms_cur_lang = forms_def_lang;
	forms_containers = $("#forms_lang");
	//create menu lang template 
	forms_menu_lang =$("<div>");
	$.each(forms_avail_langs,function() {
		forms_menu_lang.append($("<a>").html("["+this+"]").css(this==forms_def_lang?css_cur_link:css_link)[0]);
	});
	//create menu lang for the global form
	forms_make_menu_lang(forms_containers);
	//init fields
	console.time("forms_init_multi()");
	forms_init_multi();
	console.timeEnd("forms_init_multi()");
}

function forms_make_menu_lang(container,target) {
	$(forms_menu_lang).clone().find("a").click(function() {forms_change_lang(this,container,target)}).end().
	append("<div style='clear:left'></div>").appendTo(container);
}

function forms_change_lang(el,container,target) {
	var lang = el.innerHTML;
	container = container || forms_containers;
	//update lang menu with current selection
	container.find("a").each(function(){
		$(this).css("fontWeight",lang==this.innerHTML?"bold":"normal");
	}).end();
	lang = lang.slice(1,-1);
	//store the fields inputs for later use (usefull for select)
	if(!forms_fields[target]) forms_fields[target] = $('input[@id][@id^="nom_"]',target);
	//save the current values
	forms_fields[target].each(function(){forms_save_lang(this,forms_cur_lang)});
	//change current lang
	forms_cur_lang = lang;
	//reinit fields to current lang
	forms_fields[target].each(function(){forms_set_lang(this,lang)});
}

function forms_init_multi() {
	//store all the fields forms
	forms_forms = $("form.forms_champ").submit(forms_multi_submit);
	//init the value of the field to current lang
	//bug of jquery 1.0.1 Error if attribute is not defined and matching its value
	forms_fields["undefined"] = $('input[@id][@id^="nom_"]',forms_forms).each(function() {forms_init_field(this,forms_def_lang)});
	//create menu for each form. The menu is just before the form
	forms_forms.prev().each(function() {
		var id = "#"+this.id;
		//store all form containers to allow menu lang update on each container
		//when it is triggered by global menu
		forms_containers.add(id);
		forms_make_menu_lang($(id),$(this).next())
	}).end();
}

function forms_init_field(el,lang) {
	//Retrieves the following data 
	//1)the title element of the field 
	//2)boolean multi = the fields has a multi value
	//3)various lang string
	var langs;
	var m = el.value.match(/<multi>(.*?)<\/multi>/m);
	el.field_lang = {};
	el.titre_el = $("#titre_"+el.id);
	if(m!=null) {
		el.multi = true;
		match_multi.lastIndex=0;
		while((langs=match_multi.exec(m[1]))!=null) {
			el.field_lang[langs[1]] = langs[2]; 
		}
		//Put the current lang string only in the field
		forms_set_lang(el,lang);
	} else {
		el.multi = false;
		el.field_lang[lang] = el.value; 		
	}
}

function forms_set_lang(el,lang) {
	//if current lang is not setted use default lang value
	if(el.field_lang[lang]==undefined)
			el.field_lang[lang] = el.field_lang[forms_def_lang];
	el.value = el.field_lang[lang];
	el.titre_el.html(el.value);
}

function forms_save_lang(el,lang) {
	//if the lang value is equal to the def lang do nothing
	//else save value
	if(el.field_lang[forms_def_lang]!=el.value) { 
		el.multi = true;
		el.field_lang[lang] = el.value;
	}
}

//This func receives the forms that is going to be submitted
function forms_multi_submit() {
	$('input[@id][@id^="nom_"]',this).each(function(){
		//save data before submit
		forms_save_lang(this,forms_cur_lang);
		//build the string value
		if(!this.multi) this.value = this.field_lang[forms_def_lang];
		else {
			var value="",count=0;
			$.each(this.field_lang,function(name){
				value += "["+name+"]"+this;
				count++;
			});
			this.value = count!=1?"<multi>"+value+"</multi>":value.replace('^\[[a-z_]+\]','');
		} 
	});
}
