
var diapo_on=false;
var center=true;
function diaposlide(timeout,mDiapo){
	if (diapo_on){
		mClass=$("#"+mDiapo+" .diapo .diapo_grand").show().attr('class').replace(' diapo_grand','').split('_');
		mpage="diapo_img";
		mid_article=mClass[1];
		mnum=mClass[2];
	    $.get("spip.php",
			{page : mpage, id_article : mid_article, num : mnum},
			function(txt){
				debut=txt.indexOf("<!-- debut diapo_img"+mid_article+" -->");
    			fin=txt.lastIndexOf("<!-- fin diapo_img"+mid_article+" -->");
    			txt=txt.substring(debut,fin);
				$("#"+mDiapo+" .diapo").html(txt);
			});
		setTimeout('diaposlide('+timeout+',mDiapo)', timeout);
	}
}
$.fn.diapo_mode = function() {
    return this.click(function() {
    	mDiapo=$(this).attr('rel');
		$("#"+mDiapo+" .diapo_icones a").removeClass("selected");
		$(this).addClass("selected");
		mId=$(this).attr('class');
		if (mId=="diapo_ico selected"){
			$("#"+mDiapo+" .diapo_vignettes").attr("class","diapo_vignettes diapo_vignettes_invisible");
			$("#"+mDiapo+" div.diapo").attr("class","diapo diapo_grand");
			$("#"+mDiapo+" div.diapo img.diapo_petit").hide();
			$("#"+mDiapo+" div.diapo img.diapo_grand").show();
			diapo_on=!diapo_on;
			center=true;
			if ($(this).html()=='||')$(this).html('&gt;');
			else $(this).html('||');
			diaposlide(8000,mDiapo);
		}else if (mId=="diapo_icoleft selected"){
			$("#"+mDiapo+" .diapo_vignettes").attr("class","diapo_vignettes diapo_vignettes_left");
			$("#"+mDiapo+" div.diapo").attr("class","diapo diapo_petit");
			$("#"+mDiapo+" div.diapo .diapo_grand").hide();
			$("#"+mDiapo+" div.diapo .diapo_petit").show();
			$("#"+mDiapo+" .diapo_ico").html('&gt;');
			diapo_on=false;
			center=false;
		}else if (mId=="diapo_icoright selected"){
			$("#"+mDiapo+" .diapo_vignettes").attr("class","diapo_vignettes diapo_vignettes_right");
			$("#"+mDiapo+" div.diapo").attr("class","diapo diapo_petit");
			$("#"+mDiapo+" div.diapo img.diapo_grand").hide();
			$("#"+mDiapo+" div.diapo img.diapo_petit").show();
			$("#"+mDiapo+" .diapo_ico").html('&gt;');
			diapo_on=false;
			center=false;
		}else{
			$("#"+mDiapo+" .diapo_vignettes").attr("class","diapo_vignettes");
			$("#"+mDiapo+" div.diapo").attr("class",'diapo diapo_grand');
			$("#"+mDiapo+" div.diapo img.diapo_petit").hide();
			$("#"+mDiapo+" div.diapo img.diapo_grand").show();
			$("#"+mDiapo+" .diapo_ico").html('&gt;');
			diapo_on=false;
			center=true;
		}
//		return false;
    });
};
$.fn.diapo_pagination = function() {
    return this.click(function() {
    	pagin="";
    	mDiapo=$(this).attr('rel');
    	mClass=$("#"+mDiapo+" div.diapo img.diapo_grand").attr('class').replace(" diapo_grand","").split('_');
		mPage="diapo";
		malign=$("#"+mDiapo+" .diapo_icones a.selected").attr('class').replace("diapo_ico","").replace(" selected","");
		mid_article=mClass[1];
		tab=$(this).attr('href').split('#');
    	i=tab[0].lastIndexOf('debut')
    	if (i>0)
			pagin="?"+tab[0].substring(i,(tab[0].indexOf('=',i)))+"="+tab[0].substring((tab[0].indexOf('=',i)+1),tab[0].length);	
    	$.get("spip.php"+pagin,
    			{page : mPage, id_article : mid_article, align : malign},
    			function(txt){
    				debut=txt.indexOf("<!-- debut diapo"+mid_article+" -->");
    				fin=txt.lastIndexOf("<!-- fin diapo"+mid_article+" -->");
    				txt=txt.substring(debut,fin);
    				$("#diapo"+mid_article).html(txt);
    				$("#diapo"+mid_article+" .diapo_icones a").diapo_mode();
					$("#diapo"+mid_article+" .diapo_menu a.lien_pagination").diapo_pagination();
					$("#diapo"+mid_article+" .diapo_vignette a").diapo_vignette();
					$("#diapo"+mid_article+" .diapo_icones .selected").click();	
					$("#diapo"+mid_article+" .diapo_menu a.lien_pagination").each(function(){
						$(this).attr("rel",$(this).parent().parent().parent().attr("id"));
					});
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
		malign=$("#diapo"+mid_article+" .diapo_icones a.selected").attr('class').replace("diapo_ico","").replace(" selected","");
		$.get("spip.php",
				{page : mpage, id_article : mid_article, num : mnum, align : malign},
				function(txt){
					debut=txt.indexOf("<!-- debut diapo_img"+mid_article+" -->");
    				fin=txt.lastIndexOf("<!-- fin diapo_img"+mid_article+" -->");
    				txt=txt.substring(debut,fin);
    				$("#diapo"+mid_article+" .diapo").html(txt);
				});
		return false;
    });
};
$.fn.diapo_center = function() {
	return this.css("display")=="none";
}
$(document).ready(function(){
	$(".diapo_icones a").diapo_mode();
	$(".diapo_menu a.lien_pagination").diapo_pagination();
	$(".diapo_vignette a").diapo_vignette();
	$(".diapo_icones .selected").click();	
	$(".diapo_menu a.lien_pagination").each(function(){
		$(this).attr("rel",$(this).parent().parent().parent().attr("id"));
	});
	
});
