/**
 * SEhighlight plugin for jQuery
 * 
 * Thanks to Scott Yang <http://scott.yang.id.au/>
 * for the original idea and some code
 *    
 * @author Renato Formato <renatoformato@virgilio.it> 
 *  
 * @version 0.2
 */

(function($){
  jQuery.fn.SEhighlight = function(options) {
    var ref = options.debug_referrer ? options.debug_referrer : document.referrer;
    if(!ref) return this;
    
    SEhighlight.options = $.extend({exact:true,style_name:'hilite',style_name_suffix:true},options);
    
    if(options.engines) SEhighlight.engines.unshift(options.engines);  
    var q = SEhighlight.decodeURL(ref,SEhighlight.engines);
    if(q) {
      SEhighlight.buildReplaceTools(q);
      return this.each(function(){
        SEhighlight.hiliteElement(this, q); 
      })
    } else return this;
  }    

  var SEhighlight = {
    options: {},
    regex: [],
    engines: [
    [/^http:\/\/(www\.)?google\./i, /q=([^&]+)/i],                   // Google
    [/^http:\/\/(www\.)?search\.yahoo\./i, /p=([^&]+)/i],                     // Yahoo
    [/^http:\/\/(www\.)?search\.msn\./i, /q=([^&]+)/i],                       // MSN
    [/^http:\/\/(www\.)?search\.live\./i, /query=([^&]+)/i],                  // MSN Live
    [/^http:\/\/(www\.)?search\.aol\./i, /userQuery=([^&]+)/i],               // AOL
    [/^http:\/\/(www\.)?ask\.com/i, /q=([^&]+)/i],                            // Ask.com
    [/^http:\/\/(www\.)?altavista\./i, /q=([^&]+)/i],                         // AltaVista
    [/^http:\/\/(www\.)?feedster\./i, /q=([^&]+)/i],                          // Feedster
    [/^http:\/\/(www\.)?search\.lycos\./i, /q=([^&]+)/i],                     // Lycos
    [/^http:\/\/(www\.)?alltheweb\./i, /q=([^&]+)/i],                         // AllTheWeb
    [/^http:\/\/(www\.)?technorati\.com\/search\/([^\?\/]+)/i, 1],   // Technorati
    ],
    subs: {},
    decodeURL: function(URL,reg) {
      URL = decodeURIComponent(URL);
      var query = null;
      $.each(reg,function(i,n){
        if(n[0].test(URL)) {
          var match = URL.match(n[1]);
          if(match) {
            query = match[1];
            return false;
          }
        }
      })
      
      if (query) {
      query = query.replace(/(\'|")/, '\$1');
      query = query.split(/[\s,\+\.]+/);
      }
      
      return query;
    },
		regexAccent : [
      [/[\xC0-\xC5]/ig,'a'],
      [/[\xD2-\xD6\xD8]/ig,'o'],
      [/[\xC8-\xCB]/ig,'e'],
      [/\xC7/ig,'c'],
      [/[\xCC-\xCF]/ig,'i'],
      [/[\xD9-\xDC]/ig,'u'],
      [/\xFF/ig,'y'],
      [/\xD1/ig,'n']
    ],
    matchAccent : /[\xC0-\xC5\xC7-\xCF\xD1-\xD6\xD8-\xDC\xFF]/ig,  
		replaceAccent: function(q) {
      if(SEhighlight.matchAccent.test(q)) {
        $.each(SEhighlight.regexAccent,function(i,n){
          q = q.replace(n[0],n[1]);
        });
      }
      return q;
    },
    buildReplaceTools : function(query) {
        re = new Array();
        for (var i = 0, l=query.length; i < l; i ++) {
            var q = query[i] = SEhighlight.replaceAccent(query[i].toLowerCase());
            re.push(SEhighlight.options.exact?'\\b'+q+'\\b':q);
        }
    
        SEhighlight.regex = new RegExp(re.join("|"), "gi");
        
        for (var i = 0, l = query.length; i < l; i ++) {
            SEhighlight.subs[query[i]] = SEhighlight.options.style_name+
              (SEhighlight.options.style_name_suffix?i+1:''); 
        }        
    },
    nosearch: /s(?:cript|tyle)|textarea/,
    hiliteElement: function(el, query) {
        if(el==document) el = $("body")[0];
        for(var i=0,l=el.childNodes.length;i<l;i++) {
          var item = el.childNodes[i];
          if ( item.nodeType != 8 ) {//comment node
  				  //text node
            if(item.nodeType==3) {
              var text = item.data, textNoAcc = SEhighlight.replaceAccent(item.data);
              var newtext="",match,index=0;
              RegExp.lastIndex = 0;
              while(match = SEhighlight.regex.exec(textNoAcc)) {
                newtext += text.substr(index,match.index-index)+'<span class="'+
                SEhighlight.subs[match[0].toLowerCase()]+'">'+text.substr(match.index,match[0].length)+"</span>";
                index = match.index+match[0].length;
              }
              if(newtext) {
                //add ther last part of the text
                newtext += text.substring(index);
                var repl = $.merge([],$("<span>"+newtext+"</span>")[0].childNodes);
                l += repl.length-1;
                i += repl.length-1;
                $(item).before(repl).remove();
              }                
            } else {
              if(item.nodeType==1 && item.nodeName.toLowerCase().search(SEhighlight.nosearch)==-1)
                SEhighlight.hiliteElement(item,query);
            }	
          }
        }
        
        return;
    }
    
  };
})(jQuery)
