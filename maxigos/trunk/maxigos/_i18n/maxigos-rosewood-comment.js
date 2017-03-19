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
if (typeof mxG.G.prototype.createDiagram=='undefined'){
mxG.G.prototype.k2n=function(k){return (this.DY+1-k)+"";};
mxG.G.prototype.k2c=function(k){var r=((k-1)%25)+1;return String.fromCharCode(r+((r>8)?65:64))+((k>25)?(k-r)/25:"");};
mxG.G.prototype.getIndices=function(x,y)
{
if ((x==0)&&(y>0)&&(y<=this.DY)) return this.k2n(y);
if ((y==0)&&(x>0)&&(x<=this.DX)) return this.k2c(x);
if ((x==(this.DX+1))&&(y>0)&&(y<=this.DY)) return this.k2n(y);
if ((y==(this.DY+1))&&(x>0)&&(x<=this.DX)) return this.k2c(x);
return "";
};
mxG.G.prototype.setIndices=function()
{
var indicesOn=this.indicesOn;
if (this.configIndicesOn===undefined) this.indicesOn=((parseInt(this.getInfo("FG"))&1)?0:1);
if (this.indicesOn&&(this.xl==1)) this.xli=0;else this.xli=this.xl;
if (this.indicesOn&&(this.yt==1)) this.yti=0;else this.yti=this.yt;
if (this.indicesOn&&(this.xr==this.DX)) this.xri=this.DX+1;else this.xri=this.xr;
if (this.indicesOn&&(this.yb==this.DY)) this.ybi=this.DY+1;else this.ybi=this.yb;
if (indicesOn!=this.indicesOn) this.hasToDrawWholeGoban=1;
};
mxG.G.prototype.setNumbering=function()
{
if (this.configAsInBookOn===undefined) this.asInBookOn=((parseInt(this.getInfo("FG"))&256)?1:0);
if (this.configNumberingOn===undefined)
{
var aN=this.cN;
this.numberingOn=parseInt(this.getInfo("PM"));
if (this.numberingOn&&(aN!=this.rN))
{
var ka=0,kb=0,kc=0,de,bN=null,cN=null,fg;
while (aN!=this.rN)
{
if (!bN&&aN.P.MN) {kb=ka;bN=aN;}
if (!cN&&aN.P.FG) {kc=ka;cN=aN;}
if (aN.P.AB||aN.P.AW||aN.P.AE) break;
if (aN.P.B||aN.P.W) ka++;
aN=aN.Dad;
}
if (!cN) {cN=this.rN.KidOnFocus();kc=ka;}
de=((!cN.P.B&&!cN.P.W)?1:0);
fg=ka-kc+(bN?parseInt(bN.P.MN[0])-ka+kb-((bN==cN)?de:0):0);
this.numFrom=ka-kc;
if (!this.numFrom) {this.numFrom=1;fg++;}
if (this.numberingOn==2) fg=fg%100;
this.numWith=fg;
}
else
{
this.numFrom=1;
this.numWith=1;
}
}
};
mxG.G.prototype.addMarksAndLabels=function()
{
if (!this.marksAndLabelsOn) return;
var MX=["MA","TR","SQ","CR","LB","TB","TW"];
var k,aLen,s,x,y,x1,y1,x2,y2,z;
for (z=0;z<7;z++)
{
if (this.cN.P[MX[z]]) aLen=this.cN.P[MX[z]].length;else aLen=0;
for (k=0;k<aLen;k++)
{
s=this.cN.P[MX[z]][k];
if (MX[z]=="LB")
{
if (s.length>3)
{
x=s.c2n(0);
y=s.c2n(1);
this.vStr[this.xy(x,y)]="|"+s.substr(3)+"|";
}
}
else if (s.length==2)
{
x=s.c2n(0);
y=s.c2n(1);
this.vStr[this.xy(x,y)]="_"+MX[z]+"_";
}
else if (s.length==5)
{
x1=s.c2n(0);
y1=s.c2n(1);
x2=s.c2n(3);
y2=s.c2n(4);
for (x=x1;x<=x2;x++) for (y=y1;y<=y2;y++) this.vStr[this.xy(x,y)]="_"+MX[z]+"_";
}
}
}
};
mxG.G.prototype.isNumbered=function(aN)
{
if (!(aN.P["B"]||aN.P["W"])) return 0;
if (this.configNumberingOn!=undefined) return this.numberingOn;
var bN=((aN==this.rN)?aN.KidOnFocus():aN);
while(bN!=this.rN)
{
if (bN.P["PM"]) return parseInt(bN.P["PM"][0]);
bN=bN.Dad;
}
return 1;
};
mxG.G.prototype.getAsInTreeNum=function(xN)
{
var aN=xN,ka=0,kb=0,kc=0,de,bN=null,cN=null,fg;
while (aN!=this.rN)
{
if (!bN&&aN.P["MN"]) {bN=aN;kb=ka;}
if (!cN&&aN.P["FG"]) {cN=aN;kc=ka;}
if (aN.P["AB"]||aN.P["AW"]||aN.P["AE"]) break;
if (aN.P["B"]||aN.P["W"]) ka++;
if ((aN.Dad.P["B"]&&aN.P["B"])||(aN.Dad.P["W"]&&aN.P["W"])) ka++; 
aN=aN.Dad;
}
if (!cN) {cN=this.rN.KidOnFocus();kc=ka;}
de=((!cN.P.B&&!cN.P.W)?1:0);
fg=ka-kc+(bN?parseInt(bN.P.MN[0])-ka+kb-((bN==cN)?de:0):0);
if (this.isNumbered(xN)==2) fg=fg%100;
return fg+kc;
};
mxG.G.prototype.getVisibleMove=function(x,y)
{
var k,kmin,kmax;
if (this.asInBookOn&&this.numberingOn)
{
kmin=Math.min(this.gor.setup+this.numFrom,this.gor.play);
for (k=kmin;k>0;k--)
if ((!this.gor.getO(k)||(this.gor.getO(k)>=kmin))&&(this.gor.getX(k)==x)&&(this.gor.getY(k)==y)&&(this.gor.getNat(k)!="E")) return k;
kmax=this.gor.getBanNum(x,y);
if (!kmax) kmax=this.gor.play;
for (k=(kmin+1);k<=kmax;k++)
if ((this.gor.getX(k)==x)&&(this.gor.getY(k)==y)&&(this.gor.getNat(k)!="E")) return k;
return this.gor.getBanNum(x,y);
}
else return this.gor.getBanNum(x,y);
};
mxG.G.prototype.getVisibleNat=function(n)
{
return this.gor.getNat(n);
};
mxG.G.prototype.getTenuki=function(m,n)
{
var k,r=0;
for (k=m;k>n;k--) if (this.gor.getNat(k)==this.gor.getNat(k-1)) r++;
return r;
};
mxG.G.prototype.getCoreNum=function(m)
{
var s=this.gor.setup;
if (m>s)
{
var n=s+this.numFrom,r;
if (m>=n) {r=m-n+this.numWith+this.getTenuki(m,n);return (r<1)?"":r+"";}
}
return "";
};
mxG.G.prototype.getVisibleNum=function(m)
{
if (this.numberingOn) return this.getCoreNum(m);
return "";
};
mxG.G.prototype.addNatAndNum=function(x,y,z)
{
var m=this.getVisibleMove(x,y),n=this.getVisibleNum(m),k=this.xy(x,y);
this.vNat[k]=this.getVisibleNat(m);
this.vStr[k]=(this.markOnLastOn&&(z==k)&&!n)?(this.numAsMarkOnLastOn?this.getCoreNum(m):"_ML_"):n;
};
mxG.G.prototype.buildStone=function(nat,d,s)
{
var cn,cx,c;
cn=document.createElement("canvas");
cn.width=cn.height=d;
cx=cn.getContext("2d");
this.drawStone(cx,nat,d);
this.drawText(cx,0,0,d,s,{c:(nat=="B")?this.onBlackColor:this.onWhiteColor});
return '<img alt="'+nat+'" src="'+cn.toDataURL("image/png")+'">';
};
mxG.G.prototype.drawMark=function(cx,x,y,d)
{
var z=(d>>2);
cx.beginPath();
cx.moveTo(x+z,y+z);
cx.lineTo(x+d-z,y+d-z);
cx.moveTo(x+d-z,y+z);
cx.lineTo(x+z,y+d-z);
cx.stroke();
};
mxG.G.prototype.drawTriangle=function(cx,x,y,d)
{
var r=d/2,s=Math.ceil(0.866*(r*0.75)),t=Math.round(0.5*(r*0.75)),e=r*0.25;
cx.beginPath();
cx.moveTo(x+r,y+e);
cx.lineTo(x+r+s,y+r+t);
cx.lineTo(x+r-s,y+r+t);
cx.closePath();
cx.stroke();
};
mxG.G.prototype.drawCircle=function(cx,x,y,d)
{
var r=d/3;
cx.beginPath();
cx.arc(x+d/2,y+d/2,r,0,Math.PI*2,false);
cx.stroke();
};
mxG.G.prototype.drawSquare=function(cx,x,y,d)
{
var z=(d>>2),e=0.5;
cx.strokeRect(x+z+e,y+z+e,d-2*e-(z<<1),d-2*e-(z<<1));
};
mxG.G.prototype.preTerritory=function(x,y,nat,m)
{
if (this.marksAndLabelsOn&&(this.cN.P.TB||this.cN.P.TW))
{
if (this.asInBookOn&&(m!="_TB_")&&(m!="_TW_"))
{
if ((nat=="B")&&(this.gor.getBanNat(x,y)=="W")) m="_TW_";
else if ((nat=="W")&&(this.gor.getBanNat(x,y)=="B")) m="_TB_";
}
}
return m;
};
mxG.G.prototype.isLabel=function(m){return m.search(/^\|(.*)\|$/)>-1;};
mxG.G.prototype.removeLabelDelimiters=function(m){return m.replace(/^(\|)+(.*)(\|)+$/,"$2");};
mxG.G.prototype.createDiagram=function()
{
if (!this.hasC("Edit"))
{
this.configIndicesOn=this.indicesOn;
this.configAsInBookOn=this.asInBookOn;
this.configNumberingOn=this.numberingOn;
}
this.numFrom=1;
this.numWith=1;
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
if (typeof mxG.G.prototype.createGoto=='undefined'){
mxG.G.prototype.doKeyupGoto=function()
{
var k,aN=this.cN,n=parseInt(this.getE("GotoInput").value);
if (isNaN(n)) n=0;
k=Math.max(0,this.getAsInTreeNum(aN));
if (k<n) while (aN.KidOnFocus())
{
k=Math.max(0,this.getAsInTreeNum(aN));
if (k>=n) break;
aN=aN.KidOnFocus();
}
else if (k>n) while (aN.P&&(aN.P.B||aN.P.W))
{
k=Math.max(0,this.getAsInTreeNum(aN));
if (k<=n) break;
aN=aN.Dad;
}
this.backNode(aN);
this.updateAll();
};
mxG.G.prototype.doClick2Goto=function(ev)
{
var ko,k1=0,kn=0,aN=this.rN,el=this.getE("GotoDiv"),w1=el.getMClick(ev).x,wn=el.offsetWidth,wo=this.getE("GotoCanvas").offsetWidth;
while (aN=aN.KidOnFocus()) kn++;
if (kn<2) ko=0;
else if (kn==2)
{
if (this.cN.Dad==this.rN) {if (w1<wo) ko=0;else ko=1;}
else {if (w1>(wn-wo)) ko=1;else ko=0;}
}
else if (w1<wo) ko=0;
else if (w1>(wn-wo)) ko=kn-1;
else ko=Math.floor((w1-wo)/(wn-2*wo)*(kn-2))+1;
aN=this.rN.KidOnFocus();
while (aN.KidOnFocus()&&(k1<ko)) {k1++;aN=aN.KidOnFocus()};
this.backNode(aN);
this.updateAll();
};
mxG.G.prototype.doClickGoto=function(ev)
{
if (!this.inGoto) this.doClick2Goto(ev);
};
mxG.G.prototype.doMouseMoveGoto=function(ev)
{
if (this.inGoto)
{
var dv=this.getE("GotoDiv"),c=dv.getMClick(ev),cn=this.getE("GotoCanvas");
cn.style.left=Math.min(dv.offsetWidth-cn.offsetWidth+1,Math.max(0,(c.x-this.gotoOffset)))+"px";
this.doClick2Goto(ev);
}
};
mxG.G.prototype.doMouseDownGoto=function(ev)
{
this.inGoto=1;
this.gotoOffset=this.getE("GotoCanvas").getMClick(ev).x;
document.body.className+=" mxUnselectable";
};
mxG.G.prototype.doMouseUpGoto=function(ev)
{
this.inGoto=0;
document.body.className.replace(" mxUnselectable","");
};
mxG.G.prototype.initGoto=function()
{
var k=this.k;
if (this.gotoInputOn)
{
var i=document.createElement("input"),b,el=this.getE("NavigationDiv");
i.type="text";
i.maxLength="3";
i.id=this.n+"GotoInput";
i.value=0;
i.addEventListener("keyup",function(ev){mxG.D[k].doKeyupGoto();},false);
switch(this.gotoInputPosition)
{
case "left":b="First";break;
case "right":b=(this.getE("LoopBtn")?"Loop":"");break;
default:b="Next"; 
}
if (b) el.insertBefore(i,this.getE(b+"Btn"));else el.appendChild(i);
}
if (this.gotoBoxOn)
{
var cn=this.getE("GotoCanvas"),dv=this.getE("GotoDiv");
mxG.CreateUnselectable();
dv.getMClick=mxG.GetMClick;
if (cn)
{
cn.getMClick=mxG.GetMClick;
cn.addEventListener("mousedown",function(ev){mxG.D[k].doMouseDownGoto(ev);},false);
}
document.addEventListener("mousemove",function(ev){mxG.D[k].doMouseMoveGoto(ev);},false);
document.addEventListener("mouseup",function(ev){mxG.D[k].doMouseUpGoto(ev);},false);
}
};
mxG.G.prototype.updateGotoBox=function()
{
if (this.gotoBoxOn)
{
var ko=0,kn=0,aN,wo=this.getE("GotoCanvas").offsetWidth,wn=this.getE("GotoDiv").offsetWidth;
aN=this.rN.KidOnFocus();
while (aN=aN.KidOnFocus()) {kn++;if (aN==this.cN) ko=kn;}
if (!kn) kn=1;
if (!this.inGoto) this.getE("GotoCanvas").style.left=(ko/kn*(wn-wo))+"px";
this.gotoBoxWidth=wn;
this.gotoCanvasWidth=wo;
}
};
mxG.G.prototype.updateGotoInput=function()
{
if (this.gotoInputOn)
{
var e=this.getE("GotoInput"),ko,k1=e.value;
if (!this.cN.P||!(this.cN.P.B||this.cN.P.W)) ko="";
else ko=this.getAsInTreeNum(this.cN);
if (ko!=k1) e.value=ko;
if (this.gBox) e.disabled=true;
else e.disabled=false;
}
};
mxG.G.prototype.updateGoto=function()
{
this.updateGotoInput();
this.updateGotoBox();
};
mxG.G.prototype.refreshGoto=function()
{
var bW,cW;
if (this.gotoBoxOn)
{
if (this.adjustGotoWidth) this.adjust("Goto","Width",this.adjustGotoWidth);
bW=this.getE("GotoDiv").offsetWidth;
cW=this.getE("GotoCanvas").offsetWidth;
if ((bW!=this.gotoBoxWidth)||(cW!=this.gotoCanvasWidth)) this.updateGotoBox();
}
};
mxG.G.prototype.createGoto=function()
{
if (!this.hasC("Diagram")) this.gotoInputOn=0;
if (this.gotoBoxOn) this.write("<div style=\"position:relative;\" class=\"mxGotoDiv\" onclick=\""+this.g+".doClickGoto(event)\" id=\""+this.n+"GotoDiv\"><canvas style=\"display:block;position:absolute;\" id=\""+this.n+"GotoCanvas\"></canvas></div>");
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
if (typeof mxG.G.prototype.createTitle=='undefined'){
mxG.Z.fr[", "]=", ";
if (!mxG.Z.fr["translateTitle"]) mxG.Z.fr["translateTitle"]=function(ev,ro)
{
var s=ev+"",a=ro+"",c="",of="",t="",between="";
if (a!="")
{
if (a.search(/^([0-9]+)$/)==0) t="ronde";
else if (a.search(/[ ]*\((final|semi-final|quarter-final|playoff|game|round)\)/i)>=0)
{
if (s.search(/[ ]+(cup|league)/i)>=0) of=" de la ";else if (s) of=" du ";
if (a.search(/[ ]*\(final\)/i)>=0) {c="Finale"+of;t="partie";}
else if (a.search(/[ ]*\(semi-final\)/i)>=0) {c="Demi-finale"+of;t="partie";}
else if (a.search(/[ ]*\(quarter-final\)/i)>=0) {c="Quart de finale"+of;t="partie";}
else if (a.search(/[ ]*\(playoff\)/i)>=0) {c="Playoff"+of;t="partie";}
else if (a.search(/[ ]*\(game\)/i)>=0) t="partie";
else t="tour";
a=a.replace(/[ ]*\((final|semi-final|quarter-final|playoff|game|round)\)/i,"");
}
else if (a.search(/[ ]*\(final tournament\)/i)>=0)
{
if (s.search(/[ ]+(cup|league)/i)>=0) of=" de la ";else if (s) of=" du ";
c="Tournoi final"+of;t="ronde";
a=a.replace(/[ ]*\(final tournament\)/i,"");
}
if (a.search(/^([0-9]+)/)==0) a=a.replace(/^([0-9]+)(.*)/,t+(t?" ":"")+"$1$2");
}
if (s.search(/^([0-9]+)(st|nd|rd|th)/i)>=0)
{
s=s.replace(/^([0-9]+)(st|nd|rd|th)[ ]+Female[ ]+(.*)$/i,"$1$2 $3 féminin");
s=s.replace(/^([0-9]+)(st|nd|rd|th)[ ]+(Former|Old)[ ]+(.*)$/i,"$1$2 ancien $4");
s=s.replace(/^([0-9]+)(st|nd|rd|th)/i,"$1<span class=\"sup\">e</span>");
s=s.replace(/^1<span class=\"sup\">ème<\/span>/,(s.search(/[ ]+(cup|league)/i)>=0)?"1<span class=\"sup\">re</span>":"1<span class=\"sup\">er</span>");
}
s=c+s;
if (s&&(a.search(/^[a-zA-Z0-9]/)==0)) s+=", ";else if (s&&a) s+=" ";
if (s) s=s.ucFirst(); else if (a) a=a.ucFirst();
if (s) s="<span class=\"mxEVTitleSpan\">"+s+"</span>";
if (a) a="<span class=\"mxROTitleSpan\">"+a+"</span>";
return s+a;
};
if (!mxG.Z.en["translateTitle"]) mxG.Z.en["translateTitle"]=function(ev,ro)
{
var s=ev+"",a=ro+"",c="",t="",before="",between="";
if (a!="")
{
if (a.search(/^([0-9]+)$/)==0) t="round";
if (a.search(/[ ]*\((final|semi-final|quarter-final|playoff|game|round)\)/i)>=0)
{
if (s) before=", ";
if (a.search(/[ ]*\(final\)/i)>=0) {c=before+"final";t="game";}
else if (a.search(/[ ]*\(semi-final\)/i)>=0) {c=before+"semi-final";t="game";}
else if (a.search(/[ ]*\(quarter-final\)/i)>=0) {c=before+"quarter-final";t="game";}
else if (a.search(/[ ]*\(playoff\)/i)>=0) {c=before+"playoff";t="game";}
else if (a.search(/[ ]*\(game\)/i)>=0) t="game";
else t="round";
a=a.replace(/[ ]*\((final|semi-final|quarter-final|playoff|game|round)\)/i,"");
}
else if (a.search(/[ ]*\(final tournament\)/i)>=0)
{
if (s) before=", ";
c=before+"final tournament";t="round";
a=a.replace(/[ ]*\(final tournament\)/i,"");
}
if (a.search(/^([0-9]+)/)==0) a=a.replace(/^([0-9]+)(.*)/,t+(t?" ":"")+"$1$2");
}
s=s+c;
if (s&&(a.search(/^\(/)==0)) between=" ";else if (s&&a) between=", ";
s=s+between+a;
return s.ucFirst();
};
mxG.G.prototype.buildTitle=function()
{
var ev,ro,f;
ev=this.getInfoS("EV");
ro=this.getInfoS("RO");
if (this.translateTitleOn) f="translateTitle";else f="buildTitle";
if (mxG.Z[this.l]&&mxG.Z[this.l][f]) return mxG.Z[this.l][f](ev,ro);
return ev+((ev&&ro)?this.local(", "):"")+ro;
};
mxG.G.prototype.initTitle=function()
{
if (this.titleBoxOn)
{
var t=this.buildTitle();
this.title=t;
this.getE("TitleH1").innerHTML=t;
if (this.hideEmptyTitle) this.getE("TitleH1").style.visibility=(t?"visible":"hidden");
}
};
mxG.G.prototype.updateTitle=function()
{
if (this.titleBoxOn)
{
var t=this.buildTitle();
if (this.title!=t)
{
this.getE("TitleH1").innerHTML=t;
this.title=t;
if (this.hideEmptyTitle) this.getE("TitleH1").style.visibility=(t?"visible":"hidden");
}
}
};
mxG.G.prototype.createTitle=function()
{
if (this.titleBoxOn) this.write("<h1 class=\"mxTitleH1\" id=\""+this.n+"TitleH1\"></h1>");
};
}
if (typeof mxG.G.prototype.createHeader=='undefined'){
mxG.Z.fr["Header"]="Informations";
mxG.Z.fr[" "]=" ";
mxG.Z.fr[", "]=", ";
mxG.Z.fr[": "]=" : ";
mxG.Z.fr["."]=",";
mxG.Z.fr["Black"]="Noir";
mxG.Z.fr["White"]="Blanc";
mxG.Z.fr[" wins"]=" gagne";
mxG.Z.fr["Date"]="Date";
mxG.Z.fr["Place"]="Lieu";
mxG.Z.fr["Time limits"]="Durée";
mxG.Z.fr["Rules"]="Règle";
mxG.Z.fr["Handicap"]="Handicap";
mxG.Z.fr["Result"]="Résultat";
mxG.Z.fr["none"]="aucun";
mxG.Z.fr[" by resign"]=" par abandon";
mxG.Z.fr[" by time"]=" au temps";
mxG.Z.fr[" by forfeit"]=" par forfait";
mxG.Z.fr[" by "]=" de ";
mxG.Z.fr["game with no result"]="partie sans résultat";
mxG.Z.fr["draw"]="partie nulle";
mxG.Z.fr["unknown result"]="résultat inconnu";
mxG.Z.fr["Komi"]="Komi ";
mxG.Z.fr[" point"]=" point";
mxG.Z.fr[" points"]=" points";
mxG.Z.fr[" Close "]="Fermer"; 
mxG.Z.fr["h"]="h";
mxG.Z.fr["mn"]="mn";
mxG.Z.fr["s"]="s";
mxG.Z.fr[" per player"]=" par joueur";
mxG.Z.fr["Japanese"]="japonaise";
mxG.Z.fr["Chinese"]="chinoise";
mxG.Z.fr["Korean"]="coréene";
mxG.Z.fr["GOE"]="Ing";
mxG.Z.fr["AGA"]="américaine / française";
mxG.Z.fr[" move"]=" coup";
mxG.Z.fr[" moves"]=" coups";
mxG.Z.fr["Number of moves"]="Nombre de coups";
mxG.Z.fr["buildMonth"]=function(a)
{
var m=["janvier","février","mars","avril","mai","juin","juillet","août","septembre","octobre","novembre","décembre"];
return m[parseInt(a)-1];
};
mxG.Z.fr["buildDay"]=function(a)
{
if (a=="01") return "1<span class=\"sup\">er</span>";
return a.replace(/,([0-9]{2})/g,"-$1").replace(/0([1-9])/g,"$1");
};
mxG.Z.fr["buildDate2"]=function(s)
{
var r,reg=/(^\s*([0-9]{2})(-([0-9]{2}(,[0-9]{2})*))?)(([^-])(.*))*\s*$/g;
if (s.match(reg))
{
r=s.replace(reg,"$8");
m=s.replace(reg,"$2");
d=s.replace(reg,"$4");
return (d?mxG.Z.fr["buildDay"](d)+" ":"")+mxG.Z.fr["buildMonth"](m)+(r?", "+mxG.Z.fr["buildDate2"](r):"");
}
return s;
};
mxG.Z.fr["buildDate"]=function(s)
{
var r,y,m,reg=/(^\s*([0-9]{4})(-([^\.]*))*)(\.)?(.*)\s*$/g,k,km,z;
if (s.indexOf("~")>=0)
{
r=s.split("~");
km=r.length;
z=mxG.Z.fr["buildDate"](r[0]);
for (k=1;k<km;k++) z+=" ~ "+mxG.Z.fr["buildDate"](r[k]);
return z;
}
s=s.replace(/,([0-9]{4})/g,".$1");
if (s.match(reg))
{
r=s.replace(reg,"$6");
y=s.replace(reg,"$2");
m=s.replace(reg,"$4");
return (m?mxG.Z.fr["buildDate2"](m)+" ":"")+y+(r?",  "+mxG.Z.fr["buildDate"](r):"");
}
return s;
};
mxG.G.prototype.buildRules=function(a)
{
return this.local(a.ucFirst());
};
mxG.G.prototype.buildTimeLimits=function(a)
{
if (a.match(/^[0-9]+$/g))
{
var r="",t,h,mn,s;
t=parseInt(a);
h=Math.floor(t/3600);
if (h) r+=h+this.local("h");
mn=Math.floor((t-h*3600)/60);
if (mn) r+=(r?this.local(" "):"")+mn+this.local("mn");
s=t-h*3600-mn*60;
if (s) r+=(r?this.local(" "):"")+s+this.local("s");
return r+this.local(" per player");
}
return a;
};
mxG.G.prototype.buildKomi=function(k)
{
var a=k+"",b;
if (a.search(/^([0-9]+([,\.]([0-9]+)?)?)?$/)==0)
{
b=parseFloat(a.replace(",","."));
if (b==0) return this.local("none");
if ((b>-2)&&(b<2)) b+=this.local(" point");else b+=this.local(" points");
return (b+"").replace(".",this.local("."));
}
return a;
};
mxG.G.prototype.buildResult=function(a)
{
var b="";
if (a.substr(0,1)=="B") b=this.local("Black");
else if (a.substr(0,1)=="W") b=this.local("White");
else if (a.substr(0,1)=="V") return this.local("game with no result");
else if (a.substr(0,1)=="D") return this.local("draw");
else if (a.substr(0,1)=="0") return this.local("draw");
else if (a.substr(0,1)=="?") return this.local("unknown result");
else return a;
b+=this.local(" wins");
if (a.substr(1,1)=="+")
{
if (a.substr(2,1)=="R") b+=this.local(" by resign");
else if (a.substr(2,1)=="T") b+=this.local(" by time");
else if (a.substr(2,1)=="F") b+=this.local(" by forfeit");
else if (a.length>2)
{
var c=parseFloat(a.substr(2).replace(",","."));
b+=this.local(" by ")+c;
if ((c>-2)&&(c<2)) b+=this.local(" point");else b+=this.local(" points");
b=b.replace(".",this.local("."));
}
}
return b;
};
mxG.G.prototype.buildNumOfMoves=function(k)
{
return k+((k<2)?this.local(" move"):this.local(" moves"));
};
mxG.G.prototype.getNumOfMoves=function()
{
var aN=this.rN,n=0,p=0,ex="E",v;
while (aN.KidOnFocus())
{
aN=aN.Kid[0];
if (aN.P.B||aN.P.W)
{
n++;
if (aN.P.B) v=aN.P.B[0];else v=aN.P.W[0];
if (v) p=0;else p++;
if ((aN.P.B&&(ex=="B"))||(aN.P.W&&(ex=="W"))) {n++;if (p) p++;}
}
else if (aN.P.AB||aN.P.AW||aN.P.AE) ex="E";
}
return n-p;
};
mxG.G.prototype.buildHeader=function()
{
var h="",a="",t="",b,c,d,r;
if (!this.hideTitle)
{
if (this.hasC("Title")) t=this.buildTitle();
else {t=this.getInfoS("EV");a=this.getInfoS("RO");if (a) t+=(t?this.local(", "):"")+a;}
if (this.concatDateToTitle&&(a=this.getInfoS("DT"))) t+=(t?" (":"")+this.build("Date",a)+(t?")":"");
}
if (t) t="<h1 class=\"mxTitleH1\">"+t+"</h1>";
if (this.hideBlack) a="";else a=this.getInfoS("PB");
if (a)
{
h+="<span class=\"mxPBSpan\"><span class=\"mxHeaderSpan\">"+this.local("Black")+this.local(": ")+"</span>"+a;
a=this.getInfoS("BR");
if (a) h+=this.local(" ")+this.build("Rank",a);
if (this.concatTeamToPlayer&&(b=this.getInfoS("BT"))) h+=(a?" (":"")+b+(a?")":"");
h+="</span><br>";
}
if (this.hideWhite) a="";else a=this.getInfoS("PW");
if (a)
{
h+="<span class=\"mxPWSpan\"><span class=\"mxHeaderSpan\">"+this.local("White")+this.local(": ")+"</span>"+a;
a=this.getInfoS("WR");
if (a) h+=this.local(" ")+this.build("Rank",a);
if (this.concatTeamToPlayer&&(b=this.getInfoS("WT"))) h+=(a?" (":"")+b+(a?")":"");
h+="</span><br>";
}
if (this.hideDate) a="";else a=this.getInfoS("DT");
if (a&&!this.concatDateToTitle) h+="<span class=\"mxDTSpan\"><span class=\"mxHeaderSpan\">"+this.local("Date")+this.local(": ")+"</span>"+this.build("Date",a)+"</span><br>";
if (this.hidePlace) a="";else a=this.getInfoS("PC");
if (a) h+="<span class=\"mxPCSpan\"><span class=\"mxHeaderSpan\">"+this.local("Place")+this.local(": ")+"</span>"+a+"</span><br>";
if (this.hideRules) a="";else a=this.getInfoS("RU");
if (a) h+="<span class=\"mxRUSpan\"><span class=\"mxHeaderSpan\">"+this.local("Rules")+this.local(": ")+"</span>"+this.build("Rules",a)+"</span><br>";
if (this.hideTimeLimits) a="";else a=this.getInfoS("TM");
if (a) h+="<span class=\"mxTMSpan\"><span class=\"mxHeaderSpan\">"+this.local("Time limits")+this.local(": ")+"</span>"+this.build("TimeLimits",a)+"</span><br>";
if (this.hideKomi) a="";else a=this.getInfoS("KM");
if (a) b="<span class=\"mxHeaderSpan\">"+this.local("Komi")+this.local(": ")+"</span>"+this.build("Komi",a);else b="";
if (b&&!this.concatKomiToResult) h+="<span class=\"mxKMSpan\">"+b+"</span><br>";
if (this.hideHandicap) a="";else a=this.getInfoS("HA");
if (a) c="<span class=\"mxHeaderSpan\">"+this.local("Handicap")+this.local(": ")+"</span>"+this.build("handicap",a);else c="";
if (c&&!this.concatHandicapToResult) h+="<span class=\"mxHASpan\">"+c+"</span><br>";
if (!this.hideNumOfMoves)
{
a=this.getNumOfMoves()+"";
if (this.hideNumOfMovesLabel) d=this.build("NumOfMoves",a);
else d="<span class=\"mxHeaderSpan\">"+this.local("Number of moves")+this.local(": ")+"</span>"+a;
if (!this.concatNumOfMovesToResult) h+="<span class=\"mxNMSpan\">"+d+"</span><br>";
}
else d="";
if (!this.hideResult&&(a=this.getInfoS("RE")))
{
h+="<span class=\"mxRESpan\">";
r=this.build("Result",a);
if (!this.hideResultLabel) h+=("<span class=\"mxHeaderSpan\">"+this.local("Result")+this.local(": ")+"</span>"+r);
else h+=r.ucFirst();
if ((d&&this.concatNumOfMovesToResult)||(c&&this.concatHandicapToResult)||(b&&this.concatKomiToResult))
{
if (b&&this.concatKomiToResult) b=b.toLowerCase();else b="";
if (c&&this.concatHandicapToResult) c=c.toLowerCase();else c="";
if (d&&this.concatNumOfMovesToResult) d=d.toLowerCase();else d="";
h+=" (";
h+=(d?d.toLowerCase():"");
h+=((d&&(c||b))?", ":"");
h+=(c?c.toLowerCase():"");
h+=(((d||c)&&b)?", ":"");
h+=(b?b.toLowerCase():"");
h+=")";
}
h+="</span><br>";
}
if (h) h="<div class=\"mxP\">"+h+"</div>";
if (!this.hideGeneralComment&&(a=this.getInfoS("GC"))) h+="<div class=\"mxP mxGCP\">"+a.replace(/\n/g,"<br>")+"</div>";
return "<div class=\"mxHeaderContentDiv\">"+t+h+"</div>";
};
mxG.G.prototype.doHeader=function()
{
if (this.gBox=="ShowHeader") {this.hideGBox("ShowHeader");return;}
if (!this.getE("ShowHeaderDiv"))
{
var s="";
s+="<div class=\"mxShowContentDiv\" id=\""+this.n+"ShowHeaderContentDiv\"></div>";
s+="<div class=\"mxOKDiv\">";
s+="<button type=\"button\" onclick=\""+this.g+".hideGBox('ShowHeader')\"><span>"+this.local(" Close ")+"</span></button>";
s+="</div>";
this.createGBox("ShowHeader").innerHTML=s;
}
this.showGBox("ShowHeader");
this.getE("ShowHeaderContentDiv").innerHTML=this.buildHeader();
};
mxG.G.prototype.initHeader=function()
{
};
mxG.G.prototype.updateHeader=function()
{
if (this.headerBoxOn)
{
var h=this.buildHeader();
if (h!=this.header)
{
this.getE("HeaderDiv").innerHTML=h;
this.header=h;
}
}
this.refreshHeader();
};
mxG.G.prototype.refreshHeader=function()
{
if (this.headerBoxOn)
{
if (this.adjustHeaderWidth) this.adjust("Header","Width",this.adjustHeaderWidth);
if (this.adjustHeaderHeight) this.adjust("Header","Height",this.adjustHeaderHeight);
}
};
mxG.G.prototype.createHeader=function()
{
if (this.hideNumOfMoves===undefined) this.hideNumOfMoves=1;
if (this.headerBoxOn||this.headerBtnOn)
{
this.write("<div class=\"mxHeaderDiv\" id=\""+this.n+"HeaderDiv\">");
if (this.headerBtnOn) this.addBtn({n:"Header",v:this.label("Header","headerLabel")});
this.write("</div>");
}
};
}
if (typeof mxG.G.prototype.createComment=='undefined'){
mxG.Z.fr["buildMove"]=function(a){return "Coup "+k;};
mxG.Z.en["buildMove"]=function(a){return "Move "+k;};
mxG.G.prototype.getOneComment=function(aN)
{
var c=aN.P.C?this.htmlProtect(aN.P.C[0]):"";
if (this.hasC("Header")&&this.headerInComment&&(aN.Dad==this.rN)) c=this.buildHeader()+c;
return c.replace(/\n/g,"<br>");
};
mxG.G.prototype.getComment=function()
{
var aN=this.cN;
if (this.allInComment)
{
var bN=this.rN,s="",c,k=0;
while (bN=bN.KidOnFocus())
{
if (bN.P.B||bN.P.W) {k++;if ((bN.P.B&&bN.Dad.P.B)||(bN.P.W&&bN.Dad.P.W)) k++;}
else if (bN.P.AB||bN.P.AW||bN.P.AE) k=0;
if (c=this.getOneComment(bN))
{
s+="<div class=\"mxP\">";
if (k) s+="<span class=\"mxMoveNumberSpan\">"+this.build("Move",k)+"</span><br>";
s+=c+"</div>";
}
if (bN==aN) break;
}
return s;
}
else return this.getOneComment(aN);
};
mxG.G.prototype.disableComment=function()
{
var e=this.getE("CommentDiv");
if (!e.hasAttribute("data-maxigos-disabled"))
{
e.setAttribute("data-maxigos-disabled","1");
if (!mxG.IsFirefox) e.setAttribute("tabindex","-1");
}
};
mxG.G.prototype.enableComment=function()
{
var e=this.getE("CommentDiv");
if (e.hasAttribute("data-maxigos-disabled"))
{
e.removeAttribute("data-maxigos-disabled");
if (!mxG.IsFirefox) e.setAttribute("tabindex","0");
}
};
mxG.G.prototype.isCommentDisabled=function()
{
return this.getE("CommentDiv").hasAttribute("data-maxigos-disabled");
};
mxG.G.prototype.updateComment=function()
{
var e=this.getE("CommentDiv");
if (this.hasC("Solve")&&this.canPlaceSolve) return;
if (this.cN.P.BM) e.className="mxCommentDiv mxBM";
else if (this.cN.P.DO) e.className="mxCommentDiv mxDO";
else if (this.cN.P.IT) e.className="mxCommentDiv mxIT";
else if (this.cN.P.TE) e.className="mxCommentDiv mxTE";
else e.className="mxCommentDiv";
this.getE("CommentContentDiv").innerHTML=this.getComment();
this.refreshComment();
if (this.gBox) this.disableComment();else this.enableComment();
};
mxG.G.prototype.refreshComment=function()
{
if (this.adjustCommentWidth) this.adjust("Comment","Width",this.adjustCommentWidth);
if (this.adjustCommentHeight) this.adjust("Comment","Height",this.adjustCommentHeight);
};
mxG.G.prototype.createComment=function()
{
var a;
a=mxG.IsFirefox?"":" tabindex=\"0\"";
this.write("<div class=\"mxCommentDiv\" id=\""+this.n+"CommentDiv\""+a+">");
this.write("<div class=\"mxCommentContentDiv\" id=\""+this.n+"CommentContentDiv\"></div>");
this.write("</div>");
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
a+=".mxRosewoodWaitDiv {text-align:center;}"
a+=".mxRosewoodGlobalBoxDiv {line-height:1.4em;}"
a+=".mxRosewoodGlobalBoxDiv div.mxGobanDiv{position:relative;margin:0 auto;}"
a+=".mxRosewoodGlobalBoxDiv div.mxInnerGobanDiv{background-image:url(data:image/jpg;base64,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);background-size:cover;padding:6px;}"
a+=".mxRosewoodGlobalBoxDiv div.mxGobanDiv canvas{color:#eca;font-size:16px;border:2px solid #eca;}"
a+=".mxRosewoodGlobalBoxDiv div.mxVersionDiv{margin-top:0.75em;text-align:center;}";
e.type='text/css';
if (e.styleSheet) e.styleSheet.cssText=a;
else e.appendChild(document.createTextNode(a));
document.getElementsByTagName('head')[0].appendChild(e);
})();
(function(){var a="",e=document.createElement("style");
a+="div.mxRosewoodGlobalBoxDiv div.mxNavigationDiv{margin:0 auto;padding:0.25rem 0;text-align:center;line-height:0;}"
a+="div.mxRosewoodGlobalBoxDiv div.mxNavigationDiv button{font-size:1em;width:2em;height:1em;min-height:0;background-color:transparent;background-image:none;box-shadow:none;border:0;padding:0;margin:0 0.5em;vertical-align:middle;}"
a+="div.mxRosewoodGlobalBoxDiv div.mxNavigationDiv input{font-family:Arial,sans-serif;font-size:0.75em;width:2em;height:1em;min-height:0;vertical-align:middle;text-align:center;margin:0;border:1px solid rgba(0,0,0,0.4);background:transparent;}"
a+="div.mxRosewoodGlobalBoxDiv div.mxNavigationDiv button div{display:block;position:relative;top:0;height:1em;width:0;margin:0 auto;}"
a+="div.mxRosewoodGlobalBoxDiv div.mxNavigationDiv button div span {display:none;}"
a+="div.mxRosewoodGlobalBoxDiv div.mxNavigationDiv button div:before,div.mxRosewoodGlobalBoxDiv div.mxNavigationDiv button div:after{top:0;position:absolute;content:\"\";border-width:0;border-style:solid;border-color:transparent #000;}"
a+="div.mxRosewoodGlobalBoxDiv div.mxNavigationDiv button:focus div:before,div.mxRosewoodGlobalBoxDiv div.mxNavigationDiv button:focus div:after{border-color:transparent #f00;}"
a+="div.mxRosewoodGlobalBoxDiv div.mxNavigationDiv button[disabled] div:before,div.mxRosewoodGlobalBoxDiv div.mxNavigationDiv button[disabled] div:after{border-color:transparent rgba(0,0,0,0.4);}"
a+="div.mxRosewoodGlobalBoxDiv div.mxNavigationDiv .mxFirstBtn div:before{height:1em;left:-0.3125em;border-width:0 0 0 0.125em;}"
a+="div.mxRosewoodGlobalBoxDiv div.mxNavigationDiv .mxFirstBtn div:after{height:0;right:-0.3125em;border-width:0.5em 0.5em 0.5em 0; }"
a+="div.mxRosewoodGlobalBoxDiv div.mxNavigationDiv .mxTenPredBtn div:before{height:0;left:-0.5em;border-width:0.5em 0.5em 0.5em 0; }"
a+="div.mxRosewoodGlobalBoxDiv div.mxNavigationDiv .mxTenPredBtn div:after{height:0;right:-0.5em;border-width:0.5em 0.5em 0.5em 0; }"
a+="div.mxRosewoodGlobalBoxDiv div.mxNavigationDiv .mxPredBtn div:after{height:0;left:-0.25em;border-width:0.5em 0.5em 0.5em 0; }"
a+="div.mxRosewoodGlobalBoxDiv div.mxNavigationDiv .mxNextBtn div:before{height:0;left:-0.25em;border-width:0.5em 0 0.5em 0.5em;}"
a+="div.mxRosewoodGlobalBoxDiv div.mxNavigationDiv .mxTenNextBtn div:before{height:0;left:-0.5em;border-width:0.5em 0 0.5em 0.5em;}"
a+="div.mxRosewoodGlobalBoxDiv div.mxNavigationDiv .mxTenNextBtn div:after{height:0;right:-0.5em;border-width:0.5em 0 0.5em 0.5em;}"
a+="div.mxRosewoodGlobalBoxDiv div.mxNavigationDiv .mxLastBtn div:before{height:0;left:-0.3125em;border-width:0.5em 0 0.5em 0.5em;}"
a+="div.mxRosewoodGlobalBoxDiv div.mxNavigationDiv .mxLastBtn div:after{height:1em;right:-0.3125em;border-width:0 0.125em 0 0;}"
a+="div.mxRosewoodGlobalBoxDiv div.mxNavigationDiv .mxLoopBtn div:before{height:0;left:-0.625em;border-width:0.5em 0.5em 0.5em 0; }"
a+="div.mxRosewoodGlobalBoxDiv div.mxNavigationDiv .mxLoopBtn div:after{height:0;right:-0.625em;border-width:0.5em 0 0.5em 0.5em;}"
a+="div.mxRosewoodGlobalBoxDiv div.mxNavigationDiv .mxPauseBtn div:before{height:1em;left:0.25em;border-width:0 0 0 0.125em;}"
a+="div.mxRosewoodGlobalBoxDiv div.mxNavigationDiv .mxPauseBtn div:after{height:1em;right:0.25em;border-width:0 0.125em 0 0;}"
a+="div.mxRosewoodGlobalBoxDiv div.mxNavigationDiv button::-moz-focus-inner {padding:0;border:0;}"
a+="div.mxRosewoodGlobalBoxDiv div.mxNavigationDiv{-khtml-user-select: none;-webkit-user-select: none;-moz-user-select: -moz-none;-ms-user-select: none;user-select: none;}";
e.type='text/css';
if (e.styleSheet) e.styleSheet.cssText=a;
else e.appendChild(document.createTextNode(a));
document.getElementsByTagName('head')[0].appendChild(e);
})();
(function(){var a="",e=document.createElement("style");
a+="div.mxRosewoodGlobalBoxDiv.mxCommentGlobalBoxDiv div.mxCommentDiv{margin:0.5em auto;height:8em;overflow:auto;border:1px solid rgba(0,0,0,0.4);}"
a+="div.mxRosewoodGlobalBoxDiv.mxCommentGlobalBoxDiv div.mxCommentContentDiv {padding:0.25em;text-align:justify;}"
a+="div.mxRosewoodGlobalBoxDiv.mxCommentGlobalBoxDiv div.mxCommentDiv h1 {font-size:1em;text-align:left;margin:0;}";
e.type='text/css';
if (e.styleSheet) e.styleSheet.cssText=a;
else e.appendChild(document.createTextNode(a));
document.getElementsByTagName('head')[0].appendChild(e);
})();
mxG.K++;
mxG.D[mxG.K]=new mxG.G(mxG.K);
mxG.D[mxG.K].path=mxG.GetDir()+"../../../";
mxG.D[mxG.K].theme="Rosewood";
mxG.D[mxG.K].config="Comment";
mxG.D[mxG.K].b[0]={n:"Box",c:["Diagram","Goban","Navigation","Goto","Variations","Title","Header","Comment","Version"]};
mxG.D[mxG.K].markOnLastOn=1;
mxG.D[mxG.K].marksAndLabelsOn=1;
mxG.D[mxG.K].numberingOn=0;
mxG.D[mxG.K].indicesOn=1;
mxG.D[mxG.K].in3dOn=1;
mxG.D[mxG.K].stretchOn=1;
mxG.D[mxG.K].initMethod="first";
mxG.D[mxG.K].customStone="_img/stone/";
mxG.D[mxG.K].hideSingleVariationMarkOn=1;
mxG.D[mxG.K].variationMarksOn=1;
mxG.D[mxG.K].variationColor="#eca";
mxG.D[mxG.K].variationFontWeight="normal";
mxG.D[mxG.K].variationOnFocusStrokeBk="#eca";
mxG.D[mxG.K].focusColor="#eca";
mxG.D[mxG.K].starRadius=3.5;
mxG.D[mxG.K].headerInComment=1;
mxG.D[mxG.K].gotoInputOn=1;
mxG.D[mxG.K].canPlaceVariation=1;
mxG.D[mxG.K].maximizeGobanWidth=1;
mxG.D[mxG.K].adjustCommentWidth=1;
mxG.D[mxG.K].adjustNavigationWidth=1;
mxG.D[mxG.K].showHideCanvasBorderOn=1;
mxG.D[mxG.K].fitParent=3;
mxG.D[mxG.K].alone=1;
mxG.D[mxG.K].createAll();
