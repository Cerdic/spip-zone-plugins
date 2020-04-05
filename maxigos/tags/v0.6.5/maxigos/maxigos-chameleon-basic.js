// maxiGos v6.64 Copyright 1998-2017 Francois Mizessyn, BSD license (see license.txt)
if (typeof mxG=='undefined') mxG={};
if (!mxG.V){
String.prototype.c2n=function(k){var n=this.charCodeAt(k);return n-((n<97)?38:96);};
String.prototype.ucFirst=function(){return this.charAt(0).toUpperCase()+this.slice(1);}
String.prototype.lcFirst=function(){return this.charAt(0).toLowerCase()+this.slice(1);}
mxG.D=[];
mxG.K=0;
mxG.S=[];
mxG.V="6.64";
if (typeof mxG.Z=='undefined') mxG.Z=[];
if (!mxG.Z.fr) mxG.Z.fr=[];
if (!mxG.Z.en) mxG.Z.en=[];
mxG.IsArray=function(a) {return a.constructor===Array;};
mxG.GetStyle=function(e,p){return window.getComputedStyle?window.getComputedStyle(e,null)[p]:"";};
mxG.GetPxStyle=function(e,p){var r=parseFloat(mxG.GetStyle(e,p));return isNaN(r)?0:r;};
mxG.GetPxrStyle=function(e,p){return Math.round(mxG.GetPxStyle(e,p))};
mxG.GetContentWidth=function(e){return e.clientWidth-mxG.GetPxStyle(e,"paddingLeft")-mxG.GetPxStyle(e,"paddingRight");};
mxG.GetContentHeight=function(e){return e.clientHeight-mxG.GetPxStyle(e,"paddingTop")-mxG.GetPxStyle(e,"paddingBottom");};
mxG.GetDir=function()
{
var s=document.getElementsByTagName('script'),p=s[s.length-1].src.split('?')[0];
return p.split("/").slice(0,-1).join("/")+"/";
};
mxG.AddCss=function(s)
{
var k,km=mxG.S.length,e;
for (k=0;k<km;k++) if (s==mxG.S[k]) return;
mxG.S.push(s);
e=document.createElement('link');
e.setAttribute('rel','stylesheet');
e.setAttribute('type','text/css');
e.setAttribute('href',s);
document.getElementsByTagName('head')[0].appendChild(e);
};
mxG.AddCssRule=function(css)
{
var s;
document.getElementsByTagName('head')[0].appendChild(document.createElement("style"));
s=document.styleSheets[document.styleSheets.length-1];
s.insertRule(css,0);
};
mxG.Color2Rgba=function(c)
{
var cn,cx;
cn=document.createElement("canvas");
cn.width=1;
cn.height=1;
cx=cn.getContext("2d");
cx.fillStyle=c;
cx.fillRect(0,0,1,1);
return cx.getImageData(0,0,1,1).data;
};
mxG.GetMClick=function(ev)
{
var box=this.getBoundingClientRect();
return {x:ev.clientX-box.left,y:ev.clientY-box.top};
};
mxG.GetKCode=function(ev)
{
var c;
if (!ev) ev=window.event;
if (ev.altKey||ev.ctrlKey||ev.metaKey) return 0;
c=ev.keyCode;
if (ev.charCode&&(c==0)) c=ev.charCode;
return c;
};
mxG.CreateUnselectable=function()
{
if (!mxG.Unselectable)
{
var s=document.createElement('style'),c='',k,a=['-moz-','-webkit-','-ms-',''];
for (k=0;k<4;k++) c+=(a[k]+'user-select:none;');
s.type='text/css';
s.innerHTML='.mxUnselectable {'+c+'}';
document.getElementsByTagName('head')[0].appendChild(s);
mxG.Unselectable=1;
}
};
mxG.CanCn=function(){return !!document.createElement('canvas').getContext;};
mxG.CanToDataURL=function()
{
var c=document.createElement("canvas"),d=c.toDataURL("image/png");
return (d.indexOf("data:image/png")==0);
};
mxG.CanOpen=function()
{var r;return !(typeof FileReader=='undefined')&&(r=new FileReader())&&(r.readAsText);};
mxG.IsMacSafari=(function()
{
var u=navigator.userAgent.toLowerCase();
return (u.indexOf('safari')!=-1)&&(u.indexOf('macintosh')!=-1)&&!(u.indexOf('chrome')>-1);
})();
mxG.IsAndroid=(navigator.userAgent.toLowerCase().indexOf("android")>-1);
mxG.IsIOS=(navigator.userAgent.match(/(iPad|iPhone|iPod)/g)?1:0);
mxG.IsWebkit=('WebkitAppearance' in document.documentElement.style);
mxG.IsFirefox=(navigator.userAgent.toLowerCase().indexOf('firefox')>-1);
mxG.hasVerticalScrollBar=function()
{
var w=window,d=w.document,c=d.compatMode;
r=c&&/CSS/.test(c)?d.documentElement:d.body;
if (typeof w.innerWidth=='number') return w.innerWidth>r.clientWidth;
else return r.scrollWidth>r.clientWidth;
};
mxG.verticalScrollBarWidth=function()
{
var w=window,d=w.document,b=d.body,r=0,t,s;
if (b)
{
t=d.createElement('div');
s='position:absolute;overflow:scroll;top:-100px;left:-100px;width:100px;height:100px;';
t.style.cssText=s;
b.insertBefore(t,b.firstChild);
r=t.offsetWidth-t.clientWidth;
b.removeChild(t);
}
return r;
};
mxG.fileExist=function(f)
{
var xhr=new XMLHttpRequest();
xhr.z=0;
xhr.onreadystatechange=function(){if ((this.readyState==4)&&(this.status==200)) xhr.z=1;};
xhr.open("GET",f,false);
xhr.send(null);
return xhr.z;
};
}
if (!mxG.R){
mxG.R=function()
{
this.act=[""]; 
this.nat=["E"]; 
this.x=[0]; 
this.y=[0]; 
this.o=[0]; 
};
mxG.R.prototype.inGoban=function(x,y)
{
return (x>=1)&&(y>=1)&&(x<=this.DX)&&(y<=this.DY);
};
mxG.R.prototype.init=function(DX,DY)
{
var i,j;
this.play=0; 
this.setup=0; 
this.DX=DX; 
this.DY=DY; 
this.ban=[]; 
for (i=1;i<=this.DX;i++) 
{
this.ban[i]=[];
for (j=1;j<=this.DY;j++) this.ban[i][j]=0;
}
this.prisoners={B:[0],W:[0]}; 
};
mxG.R.prototype.lib=function(nat,x,y)
{
var k,km;
if (!this.inGoban(x,y)) return 0;
if (this.nat[this.ban[x][y]]=="E") return 1;
if (this.nat[this.ban[x][y]]!=nat) return 0;
km=this.s.length;
for (k=0;k<km;k++) if ((this.s[k].x==x)&&(this.s[k].y==y)) return 0;
this.s[km]={x:x,y:y};
if (this.lib(nat,x,y-1)||this.lib(nat,x+1,y)||this.lib(nat,x,y+1)||this.lib(nat,x-1,y)) return 1;
return 0;
};
mxG.R.prototype.capture=function(nat,x,y)
{
this.s=[];
if (this.lib(nat,x,y)) return 0;
var numOfPrisoner=this.s.length,pt;
while (this.s.length)
{
pt=this.s.pop();
this.o[this.ban[pt.x][pt.y]]=this.play;
this.ban[pt.x][pt.y]=0;
}
return numOfPrisoner;
};
mxG.R.prototype.place=function(nat,x,y)
{
this.play++;
var act=((nat.length>1)?"A":""),pNat=nat.substr(nat.length-1,1),oNat=((pNat=="B")?"W":((pNat=="W")?"B":"E")),prisoners,m=this.play;
this.act[m]=act;
this.nat[m]=pNat;
this.prisoners.B[m]=this.prisoners.B[m-1];
this.prisoners.W[m]=this.prisoners.W[m-1];
this.o[m]=0;
if (this.inGoban(x,y))
{
this.x[m]=x;
this.y[m]=y;
if (act!="A") 
{
this.ban[x][y]=m;
prisoners=this.capture(oNat,x-1,y);
prisoners+=this.capture(oNat,x+1,y);
prisoners+=this.capture(oNat,x,y-1);
prisoners+=this.capture(oNat,x,y+1);
if (!prisoners)
{
prisoners=this.capture(pNat,x,y); 
this.prisoners[oNat][m]+=prisoners;
}
else this.prisoners[pNat][m]+=prisoners;
}
else 
{
this.setup=m;
this.ban[x][y]=(pNat!="E"?m:0);
}
}
else
{
this.x[m]=0;
this.y[m]=0;
}
};
mxG.R.prototype.back=function(play)
{
this.init(this.DX,this.DY);
for (var k=1;k<=play;k++) this.place(this.act[k]+this.nat[k],this.x[k],this.y[k]);
};
mxG.R.prototype.isOccupied=function(x,y)
{
return this.nat[this.ban[x][y]]!="E";
};
mxG.R.prototype.isOnlyOne=function(k,nat)
{
var n=1,x=this.x[k],y=this.y[k];
if ((x>1)&&(this.nat[this.ban[x-1][y]]==nat)) n++;
if ((y>1)&&(this.nat[this.ban[x][y-1]]==nat)) n++;
if ((x<this.DX)&&(this.nat[this.ban[x+1][y]]==nat)) n++;
if ((y<this.DY)&&(this.nat[this.ban[x][y+1]]==nat)) n++;
return n==1;
};
mxG.R.prototype.hasOnlyOneLib=function(k)
{
var n=0,x=this.x[k],y=this.y[k];
if ((x>1)&&(this.nat[this.ban[x-1][y]]=="E")) n++;
if ((y>1)&&(this.nat[this.ban[x][y-1]]=="E")) n++;
if ((x<this.DX)&&(this.nat[this.ban[x+1][y]]=="E")) n++;
if ((y<this.DY)&&(this.nat[this.ban[x][y+1]]=="E")) n++;
return n==1;
};
mxG.R.prototype.captureOnlyOnePrisoner=function(k,nat)
{
return (this.prisoners[nat][k]-this.prisoners[nat][k-1])==1;
};
mxG.R.prototype.isKo=function(nat,x,y)
{
var m=this.play;
if (m<4) return 0;
var pNat=nat.substr(nat.length-1,1),oNat=((pNat=="B")?"W":((pNat=="W")?"B":"E")),
nNat=this.nat[m-1],mxNat=this.nat[m],
xpred=this.x[m],ypred=this.y[m];
return (((xpred==(x-1))&&(ypred==y))||((xpred==x)&&(ypred==(y-1)))||((xpred==(x+1))&&(ypred==y))||((xpred==x)&&(ypred==(y+1))))
&&this.isOnlyOne(m,oNat)
&&this.hasOnlyOneLib(m)
&&this.captureOnlyOnePrisoner(m,oNat)
&&(pNat==nNat)
&&(oNat==mxNat);
};
mxG.R.prototype.canCapture=function(nat,x,y)
{
this.s=[];
if (this.lib(nat,x,y)) return 0;
return this.s.length;
};
mxG.R.prototype.isLib=function(x,y)
{
return this.inGoban(x,y)&&(this.nat[this.ban[x][y]]=="E");
};
mxG.R.prototype.isSuicide=function(nat,x,y)
{
var m=this.play,pNat=nat.substr(nat.length-1,1),oNat=((pNat=="B")?"W":((pNat=="W")?"B":"E")),
s=1,exNat=this.nat[m+1],exBan=this.ban[x][y];
this.nat[m+1]=pNat;
this.ban[x][y]=m+1;
if (this.isLib(x-1,y)||this.isLib(x,y-1)||this.isLib(x+1,y)||this.isLib(x,y+1)
||this.canCapture(oNat,x-1,y)||this.canCapture(oNat,x,y-1)
||this.canCapture(oNat,x+1,y)||this.canCapture(oNat,x,y+1)
||!this.canCapture(pNat,x,y)) s=0;
this.ban[x][y]=exBan;
this.nat[m+1]=exNat;
return s;
};
mxG.R.prototype.isValid=function(nat,x,y)
{
return (!x&&!y)||!(this.inGoban(x,y)&&(this.isOccupied(x,y)||this.isKo(nat,x,y)||this.isSuicide(nat,x,y)));
};
mxG.R.prototype.getBanNum=function(x,y){return this.ban[x][y];};
mxG.R.prototype.getBanNat=function(x,y){return this.nat[this.ban[x][y]];};
mxG.R.prototype.getNat=function(n){return this.nat[n];};
mxG.R.prototype.getX=function(n){return this.x[n];};
mxG.R.prototype.getY=function(n){return this.y[n];};
mxG.R.prototype.getAct=function(n){return this.act[n];};
mxG.R.prototype.getPrisoners=function(nat){return this.prisoners[nat][this.play];};
mxG.R.prototype.getO=function(n){return this.o[n];};
}
if (!mxG.N){
mxG.N=function(n,p,v)
{
this.Kid=[];
this.P={}; 
this.Dad=n;
this.Focus=0; 
if (n) {n.Kid.push(this);if (!n.Focus) n.Focus=1;}
if (p) this.P[p]=[v];
};
mxG.N.prototype.N=function(p,v){return new mxG.N(this,p,v);};
mxG.N.prototype.KidOnFocus=function(){return this.Focus?this.Kid[this.Focus-1]:0;};
mxG.N.prototype.TakeOff=function(p,k)
{
if (this.P[p])
{
if (k<0) this.P[p].splice(0,this.P[p].length);else this.P[p].splice(k,1);
if (!this.P[p].length) delete this.P[p];
}
};
mxG.N.prototype.Set=function(p,v)
{
if (typeof(v)=="object") this.P[p]=v;
else this.P[p]=[v];
};
mxG.N.prototype.Clone=function(dad)
{
var p,k,bN=new mxG.N(dad,null,null);
for (p in this.P) if (p.match(/^[A-Z]+$/)) bN.P[p]=this.P[p].concat();
for (k=0;k<this.Kid.length;k++) bN.Kid[k]=this.Kid[k].Clone(bN);
bN.Focus=this.Focus;
return bN;
};
}
if (!mxG.P){
mxG.P=function(gos,s)
{
this.rN=gos.rN;
this.coreOnly=gos.sgfLoadCoreOnly;
this.mainOnly=gos.sgfLoadMainOnly;
this.parseSgf(s);
if (!this.rN.Focus) this.parseSgf(gos.so);
if (gos.repareSgfOn) gos.repareSgf(gos.rN);
};
mxG.P.prototype.keep=function(a,p,v)
{
if (this.coreOnly&&((a=="N")||(a=="P")||(a=="V")))
return (p=="B")||(p=="W")||(p=="AB")||(p=="AW")||(p=="AE")
||(p=="FF")||(p=="CA")||(p=="GM")||(p=="SZ")||(p=="EV")||(p=="RO")||(p=="DT")||(p=="PC")
||(p=="PW")||(p=="WR")||(p=="WT")||(p=="PB")||(p=="BR")||(p=="BT")
||(p=="RU")||(p=="TM")||(p=="OT")||(p=="HA")||(p=="KM")||(p=="RE")||(p=="VW");
return 1;
};
mxG.P.prototype.out=function(a,p,v)
{
if (this.keep(a,p,v))
switch(a)
{
case "N":this.nN=this.nN.N(p,v);break;
case "P":this.nN.P[p]=[v];break;
case "V":this.nN.P[p].push(v);break;
case "v=":this.nN=this.v[this.v.length-1];break;
case "v+":this.v.push(this.nN);break;
case "v-":this.v.pop();break;
}
};
mxG.P.prototype.clean=function(s)
{
var r=s;
r=r.replace(/([^\\])((\\\\)*)\\((\n\r)|(\r\n)|\r|\n)/g,'$1$2');
r=r.replace(/^((\\\\)*)\\((\n\r)|(\r\n)|\r|\n)/g,'$1');
r=r.replace(/([^\\])((\\\\)*)\\/g,'$1$2');
r=r.replace(/^((\\\\)*)\\/g,'$1');
r=r.replace(/\\\\/g,'\\');
r=r.replace(/(\n\r)|(\r\n)|\r/g,"\n");
return r;
};
mxG.P.prototype.parseValue=function(p,K,c)
{
var v="",a;
K++; 
while ((K<this.l)&&((a=this.s.charAt(K))!=']'))
{
if (a=='\\') {v+=a;K++;a=this.s.charAt(K);}
if (K<this.l) v+=a;
K++;
}
v=this.clean(v);
if (p=="RE") {a=v.slice(0,1);if ((a=="V")||(a=="D")) v=a;}
if (this.nc) {this.nc=0;this.out("N",p,v);}
else if (!c) this.out("P",p,v);
else this.out("V",p,v);
K++; 
while (K<this.l)
{
a=this.s.charAt(K);
if ((a=='(')||(a==';')||(a==')')||((a>='A')&&(a<='Z'))||(a=='[')) break;else K++;
}
return K;
};
mxG.P.prototype.parseProperty=function(K)
{
var a,p="",c=0;
while ((K<this.l)&&((a=this.s.charAt(K))!='['))
{
if ((a>='A')&&(a<='Z')) p+=a;
K++;
}
while ((K<this.l)&&(this.s.charAt(K)=='[')) {K=this.parseValue(p,K,c);c++;}
return K;
};
mxG.P.prototype.parseNode=function(K)
{
var a;
this.nc=1;
while (K<this.l)
{
switch(a=this.s.charAt(K))
{
case '(':
case ';':
case ')':return K;
default : if ((a>='A')&&(a<='Z')) K=this.parseProperty(K);else K++;
}
}
return K;
};
mxG.P.prototype.parseVariation=function(K)
{
var a=(this.mainOnly?1:0);
if (this.nv) {if (this.v.length) this.out("v=","","");this.nv=0;} else this.out("v+","","");
while (K<this.l)
switch(this.s.charAt(K))
{
case '(':if (a) K++;else return K;break;
case ';':K++;K=this.parseNode(K);break;
case ')':K++;
if (this.nv) {if (this.v.length) this.out("v-","","");} else this.nv=1;
if (a) return this.l;break;
default :K++;
}
return K;
};
mxG.P.prototype.parseSgf=function(s)
{
var K=0;
this.rN.Kid=[];
this.rN.Focus=0;
this.nN=this.rN;
this.v=[];
this.nv=0; 
this.nc=0; 
this.s=s;
this.l=this.s.length;
while (K<this.l) if (this.s.charAt(K)=='(') {K++;K=this.parseVariation(K);} else K++;
while (this.v.length) this.out("v-","","");
};
}
if (!mxG.G){
mxG.Z.fr["Require HTML5!"]="Requiert HTML5 !";
mxG.Z.fr["Loading..."]="Chargement...";
mxG.G=function(k)
{
this.k=k; 
this.n="d"+k; 
this.g="mxG.D["+k+"]"; 
this.b=[]; 
this.c=[]; 
this.gBox=""; 
this.initMethod="last"; 
this.refreshTime=1000;
this.so="(;FF[4]CA[UTF-8]GM[1]SZ[19])";
this.gor=new mxG.R(); 
this.rN=new mxG.N(null,null,null);
this.rN.sgf=""; 
this.sgf=""; 
this.j=document.scripts[document.scripts.length-1]; 
this.t=this.j; 
this.h=""; 
};
mxG.G.prototype.debug=function(s,m){var e=this.getE("DebugDiv");if (e) {if (m) e.innerHTML+=s;else e.innerHTML=s;}};
mxG.G.prototype.write=function(s){if (this.t!=this.j) this.h+=s;else document.write(s);};
mxG.G.prototype.local=function(s){return (mxG.Z[this.l]&&(mxG.Z[this.l][s]!==undefined))?mxG.Z[this.l][s]:s;};
mxG.G.prototype.build=function(x,a)
{var f="build"+x;if (mxG.Z[this.l]&&mxG.Z[this.l][f]) return mxG.Z[this.l][f](a);if (this[f]) return this[f](a);return a+"";};
mxG.G.prototype.label=function(s,t)
{return this[t+"_"+this.l_]?this[t+"_"+this.l_]:this.local(s);};
mxG.G.prototype.hasC=function(x)
{
var b,bm,c,cm;
bm=this.b.length;
for (b=0;b<bm;b++)
{
cm=this.b[b].c.length;
for (c=0;c<cm;c++) if (this.b[b].c[c]==x) return 1;
}
return 0;
};
mxG.G.prototype.getE=function(id){return document.getElementById(this.n+id);};
mxG.G.prototype.getDW=function(e)
{
var r=0;
r+=mxG.GetPxStyle(e,"paddingLeft");
r+=mxG.GetPxStyle(e,"paddingRight");
r+=mxG.GetPxStyle(e,"borderLeftWidth");
r+=mxG.GetPxStyle(e,"borderRightWidth");
return r;
};
mxG.G.prototype.getDH=function(e)
{
var r=0;
r+=mxG.GetPxStyle(e,"paddingTop");
r+=mxG.GetPxStyle(e,"paddingBottom");
r+=mxG.GetPxStyle(e,"borderTopWidth");
r+=mxG.GetPxStyle(e,"borderBottomWidth");
return r;
};
mxG.G.prototype.adjust=function(c,a,b)
{
var x,z,p=a.toLowerCase(),i=a.substr(0,1),e=this.getE(c+"Div"),o;
if (b==1) b="Goban";
o=this.getE(b+"Div");
if (o)
{
if (this["adjust"+c+a+"ContentOnly"]) z=mxG.GetPxStyle(o,p)+this["getD"+i](o);
else z=mxG.GetPxStyle(o,p)+this["getD"+i](o)-this["getD"+i](e);
if (z!=this["last"+c+i]) {this["last"+c+i]=z;e.style[p]=z+"px";}
}
};
mxG.G.prototype.createGBox=function(b)
{
var e=document.createElement('div'),g;
if (!this[b+"Parent"]) this[b+"Parent"]="Goban";
g=this.getE(this[b+"Parent"]+"Div");
e.className="mx"+b+"Div";
e.id=this.n+b+"Div";
e.tabIndex="-1";
e.style.position="absolute";
e.style.left="0";
e.style.top="0";
e.style.right="0";
e.style.bottom="0";
e.style.display="none";
e.style.outline="0";
g.appendChild(e);
return e;
};
mxG.G.prototype.hideGBox=function(b)
{
if (b==this.gBox)
{
var e=this.getE(b+"Div"),p,c;
e.style.display="none";
this.gBox="";
p=this.getE(this[b+"Parent"]+"Div");
c=p.className;
p.className=c.replace(/\smxUnder/,"");
this.updateAll();
}
};
mxG.G.prototype.showGBox=function(b)
{
if (b==this.gBox) return;
var e=this.getE(b+"Div"),p,c;
if (this.inLoop) this.inLoop=0; 
if (this.gBox)
{
this.getE(this.gBox+"Div").style.display="none";
p=this.getE(this[this.gBox+"Parent"]+"Div");
c=p.className;
p.className=c.replace(/\smxUnder/,"");
}
e.style.display="block";
this.gBox=b;
p=this.getE(this[b+"Parent"]+"Div");
p.className+=" mxUnder";
this.updateAll();
};
mxG.G.prototype.enableBtn=function(b)
{
var b=this.getE(b+"Btn");
if (b) b.disabled=false;
};
mxG.G.prototype.disableBtn=function(b)
{
var b=this.getE(b+"Btn");
if (b) b.disabled=true;
};
mxG.G.prototype.addBtn=function(b)
{
if (!b.t&&this[b.n.lcFirst()+"Tip_"+this.l_]) b.t=this[b.n.lcFirst()+"Tip_"+this.l_];
this.write("<button class=\"mxBtn mx"+b.n+"Btn\""
+" "+(b.t?"title=\""+b.t+"\"":"")
+" autocomplete=\"off\""
+" id=\""+this.n+b.n+"Btn\""
+" onclick=\""+this.g+".do"+b.n+"();\">");
this.write("<div><span>"+(b.v?b.v:"")+"</span></div>");
this.write("</button>");
};
mxG.G.prototype.xy=function(x,y){return (x-1)*this.DY+y-1;};
mxG.G.prototype.xy2s=function(x,y)
{return (x&&y)?String.fromCharCode(x+((x>26)?38:96),y+((y>26)?38:96)):"";};
mxG.G.prototype.placeAX=function()
{
var v,z,k,km,s,x,y,x1,y1,x2,y2,AX=["AB","AW","AE"];
for (z=0;z<3;z++)
{
km=((v=this.cN.P[AX[z]])?v.length:0);
for (k=0;k<km;k++)
{
s=v[k];
if (s.length==2)
{
x=s.c2n(0);
y=s.c2n(1);
this.gor.place(AX[z],x,y);
}
else if (s.length==5)
{
x1=s.c2n(0);
y1=s.c2n(1);
x2=s.c2n(3);
y2=s.c2n(4);
for (x=x1;x<=x2;x++) for (y=y1;y<=y2;y++) this.gor.place(AX[z],x,y);
}
}
}
};
mxG.G.prototype.placeBW=function(nat)
{
var s=this.cN.P[nat][0],x=0,y=0;
if (s.length==2)
{
x=s.c2n(0);
y=s.c2n(1);
}
this.gor.place(nat,x,y);
};
mxG.G.prototype.repareNode=function(aN)
{
var k,ko,km,c;
if (aN.P.L)
{
km=aN.P.L.length;
if (km)
{
if (!aN.P.LB) aN.P.LB=[];
ko=aN.P.LB.length;
for (k=0;k<km;k++) aN.P.LB[k+ko]=aN.P.L[k]+":"+String.fromCharCode(97+k);
}
delete aN.P.L;
}
if (aN.P.M)
{
if (aN.P.M.length)
{
if (!aN.P.MA) aN.P.MA=aN.P.M;
else aN.P.MA=aN.P.MA.concat(aN.P.M);
}
delete aN.P.M;
}
};
mxG.G.prototype.placeNode=function()
{
if (this.cN.KidOnFocus())
{
this.cN=this.cN.KidOnFocus();
if (this.cN.P.L||this.cN.P.M) this.repareNode(this.cN);
if (this.cN.P.B) this.placeBW("B");
else if (this.cN.P.W) this.placeBW("W");
else if (this.cN.P.AB||this.cN.P.AW||this.cN.P.AE) this.placeAX();
}
};
mxG.G.prototype.changeFocus=function(aN)
{
var k,km,bN=aN;
while (bN!=this.rN)
{
if (bN.Dad.KidOnFocus()!=bN)
{
km=bN.Dad.Kid.length;
for (k=0;k<km;k++) if (bN.Dad.Kid[k]==bN) {bN.Dad.Focus=k+1;break;}
}
bN=bN.Dad;
}
};
mxG.G.prototype.backNode=function(aN)
{
this.changeFocus(aN);
this.cN=this.rN;
this.setSz();
this.gor.init(this.DX,this.DY);
while (this.cN!=aN) this.placeNode();
};
mxG.G.prototype.htmlProtect=function(s)
{
var r=s+'';
r=r.replace(/</g,'&lt;').replace(/>/g,'&gt;');
if (this.mayHaveExtraTags&&(this.htmlP===undefined))
{
r=r.replace(/&lt;p&gt;/gi,'');
r=r.replace(/&lt;\/p&gt;/gi,'<br><br>');
}
else if (this.htmlP) r=r.replace(/&lt;(\/?)p(\s+class="[a-zA-Z0-9_-]+")?&gt;/gi,'<$1p$2>');
if ((this.mayHaveExtraTags&&(this.htmlBr===undefined))||this.htmlBr) r=r.replace(/&lt;br\s?\/?&gt;/gi,'<br>');
if (this.htmlSpan) r=r.replace(/&lt;(\/?)span(\s+class="[a-zA-Z0-9_-]+")?&gt;/gi,'<$1span$2>');
if (this.htmlDiv) r=r.replace(/&lt;(\/?)div(\s+class="[a-zA-Z0-9_-]+")?&gt;/gi,'<$1div$2>');
return r;
};
mxG.G.prototype.getInfo=function(p)
{
var aN=this.cN;
if ((p=="MN")||(p=="PM")||(p=="FG")) {if (aN==this.rN) aN=aN.KidOnFocus();}
if ((p=="PM")||(p=="FG")) while ((aN!=this.rN)&&!aN.P[p]) aN=aN.Dad;
else {aN=this.rN;while (aN&&!aN.P[p]) aN=aN.KidOnFocus();}
if (aN&&aN.P[p]) return aN.P[p][0]+"";
if (p=="SZ") return "19";
if (p=="PM") return "1";
if ((p=="ST")||(p=="FG")) return "0";
return "";
};
mxG.G.prototype.getInfoS=function(p)
{
return this.htmlProtect(this.getInfo(p));
};
mxG.G.prototype.setSz=function()
{
var D=this.getInfo("SZ").split(":"),DX=this.DX,DY=this.DY;
this.DX=parseInt(D[0]);
this.DY=((D.length>1)?parseInt(D[1]):this.DX);
if ((DX!=this.DX)||(DY!=this.DY)) this.hasToDrawWholeGoban=1;
};
mxG.G.prototype.setVw=function()
{
var aN=this.cN,x,y,s,k,km,xl,yt,xr,yb;
if (aN==this.rN) aN=this.rN.KidOnFocus();
while ((aN!=this.rN)&&!aN.P.VW) aN=aN.Dad;
xl=this.xl;
yt=this.yt;
xr=this.xr;
yb=this.yb;
if (aN.P.VW)
{
this.xl=this.DX;
this.yt=this.DY;
this.xr=1;
this.yb=1;
km=aN.P.VW.length;
for (k=0;k<km;k++)
{
s=aN.P.VW[k];
if (s.length==5)
{
this.xl=Math.min(this.xl,s.c2n(0));
this.yt=Math.min(this.yt,s.c2n(1));
this.xr=Math.max(this.xr,s.c2n(3));
this.yb=Math.max(this.yb,s.c2n(4));
}
else if (s.length==2)
{
x=s.c2n(0);
y=s.c2n(1);
this.xl=Math.min(this.xl,x);
this.yt=Math.min(this.yt,y);
this.xr=Math.max(this.xl,x);
this.yb=Math.max(this.yt,y);
}
else
{
this.xl=1;
this.yt=1;
this.xr=this.DX;
this.yb=this.DY;
break;
}
}
}
else
{
this.xl=1;
this.yt=1;
this.xr=this.DX;
this.yb=this.DY;
}
this.xli=this.xl;
this.yti=this.yt;
this.xri=this.xr;
this.ybi=this.yb;
if ((xl!=this.xl)||(yt!=this.yt)||(xr!=this.xr)||(yb!=this.yb)) this.hasToDrawWholeGoban=1;
};
mxG.G.prototype.setPl=function()
{
var aN=this.rN;
this.uC="B";
while (aN.Focus)
{
aN=aN.Kid[0];
if (aN.P)
{
if (aN.P.PL)
{
this.uC=aN.P.PL;
break;
}
else if (aN.P.B||aN.P.W)
{
if (aN.P.B) this.uC="B";
else if (aN.P.W) this.uC="W";
break;
}
}
}
this.oC=((this.uC=="W")?"B":"W");
};
mxG.G.prototype.colorize=function(a,b) {return Math.floor(a+b*(255-a)/255);};
mxG.G.prototype.setImg=function(nat,d)
{
var cn,cx,im=new Image(),s,sz,c=(nat=="B")?"black":"white",cs;
im.canDraw=0;
im.onload=function(){if (this.src) this.canDraw=1;};
if (this.customStone)
{
if (d<9) sz=9;else if (d<31) sz=d;else sz=31;
s=c+(this.in3dOn?"3d":"2d")+sz;
if (this.customStone=="data:")
{
if (this[s]) {im.src=this[s];return im;}
s=c+"StoneData";
if (this[s]) {im.src=this[s];return im;}
}
else
{
im.src=this.path+this.customStone+s+".png";
return im;
}
}
cn=document.createElement("canvas");
cn.width=cn.height=d;
cx=cn.getContext("2d");
this.drawStone(cx,nat,d);
if (this.in3dOn)
{
cs=mxG.Color2Rgba((nat=="B")?this.blackStoneColor:this.whiteStoneColor);
if (((nat=="B")&&(cs[0]!=0||cs[1]!=0||cs[2]!=0))
||((nat=="W")&&(cs[0]!=255||cs[1]!=255||cs[2]!=255)))
{
var imgData,data,k,kmax;
imgData=cx.getImageData(0,0,d,d);
data=imgData.data;
kmax=data.length;
for (k=0;k<kmax;k+=4)
{
data[k]=this.colorize(data[k],cs[0]);
data[k+1]=this.colorize(data[k+1],cs[1]);
data[k+2]=this.colorize(data[k+2],cs[2]);
}
cx.putImageData(imgData,0,0);
}
}
im.src=cn.toDataURL("image/png");
return im;
};
mxG.G.prototype.setD=function()
{
var exD=(this.d?this.d:0),cn,fs,fso,wgbp,z,dx,x;
cn=this.gcn;
if (!exD&&this.gobanFs) cn.style.fontSize=this.gobanFs;
fso=mxG.GetPxStyle(cn,"fontSize");
if (this.fitParent&1)
{
if (this.configFitMax===undefined) this.configFitMax=this.fitMax?this.fitMax:0;
if (!this.configFitMax)
{
x=((this.configIndicesOn||this.indicesOn)?2:0);
if (this.maximizeGobanWidth) dx=Math.max(19,this.DX)+x;
else if (this.xri) dx=this.xri-this.xli+1;
else if (this.DX) dx=this.DX+x;
else dx=19+x;
this.fitMax=dx;
}
wgbp=mxG.GetContentWidth(this.gbp)-this.getDW(this.gb);
wgbp-=(this.getDW(this.gop)+this.getDW(this.go)+this.getDW(this.ig)+this.getDW(cn));
wgbp-=(this.fitDelta?this.fitDelta:0);
if (!mxG.hasVerticalScrollBar()) wgbp-=mxG.verticalScrollBarWidth();
fs=Math.max(3,Math.min(fso,Math.floor(wgbp/(this.fitMax*1.5))));
this.d=2*Math.floor(fs*3/4)+1;
z=(this.border===undefined)?this.d>>4:this.border;
if ((this.d*this.fitMax+z*2)>wgbp)
{
this.d-=2;
z=(this.border===undefined)?this.d>>4:this.border;
}
}
else
{
this.d=2*Math.floor(fso*3/4)+1;
z=(this.border===undefined)?this.d>>4:this.border;
}
if (this.d!=exD)
{
this.z=z;
this.d2=(this.stretchOn?Math.floor(this.d/10):0);
this.lw=(this.lineWidth?this.lineWidth:Math.floor(1+this.d/42));
this.img={B:this.setImg("B",this.d),W:this.setImg("W",this.d)};
this.imgSmall={B:this.setImg("B",1+this.d>>1),W:this.setImg("W",1+this.d>>1)};
if (this.hasC("Edit"))
this.imgBig={B:this.setImg("B",this.toolSize()-this.et*2),
W:this.setImg("W",this.toolSize()-this.et*2)};
}
};
mxG.G.prototype.setLayout=function()
{
var w,wsm,r,gb,sm,sb,se,b,bm;
bm=this.b.length;
for (b=0;b<bm;b++)
{
if (this["adjust"+this.b[b].n+"Width"]) this.adjust(this.b[b].n,"Width",this["adjust"+this.b[b].n+"Width"]);
if (this["adjust"+this.b[b].n+"Height"]) this.adjust(this.b[b].n,"Height",this["adjust"+this.b[b].n+"Height"]);
}
if (this.swapOn)
{
if (this.swapMain&&this.swapBeside)
{
if (this.swapRatio)
{
r=parseFloat(this.swapRatio+"");
sm=this.getE(this.swapMain+"Div");
sb=this.getE(this.swapBeside+"Div");
wsm=mxG.GetPxStyle(sm,"width");
w=wsm*(1+r);
gb=this.gb;
wgbp=mxG.GetContentWidth(this.gbp)-this.getDW(gb)-this.getDW(this.gop);
wgbp-=this.getDW(this.go)-this.getDW(this.ig)-this.getDW(this.gcn);
wgbp-=(this.fitDelta?this.fitDelta:0);
if (!mxG.hasVerticalScrollBar()) wgbp-=mxG.verticalScrollBarWidth();
if (this.swapExtend) se=this.getE(this.swapExtend+"Div");
if (w>wgbp)
{
gb.classList.remove("mxHorizontal");
gb.classList.add("mxVertical");
sm.style.display=sb.style.display="";
sm.style.verticalAlign=sb.style.verticalAlign="";
sb.style.height="";
sb.style.width=wsm+"px";
if (se) se.style.height="";
this.swapExtendElement=0;
}
else
{
gb.classList.remove("mxVertical");
gb.classList.add("mxHorizontal");
sm.style.display=sb.style.display="inline-block";
sm.style.verticalAlign=sb.style.verticalAlign="top";
sb.style.height="auto";
sb.style.width=(wsm*r-this.getDW(sb)-this.getDW(sm))+"px";
if (se) this.swapExtendElement=se;
}
}
}
}
};
mxG.G.prototype.adjustLayout=function()
{
var sm,sb,se,hsm,hsb,hse;
if (this.swapExtendElement)
{
se=this.swapExtendElement;
sb=this.getE(this.swapBeside+"Div");
sm=this.getE(this.swapMain+"Div");
hsm=mxG.GetPxStyle(sm,"height")+this.getDH(sm);
hsb=mxG.GetPxStyle(sb,"height")+this.getDH(sb);
se.style.height=mxG.GetPxStyle(se,"height")+hsm-hsb+"px";
}
};
mxG.G.prototype.initAll=function()
{
var c,s;
this.gb=this.getE("GlobalBoxDiv");
this.gbp=this.gb.parentNode;
this.go=this.getE("GobanDiv");
this.gop=this.go.parentNode;
this.ig=this.getE("InnerGobanDiv");
this.gcn=this.getE("GobanCanvas");
this.gcx=this.gcn.getContext("2d");
if (!this.rN.Focus) {this.mayHaveExtraTags=0;new mxG.P(this,this.so);}
this.cN=this.rN;
this.setSz();
this.gor.init(this.DX,this.DY);
this.setD();
for (c=0;c<this.m;c++) {s="init"+this.c[c];if (this[s]) this[s]();}
};
mxG.G.prototype.updateAll=function()
{
var c,s;
if (this.hasC("Loop")&&this.hasC("Lesson")) this.resetLoop();
if (this.hasC("Variations")) this.setMode();
this.setVw();
if (this.hasC("Diagram")) {this.setIndices();this.setNumbering();}
for (c=0;c<this.m;c++) {s="update"+this.c[c];if (this[s]) this[s]();}
};
mxG.G.prototype.createWait=function()
{
var cls,gi="Wait";
cls="mx"+gi+"Div";
cls+=(this.theme?" mx"+this.theme+gi+"Div":"");
cls+=(this.config?" mx"+this.config+gi+"Div":"");
cls+=" mxIn"+(this.in3dOn?"3d":"2d");
cls+=" mx"+this.l_.ucFirst();
this.write("<div class=\""+cls+"\" id=\""+this.n+gi+"Div\">"+this.local("Loading...")+"</div>");
};
mxG.G.prototype.stopWait=function()
{
var e=this.getE("WaitDiv");
if (e) e.style.display="none";
};
mxG.G.prototype.refreshAll=function()
{
var c,s;
this.setD();
this.setLayout();
for (c=0;c<this.m;c++) {s="refresh"+this.c[c];if (this[s]) this[s]();}
this.adjustLayout();
if (!this.onceDone&&!this.hasToDrawWholeGoban)
{
this.onceDone=1;
this.stopWait();
this.getE("GlobalBoxDiv").style.height="auto";
this.getE("GlobalBoxDiv").style.opacity="1";
if (mxG.ExecutionTime) mxG.ExecutionTime();
}
};
mxG.G.prototype.start=function()
{
var t=this.refreshTime,s=this.g+".refreshAll()";
this.initAll();
this.placeNode();
if (this.initMethod=="last") while (this.cN.KidOnFocus()) this.placeNode();
this.updateAll();
this.startDone=1;
setTimeout(s,t/10);
setTimeout(s,t/2);
setInterval(s,t);
if (mxG.ExecutionTime) mxG.ExecutionTime();
};
mxG.G.prototype.createBox=function(c)
{
var s="create"+c;
this.c.push(c);
if (this[s]) this[s]();
};
mxG.G.prototype.setA=function()
{
var i,j,im=this.t.attributes.length,jm,n,s,a,b,bs,k,km;
for (i=0;i<im;i++)
{
n=this.t.attributes.item(i).nodeName;
if (n.match(/^data-maxigos-/))
{
a=n.replace(/^data-maxigos-/,"").split("-");
s=a[0];
jm=a.length;
for (j=1;j<jm;j++) s+=a[j].ucFirst();
b=this.t.getAttribute(n);
this[s]=b.match(/^[0-9]+$/)?parseInt(b):b;
}
}
};
mxG.G.prototype.afterGetF=function()
{
if (!this.startDone) {setTimeout(this.g+".afterGetF()",25);return;}
this.mayHaveExtraTags=0;
new mxG.P(this,this.fromF);
if (this.hasC("Tree")) this.initTree();
this.backNode(this.rN.KidOnFocus());
if (this.initMethod=="last") while (this.cN.KidOnFocus()) this.placeNode();
this.updateAll();
this.refreshAll();
if (mxG.ExecutionTime) mxG.ExecutionTime();
};
mxG.G.prototype.getF=function(f,c)
{
var xhr=new XMLHttpRequest();
xhr.gos=this;
xhr.f=f;
xhr.c=c;
xhr.onreadystatechange=function()
{
var s,m,c;
if (this.readyState==4)
{
if (this.status!=200) return;
s=this.responseText;
if (!this.c&&this.overrideMimeType)
{
if (m=s.match(/CA\[([^\]]*)\]/)) c=m[1].toUpperCase();else c="ISO-8859-1";
if (c!="UTF-8")
{
this.gos.getF(this.f,c);
return;
}
}
this.gos.fromF=s;
this.gos.afterGetF();
}
};
xhr.open("GET",xhr.f,c?false:true); 
if (c&&xhr.overrideMimeType) xhr.overrideMimeType("text/plain;charset="+c);
xhr.send(null);
};
mxG.G.prototype.getS=function()
{
var e=this.t,s,fo,f;
this.mayHaveExtraTags=0;
if (this.sgf)
{
s=this.sgf;
if (s.indexOf("(")<0) f=s;
}
else if (((e==this.j)&&(e.getAttribute("src")))||(e!=this.j))
{
s=e.innerHTML;
if (this.htmlParenthesis) s=s.replace(/&#40;/g,'(').replace(/&#41;/g,')');
if (s.indexOf("(")<0) f=s.replace(/^\s+([^\s])/,"$1").replace(/([^\s])\s+$/,"$1");
else this.mayHaveExtraTags=1;
}
else s=this.so;
if (f)
{
fo=f.split("?")[0].split("/").reverse()[0];
if (fo.match(/\.sgf$/)||(this.sourceFilter&&f.match(new RegExp(this.sourceFilter))))
{
this.getF(f.replace("&amp;","&"),"");
return;
}
}
if (!this.rN.Focus) new mxG.P(this,s);
};
mxG.G.prototype.createAll=function()
{
var b,bm,c,cm,k=this.k,cls,gb="GlobalBox";
if (!mxG.CanCn()||!mxG.CanToDataURL())
{
this.write("<div class=\"mxErrorDiv\">"+this.local("Require HTML5!")+"</div>");
return;
}
this.setA();
if (!this.l) this.l="fr";
this.l_=this.l.replace("-","_"); 
this.createWait();
cls="mx"+gb+"Div";
cls+=(this.theme?" mx"+this.theme+gb+"Div":"");
cls+=(this.config?" mx"+this.config+gb+"Div":"");
cls+=" mxIn"+(this.in3dOn?"3d":"2d");
cls+=" mx"+this.l_.ucFirst();
this.write("<div style=\"opacity:0;height:0;\" class=\""+cls+"\" id=\""+this.n+gb+"Div\">");
this.write("<div id=\""+this.n+"DebugDiv\"></div>");
bm=this.b.length;
for (b=0;b<bm;b++)
{
this.write("<div id=\""+this.n+this.b[b].n+"Div\" class=\"mx"+this.b[b].n+"Div\">");
cm=this.b[b].c.length;
for (c=0;c<cm;c++) this.createBox(this.b[b].c[c]);
this.write("</div>");
}
this.write("</div>");
if (!this.rN.Focus) this.getS();
this.m=this.c.length;
if (this.j==this.t) 
window.addEventListener("load",function(){mxG.D[k].start();},false);
else 
{
this.t.innerHTML=this.h;
this.start();
}
};
}
if (typeof mxG.G.prototype.createGoban=='undefined'){
mxG.G.prototype.deplonkGoban=function()
{
this.go.style.visibility="visible";
};
mxG.G.prototype.plonk=function()
{
if (!this.silentFail)
{
this.go.style.visibility="hidden";
setTimeout(this.g+".deplonkGoban()",50);
}
};
mxG.G.prototype.getEmphasisColor=function(k)
{
if (k)
{
if (k&this.goodnessCode.Good) return this.goodColor?this.goodColor:0;
if (k&this.goodnessCode.Bad) return this.badColor?this.badColor:0;
if (k&this.goodnessCode.Even) return this.evenColor?this.evenColor:0;
if (k&this.goodnessCode.Warning) return this.warningColor?this.warningColor:0;
if (k&this.goodnessCode.Unclear) return this.unclearColor?this.unclearColor:0;
if (k&this.goodnessCode.OffPath) return this.offPathColor?this.offPathColor:0;
}
return this.neutralColor?this.neutralColor:0;
};
mxG.G.prototype.getC=function(ev)
{
var x,y,cn=this.gcn,c=cn.getMClick(ev);
c.x-=(this.z+mxG.GetPxStyle(cn,"borderLeftWidth")+mxG.GetPxStyle(cn,"paddingLeft"));
c.y-=(this.z+mxG.GetPxStyle(cn,"borderTopWidth")+mxG.GetPxStyle(cn,"paddingTop"));
x=Math.max(Math.min(Math.floor(c.x/this.d)+this.xli,this.xri),this.xli);
y=Math.max(Math.min(Math.floor(c.y/(this.d+this.d2))+this.yti,this.ybi),this.yti);
return {x:x,y:y}
};
mxG.G.prototype.whichMove=function(x,y)
{
var cN=this.cN,aN,s,a,b,km;
if (!(this.styleMode&3))
{
km=cN.Kid.length;
for (k=0;k<km;k++)
{
aN=cN.Kid[k];
if (aN.P.B) s=aN.P.B[0];
else if (aN.P.W) s=aN.P.W[0];
else s="";
if (s)
{
a=s.c2n(0);
b=s.c2n(1);
if ((a==x)&&(b==y)) return aN;
}
}
}
return 0;
};
mxG.G.prototype.isNextMove=function(x,y)
{
var aN,s,a,b;
if (!(this.styleMode&3))
{
aN=this.cN.KidOnFocus();
if (aN)
{
if (aN.P.B) s=aN.P.B[0];
else if (aN.P.W) s=aN.P.W[0];
else s="";
if (s)
{
a=s.c2n(0);
b=s.c2n(1);
if ((a==x)&&(b==y)) return aN;
}
}
}
return 0;
};
mxG.G.prototype.star=function(x,y)
{
var DX=this.DX,DY=this.DY,A=4,B=((DX+1)>>1),C=DX+1-A,D=((DY+1)>>1),E=DY+1-A;
if ((DX&1)&&(DY&1))
{
if ((DX>17)&&(DY>17)) return ((x==A)||(x==B)||(x==C))&&((y==A)||(y==D)||(y==E));
if ((DX>11)&&(DY>11)) return (((x==A)||(x==C))&&((y==A)||(y==E)))||((x==B)&&(y==D));
return (x==B)&&(y==D);
}
if ((DX>11)&&(DY>11)) return ((x==A)||(x==C))&&((y==A)||(y==E));
return false;
};
mxG.G.prototype.inView=function(x,y)
{
return (x>=this.xl)&&(y>=this.yt)&&(x<=this.xr)&&(y<=this.yb);
};
mxG.G.prototype.isCross=function(x,y)
{
return (this.inView(x,y)&&(this.vNat[this.xy(x,y)]=="E")&&((this.vStr[this.xy(x,y)]=="")||(this.vStr[this.xy(x,y)]=="_TB_")||(this.vStr[this.xy(x,y)]=="_TW_")));
};
mxG.G.prototype.drawStar=function(cx,a,b,r)
{
if (r>1)
{
var q=(this.starRatio?this.starRatio:0.2);
cx.fillStyle=this.starColor?this.starColor:this.lineColor;
cx.beginPath();
cx.arc(a+r,b+r,this.starRadius?this.starRadius:Math.max(1.5,r*q+0.5),0,Math.PI*2,false);
cx.fill();
}
};
mxG.G.prototype.drawStone=function(cx,nat,d)
{
var r=d/2,c1,c2;
cx.beginPath();
cx.arc(r,r,r-0.6*this.lw,0,Math.PI*2,false);
if (this.in3dOn)
{
var zx=0.8,zy=0.5,x1,y1,rG;
x1=zx*r;
y1=zy*r;
rG=cx.createRadialGradient(x1,y1,0.2*r,x1,y1,2*r);
rG.addColorStop(0,(nat=="B")?"#999":"#fff");
rG.addColorStop(0.3,(nat=="B")?"#333":"#ccc");
rG.addColorStop(1,"#000");
cx.fillStyle=rG;
cx.fill();
if (nat=="B")
{
rG=cx.createRadialGradient((zx>1?0.8:1.2)*r,(zy>1?0.8:1.2)*r,1,(zx>1?0.8:1.2)*r,(zy>1?0.8:1.2)*r,0.9*r);
rG.addColorStop(0,"rgba(0,0,0,0.8)");
rG.addColorStop(0.5,"rgba(0,0,0,0.6)");
rG.addColorStop(1,"rgba(0,0,0,0.1)");
cx.fillStyle=rG;
cx.fill();
}
}
else
{
if (nat=="B")
{
c1=this.blackStoneColor;
c2=this.blackStoneBorderColor?this.blackStoneBorderColor:"#000";
}
else
{
c1=this.whiteStoneColor;
c2=this.whiteStoneBorderColor?this.whiteStoneBorderColor:"#000";
}
cx.fillStyle=c1;
cx.fill();
cx.strokeStyle=c2;
cx.lineWidth=this.lw;
cx.stroke();
}
};
mxG.G.prototype.getFs=function(cx,d,fw)
{
var fs=0; 
do {cx.font=fw+" "+(fs++)+"px "+this.gobanFont;} while ((fs<99)&&(3*cx.measureText("9").width<d));
return fs;
};
mxG.G.prototype.getGobanTextH=function(fontSizeFace,d)
{
var width=d*10;
var height=d*2;
var canvas=document.createElement("canvas");
canvas.width=width;
canvas.height=height;
var cx=canvas.getContext("2d");
var text="0123456789";
cx.font=fontSizeFace;
cx.clearRect(0,0,width,height);
cx.fillText(text, 0, d);
var data=cx.getImageData(0,0,width,height).data;
var first=false,last=false,r=height,c=0;
while (!last&&r)
{
r--;
for (c=0;c<width;c++) if (data[r*width*4+c*4+3]) {last=r;break;}
}
while (r)
{
r--;
for (c=0;c<width;c++) if (data[r*width*4+c*4+3]) {first=r;break;}
if (first!=r) return [first-d/2,last-d/2];
}
return 0;
};
mxG.G.prototype.drawText=function(cx,x,y,d,s,op)
{
var r=d/2,sf,c=0,sc=0,fs,xo,yo,gth,fsf;
cx.save();
if (op&&op.c) c=op.c;
if (op&&op.sc) sc=op.sc;
if (c) cx.fillStyle=c;
if (sc) {cx.strokeStyle=sc;cx.lineWidth=3;}
else if (mxG.IsMacSafari&&(c=="#fff"))
{
sc=c;cx.strokeStyle=sc;cx.lineWidth=0.75;
}
if (op&&op.fw) fw=op.fw;
else fw="normal";
s+="";
cx.textBaseline="alphabetic"; 
cx.textAlign="center";
fs=this.getFs(cx,d,fw);
cx.font=fw+" "+fs+"px "+this.gobanFont;
fsf=fw+" "+fs+"px "+this.gobanFont;
gth=fsf+" "+d;
if (gth!=this.gth)
{
a=this.getGobanTextH(fsf,d);
this.yFontAdjust=(-a[0]-(a[1]-a[0])/2+d/2)/d;
this.gth=gth;
}
sf=(s.length>3)?0.5:(s.length>2)?0.7:(s.length>1)?0.9:1;
cx.scale(sf,1);
xo=(x+r)/sf;
yo=Math.floor(y+this.yFontAdjust*d+d/2)-0.5;
if (sc) cx.strokeText(s,xo,yo);
cx.fillText(s,xo,yo);
cx.restore();
};
mxG.G.prototype.drawMarkOnLast=function(cx,x,y,d,c)
{
var dm;
if (this.markOnLastType&&(this.markOnLastType=="framedCircle"))
{
dm=Math.floor(d/7);
cx.strokeStyle=c;
cx.lineWidth=(this.markLineWidth?this.markLineWidth:1)*this.d/23*this.lw;
this.drawCircle(cx,x+dm,y+dm,d-2*dm);
}
else
{
dm=Math.floor(d/3);
cx.fillStyle=this.markOnLastColor?this.markOnLastColor:c;
cx.fillRect(x+dm,y+dm,d-2*dm,d-2*dm);
}
};
mxG.G.prototype.drawVariationEmphasis=function(cx,a,b,d,x,y,m)
{
var aN,c,fw,sc;
aN=this.whichMove(x,y);
c=this.getEmphasisColor(aN?aN.Good:0);
c=(c?c:this.lineColor);
if (this.variationAsMarkOn||!this.hasC("Diagram"))
{
cx.lineWidth=2;
cx.strokeStyle=c;
cx.beginPath();
cx.arc(a+d/2,b+d/2,d/5,0,Math.PI*2,false);
cx.stroke();
if (this.isNextMove(x,y))
{
cx.fillStyle=c;
cx.beginPath();
cx.arc(a+d/2,b+d/2,d/10,0,Math.PI*2,false);
cx.fill();
}
}
else
{
if (this.variationOnFocusFontWeight&&this.isNextMove(x,y)) fw=this.variationOnFocusFontWeight;
else if (this.variationFontWeight) fw=this.variationFontWeight;
else fw="normal";
if (this.variationOnFocusStrokeColor&&this.isNextMove(x,y)) sc=this.variationOnFocusStrokeColor;
else if (this.variationStrokeColor) sc=this.variationStrokeColor;
else sc=0;
m=this.removeLabelDelimiters(m);
this.drawText(cx,a,b,d,m,{c:c,fw:fw,sc:sc});
}
};
mxG.G.prototype.drawStoneShadow=function(cx,a,b,d)
{
var e=d/10,de=d/20;
cx.fillStyle="rgba(0,0,0,0.25)";
cx.beginPath();
cx.arc(a+d/2+e,b+d/2+e,d/2-de,0,Math.PI*2,false);
cx.fill();
};
mxG.G.prototype.pointColor=function(x,y,nat,v,l,mtsc)
{
var c;
if (v&&this.variationOnFocusColor&&this.isNextMove(x,y)) c=this.variationOnFocusColor;
else if (v&&this.variationColor) c=this.variationColor;
else if ((l||mtsc)&&this.markAndLabelColor) c=this.markAndLabelColor;
else c=(nat=="B")?this.onBlackColor:(nat=="W")?this.onWhiteColor:((nat=="O")&&this.outsideColor)?this.outsideColor:this.lineColor;
return c;
};
mxG.G.prototype.drawTerritoryMark=function(cx,a,b,d,nat,m)
{
if ((nat=="B")||(nat=="W"))
{
cx.globalAlpha=0.5;
if (this.in3dOn&&this.stoneShadowOn) this.drawStoneShadow(cx,a,b,d);
cx.drawImage(this.img[nat],a,b,d,d);
cx.globalAlpha=1;
}
if (this.territoryMark=="MA")
{
cx.save();
cx.lineWidth=(this.markLineWidth?this.markLineWidth:1)*this.d/23*this.lw;
if (m=="_TW_") cx.strokeStyle=this.whiteTerritoryMarkColor?this.whiteTerritoryMarkColor:"#fff";
else cx.strokeStyle=this.blackTerritoryMarkColor?this.blackTerritoryMarkColor:"#000";
this.drawMark(cx,a,b,d);
cx.restore();
}
else cx.drawImage(this.imgSmall[(m=="_TW_")?"W":"B"],a+d/4,b+d/4,1+d>>1,1+d>>1);
};
mxG.G.prototype.drawPoint=function(cx,x,y,nat,m)
{
var d=this.d,r=d/2,z=this.z,d2=this.d2,d3=(d2&1?1:0),d4;
var a=(x-this.xli)*d+z,b=(y-this.yti)*(d+d2)+(d2>>1)+d3+z;
var dxl=0,dyt=0,dxr=0,dyb=0,v=0,l=0,mtsc=0,xo,yo,wo,ho,bk,c,fw,sbk,sbkw,sc;
var aN;
var m2;
cx.lineWidth=this.lw;
if (this.hasC("Diagram")) m=this.preTerritory(x,y,nat,m);
if (x==this.xl) dxl=z;
if (y==this.yt) dyt=z;
if (x==this.xr) dxr=z;
if (y==this.yb) dyb=z;
if (x==0) a=a-z;
if (y==0) {b=b-z;dyb=dyb-d3;}
if (x==(this.DX+1)) a=a+z;
if (y==(this.DY+1)) {b=b+z+d3;dyb=dyb-d3;}
xo=a-dxl;
yo=b-(d2>>1)-d3-dyt;
wo=d+dxl+dxr;
ho=d+d2+d3+dyt+dyb;
cx.beginPath();
if ((nat=="O")&&this.outsideBk)
{
cx.fillStyle=this.outsideBk;
cx.fillRect(xo,yo,wo,ho);
}
else if (!this.hasToDrawWholeGoban) cx.clearRect(xo,yo,wo,ho);
if (this.hasC("Variations")) m2=this.removeVariationDelimiters(m);else m2=m;
if (this.hasC("Variations")&&this.isVariation(m))
{
v=1;
m=this.removeVariationDelimiters(m);
if (!this.variationEmphasisOn)
{
if (this.variationOnFocusBk&&this.isNextMove(x,y)) bk=this.variationOnFocusBk;
else if (this.variationBk) bk=this.variationBk;
if (bk) {cx.fillStyle=bk;cx.fillRect(a+1,b+1,d-2,d-2);}
if (this.variationOnFocusStrokeBk&&this.isNextMove(x,y)) sbk=this.variationOnFocusStrokeBk;
else if (this.variationOnFocusStroked&&this.isNextMove(x,y))
{
if (this.variationOnFocusColor) sbk=this.variationOnFocusColor;
else if (this.variationColor) sbk=this.variationColor;
else sbk=this.lineColor;
}
else if (this.variationStrokeBk) sbk=this.variationStrokeBk;
if (sbk) {sbkw=this.lw/2;cx.strokeStyle=sbk;cx.strokeRect(a+1+sbkw,b+1+sbkw,d-2-2*sbkw,d-2-2*sbkw);}
}
}
if ((!v&&(nat=="E")&&!m)||(v&&this.variationEmphasisOn)||(m2=="_TB_")||(m2=="_TW_"))
{
if ((m2=="_TB_")||(m2=="_TW_")||!(v&&this.variationEmphasisOn&&!this.variationAsMarkOn))
{
cx.strokeStyle=this.lineColor;
if (this.borderLineWidth&&((x==1)||(x==this.DX))) cx.lineWidth=this.borderLineWidth;
cx.beginPath();
if ((d3==1)&&!this.isCross(x,y-1)) d4=1;else d4=0;
cx.moveTo(a+r,b+(y==1?r:-(d2>>1)-d3+d4));
if ((d3==1)&&!this.isCross(x,y+1)) d4=1;else d4=0;
cx.lineTo(a+r,b+(y==this.DY?r:d+(d2>>1)+d3-d4));
cx.stroke();
cx.lineWidth=this.lw;
if (this.borderLineWidth&&((y==1)||(y==this.DY))) cx.lineWidth=this.borderLineWidth;
cx.beginPath();
cx.moveTo(a+(x==1?r:0),b+r);
cx.lineTo(a+(x==this.DX?r:d),b+r);
cx.stroke();
cx.lineWidth=this.lw;
}
if ((m2=="_TB_")||(m2=="_TW_")) this.drawTerritoryMark(cx,a,b,d,nat,m2);
else if (v&&this.variationEmphasisOn) this.drawVariationEmphasis(cx,a,b,d,x,y,m);
else if (this.star(x,y)) this.drawStar(cx,a,b,r);
}
else
{
if (!v&&((nat=="B")||(nat=="W")))
{
if (this.in3dOn&&this.stoneShadowOn) this.drawStoneShadow(cx,a,b,d);
cx.drawImage(this.img[nat],a,b,d,d);
}
if (m)
{
if (this.hasC("Diagram"))
{
if (this.isLabel(m)) {l=1;m=this.removeLabelDelimiters(m);}
else if ((m=="_MA_")||(m=="_TR_")||(m=="_SQ_")||(m=="_CR_")) mtsc=1;
}
c=this.pointColor(x,y,nat,v,l,mtsc);
if (mtsc)
{
cx.strokeStyle=c;
cx.lineWidth=(this.markLineWidth?this.markLineWidth:1)*this.d/23*this.lw;
switch(m)
{
case "_MA_":this.drawMark(cx,a,b,d);break;
case "_TR_":this.drawTriangle(cx,a,b,d);break;
case "_SQ_":this.drawSquare(cx,a,b,d);break;
case "_CR_":this.drawCircle(cx,a,b,d);break;
}
}
else
{
if (m=="_ML_") this.drawMarkOnLast(cx,a,b,d,c);
else
{
if (v&&this.variationOnFocusFontWeight&&this.isNextMove(x,y)) fw=this.variationOnFocusFontWeight;
else if (v&&this.variationFontWeight) fw=this.variationFontWeight;
else if (l&&this.labelFontWeight) fw=this.labelFontWeight;
else if ((nat=="O")&&this.outsideFontWeight) fw=this.outsideFontWeight;
else fw="normal";
if (v&&this.variationOnFocusStrokeColor&&this.isNextMove(x,y)) sc=this.variationOnFocusStrokeColor;
else if (v&&this.variationStrokeColor) sc=this.variationStrokeColor;
else sc=0;
this.drawText(cx,a,b,d,m,{c:c,fw:fw,sc:sc});
}
}
}
}
if (this.gobanFocusVisible&&(this.xFocus==x)&&(this.yFocus==y)&&this.inView(x,y)&&!this.inSelect)
{
this.flw=(this.focusLineWidth?this.focusLineWidth:2*this.lw);
cx.lineWidth=this.flw;
cx.strokeStyle=this.focusColor;
cx.strokeRect(a+this.flw/2,b+this.flw/2,d-this.flw,d-this.flw);
cx.lineWidth=this.lw;
}
};
mxG.G.prototype.gobanCnWidth=function(){return (this.xri-this.xli+1)*this.d+2*this.z;};
mxG.G.prototype.gobanCnHeight=function(){return (this.ybi-this.yti+1)*(this.d+this.d2)+((this.d2)&1?1:0)+2*this.z;};
mxG.G.prototype.gobanWidth=function(){return this.maximizeGobanWidth?(Math.max(19,this.DX)+((this.configIndicesOn||this.indicesOn)?2:0))*this.d+2*this.z:this.gobanCnWidth();};
mxG.G.prototype.gobanHeight=function(){return this.maximizeGobanHeight?(Math.max(19,this.DY)+((this.configIndicesOn||this.indicesOn)?2:0))*(this.d+this.d2)+((this.d2)&1?1:0)+2*this.z:this.gobanCnHeight();};
mxG.G.prototype.setGobanSize=function()
{
var go=this.go,ig=this.ig,cn=this.gcn,cnw,cnh,gw,gh,iw,ih,iw2,ih2;
cnw=this.gobanCnWidth();
cnh=this.gobanCnHeight();
cn.width=cnw;
cn.height=cnh;
gw=this.gobanWidth();
gh=this.gobanHeight();
iw=cnw+this.getDW(cn);
ih=cnh+this.getDH(cn);
ig.style.width=iw+"px";
ig.style.height=ih+"px";
ig.style.left=((gw-cnw)>>1)+"px";
ig.style.top=((gh-cnh)>>1)+"px";
go.style.width=(gw+this.getDW(ig)+this.getDW(cn))+"px";
go.style.height=(gh+this.getDH(ig)+this.getDH(cn))+"px";
};
mxG.G.prototype.drawGoban=function()
{
if (!this.img.B.canDraw||!this.img.W.canDraw) {setTimeout(this.g+".drawGoban()",25);return;}
var i,j,k;
if (mxG.IsAndroid) this.hasToDrawWholeGoban=1;
if (this.d!=this.exD) this.hasToDrawWholeGoban=1;
if (this.stoneShadowOn) this.hasToDrawWholeGoban=1;
if (this.hasToDrawWholeGoban) {this.dNat=[];this.dStr=[];this.setGobanSize();}
for (i=this.xl;i<=this.xr;i++)
for (j=this.yt;j<=this.yb;j++)
{
k=this.xy(i,j);
if ((this.dNat[k]!=this.vNat[k])||(this.dStr[k]!=this.vStr[k])||this.variationEmphasisOn)
{
this.dNat[k]=this.vNat[k];
this.dStr[k]=this.vStr[k];
this.drawPoint(this.gcx,i,j,this.vNat[k],this.vStr[k]);
}
}
if (this.hasC("Diagram")&&this.indicesOn&&this.hasToDrawWholeGoban)
for (i=this.xli;i<=this.xri;i++)
for (j=this.yti;j<=this.ybi;j++)
if (!this.inView(i,j)) this.drawPoint(this.gcx,i,j,"O",this.getIndices(i,j));
if (this.hasC("Edit")&&this.selection) this.selectView();
this.exD=this.d;
this.hasToDrawWholeGoban=0;
};
mxG.G.prototype.focusInView=function()
{
this.xFocus=Math.min(Math.max(this.xFocus,this.xl),this.xr);
this.yFocus=Math.min(Math.max(this.yFocus,this.yt),this.yb);
};
mxG.G.prototype.doFocusGoban=function(ev)
{
if (this.doNotFocusGobanJustAfter) return;
this.focusInView();
this.dNat[this.xy(this.xFocus,this.yFocus)]=0;
this.gobanFocusVisible=1;
this.drawGoban();
};
mxG.G.prototype.hideGobanFocus=function()
{
if (this.inView(this.xFocus,this.yFocus)) this.dNat[this.xy(this.xFocus,this.yFocus)]=0;
this.gobanFocusVisible=0;
this.drawGoban();
};
mxG.G.prototype.doBlur4FocusGoban=function(ev)
{
var magic;
magic=(!this.gobanFocusVisible&&(document.activeElement==this.getE("InnerGobanDiv")));
if (this.gobanFocusVisible) this.hideGobanFocus();
this.doNotFocusGobanJustAfter=(magic?1:0);
};
mxG.G.prototype.doMouseDown4FocusGoban=function(ev)
{
if (this.gobanFocusVisible) this.hideGobanFocus();
this.doNotFocusGobanJustAfter=1;
};
mxG.G.prototype.doContextMenu4FocusGoban=function(ev)
{
if (this.gobanFocusVisible) this.hideGobanFocus();
this.doNotFocusGobanJustAfter=0;
};
mxG.G.prototype.doKeydownGoban=function(ev)
{
var r=0;
switch(mxG.GetKCode(ev))
{
case 37:case 72:if (this.gobanFocusVisible) this.xFocus--;r=1;break;
case 39:case 74:if (this.gobanFocusVisible) this.xFocus++;r=1;break;
case 38:case 85:if (this.gobanFocusVisible) this.yFocus--;r=1;break;
case 40:case 78:if (this.gobanFocusVisible) this.yFocus++;r=1;break;
}
if (r)
{
this.focusInView();
if (this.hasC("Edit")&&(this.editTool=="Select"))
{
if (this.inSelect==2) this.selectGobanArea(this.xFocus,this.yFocus);
else this.gobanFocusVisible=1;
}
this.hasToDrawWholeGoban=1;
this.updateAll();
ev.preventDefault();
}
this.lastKeydownOnGoban=r;
};
mxG.G.prototype.initGoban=function()
{
var s,k=this.k,bki;
if (this.gobanFocus)
{
this.xFocus=0;
this.yFocus=0;
this.getE("InnerGobanDiv").addEventListener("keydown",function(ev){mxG.D[k].doKeydownGoban(ev);},false);
this.getE("InnerGobanDiv").addEventListener("focus",function(ev){mxG.D[k].doFocusGoban(ev);},false);
this.getE("InnerGobanDiv").addEventListener("blur",function(ev){mxG.D[k].doBlur4FocusGoban(ev);},false);
this.getE("InnerGobanDiv").addEventListener("mousedown",function(ev){mxG.D[k].doMouseDown4FocusGoban(ev);},false);
this.getE("InnerGobanDiv").addEventListener("contextmenu",function(ev){mxG.D[k].doContextMenu4FocusGoban(ev);},false);
}
if (this.gobanBk) mxG.AddCssRule("#"+this.n+"GobanCanvas {background:"+this.gobanBk+";}");
else this.gobanBk="";
if (!this.lineColor) this.lineColor=mxG.GetStyle(this.gcn,"color");
if (this.gobanFocus&&!this.focusColor) this.focusColor="#f00";
};
mxG.G.prototype.disableGoban=function()
{
var e=this.getE("InnerGobanDiv");
if (!e.hasAttribute("data-maxigos-disabled"))
{
e.setAttribute("data-maxigos-disabled","1");
e.setAttribute("tabindex","-1");
}
};
mxG.G.prototype.enableGoban=function()
{
var e=this.getE("InnerGobanDiv");
if (e.hasAttribute("data-maxigos-disabled"))
{
e.removeAttribute("data-maxigos-disabled");
e.setAttribute("tabindex","0");
}
};
mxG.G.prototype.isGobanDisabled=function()
{
return this.getE("InnerGobanDiv").hasAttribute("data-maxigos-disabled");
};
mxG.G.prototype.updateGoban=function()
{
var i,j,k,x,y,z=-1,m;
if (this.markOnLastOn)
{
m=this.gor.play;
if (this.gor.getAct(m)=="")
{
x=this.gor.getX(m);
y=this.gor.getY(m);
if (this.inView(x,y)) z=this.xy(x,y);
}
}
for (i=this.xl;i<=this.xr;i++)
for (j=this.yt;j<=this.yb;j++)
{
if (this.hasC("Diagram")) this.addNatAndNum(i,j,z);
else
{
k=this.xy(i,j);
this.vNat[k]=this.gor.getBanNat(i,j);
this.vStr[k]=(z==k)?"_ML_":"";
}
}
if (this.hasC("Diagram")) this.addMarksAndLabels();
if (this.hasC("Variations")) this.addVariationMarks();
this.drawGoban();
if (this.gobanFocus)
{
if (this.gBox) this.disableGoban();else this.enableGoban();
}
};
mxG.G.prototype.refreshGoban=function()
{
if (this.d!=this.exD) this.drawGoban();
if (this.showHideCanvasBorderOn)
{
if (this.xl!=1) this.gcn.style.borderLeftWidth="0";else this.gcn.style.borderLeftWidth="";
if (this.xr!=this.DX) this.gcn.style.borderRightWidth="0";else this.gcn.style.borderRightWidth="";
if (this.yt!=1) this.gcn.style.borderTopWidth="0";else this.gcn.style.borderTopWidth="";
if (this.yb!=this.DY) this.gcn.style.borderBottomWidth="0";else this.gcn.style.borderBottomWidth="";
}
};
mxG.G.prototype.createGoban=function()
{
var s;
if (!this.gobanFont) this.gobanFont="sans-serif";
if (!this.onBlackColor) this.onBlackColor="#fff";
if (!this.onWhiteColor) this.onWhiteColor="#000";
if (!this.blackStoneColor) this.blackStoneColor="#000";
if (!this.whiteStoneColor) this.whiteStoneColor="#fff";
this.goodnessCode={Good:1,Bad:2,Even:4,Warning:8,Unclear:16,OffPath:32};
this.gobanFocus=(this.hasC("Solve")
||this.hasC("Variations")
||this.hasC("Guess")
||this.hasC("Score"))?1:0;
this.vNat=[];
this.dNat=[];
this.vStr=[];
this.dStr=[];
this.write("<div class=\"mxGobanDiv\" id=\""+this.n+"GobanDiv\">");
s="position:relative;outline:none;";
this.write("<div"+(this.gobanFocus?" tabindex=\"0\"":"")+" class=\"mxInnerGobanDiv\" id=\""+this.n+"InnerGobanDiv\" style=\""+s+"\">");
s="display:block;position:relative;-webkit-tap-highlight-color:rgba(0,0,0,0);-webkit-text-size-adjust:none;";
this.write("<canvas width=\"0\" height=\"0\" style=\""+s+"\" id=\""+this.n+"GobanCanvas\">");
this.write("</canvas></div></div>");
};
}
if (typeof mxG.G.prototype.createVariations=='undefined'){
mxG.Z.fr["Variations: "]="Variations : ";
mxG.Z.fr["no variation"]="aucune";
mxG.G.prototype.setMode=function()
{
this.styleMode=parseInt(this.getInfo("ST"));
if (this.configVariationMarksOn===undefined) this.variationMarksOn=(this.styleMode&2)?0:1;
else
{
if (this.variationMarksOn) this.styleMode&=~2;
else this.styleMode|=2;
}
if (this.configSiblingsOn===undefined) this.siblingsOn=(this.styleMode&1)?1:0;
else
{
if (this.siblingsOn) this.styleMode|=1;
else this.styleMode&=~1;
}
if (this.hideSingleVariationMarkOn) this.styleMode|=4;
};
mxG.G.prototype.doClickVariationInBox=function(a)
{
var aN=this.styleMode&1?this.cN.Dad:this.cN;
if (this.styleMode&1) this.backNode(aN);
aN.Focus=a+1;
this.placeNode();
this.updateAll();
};
mxG.G.prototype.addVariationMarkInBox=function(a,m)
{
var i=document.createElement("input"),k=this.k;
if (this.hasC("Diagram")&&this.isLabel(m)) m=this.removeLabelDelimiters(m);
i.type="button";
i.value=m;
i.addEventListener("click",function(ev){mxG.D[k].doClickVariationInBox(a);},false);
this.getE("VariationsDiv").appendChild(i);
};
mxG.G.prototype.buildVariationMark=function(l)
{
if (this.variationMarkSeed) return String.fromCharCode(this.variationMarkSeed.charCodeAt(0)-1+l);
else return l+"";
};
mxG.G.prototype.addVariationMarks=function()
{
var aN,s,k,km,l=0,x,y,z,m,e=this.getE("VariationsDiv");
var s1="<span class=\"mxVariationsSpan\">"+this.local("Variations: ")+"</span>";
var s2="<span class=\"mxNoVariationSpan\">"+this.local("no variation")+"</span>";
if (this.variationsBoxOn) e.innerHTML=s1;
if (this.styleMode&1)
{
if (!this.cN||!this.cN.Dad) 
{
if (this.variationsBoxOn) e.innerHTML=s1+s2;
return;
}
aN=this.cN.Dad;
}
else
{
if (!this.cN||!this.cN.KidOnFocus())
{
if (this.variationsBoxOn) e.innerHTML=s1+s2;
return;
}
aN=this.cN;
}
km=aN.Kid.length;
if ((this.styleMode&4)&&(km==1))
{
if (this.variationsBoxOn) e.innerHTML=s1;
return;
}
for (k=0;k<km;k++)
if (aN.Kid[k]!=this.cN)
{
s="";
l++;
if (aN.Kid[k].P.B) s=aN.Kid[k].P.B[0];
else if (aN.Kid[k].P.W) s=aN.Kid[k].P.W[0];
if (s.length==2)
{
x=s.c2n(0);
y=s.c2n(1);
z=this.xy(x,y);
if (this.inView(x,y)) m=this.vStr[z];else m=this.buildVariationMark(l);
if ((m+"").search(/^\((.*)\)$/)==-1)
{
if (!m) m=this.buildVariationMark(l);
if (!(this.styleMode&2)&&(!(this.styleMode&1)||(aN.Kid[k]!=this.cN))) this.vStr[z]="("+m+")";
}
if ((m+"").search(/^_.*_$/)==0) m=this.buildVariationMark(l);
}
else m=this.buildVariationMark(l);
if (this.variationsBoxOn&&(aN.Kid[k]!=this.cN)) this.addVariationMarkInBox(k,m);
}
};
mxG.G.prototype.isVariation=function(m)
{
return m.search(/^\((.*)\)$/)>-1;
};
mxG.G.prototype.removeVariationDelimiters=function(m)
{
return m.replace(/^(\()+(.*)(\))+$/,"$2");
};
mxG.G.prototype.getVariationNextNat=function()
{
var aN,k,km;
aN=this.cN;
if (aN.P.PL) return aN.P.PL[0];
aN=this.cN.KidOnFocus();
if (aN)
{
if (aN.P.B) return "B";
if (aN.P.W) return "W";
}
aN=this.cN;
if (aN.P.B) return "W";
if (aN.P.W) return "B";
if (aN.P.AB&&!aN.P.AW) return "W";
else if (aN.P.AW&&!aN.P.AB) return "B";
km=this.cN.Kid.length;
for (k=0;k<km;k++)
{
aN=this.cN.Kid[k];
if (aN.P.B) return "B";
if (aN.P.W) return "W";
}
km=this.cN.Dad.Kid.length;
for (k=0;k<km;k++)
{
aN=this.cN.Dad.Kid[k];
if (aN.P.B) return "W";
if (aN.P.W) return "B";
}
return "B";
};
mxG.G.prototype.addVariationPlay=function(aP,x,y)
{
var aN,aV=this.xy2s(x,y);
aN=this.cN.N(aP,aV);
aN.Add=1;
this.cN.Focus=this.cN.Kid.length;
};
mxG.G.prototype.checkBW=function(aN,a,b)
{
var s="",x,y;
if (aN.P.B||aN.P.W)
{
if (aN.P.B) s=aN.P.B[0];else s=aN.P.W[0];
if (s.length==2) {x=s.c2n(0);y=s.c2n(1);}
else {x=0;y=0;}
return (x==a)&&(y==b);
}
return 0;
};
mxG.G.prototype.checkAX=function(aN,a,b)
{
var AX=["AB","AW","AE"];
var s,x,y,aP,z,k,aLen,x1,x2,y1,y2;
s="";
if (aN.P.AB) aP="AB";
else if (aN.P.AW) aP="AW";
else if (aN.P.AE) aP="AE";
else aP=0;
if (aP) for (z=0;z<3;z++)
{
aP=AX[z];
if (aN.P[aP])
{
aLen=aN.P[aP].length;
for (k=0;k<aLen;k++)
{
s=aN.P[aP][k];
if (s.length==2)
{
x=s.c2n(0);
y=s.c2n(1);
if ((x==a)&&(y==b)) return 1;
}
else if (s.length==5)
{
x1=s.c2n(0);
y1=s.c2n(1);
x2=s.c2n(3);
y2=s.c2n(4);
for (x=x1;x<=x2;x++) for (y=y1;y<=y2;y++) if ((x==a)&&(y==b)) return 1;
}
}
}
}
return 0;
};
mxG.G.prototype.checkVariation=function(a,b)
{
var aN,bN,k,km,ok=0;
if ((this.styleMode&1)&&(this.cN.Dad==this.rN)) {this.plonk();return;}
if (a&&b&&this.gor.isOccupied(a,b))
{
aN=this.cN.Dad;
while (!ok&&(aN!=this.rN))
{
if (this.checkBW(aN,a,b)||this.checkAX(aN,a,b)) ok=1;
else aN=aN.Dad;
}
if (ok)
{
this.backNode(aN);
this.updateAll();
}
return;
}
aN=this.styleMode&1?this.cN.Dad:this.cN;
km=aN.Kid.length;
for (k=0;k<km;k++)
{
bN=aN.Kid[k];
if (this.checkBW(bN,a,b))
{
if (this.styleMode&1) this.backNode(aN);
aN.Focus=k+1;
this.placeNode();
this.updateAll();
return;
}
}
if (this.styleMode&1) {this.plonk();return;}
this.addVariationPlay(this.getVariationNextNat(),a,b);
this.placeNode();
if (this.hasC("Tree")) this.addNodeInTree(this.cN);
this.updateAll();
};
mxG.G.prototype.doClickVariations=function(ev)
{
if (this.isGobanDisabled()) return;
if (this.canPlaceVariation)
{
var c=this.getC(ev);
if (!this.inView(c.x,c.y)) {this.plonk();return;}
this.checkVariation(c.x,c.y);
}
};
mxG.G.prototype.doKeydownGobanForVariations=function(ev)
{
var c;
if (this.isGobanDisabled()) return;
if (this.canPlaceVariation&&this.gobanFocusVisible)
{
c=mxG.GetKCode(ev);
if ((c==13)||(c==32))
{
this.checkVariation(this.xFocus,this.yFocus);
ev.preventDefault();
}
}
};
mxG.G.prototype.initVariations=function()
{
var e=this.gcn,k=this.k;
e.getMClick=mxG.GetMClick;
e.addEventListener("click",function(ev){mxG.D[k].doClickVariations(ev);},false);
if (this.gobanFocus) this.go.addEventListener("keydown",function(ev){mxG.D[k].doKeydownGobanForVariations(ev);},false);
};
mxG.G.prototype.refreshVariations=function()
{
if (this.variationsBoxOn&&this.adjustVariationsWidth)
this.adjust("Variations","Width",this.adjustVariationsWidth);
};
mxG.G.prototype.createVariations=function()
{
if (!this.hasC("Edit"))
{
this.configVariationMarksOn=this.variationMarksOn;
this.configSiblingsOn=this.siblingsOn;
}
if (this.variationsBoxOn) this.write("<div class=\"mxVariationsDiv\" id=\""+this.n+"VariationsDiv\"></div>");
};
}
if (typeof mxG.G.prototype.createNavigation=='undefined'){
mxG.G.prototype.setNFocus=function(b)
{
if (this.getE(b+"Btn").disabled) this.getE("NavigationDiv").focus();
};
mxG.G.prototype.doFirst=function()
{
this.backNode(this.rN.KidOnFocus());
this.updateAll();
this.setNFocus("First");
};
mxG.G.prototype.doTenPred=function()
{
var k,aN=this.cN;
for (k=0;k<10;k++)
{
if (aN.Dad!=this.rN) aN=aN.Dad;else break;
if (this.hasC("Variations")&&!(this.styleMode&2))
{
if (this.styleMode&1) {if (aN.Dad.Kid.length>1) break;}
else if (aN.Kid.length>1) break;
}
}
this.backNode((aN==this.rN)?aN.KidOnFocus():aN);
this.updateAll();
this.setNFocus("TenPred");
};
mxG.G.prototype.doPred=function()
{
var aN=this.cN.Dad;
this.backNode((aN==this.rN)?aN.KidOnFocus():aN);
this.updateAll();
this.setNFocus("Pred");
};
mxG.G.prototype.doNext=function()
{
this.placeNode();
this.updateAll();
this.setNFocus("Next");
};
mxG.G.prototype.doTenNext=function()
{
for (var k=0;k<10;k++)
{
if (this.cN.KidOnFocus()) this.placeNode();else break;
if (this.hasC("Variations")&&!(this.styleMode&2))
{
if (this.styleMode&1) {if (this.cN.Dad.Kid.length>1) break;}
else if (this.cN.Kid.length>1) break;
}
}
this.updateAll();
this.setNFocus("TenNext");
};
mxG.G.prototype.doLast=function()
{
while (this.cN.KidOnFocus()) this.placeNode();
this.updateAll();
this.setNFocus("Last");
};
mxG.G.prototype.doTopVariation=function()
{
var aN,k,km;
if (this.styleMode&1) aN=this.cN.Dad;else aN=this.cN;
k=aN.Focus;
km=aN.Kid.length;
if (km>1)
{
aN.Focus=(k>1)?k-1:km;
if (this.styleMode&1) this.backNode(aN.KidOnFocus());
this.hasToDrawWholeGoban=1;
this.updateAll();
}
};
mxG.G.prototype.doBottomVariation=function()
{
var aN,bN,k,km;
if (this.styleMode&1) aN=this.cN.Dad;else aN=this.cN;
k=aN.Focus;
km=aN.Kid.length;
if (km>1)
{
aN.Focus=(k<km)?k+1:1;
if (this.styleMode&1) this.backNode(aN.KidOnFocus());
this.hasToDrawWholeGoban=1;
this.updateAll();
}
};
mxG.G.prototype.doKeydownNavigation=function(ev)
{
var r=0;
switch(mxG.GetKCode(ev))
{
case 36:case 70:this.doFirst();r=1;break;
case 33:case 71:this.doTenPred();r=2;break;
case 37:case 72:this.doPred();r=3;break;
case 39:case 74:this.doNext();r=4;break;
case 34:case 75:this.doTenNext();r=5;break;
case 35:case 76:this.doLast();r=6;break;
case 38:case 85:this.doTopVariation();r=7;break;
case 40:case 78:this.doBottomVariation();r=8;break;
}
if (r) ev.preventDefault();
};
mxG.G.prototype.doWheelNavigation=function(ev)
{
if (!this.gBox)
{
if (ev.deltaY>0) {this.doNext();}
else if (ev.deltaY<0) {this.doPred();}
ev.preventDefault();
}
};
mxG.G.prototype.initNavigation=function()
{
var k=this.k;
this.getE("NavigationDiv").addEventListener("keydown",function(ev){mxG.D[k].doKeydownNavigation(ev);},false);
this.go.addEventListener("wheel",function(ev){mxG.D[k].doWheelNavigation(ev);},false);
};
mxG.G.prototype.updateNavigation=function()
{
if (this.gBox)
{
this.disableBtn("First");
this.disableBtn("Pred");
this.disableBtn("TenPred");
this.disableBtn("Next");
this.disableBtn("TenNext");
this.disableBtn("Last");
}
else
{
if (this.cN.Kid.length)
{
this.enableBtn("Next");
this.enableBtn("TenNext");
this.enableBtn("Last");
}
else
{
this.disableBtn("Next");
this.disableBtn("TenNext");
this.disableBtn("Last");
}
if (this.cN.Dad==this.rN)
{
this.disableBtn("First");
this.disableBtn("Pred");
this.disableBtn("TenPred");
}
else
{
this.enableBtn("First");
this.enableBtn("Pred");
this.enableBtn("TenPred");
}
}
};
mxG.G.prototype.getNavigationElementFullWidth=function(e)
{
var r=0;
r+=mxG.GetPxStyle(e,"marginLeft");
r+=mxG.GetPxStyle(e,"marginRight");
r+=mxG.GetPxStyle(e,"paddingLeft");
r+=mxG.GetPxStyle(e,"paddingRight");
r+=mxG.GetPxStyle(e,"borderLeftWidth");
r+=mxG.GetPxStyle(e,"borderRightWidth");
r+=mxG.GetPxStyle(e,"width");
return r;
};
mxG.G.prototype.getMinimalNavigationWidth=function()
{
var e,w,list,gti,k,km;
e=this.getE("NavigationDiv");
gti=this.getE("GotoInput");
list=e.getElementsByTagName("button");
w=(gti?this.getNavigationElementFullWidth(gti):0);
km=list.length;
for (k=0;k<km;k++)
{
if (mxG.GetStyle(list[k],"display")!="none")
w+=this.getNavigationElementFullWidth(list[k]);
}
return w;
};
mxG.G.prototype.refreshNavigation=function()
{
var e,w,fs;
if (this.adjustNavigationWidth) this.adjust("Navigation","Width",this.adjustNavigationWidth);
if (this.reduceNavigationButtonsThreshold||(this.fitParent&2))
{
e=this.getE("NavigationDiv");
w=mxG.GetPxStyle(e,"width");
if (this.reduceNavigationButtonsThreshold)
{
b=this.getE("TenPredBtn");
if (b) b.style.display=(w<this.reduceNavigationButtonsThreshold)?"none":"";
b=this.getE("TenNextBtn");
if (b) b.style.display=(w<this.reduceNavigationButtonsThreshold)?"none":"";
}
if (this.fitParent&2)
{
if (w!=this.lastMinimalNavigationWidth)
{
fs=24;
while (this.getMinimalNavigationWidth()<w)
{
if (fs>63) break;
fs++;
e.style.fontSize=fs+"px";
}
while (this.getMinimalNavigationWidth()>w)
{
if (fs<3) break;
fs--;
e.style.fontSize=fs+"px";
}
this.lastMinimalNavigationWidth=this.getMinimalNavigationWidth();
}
}
}
};
mxG.G.prototype.addFirstBtn=function() {this.addBtn({n:"First",v:this.local("|<")});};
mxG.G.prototype.addTenPredBtn=function() {this.addBtn({n:"TenPred",v:this.local("<<")});};
mxG.G.prototype.addPredBtn=function() {this.addBtn({n:"Pred",v:this.local("<")});};
mxG.G.prototype.addNextBtn=function() {this.addBtn({n:"Next",v:this.local(">")});};
mxG.G.prototype.addTenNextBtn=function() {this.addBtn({n:"TenNext",v:this.local(">>")});};
mxG.G.prototype.addLastBtn=function() {this.addBtn({n:"Last",v:this.local(">|")});};
mxG.G.prototype.createNavigation=function()
{
var a,m,k,km;
if (this.navigations) a=this.navigations.split(/[\s]*[,][\s]*/);
else a=["First","TenPred","Pred","Next","TenNext","Last"];
km=a.length;
if (this.navigationBtnColor)
{
mxG.AddCssRule("#"+this.n+"NavigationDiv button {color:"+this.navigationBtnColor+";}");
mxG.AddCssRule("#"+this.n+"NavigationDiv button div:before {border-color:transparent "+this.navigationBtnColor+";}");
mxG.AddCssRule("#"+this.n+"NavigationDiv button div:after {border-color:transparent "+this.navigationBtnColor+";}");
}
if (this.navigationBtnFs)
{
mxG.AddCssRule("#"+this.n+"NavigationDiv button {font-size:"+this.navigationBtnFs+";}");
}
if (this.reduceNavigationButtonsThreshold==undefined) this.reduceNavigationButtonsThreshold=400;
this.write("<div tabindex=\"-1\" style=\"outline:none;\" class=\"mxNavigationDiv\" id=\""+this.n+"NavigationDiv\">");
for (k=0;k<km;k++)
{
m=a[k];
if (this["add"+m+"Btn"]) this["add"+m+"Btn"]();
else this.addBtn({n:m,v:this.local(m)});
}
this.write("</div>");
};
}
if (typeof mxG.G.prototype.createLoop=='undefined'){
mxG.G.prototype.resetLoop=function()
{
if (this.loopTimeout&&!this.inStepLoop) {clearTimeout(this.loopTimeout);this.loopTimeout=0;}
};
mxG.G.prototype.getLoopTime=function()
{
if (this.initialLoopTime&&(this.cN.Dad==this.rN)) return Math.round(this.initialLoopTime*this.loopTime/1000);
if (this.finalLoopTime&&(this.cN.Focus==0)) return Math.round(this.finalLoopTime*this.loopTime/1000);
if (this.hasC("Comment")||this.hasC("Lesson"))
{
var s=(this.cN.P.C?this.cN.P.C[0]:"").replace(/\n/g,"<br>");
return Math.floor(s.length*this.loopTime/10+this.loopTime);
}
return this.loopTime;
};
mxG.G.prototype.stepLoop=function()
{
this.inStepLoop=1;
if (mxG.IsMacSafari) this.gcn.offsetHeight;
if (this.cN.KidOnFocus()) {this.cN.Focus=1;this.placeNode();}
else if (this.mainVariationOnlyLoop) {this.rN.Focus=1;this.backNode(this.rN.KidOnFocus());}
else if (this.cN.Dad)
{
var aN=this.cN.Dad,bN;
while ((aN!=this.rN)&&(aN.Focus==aN.Kid.length)) aN=aN.Dad;
if (aN.Focus<aN.Kid.length) aN.Focus++;
else aN.Focus=1; 
bN=aN=aN.KidOnFocus();
while (bN.Kid.length) {bN.Focus=1;bN=bN.Kid[0];}
this.backNode(aN);
}
this.updateAll();
this.loopTimeout=setTimeout(this.g+".stepLoop()",this.getLoopTime());
this.inStepLoop=0;
};
mxG.G.prototype.doLoop=function()
{
this.inLoop=this.inLoop?0:1;
this.updateAll();
};
mxG.G.prototype.initLoop=function()
{
var e=this.getE("NavigationDiv"),i,k;
this.inLoop=(this.initMethod=="loop")?1:0;
if (e&&this.loopBtnOn)
{
k=this.k;
i=document.createElement("button");
i.type="button";
i.id=this.n+"LoopBtn";
i.className="mxBtn "+(this.inLoop?"mxPauseBtn":"mxLoopBtn");
i.addEventListener("click",function(ev){mxG.D[k].doLoop();},false);
i.innerHTML="<div><span>&lt;&gt;</div></span>";
if (this["loopTip_"+this.l_]) i.title=this["loopTip_"+this.l_];
switch(this.loopBtnPosition)
{
case "left":e.insertBefore(i,this.getE("FirstBtn"));break;
case "center":e.insertBefore(i,this.getE("NextBtn"));break;
default:e.appendChild(i); 
}
}
};
mxG.G.prototype.updateLoop=function()
{
var b;
if (this.inLoop)
{
if (!this.loopTimeout)
this.loopTimeout=setTimeout(this.g+".stepLoop()",this.getLoopTime());
}
else this.resetLoop();
if (b=this.getE("LoopBtn"))
{
b.className="mxBtn "+(this.inLoop?"mxPauseBtn":"mxLoopBtn");
if (this.gBox) this.disableBtn("Loop");
else
{
if (this.cN.Kid.length||(this.cN.Dad!=this.rN)) this.enableBtn("Loop");
else this.disableBtn("Loop");
}
}
};
mxG.G.prototype.addLoopBtn=function() {this.addBtn({n:"Loop",v:this.local("<>")});};
mxG.G.prototype.createLoop=function()
{
if (this.loopTime===undefined) this.loopTime=1000;
};
}
if (typeof mxG.G.prototype.createVersion=='undefined'){
mxG.G.prototype.refreshVersion=function()
{
if (this.adjustVersionWidth) this.adjust("Version","Width",this.adjustVersionWidth);
if (this.adjustVersionHeight) this.adjust("Version","Height",this.adjustVersionHeight);
};
mxG.G.prototype.createVersion=function()
{
this.write("<div class=\"mxVersionDiv\" id=\""+this.n+"VersionDiv\">");
this.write("<span>maxiGos "+mxG.V+"</span>");
this.write("</div>");
};
}
(function(){var a="",e=document.createElement("style");
a+="@keyframes rotateInitial{    0% {transform: rotate(0deg);}"
a+="100% {transform: rotate(360deg);}"
a+="}div.mxChameleonWaitDiv{position:relative;margin:0 auto;font-size:2em;height:1em;width:1em;color:transparent;}"
a+="div.mxChameleonWaitDiv:after{position:absolute;display:block;content:\"\";top:0;left:0;right:0;bottom:0;background:url(\"data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' fill=\'rgb(255,0,0)\' viewBox=\'0 0 1000 1000\' height=\'1000\' width=\'1000\'%3E%3Ccircle cx=\'500\' cy=\'100\' r=\'90\'/%3E%3Ccircle cx=\'217\' cy=\'217\' r=\'90\'/%3E%3Ccircle cx=\'900\' cy=\'500\' r=\'90\'/%3E%3Ccircle cx=\'783\' cy=\'217\' r=\'90\'/%3E%3Ccircle cx=\'500\' cy=\'900\' r=\'90\'/%3E%3Ccircle cx=\'783\' cy=\'783\' r=\'90\'/%3E%3Ccircle cx=\'100\' cy=\'500\' r=\'90\'/%3E%3Ccircle cx=\'217\' cy=\'783\' r=\'90\'/%3E%3C/svg%3E\");background-size:cover;animation:rotateInitial 1s linear infinite;}"
a+="div.mxChameleonGlobalBoxDiv {line-height:1.4em;}"
a+="div.mxChameleonGlobalBoxDiv>div {background:transparent;}"
a+="div.mxChameleonGlobalBoxDiv div.mxGobanDiv {margin:0 auto;position:relative;background:rgba(0,0,0,0.15);}"
a+="div.mxChameleonGlobalBoxDiv div.mxGobanDiv canvas{background-image:url(data:image/jpg;base64,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);background-size:cover;}"
a+="div.mxChameleonGlobalBoxDiv button:hover {cursor:pointer;}"
a+="div.mxChameleonGlobalBoxDiv button:disabled:hover {cursor:default;}"
a+="div.mxChameleonGlobalBoxDiv button::-moz-focus-inner {padding:0;}"
a+="div.mxChameleonGlobalBoxDiv div.mxWaitDiv {border:0.125em solid #f00;color:#f00;background:#fff;font-size:2em;}"
a+="div.mxChameleonGlobalBoxDiv div.mxVersionDiv{margin:0 auto;text-align:center;padding-top:0.5rem;padding-bottom:0.5rem;}"
a+="div.mxChameleonGlobalBoxDiv div.mxButtonsDiv,div.mxChameleonGlobalBoxDiv div.mxNavigationDiv{background:rgba(0,0,0,0.1);}"
a+="div.mxChameleonGlobalBoxDiv div.mxButtonsDiv{text-align:center;padding-top:0.5rem;padding-bottom:0.5rem;}"
a+="div.mxChameleonGlobalBoxDiv div.mxButtonsDiv button,div.mxChameleonGlobalBoxDiv div.mxOKDiv button{font-family:Arial,sans-serif;font-size:0.875em;height:auto;min-width:6em;min-height:0;border:0;margin:1px 2px;padding:0.125em 0.375em;color:#000;box-shadow:none;border-radius:0;background:rgba(255,255,255,0.9);}"
a+="div.mxChameleonGlobalBoxDiv div.mxButtonsDiv button.mxActivatedScoreBtn{color:#f00;}"
a+="div.mxChameleonGlobalBoxDiv input[type=text]{text-align:center;}"
a+="div.mxChameleonGlobalBoxDiv div.mxUnder.mxGobanDiv {background:transparent;}"
a+="div.mxChameleonGlobalBoxDiv div.mxUnder canvas {opacity:0;}"
a+="div.mxChameleonGlobalBoxDiv div.mxShowContentDiv{position:absolute;top:0;left:0;right:0;bottom:2.5em;overflow:auto;padding:0.5em;background:rgba(0,0,0,0.05);}"
a+="div.mxChameleonGlobalBoxDiv div.mxShowContentDiv h1{font-size:1.25em;margin:0;padding:0 0 0.5em 0;border:0;}"
a+="div.mxChameleonGlobalBoxDiv div.mxShowSgfDiv div.mxP{font-family:monospace;line-height:normal;}"
a+="div.mxChameleonGlobalBoxDiv div.mxOKDiv{position:absolute;bottom:0;left:0;right:0;padding:0.5em;text-align:center;background:rgba(0,0,0,0.15);}"
a+="div.mxChameleonGlobalBoxDiv div.mxShowContentDiv input[type=text]{font-size:0.875em;width:3em;height:1em;min-height:0;padding:0.125em;text-align:center;border:1px solid rgba(0,0,0,0.3);background:#fff;border-radius:0;}"
a+="div.mxChameleonGlobalBoxDiv div.mxShowContentDiv input[type=text]:disabled{border:1px solid rgba(0,0,0,0.1);}";
e.type='text/css';
if (e.styleSheet) e.styleSheet.cssText=a;
else e.appendChild(document.createTextNode(a));
document.getElementsByTagName('head')[0].appendChild(e);
})();
(function(){var a="",e=document.createElement("style");
a+="div.mxChameleonGlobalBoxDiv div.mxNavigationDiv{padding-top:0.5rem;padding-bottom:0.5rem;text-align:center;line-height:0;}"
a+="div.mxChameleonGlobalBoxDiv div.mxNavigationDiv button{font-size:1em;width:2em;height:1em;min-height:0;background-color:transparent;background-image:none;box-shadow:none;border:0;padding:0;margin:0 0.5em;vertical-align:middle;}"
a+="div.mxChameleonGlobalBoxDiv div.mxNavigationDiv input{font-family:Arial,sans-serif;font-size:0.75em;width:2em;height:1em;min-height:0;vertical-align:middle;text-align:center;margin:0;padding:0.125em;border:0;background:#fff;border-radius:0;}"
a+="div.mxChameleonGlobalBoxDiv div.mxNavigationDiv button div{display:block;position:relative;top:0;height:1em;width:0;margin:0 auto;}"
a+="div.mxChameleonGlobalBoxDiv div.mxNavigationDiv button div span {display:none;}"
a+="div.mxChameleonGlobalBoxDiv div.mxNavigationDiv button div:before,div.mxChameleonGlobalBoxDiv div.mxNavigationDiv button div:after{top:0;position:absolute;content:\"\";border-width:0;border-style:solid;border-color:transparent #000;}"
a+="div.mxChameleonGlobalBoxDiv div.mxNavigationDiv button:focus div:before,div.mxChameleonGlobalBoxDiv div.mxNavigationDiv button:focus div:after{border-color:transparent #f00;}"
a+="div.mxChameleonGlobalBoxDiv div.mxNavigationDiv button[disabled] div:before,div.mxChameleonGlobalBoxDiv div.mxNavigationDiv button[disabled] div:after{border-color:transparent rgba(0,0,0,0.3);}"
a+="div.mxChameleonGlobalBoxDiv div.mxNavigationDiv .mxFirstBtn div:before{height:1em;left:-0.3125em;border-width:0 0 0 0.125em;}"
a+="div.mxChameleonGlobalBoxDiv div.mxNavigationDiv .mxFirstBtn div:after{height:0;right:-0.3125em;border-width:0.5em 0.5em 0.5em 0; }"
a+="div.mxChameleonGlobalBoxDiv div.mxNavigationDiv .mxTenPredBtn div:before{height:0;left:-0.5em;border-width:0.5em 0.5em 0.5em 0; }"
a+="div.mxChameleonGlobalBoxDiv div.mxNavigationDiv .mxTenPredBtn div:after{height:0;right:-0.5em;border-width:0.5em 0.5em 0.5em 0; }"
a+="div.mxChameleonGlobalBoxDiv div.mxNavigationDiv .mxPredBtn div:after{height:0;left:-0.25em;border-width:0.5em 0.5em 0.5em 0; }"
a+="div.mxChameleonGlobalBoxDiv div.mxNavigationDiv .mxNextBtn div:before{height:0;left:-0.25em;border-width:0.5em 0 0.5em 0.5em;}"
a+="div.mxChameleonGlobalBoxDiv div.mxNavigationDiv .mxTenNextBtn div:before{height:0;left:-0.5em;border-width:0.5em 0 0.5em 0.5em;}"
a+="div.mxChameleonGlobalBoxDiv div.mxNavigationDiv .mxTenNextBtn div:after{height:0;right:-0.5em;border-width:0.5em 0 0.5em 0.5em;}"
a+="div.mxChameleonGlobalBoxDiv div.mxNavigationDiv .mxLastBtn div:before{height:0;left:-0.3125em;border-width:0.5em 0 0.5em 0.5em;}"
a+="div.mxChameleonGlobalBoxDiv div.mxNavigationDiv .mxLastBtn div:after{height:1em;right:-0.3125em;border-width:0 0.125em 0 0;}"
a+="div.mxChameleonGlobalBoxDiv div.mxNavigationDiv .mxLoopBtn div:before{height:0;left:-0.625em;border-width:0.5em 0.5em 0.5em 0; }"
a+="div.mxChameleonGlobalBoxDiv div.mxNavigationDiv .mxLoopBtn div:after{height:0;right:-0.625em;border-width:0.5em 0 0.5em 0.5em;}"
a+="div.mxChameleonGlobalBoxDiv div.mxNavigationDiv .mxPauseBtn div:before{height:1em;left:0.25em;border-width:0 0 0 0.125em;}"
a+="div.mxChameleonGlobalBoxDiv div.mxNavigationDiv .mxPauseBtn div:after{height:1em;right:0.25em;border-width:0 0.125em 0 0;}"
a+="div.mxChameleonGlobalBoxDiv div.mxNavigationDiv button::-moz-focus-inner {padding:0;border:0;}"
a+="div.mxChameleonGlobalBoxDiv div.mxNavigationDiv{-khtml-user-select: none;-webkit-user-select: none;-moz-user-select: -moz-none;-ms-user-select: none;user-select: none;}";
e.type='text/css';
if (e.styleSheet) e.styleSheet.cssText=a;
else e.appendChild(document.createTextNode(a));
document.getElementsByTagName('head')[0].appendChild(e);
})();
(function(){var a="",e=document.createElement("style");
a+="div.mxChameleonGlobalBoxDiv.mxBasicGlobalBoxDiv div.mxMainBoxDiv{margin:0 auto;}";
e.type='text/css';
if (e.styleSheet) e.styleSheet.cssText=a;
else e.appendChild(document.createTextNode(a));
document.getElementsByTagName('head')[0].appendChild(e);
})();
mxG.K++;
mxG.D[mxG.K]=new mxG.G(mxG.K);
mxG.D[mxG.K].path=mxG.GetDir()+"../../../";
mxG.D[mxG.K].theme="Chameleon";
mxG.D[mxG.K].config="Basic";
mxG.D[mxG.K].b[0]={n:"MainBox",c:["Goban","Variations","Navigation","Loop"]};
mxG.D[mxG.K].b[1]={n:"VersionBox",c:["Version"]};
mxG.D[mxG.K].markOnLastOn=1;
mxG.D[mxG.K].markOnLastColor="#f00";
mxG.D[mxG.K].in3dOn=1;
mxG.D[mxG.K].stretchOn=1;
mxG.D[mxG.K].initMethod="last";
mxG.D[mxG.K].sgfLoadCoreOnly=1;
mxG.D[mxG.K].sgfLoadMainOnly=0;
mxG.D[mxG.K].variationsBoxOn=0;
mxG.D[mxG.K].hideSingleVariationMarkOn=1;
mxG.D[mxG.K].variationMarksOn=1;
mxG.D[mxG.K].canPlaceVariation=1;
mxG.D[mxG.K].variationOnFocusStroked=1;
mxG.D[mxG.K].focusColor="#f00";
mxG.D[mxG.K].siblingsOn=0;
mxG.D[mxG.K].navigations="First,TenPred,Pred,Loop,Next,TenNext,Last";
mxG.D[mxG.K].maximizeGobanWidth=1;
mxG.D[mxG.K].adjustMainBoxWidth=1;
mxG.D[mxG.K].fitParent=3;
mxG.D[mxG.K].alone=1;
mxG.D[mxG.K].createAll();
