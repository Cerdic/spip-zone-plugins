/* jDemo - a jQuery plugin to make great online demos
 * version 0.1 (8/6/2007)
 * 
 * Copyright (c) 2007 - Renato Formato <renatoformato@virgilio.it>
 *
 * Dual licensed under the MIT (MIT-LICENSE.txt)
 * and GPL (GPL-LICENSE.txt) licenses.
 */ 

(function($) {
  
  $.startDemo = function(url,name,step) {
    $.jDemo.loadDemo(url,name,step);
  };
  
  $.jDemo = {
    xmlData: null,
    actionsToRevert: [],
    init: function(url,name,step) {
      $.get(url,function(data){
        $.jDemo.xmlData = {data:data,steps:{}};
        $.jDemo.loadDemo(url,name,step);
      });
    },
    loadDemo: function(url,name,step) {
      if(!$.jDemo.xmlData) 
        return $.jDemo.init(url,name);
      if(!$.jDemo.xmlData.steps[name]) {
        var $demo = $("demo[@name="+name+"]",$.jDemo.xmlData.data);
        if(!$demo.size()) 
          return alert("Demo non trovata");
        $.jDemo.xmlData.steps[name] = $('step',$demo);
      }    
      $steps = $.jDemo.xmlData.steps[name]; 
      
      if(!step) step = 1;
      step--;
      $step = $steps.eq(step);
      if(!$step.size())
        return alert("Step non trovato");
      var title = $('title',$step).text();
      var content = $('content',$step).text();
      var res = '<div id="demo_spip" class="jqmWindow">'+
                (title?'<h3>'+title+'</h3>':'')+
                (content?'<div class="jDemo_content">'+content+'</div>':'')+
                '<div class="jDemo_footer">';
      
      if(step!=0) 
        res += ' <a rel="'+name+'&'+step+'" href="#" id="jDemo_prev">Precedente</a>';
  
      if(step!=$steps.size()-1) 
        res += ' <a rel="'+name+'&'+(step+2)+'" href="#" id="jDemo_next">Successivo</a>';
  
      res += ' <a href="#" class="jqmClose">Chiudi</a></div></div>';
      $('body').append(res);
      $('#jDemo_prev').click($.jDemo.navigate);
      $('#jDemo_next').click($.jDemo.navigate);
      $('#demo_spip').jqm({overlay:0,onHide:function(hash){hash.w.hide();$.jDemo.revertActions();}}).jqmShow();
      $.jDemo.doActions($step);
    },
    doActions: function($step) {
      var $actions = $('actions>',$step);
      $actions.each(function(){
        $el = $(this);
        switch(this.tagName) {
          case 'highlight':
            var $target = $($el.attr('selector'));
            var curr_color = $target.css('background-color');
            var f = function() {$target.css('background-color',curr_color)}
            $.jDemo.actionsToRevert.push(f);
            $target.css('background-color',$el.attr('color') || 'yellow');
            break;
        }
      });
    },
    revertActions : function() {
      var action;
      while(action = $.jDemo.actionsToRevert.pop()) {
        action();
      }      
    },
    navigate: function() {
      $('#demo_spip').jqmHide().remove();
      $.jDemo.revertActions();
      var data = this.rel.split(/&/);
      $.jDemo.loadDemo('',data[0],data[1]);
      return false;    
    }
  }
  
})(jQuery)
