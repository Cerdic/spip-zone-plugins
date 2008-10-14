var jar_cache_files =    "jclic.jar,activities.jar,utilities.jar,jclicxml.jar,jmfhandlers.jar,intl.jar,qt60.jar,qt61.jar,soundspi.jar";
var jar_cache_versions = "  1.0.6.0,       1.0.2.8,      1.0.3.7,     1.0.1.7,        1.0.1.6, 1.0.0.3, 1.0.0.0, 1.0.0.1,     1.0.0.2";

var _info = navigator.userAgent;
var _ns = false;
var _mac = false;
var _ie = (_info.indexOf("MSIE") > 0 && _info.indexOf("Win") > 0 && _info.indexOf("Windows 3.1") < 0);
if(_info.indexOf("Opera")>=0){
   _ie=false;
   _ns=true;
}
else if(_ie==false){
  _ns = (navigator.appName.indexOf("Netscape") >= 0 && ((_info.indexOf("Win") > 0 && _info.indexOf("Win16") < 0 /*&& java.lang.System.getProperty("os.version").indexOf("3.5") < 0*/) || (_info.indexOf("Sun") > 0) || (_info.indexOf("Linux") > 0) || (_info.indexOf("AIX") > 0) || (_info.indexOf("OS/2") > 0)));
  _mac = _info.indexOf("Mac_PowerPC") > 0;
}

var jarBase='http://clic.xtec.net/dist/jclic';
function setJarBase(base){
   jarBase=base;
}

var useLanguage=false;
var language='';
var country='';
var variant='';
function setLanguage(l, c, v){
   if(l!=null){
     language=l.toString();
     if(c!=null) country=c.toString();
     if(v!=null) variant=v.toString();
     useLanguage=true;
   }
}

var useReporter=false;
var reporterClass='';
var reporterParams='';
function setReporter(rClass, rParams){
   if(rClass!=null){
     reporterClass=rClass.toString();
     if(rParams!=null) reporterParams=rParams.toString();
     useReporter=true;
   }
}

var useSkin=false;
var skinName='';
function setSkin(skName){
   if(skName!=null){
     skinName=skName.toString();
     useSkin=true;
   }
}

var useCookie=false;
var cookie='';
function setCookie(text){
   if(text!=null){
     cookie=text.toString();
     useCookie=true;
   }
}

var useExitUrl=false;
var exitUrl='';
function setExitUrl(text){
   if(text!=null){
     exitUrl=text.toString();
     useExitUrl=true;
   }
}

var useInfoUrlFrame=false;
var infoUrlFrame='';
function setInfoUrlFrame(text){
   if(text!=null){
     infoUrlFrame=text.toString();
     useInfoUrlFrame=true;
   }
}

var useSequence=false;
var sequence='';
function setSequence(text){
   if(text!=null){
     sequence=text.toString();
     useSequence=true;
   }
}

var useSystemSounds=false;
var systemSounds=false;
function setSystemSounds(value){
   if(value!=null){
     systemSounds=value.toString();
     useSystemSounds=true;
   }
}

var useCompressImages=false;
var compressImages=true;
function setCompressImages(value){
   if(value!=null){
      compressImages=value.toString();
      useCompressImages=true;
   }
}

var useTrace=false;
var trace=false;
function setTrace(value){
   if(value!=null){
      trace=value.toString();
      useTrace=true;
   }
}

function writePlugin(project, width, height, rWidth, rHeight){
   var w=width.toString();
   var h=height.toString();
   var nsw=w;
   var nsh=h;
   if(rWidth!=null) w=rWidth.toString();
   if(rHeight!=null) h=rHeight.toString();

   if (_ie == true){
      document.writeln(
        '<OBJECT classid="clsid:8AD9C840-044E-11D1-B3E9-00805F499D93"' +
        ' WIDTH="' +w+ '" HEIGHT="' +h+ '">');
      document.writeln('<PARAM NAME=CODE VALUE="JClicApplet">');
      document.writeln('<PARAM NAME=CODEBASE VALUE="' +jarBase+ '">');
   	  document.writeln('<PARAM NAME=ARCHIVE VALUE="jclicapplet.jar">');
      writeCacheInfo(true);
      document.writeln('<PARAM NAME="type" VALUE="application/x-java-applet;jpi-version=1.3.1">');
      document.writeln('<PARAM NAME="scriptable" VALUE="false">');
      writeParams(project, true);
      writeDownloadPageInfo();
      document.writeln('</OBJECT>');
   }
   else if (_ns == true){
      document.write(
        '<EMBED type="application/x-java-applet;version=1.3"'+
        ' CODE="JClicApplet" CODEBASE="' +jarBase+ '" ARCHIVE="jclicapplet.jar"'+
        ' WIDTH="' +nsw+ '" HEIGHT="' +nsh +'" ');
      writeCacheInfo(false);
      writeParams(project, false);
      document.writeln(
        ' scriptable=false'+
        ' pluginspage="http://www.java.com/">');
      document.writeln('<NOEMBED>');
      writeDownloadPageInfo();
      document.writeln('</NOEMBED>');     
   }
   else{
     document.write('<APPLET CODE="JClicApplet" CODEBASE="' +jarBase+ '"');
     document.write(' ARCHIVE="jclicapplet.jar,'+ jar_cache_files +'"');
     document.writeln(' WIDTH="' +nsw+ '" HEIGHT="' +nsh+ '">');
     document.writeln('<PARAM NAME="type" VALUE="application/x-java-applet;version=1.3">');
     document.writeln('<PARAM NAME="scriptable" VALUE="false">');
     writeParams(project, true);
     document.writeln('</APPLET>');
  }
}

function writeCacheInfo(p){
  if(p){
    document.writeln('<PARAM NAME="cache_option" VALUE ="Plugin">');
    document.writeln('<PARAM NAME="cache_archive" VALUE ="' +jar_cache_files+ '">');
    document.writeln('<PARAM NAME="cache_version" VALUE ="' +jar_cache_versions+ '">');
  }else{
    document.write(' cache_option="Plugin"');
    document.write(' cache_archive="' +jar_cache_files+ '"');
    document.write(' cache_version="' +jar_cache_versions+ '"');
  }
}

function writeDownloadPageInfo(){
  var pluginBase="http://clic.xtec.net/";
  var pluginCat=pluginBase+"ca/jclic/instjava.htm";
  var pluginEsp=pluginBase+"es/jclic/instjava.htm";
  var pluginEng=pluginBase+"en/jclic/instjava.htm";
  document.writeln(
    '<SPAN STYLE="background-color: #F5DEB3; color: Black; display: block; padding: 10; font-family: Verdana,Arial; border-style: solid; border-width: thin; font-size: 12px; text-align: center; font-weight: bold;">'+
    'Per utilitzar aquesta aplicaci&oacute; cal instal&middot;lar un plug-in Java&#153; actualitzat<BR><A HREF="'+pluginCat+'" TARGET="_blank">Feu clic aqu&iacute; per descarregar-lo</A><BR>&nbsp;<BR>'+
    'Para utilizar esta aplicaci&oacute;n es necesario un plug-in Java&#153; actualizado<BR><A HREF="'+pluginEsp+'" TARGET="_blank">Haga clic aqu&iacute; para descargarlo</A><BR>&nbsp;<BR>'+
    'In order to run this program you need an updated Java&#153; plug-in<BR><A HREF="'+pluginEng+'" TARGET="_blank">Click here to download it</A><BR>'+
    '</SPAN>');
}

function writeParams(project, p){

  if(p) document.writeln('<PARAM NAME="activityPack" VALUE ="' +project+ '">');
  else document.write(' activityPack="' +project+ '"');

  if(useSequence){
    if(p) document.writeln('<PARAM NAME="sequence" VALUE="' +sequence+ '">');
    else document.write(' sequence="' +sequence+ '" ');
  }

  if(useLanguage){
    if(p){
      document.writeln('<PARAM NAME="language" VALUE="' +language+ '">');
      document.writeln('<PARAM NAME="country" VALUE="' +country+ '">');
      document.writeln('<PARAM NAME="variant" VALUE="' +variant+ '">');
    }
    else document.write(' language="' +language+ '" country="' +country+ '" variant="' +variant+ '" ');
  }

  if(useSkin){
    if(p) document.writeln('<PARAM NAME="skin" VALUE="' +skinName+ '">');
    else document.write(' skin="' +skinName+ '" ');
  }

  if(useExitUrl){
    if(p) document.writeln('<PARAM NAME="exitUrl" VALUE="' +exitUrl+ '">');
    else document.write(' exitUrl="' +exitUrl+ '" ');
  }

  if(useInfoUrlFrame){
    if(p) document.writeln('<PARAM NAME="infoUrlFrame" VALUE="' +infoUrlFrame+ '">');
    else document.write(' infoUrlFrame="' +infoUrlFrame+ '" ');
  }

  if(useReporter){
    if(p){
      document.writeln('<PARAM NAME="reporter" VALUE="' +reporterClass+ '">');
      document.writeln('<PARAM NAME="reporterParams" VALUE="' +reporterParams+ '">');
    }
    else document.write(' reporter="' +reporterClass+ '" reporterParams="' +reporterParams+ '" ');
  }

  if(useCookie){
    if(p) document.writeln('<PARAM NAME="cookie" VALUE="' +cookie+ '">');
    else document.write(' cookie="' +cookie+ '" ');
  }

  if(useSystemSounds){
    if(p) document.writeln('<PARAM NAME="systemSounds" VALUE="' +systemSounds+ '">');
    else document.write(' systemSounds="' +systemSounds+ '" ');
  }

  if(useCompressImages){
    if(p) document.writeln('<PARAM NAME="compressImages" VALUE="' +compressImages+ '">');
    else document.write(' compressImages="' +compressImages+ '" ');
  }

  if(useTrace){
    if(p) document.writeln('<PARAM NAME="trace" VALUE="' +trace+ '">');
    else document.write(' trace="' +trace+ '" ');
  }

}

function writeTable(w, h, nsw, nsh, s){
	document.write('<TABLE '+ s);
    if(_ie == true){
	  if(w!=null) document.write(' WIDTH='+ w);
	  if(h!=null) document.write(' HEIGHT='+ h);
	}
	else{
	  if(nsw!=null) document.write(' WIDTH='+ nsw);
	  if(nsh!=null) document.write(' HEIGHT='+ nsh);
	}
	document.writeln('>');
}
