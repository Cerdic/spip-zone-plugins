
url_widgets_html = 'spip.php?action=widgets_html&class=';

$.cancelwidgets = function(e){
  $(".widget").each(function(){
    var html = $(this).attr('orig_html');
    if (html != '<>')
      $(this).html(html);
  }).attr('orig_html', '<>');
}

$.setupwidget = function(e){
    var me = this;
    e.stopPropagation(); // avoid sending a global click to the body onclick

    // si je suis en mode "widget"
    if ($(me).attr('orig_html') == '<>') {
      $(me).attr('orig_html', $(me).html());
    } else {
      return;
    }

    // reglages de taille mini/maxi; pas tres beau
    var w,h;
    w = $(me).width()-50; // 50 = largeur du bouton "ok"
    if (w<100) w=100;
    if (w>700) w=700;
    h = $(me).height();
    if (h<12) h=12;

    // charger le formulaire
    $.get(url_widgets_html+encodeURIComponent(this.className),
       function (c) {
         $(me)
         .html(c)
         .find('form')
           .ajaxForm(function(c){
             $(me)
             .html(c.responseText)
             .attr('orig_html','<>');
           })
           .find(".widget-active")
             .css('backgroundColor', 'yellow')
             .css('font', 'inherit') // pour safari
             .css({"width":w+'px',"height":h+'px'})
             .css({
               'fontSize': $(me).css('fontSize'),
               'fontFamily': $(me).css('fontFamily')
             })
             .each(function(){this.focus();})
             .keypress(function(e){
               if (e.keyCode == 27) {
                 $(me)
                 .html($(me).attr('orig_html'))
                 .attr('orig_html','<>');
               }
             })
           .end()
           .find(".cancel_widget")
             .click(function(){
               $(me)
               .html($(me).attr('orig_html')); //restore original html
               $(me).attr('orig_html', '<>');
               return false;
             })
           .end()
         .end()
         ;
       }
     );
  }

$(function() {
  $(".widget")
  .attr('orig_html', '<>')
  .click($.setupwidget);
  $("body")
  .click($.cancelwidgets);
});

