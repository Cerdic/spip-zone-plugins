
var diapo_on=false;
var center=true;
function diaposlide(timeout){
	if (diapo_on){
		mClass=$("#diapo_grand").show().attr('class').split('_');
		mpage="diapo_img";
		mid_article=mClass[1];
		mnum=mClass[2];
		$.get("spip.php",
			{page : mpage, id_article : mid_article, num : mnum},
			function(txt){
				debut=txt.indexOf("<!-- debut diapo_img"+mid_article+" -->");
    			fin=txt.lastIndexOf("<!-- fin diapo_img"+mid_article+" -->");
    			txt=txt.substring(debut,fin);
				$("#diapo").html(txt);
			});
		setTimeout('diaposlide('+timeout+')', timeout);
	}
}
$.fn.diapo_mode = function() {
    return this.click(function() {
    	$("#diapo_icones a").removeClass("selected");
		$(this).addClass("selected");
		mId=$(this).id();
		if (mId=="diapo_ico"){
			$("#diapo_vignettes").attr("class","diapo_vignettes_invisible");
			$("#diapo").attr("class","diapo_grand");
			$("#diapo_petit").hide();
			$("#diapo_grand").show();
			diapo_on=!diapo_on;
			center=true;
			if ($(this).html()=='||')$(this).html('&gt;');
			else $(this).html('||');
			diaposlide(8000);
		}else if (mId=="diapo_icoleft"){
			$("#diapo_vignettes").attr("class","diapo_vignettes_left");
			$("#diapo").attr("class","diapo_petit");
			$("#diapo_grand").hide();
			$("#diapo_petit").show();
			$("#diapo_ico").html('&gt;');
			diapo_on=false;
			center=false;
		}else if (mId=="diapo_icoright"){
			$("#diapo_vignettes").attr("class","diapo_vignettes_right");
			$("#diapo").attr("class","diapo_petit");
			$("#diapo_grand").hide();
			$("#diapo_petit").show();
			$("#diapo_ico").html('&gt;');
			diapo_on=false;
			center=false;
		}else{
			$("#diapo_vignettes").attr("class","diapo_vignettes");
			$("#diapo").attr("class",'diapo_grand');
			$("#diapo_petit").hide();
			$("#diapo_grand").show();
			$("#diapo_ico").html('&gt;');
			diapo_on=false;
			center=true;
		}
//		return false;
    });
};
$.fn.diapo_pagination = function() {
    return this.click(function() {
    	pagin="";
    	mClass=$("#diapo_grand").attr('class').split('_');
		mPage="diapo";
		malign=$("#diapo_icones a.selected").id().replace("diapo_ico","");
		mid_article=mClass[1];
		tab=$(this).href().split('#');
    	i=tab[0].lastIndexOf('debut_')
    	if (i>0)
			pagin="?"+tab[0].substring(i,(tab[0].indexOf('=',i)))+"="+tab[0].substring((tab[0].indexOf('=',i)+1),tab[0].length);	
    	$.get("spip.php"+pagin,
    			{page : mPage, id_article : mid_article, align : malign},
    			function(txt){
    				debut=txt.indexOf("<!-- debut diapo"+mid_article+" -->");
    				fin=txt.lastIndexOf("<!-- fin diapo"+mid_article+" -->");
    				txt=txt.substring(debut,fin);
    				$("#diapo"+mid_article).html(txt);
    				$("#diapo_icones a").diapo_mode();
					$("div.diapo_menu a.lien_pagination").diapo_pagination();
					$("div.diapo_vignette a").diapo_vignette();
    			});
		return false;
    });
};
$.fn.diapo_vignette = function() {
    return this.click(function() {
    	mClass=$(this).attr("class").split('_');
 		mpage="diapo_img";
		mid_article=mClass[1];
		mnum=mClass[2]-1;
		malign=$("#diapo_icones a.selected").id().replace("diapo_ico","");
		$.get("spip.php",
				{page : mpage, id_article : mid_article, num : mnum, align : malign},
				function(txt){
					debut=txt.indexOf("<!-- debut diapo_img"+mid_article+" -->");
    				fin=txt.lastIndexOf("<!-- fin diapo_img"+mid_article+" -->");
    				txt=txt.substring(debut,fin);
    				$("#diapo").html(txt);
				});
		return false;
    });
};
$.fn.diapo_center = function() {
	return this.css("display")=="none";
}
$(document).ready(function(){
	$("#diapo_icones a").diapo_mode();
	$("div.diapo_menu a.lien_pagination").diapo_pagination();
	$("div.diapo_vignette a").diapo_vignette();
	diapogrand=$("#diapo_grand").get(0);
	if (diapogrand) center=!($("#diapo_grand").css("display")=="none");
});
