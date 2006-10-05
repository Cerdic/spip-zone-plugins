
url_widgets_html = 'spip.php?action=widgets_html&class=';

$.cancelwidgets = function() {
  $(".widget").each(function(){
    var html = $(this).attr('orig_html');
    if (html == null) {
      $(this).html(html);
    }
    $(this).removeAttr('orig_html');
  });
}

$.initallwidgets = function(e) {
  $('.widget').each(function(){
    $.initwidget(this);}
  );
  e.stopPropagation();
}

$.initwidget = function(me) {
    // voir si je suis en mode "widget"
    if ($(me).attr('orig_html') != null)
      return;

    // reglages de taille mini/maxi; pas tres beau
    var w,h;
    w = $(me).width()-50; // 50 = largeur du bouton "ok"
    h = $(me).height();

    // charger le formulaire
    $.get(url_widgets_html+encodeURIComponent(me.className),
       function (c) {
         $(me)
         .attr('orig_html', $(me).html())
         .html(c)
         .find('form')
           .ajaxForm(function(c){
             $(me)
             .html(c.responseText)
             .removeAttr('orig_html');
           })
           .find(".widget-active")
             .css('backgroundColor', 'yellow')
             .css('font', 'inherit') // pour safari
             .css({
                 'fontSize': $(me).css('fontSize'),
                 'fontFamily': $(me).css('fontFamily')
             })
             .each(function() {
               if (w<100) w=100;
               if (w>700) w=700;
               if (this.nodeName.toUpperCase()=='TEXTAREA') {
                 if (h<36) h=36;
                 h+='px';
               } else {
                 if (h<12) h=$(me).css('fontSize');
                 else h+='px';
               }
               $(this).css({"width":w+'px',"height":h});
             })
             .each(function(n){if (n==0) this.focus();})
             .keypress(function(e){
               if (e.keyCode == 27) {
                 $(me)
                 .html($(me).attr('orig_html'))
                 .removeAttr('orig_html');
               }
             })
           .end()
           .find(".cancel_widget")
             .click(function(){
               $(me)
               .html($(me).attr('orig_html'))
               .removeAttr('orig_html');
               return false;
             })
           .end()
         .end()
         ;
       }
     );
  }

$.clickwidget = function(e){
  e.stopPropagation(); // avoid sending a global click to the body onclick
  $.initwidget(this);
}

$(function() {
  $(".widget")
  .removeAttr('orig_html')
  .click($.clickwidget);
  $("html")
  .click($.cancelwidgets);
});

