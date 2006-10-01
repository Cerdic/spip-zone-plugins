url_widgets_html = 'spip.php?action=widgets_html&class=';

setupwidget = function(){
    var me = this;
    $.get(url_widgets_html+encodeURIComponent(this.className),
       function (c) {
         $(me)
         .unclick()
         .html(c)
         .find('form').ajaxForm(function(c){
           $(me)
           .html(c.responseText)
           .click(setupwidget); // recursif
         }).end();
       }
     );
  }

$(function() {
  $(".widget")
  .click(setupwidget);
});

