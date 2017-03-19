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
if (typeof mxG.G.prototype.createLesson=='undefined'){
mxG.G.prototype.updateLesson=function()
{
if (this.hasC("Solve")&&this.uC&&this.cN.P[this.uC]&&this.cN.KidOnFocus()&&!this.cN.KidOnFocus().P[this.uC]) return;
var a=this.getE("AssistantImg"),b=this.getE("BalloonDiv"),
c=this.getE("BalloonContentDiv"),l=this.getE("LessonDiv"),
s=this.cN.P.C?this.htmlProtect(this.cN.P.C[0]):"",cls;
s=s.replace(/\n/g,"<br>");
b.style.display=s?"block":"none";
a.src=s?this.assistantOnSrc:this.assistantOffSrc;
c.innerHTML=s;
cls="mxLessonDiv";
l.className=cls;
l.offsetHeight;
if (this.cN.P.BM) cls+=" mxBM";
else if (this.cN.P.DO) cls+=" mxDO";
else if (this.cN.P.IT) cls+=" mxIT";
else if (this.cN.P.TE) cls+=" mxTE";
cls+=" mxAfter";
l.className=cls;
};
mxG.G.prototype.refreshLesson=function()
{
if (this.adjustLessonWidth) this.adjust("Lesson","Width",this.adjustLessonWidth);
if (this.adjustLessonHeight) this.adjust("Lesson","Height",this.adjustLessonHeight);
};
mxG.G.prototype.createLesson=function()
{
var s="<div class=\"mxLessonDiv\" id=\""+this.n+"LessonDiv\">";
if (!this.assistantOnSrc) this.assistantOnSrc="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAKAAAABwCAYAAACZ8XsCAAAABmJLR0QA/wD/AP+gvaeTAAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH2gMCEToDM5xhNgAAHWVJREFUeNrtnXuQI0d9x7+yddATn12j4IOZ5OxkgDPMwjloTQ5WTlzF5GGjA1dKG5ywomJAIUWxDgXskZDcOjiJXHElZ0KFvfzlrUqKWid/ZK8qj1VIpVYFIZEgwStwQMKY7FyMYQTYzFAnPH32njd/nHrS6u15aR+3utVUTekxrdFMz6d/r/51NzY3NzHeB/dqtXrasqxVx3G0qHJzc3Nn5ubmzkSVcRxHsyxrdWFhYXZct1v3azDetmyGYdj1et0qFAoN27aNsHK2bRtRxzudjmlZVr1er1uGYdjjmpVs41Yo3xcWFmYBbKqq6q6vrxuyMqVSaXlmZmZJdqzdbpuapjkANpeWlmbGdSrfx5WQAELTNNsydTwzM7NUKpWWxe/X19cNVVVdAJtj1TsGcNcgLJVKyyKAjuNopmm2x/CNAdxxCA3DWOfVsaiC2+22yeAbq90xgLsCoaZpTrvdNkUJOLb5xgDuqSR0XVdlEpCHb6x2xwDumU1oWdaqZVmrhmGsj+EbA7jnELKdEOKP1e5we2Zzc3McDI3YKKVE9v3i4mKlXq9blFJSLpeXSqXSOf43hBBKCKHjGozeDiSADCpKKeEB831fkUHHgBI/y+AMOybCqKqqBwCKovgM1oMI7FULoAwyBlh/OwRAVxTlBt/3bwLwsv6eUxTlsO/7OQDXK4qi+L5/GMBLuf1aAFlWh5TSTQCX+vvzAC4CuEgp9QE8B6AH4AKl9DlCyDOU0h4AF8Czvu/3AHwfwAuUUpLL5VweSP7zGMB9Dhp79X1fURTFB6ACOAbglQAMADf396OEkCOU0gwhBJRShL32pVfwXtx834eiKAOvkmuM/Y5S+gMAXd/3vwfAAfBtSun3AHwLwAVCCGXSUlVVL5fLuUyKjgHcB+oTgAngdYSQ1wCY6O8vB5DhIRLAyrDveDBkZaNAlJWVARoBHwghm+x78bjv+z8AcJ5S+hQAG8A6pdShlBJN07q5XM7Vdd1RVdUbNUk5EgAy2Dj76iiANxJC3gDgNgCTl/kgGdk9ygAKgyzmOrbAGlc+TmqGXYMA4yb/2/73zwP4BqV0HcATlNLzAH7IgDQMwx4FCblvAfQ8T+Uk3g2EkDcB+HkABUKIyYPFATWgUnepMURKxDDoomCMUc3S977vb0pgfQpAm1L6DQBfBfCcYRi2pmnd/ZoOtq8A5KEjhBwD8IsAfoEQMiUBLrMPJHMiSSo7Hqauh4SRfTcAJaX0CQCP+77/dUVRnlBV1TMMwzYMw94vqvqKAyhAdyuAk4SQuwC8GkCGk2QZjMgWpVaTSs04mzEGRPYVb1d2fd9/HEALwDd1XXdM0+zouu5cSRivCICCTWcAmCaE3A3gVYJjMDLQRdmLadSyCFSUA5MURN6O7L%2f%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%2f%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%2f%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";
if (!this.assistantOffSrc) this.assistantOffSrc="data:image/png;base64,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%2f%2fnc3NyqrBfLngAwC2K73Z7qdDq3M/PNAA4S0SXILAeRlyXnxXdF4RsGrq1MSIZpkisCnUTx3mbm133ff2Zubu4vt0rxdgRASbJySxAEdwC40XGcayBZkcmk+U7X1DaIyRKaok13pjHgoN2tdMCpkgqdmwVwCsD/1ev1v52fn/+rUSUXuxbA1JLxJ1OtVuvTACYB+AAu02XMumK1TgHz1M/EVesUUVX6MYnfZJMMqc5T9ZpRJRuadtwzzHzK87wX6vX6o3Nzc6tFWzD2PIBi5txut6e63e5tRPQhANcBuCwva1bBOaoMOa+mOEy2W6QlQ4wDTWp7kvEhZ5Kk4uV6vf7ooUOHHh+mgHxBAZjNnjudTiUIAq/dbt8OwAPwIQDXyNTPpIe1buYok+2mpRidyy6iiiPuy/d6FEVveJ73Yq1W++709PRatVptDVs8vmABFC0IAi8ZGjAVBMHNAH4GwNVEdHnR+FCWzJg8z/6ww/Z+LuKOi/Thy6jf6SSmO1WtVr83PT39ZK1Wa4oj0XaTje2VWU2jKHK73a6/vr4+GQTBDf1+/wYAP53AeBXeme3VqGQjez1o89yw/f+KApdmrQBOJdDFlUrlB57ndWu1WnO3qdwFAaAOyH6/P7G+vl51HOcAgMsBlJLHpSZ9CActRuvOz4v5kgE7pm25bwBgADEzv0lEr/q+356cnFyfnp5em5qaam9lrc4CaBA7EhEHQeD1+/2JIAi8Xq9XDsPw6iAIbmTmy+gsde9LHgccx6G8Es4gvaeH6Ez6BoDTAE4z85vM/Ha5XO65rvuS7/vPlsvlnu/73fQvLhDbEQAbjUa90+lU4jh2HMeJB/m7trY23Ww2a+k1V1dX51TF0yiK3CAIvDAMSymgYRhewcxXBEFw7VkRozFmPgDgEqJzK0iNM/N7iGgfgH3MvC85bhzvrCo6hrMTOm0y8yYRvc3Mmzg7CfdbAN6K43jTcZzTcRxvAngzAfBNAG94nheUSqWXXNd9xfO8oFwu9zzPC3zf76rc6PLy8pGjR48upa/r9XqjUql0Br2XjuPE09PTa6Nu5zWx/TtBfaPRqC8vLx8ZdcKi2ue6blSpVDo6d57CCQC9Xq/MzBTHsRNFkZs+V/2AKkV2HCd2XTciIi6VSmHqJlPQBo3TxGXvG41GPV19clBbXFx8YCcA/H9zJy6uO/Pk3AAAAABJRU5ErkJggg==";
s+="<div class=\"mxBalloonDiv\" id=\""+this.n+"BalloonDiv\"><div class=\"mxBalloonContentDiv\" id=\""+this.n+"BalloonContentDiv\"></div></div>";
s+="<img class=\"mxAssistantImg\" id=\""+this.n+"AssistantImg\" width=160 height=112 src=\""+this.assistantOnSrc+"\">";
s+="</div>";
this.write(s);
};
}
if (typeof mxG.G.prototype.createSpeed=='undefined'){
mxG.G.prototype.setNewSpeed=function(w1)
{
var el=this.getE("SpeedBarDiv"),wn=mxG.GetPxStyle(el,"width"),cn=this.getE("SpeedCanvas"),wo=mxG.GetPxStyle(cn,"width");
if (this.hasC("Loop")) this.resetLoop();
if (w1>wn/2) this.loopTime=1000*(1-w1/wn)*2;
else if (w1<wn/2) this.loopTime=1000+18000*(0.5-w1/wn);
else this.loopTime=1000;
cn.style.left=w1+"px";
this.speedTokenPos=w1/wn;
};
mxG.G.prototype.doClickSpeed=function(ev)
{
this.setNewSpeed(this.getE("SpeedBarDiv").getMClick(ev).x);
this.updateAll();
};
mxG.G.prototype.doClickSpeedPlus=function()
{
var w1=mxG.GetPxStyle(this.getE("SpeedCanvas"),"left"),wn=mxG.GetPxStyle(this.getE("SpeedBarDiv"),"width");
this.setNewSpeed(Math.min(wn,w1+wn/10));
this.updateAll();
};
mxG.G.prototype.doClickSpeedMinus=function()
{
var w1=mxG.GetPxStyle(this.getE("SpeedCanvas"),"left"),wn=mxG.GetPxStyle(this.getE("SpeedBarDiv"),"width");
this.setNewSpeed(Math.max(0,w1-wn/10));
this.updateAll();
};
mxG.G.prototype.doMouseMoveSpeed=function(ev)
{
if (this.inSpeed)
{
var dv=this.getE("SpeedBarDiv"),c=dv.getMClick(ev),x;
x=Math.min(mxG.GetPxStyle(dv,"width"),Math.max(0,c.x-this.speedTokenOffset));
this.setNewSpeed(x);
this.updateAll();
}
};
mxG.G.prototype.doMouseDownSpeed=function(ev)
{
var cn=this.getE("SpeedCanvas");
this.inSpeed=1;
this.speedTokenOffset=cn.getMClick(ev).x-mxG.GetPxStyle(cn,"width")/2;
document.body.className+=" mxUnselectable";
};
mxG.G.prototype.doMouseUpSpeed=function(ev)
{
this.inSpeed=0;
document.body.className.replace(" mxUnselectable","");
};
mxG.G.prototype.doKeydownSpeed=function(ev)
{
var r=0;
switch(mxG.GetKCode(ev))
{
case 39:case 72:case 107:this.doClickSpeedPlus();r=1;break;
case 37:case 74:case 109:this.doClickSpeedMinus();r=1;break;
}
if (r) ev.preventDefault();
};
mxG.G.prototype.initSpeed=function()
{
var dv=this.getE("SpeedBarDiv"),cn=this.getE("SpeedCanvas"),k=this.k;
mxG.CreateUnselectable();
dv.getMClick=mxG.GetMClick;
cn.getMClick=mxG.GetMClick;
cn.addEventListener("mousedown",function(ev){mxG.D[k].doMouseDownSpeed(ev);},false);
document.addEventListener("mousemove",function(ev){mxG.D[k].doMouseMoveSpeed(ev);},false);
document.addEventListener("mouseup",function(ev){mxG.D[k].doMouseUpSpeed(ev);},false);
this.speedTokenPos=0.5;
this.speedTokenWidth=cn.width;
this.speedBarWidth=dv.width;
cn.style.left=(this.speedTokenPos*this.speedBarWidth)+"px";
cn.style.marginLeft=-(this.speedTokenWidth/2+mxG.GetPxStyle(cn,"borderLeftWidth"))+"px";
};
mxG.G.prototype.refreshSpeed=function()
{
var e=this.getE("SpeedBarDiv"),cn=this.getE("SpeedCanvas"),go=this.go,z;
if (this.adjustSpeedBarWidth)
{
z=mxG.GetPxStyle(this.getE("SpeedMinusBtn"),"width")+mxG.GetPxStyle(this.getE("SpeedPlusBtn"),"width")
e.style.width=(mxG.GetPxStyle(go,"width")+this.getDW(go)-this.getDW(e)-z)+"px";
}
if ((mxG.GetPxStyle(cn,"width")!=this.speedTokenWidth)||(mxG.GetPxStyle(e,"width")!=this.speedBarWidth))
{
this.speedTokenWidth=mxG.GetPxStyle(cn,"width");
this.speedBarWidth=mxG.GetPxStyle(e,"width");
cn.style.left=(this.speedTokenPos*this.speedBarWidth)+"px";
cn.style.marginLeft=-(this.speedTokenWidth/2+mxG.GetPxStyle(cn,"borderLeftWidth"))+"px";
}
};
mxG.G.prototype.createSpeed=function()
{
var s="";
s+="<div tabindex=\"-1\" style=\"outline:none;position:relative;\" class=\"mxSpeedDiv\" onkeydown=\""+this.g+".doKeydownSpeed(event)\">";
s+="<button id=\""+this.n+"SpeedMinusBtn\" type=\"button\" class=\"mxSpeedMinusBtn\" onclick=\""+this.g+".doClickSpeedMinus()\"><span>-</span></button>";
s+="<div style=\"position:relative;\" class=\"mxSpeedBarDiv\"  onclick=\""+this.g+".doClickSpeed(event)\" id=\""+this.n+"SpeedBarDiv\">";
s+="<canvas style=\"position:absolute;\" id=\""+this.n+"SpeedCanvas\"></canvas>";
s+="</div>";
s+="<button id=\""+this.n+"SpeedPlusBtn\" type=\"button\" class=\"mxSpeedPlusBtn\" onclick=\""+this.g+".doClickSpeedPlus()\"><span>+</span></button>";
s+="</div>";
this.write(s);
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
a+="div.mxClassicWaitDiv {text-align:center;}"
a+="div.mxClassicGlobalBoxDiv {line-height:1.4em;}"
a+="div.mxClassicGlobalBoxDiv div.mxGobanDiv {margin:0 auto 0.5em auto;}"
a+="div.mxClassicGlobalBoxDiv div.mxGobanDiv canvas{background:#fcba54;font-size:0.875em;}"
a+="div.mxClassicGlobalBoxDiv.mxIn3d div.mxGobanDiv {padding:0 4px 4px 0;}"
a+="div.mxClassicGlobalBoxDiv.mxIn3d div.mxInnerGobanDiv {box-shadow:1px 1px #965400,2px 2px #965400,3px 3px #965400,4px 4px #965400,8px 8px 16px rgba(0,0,0,0.5);}"
a+="div.mxClassicGlobalBoxDiv button:hover {cursor:pointer;}"
a+="div.mxClassicGlobalBoxDiv button:disabled:hover {cursor:default;}"
a+="div.mxClassicGlobalBoxDiv button::-moz-focus-inner {padding:0;}"
a+="div.mxClassicGlobalBoxDiv button {-webkit-appearance:none;}"
a+="div.mxClassicGlobalBoxDiv div.mxWaitDiv {border:0.125em solid #f00;color:#f00;background:#fff;font-size:1.5em;}"
a+="div.mxClassicGlobalBoxDiv div.mxVersionDiv {text-align:center;}";
e.type='text/css';
if (e.styleSheet) e.styleSheet.cssText=a;
else e.appendChild(document.createTextNode(a));
document.getElementsByTagName('head')[0].appendChild(e);
})();
(function(){var a="",e=document.createElement("style");
a+="div.mxClassicGlobalBoxDiv div.mxNavigationDiv{margin:0 auto;padding:0 0 0.25rem 0;text-align:center;line-height:0;}"
a+="div.mxClassicGlobalBoxDiv div.mxNavigationDiv button{font-size:1em;width:2em;height:1em;min-height:0;background-color:transparent;background-image:none;box-shadow:none;border:0;padding:0;margin:0 0.5em;vertical-align:middle;}"
a+="div.mxClassicGlobalBoxDiv div.mxNavigationDiv input{font-family:Arial,sans-serif;font-size:0.75em;width:2em;height:1em;min-height:0;vertical-align:middle;text-align:center;margin:0;padding:0.125em;border:1px solid #999;background:transparent;}"
a+="div.mxClassicGlobalBoxDiv div.mxNavigationDiv button div{position:relative;top:0;height:1em;width:0;margin:0 auto;}"
a+="div.mxClassicGlobalBoxDiv div.mxNavigationDiv button div span {display:none;}"
a+="div.mxClassicGlobalBoxDiv div.mxNavigationDiv button div:before,div.mxClassicGlobalBoxDiv div.mxNavigationDiv button div:after{top:0;position:absolute;content:\"\";border-width:0;border-style:solid;border-color:transparent #000;}"
a+="div.mxClassicGlobalBoxDiv div.mxNavigationDiv button:focus div:before,div.mxClassicGlobalBoxDiv div.mxNavigationDiv button:focus div:after{border-color:transparent #f00;}"
a+="div.mxClassicGlobalBoxDiv div.mxNavigationDiv button[disabled] div:before,div.mxClassicGlobalBoxDiv div.mxNavigationDiv button[disabled] div:after{border-color:transparent rgba(0,0,0,0.3);}"
a+="div.mxClassicGlobalBoxDiv div.mxNavigationDiv .mxFirstBtn div:before{height:1em;left:-0.3125em;border-width:0 0 0 0.125em;}"
a+="div.mxClassicGlobalBoxDiv div.mxNavigationDiv .mxFirstBtn div:after{height:0;right:-0.3125em;border-width:0.5em 0.5em 0.5em 0; }"
a+="div.mxClassicGlobalBoxDiv div.mxNavigationDiv .mxTenPredBtn div:before{height:0;left:-0.5em;border-width:0.5em 0.5em 0.5em 0; }"
a+="div.mxClassicGlobalBoxDiv div.mxNavigationDiv .mxTenPredBtn div:after{height:0;right:-0.5em;border-width:0.5em 0.5em 0.5em 0; }"
a+="div.mxClassicGlobalBoxDiv div.mxNavigationDiv .mxPredBtn div:after{height:0;left:-0.25em;border-width:0.5em 0.5em 0.5em 0; }"
a+="div.mxClassicGlobalBoxDiv div.mxNavigationDiv .mxNextBtn div:before{height:0;left:-0.25em;border-width:0.5em 0 0.5em 0.5em;}"
a+="div.mxClassicGlobalBoxDiv div.mxNavigationDiv .mxTenNextBtn div:before{height:0;left:-0.5em;border-width:0.5em 0 0.5em 0.5em;}"
a+="div.mxClassicGlobalBoxDiv div.mxNavigationDiv .mxTenNextBtn div:after{height:0;right:-0.5em;border-width:0.5em 0 0.5em 0.5em;}"
a+="div.mxClassicGlobalBoxDiv div.mxNavigationDiv .mxLastBtn div:before{height:0;left:-0.3125em;border-width:0.5em 0 0.5em 0.5em;}"
a+="div.mxClassicGlobalBoxDiv div.mxNavigationDiv .mxLastBtn div:after{height:1em;right:-0.3125em;border-width:0 0.125em 0 0;}"
a+="div.mxClassicGlobalBoxDiv div.mxNavigationDiv .mxLoopBtn div:before{height:0;left:-0.625em;border-width:0.5em 0.5em 0.5em 0; }"
a+="div.mxClassicGlobalBoxDiv div.mxNavigationDiv .mxLoopBtn div:after{height:0;right:-0.625em;border-width:0.5em 0 0.5em 0.5em;}"
a+="div.mxClassicGlobalBoxDiv div.mxNavigationDiv .mxPauseBtn div:before{height:1em;left:0.25em;border-width:0 0 0 0.125em;}"
a+="div.mxClassicGlobalBoxDiv div.mxNavigationDiv .mxPauseBtn div:after{height:1em;right:0.25em;border-width:0 0.125em 0 0;}"
a+="div.mxClassicGlobalBoxDiv div.mxNavigationDiv button::-moz-focus-inner {padding:0;border:0;}"
a+="div.mxClassicGlobalBoxDiv div.mxNavigationDiv{-khtml-user-select: none;-webkit-user-select: none;-moz-user-select: -moz-none;-ms-user-select: none;user-select: none;}";
e.type='text/css';
if (e.styleSheet) e.styleSheet.cssText=a;
else e.appendChild(document.createTextNode(a));
document.getElementsByTagName('head')[0].appendChild(e);
})();
(function(){var a="",e=document.createElement("style");
a+="div.mxClassicGlobalBoxDiv.mxLessonGlobalBoxDiv div.mxLessonDiv{position:relative;min-height:6em;margin:1em auto 0 auto;padding-bottom:120px;}"
a+="div.mxClassicGlobalBoxDiv.mxLessonGlobalBoxDiv img.mxAssistantImg{position:absolute;display:block;left:50%;bottom:0;margin-left:-80px;}"
a+="div.mxClassicGlobalBoxDiv.mxLessonGlobalBoxDiv div.mxBalloonDiv{position:absolute;bottom:120px;left:10%;right:10%;border-radius:1.5em;font-family:fantasy;background-color:#fff;text-align:center;}"
a+="div.mxClassicGlobalBoxDiv.mxLessonGlobalBoxDiv.mxIn3d div.mxBalloonDiv {padding:8px 8px 8px 12px;box-shadow:4px 4px 12px rgba(0,0,0,0.5);}"
a+="div.mxClassicGlobalBoxDiv.mxLessonGlobalBoxDiv.mxIn2d div.mxBalloonDiv {padding:8px;border:1px solid #999;}"
a+="div.mxClassicGlobalBoxDiv.mxLessonGlobalBoxDiv div.mxBalloonContentDiv{display:inline-block;text-align:justify;white-space:normal;position:relative;}"
a+=".mxClassicGlobalBoxDiv.mxLessonGlobalBoxDiv .mxTE div.mxBalloonContentDiv,.mxClassicGlobalBoxDiv.mxLessonGlobalBoxDiv .mxBM div.mxBalloonContentDiv{margin-left:2.25em;min-height:2.25em;}"
a+=".mxClassicGlobalBoxDiv.mxLessonGlobalBoxDiv .mxTE .mxBalloonDiv:before,.mxClassicGlobalBoxDiv.mxLessonGlobalBoxDiv .mxBM .mxBalloonDiv:before{background-repeat:no-repeat;background-position:left center;background-size:2em;display:block;height:2em;width:2em;content:\"\";position:absolute;top:0.25em;left:0.25em;}"
a+="/*div.mxClassicGlobalBoxDiv.mxLessonGlobalBoxDiv .mxTE .mxBalloonDiv:before {background-image:url(data:image/png;base64,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);}"
a+="div.mxClassicGlobalBoxDiv.mxLessonGlobalBoxDiv .mxBM .mxBalloonDiv:before {background-image:url(data:image/png;base64,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);}"
a+="*/div.mxClassicGlobalBoxDiv.mxLessonGlobalBoxDiv .mxTE .mxBalloonDiv:before {background-image:url(data:image/png;base64,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);}"
a+="div.mxClassicGlobalBoxDiv.mxLessonGlobalBoxDiv .mxBM .mxBalloonDiv:before {background-image:url(data:image/png;base64,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);}"
a+="div.mxClassicGlobalBoxDiv.mxLessonGlobalBoxDiv .mxSpeedDiv {margin:0.5em auto;text-align:center;max-width:24em;padding:0 0.5em;}"
a+="div.mxClassicGlobalBoxDiv.mxLessonGlobalBoxDiv .mxSpeedDiv div {cursor:pointer;display:inline-block;vertical-align:middle;}"
a+="div.mxClassicGlobalBoxDiv.mxLessonGlobalBoxDiv .mxSpeedDiv .mxSpeedPlusBtn,div.mxClassicGlobalBoxDiv.mxLessonGlobalBoxDiv .mxSpeedDiv .mxSpeedMinusBtn{font-size:1em;border:0;margin:0;padding:0;width:1em;vertical-align:middle;background-color:transparent;}"
a+="div.mxClassicGlobalBoxDiv.mxLessonGlobalBoxDiv .mxSpeedBarDiv {border:1px solid #666;background-color:#ccc;height:0.5em;width:80%;margin:1em 0.25em;}"
a+="div.mxClassicGlobalBoxDiv.mxLessonGlobalBoxDiv .mxSpeedDiv .mxSpeedBarDiv canvas {outline:1px solid #666;background-color:#ccc;height:1em;width:0.5em;top:-0.3em}"
a+="div.mxClassicGlobalBoxDiv.mxLessonGlobalBoxDiv div.mxVersionDiv {height:1.25em;padding:0.2em 2px;}";
e.type='text/css';
if (e.styleSheet) e.styleSheet.cssText=a;
else e.appendChild(document.createTextNode(a));
document.getElementsByTagName('head')[0].appendChild(e);
})();
mxG.K++;
mxG.D[mxG.K]=new mxG.G(mxG.K);
mxG.D[mxG.K].path=mxG.GetDir()+"../../../";
mxG.D[mxG.K].theme="Classic";
mxG.D[mxG.K].config="Lesson";
mxG.D[mxG.K].b[0]={n:"LBox",c:["Lesson","Speed"]};
mxG.D[mxG.K].b[1]={n:"GBox",c:["Diagram","Goban","Navigation","Loop","Variations"]};
mxG.D[mxG.K].b[2]={n:"VBox",c:["Version"]};
mxG.D[mxG.K].markOnLastOn=1;
mxG.D[mxG.K].marksAndLabelsOn=1;
mxG.D[mxG.K].numberingOn=0;
mxG.D[mxG.K].indicesOn=1;
mxG.D[mxG.K].in3dOn=1;
mxG.D[mxG.K].stretchOn=1;
mxG.D[mxG.K].initMethod="loop";
mxG.D[mxG.K].hideSingleVariationMarkOn=1;
mxG.D[mxG.K].variationMarksOn=1;
mxG.D[mxG.K].variationOnFocusColor="#f00";
mxG.D[mxG.K].loopBtnOn=1;
mxG.D[mxG.K].loopBtnPosition="center";
mxG.D[mxG.K].canPlaceVariation=1;
mxG.D[mxG.K].maximizeGobanWidth=1;
mxG.D[mxG.K].adjustNavigationWidth=1;
mxG.D[mxG.K].adjustLessonWidth=1;
mxG.D[mxG.K].fitParent=3;
mxG.D[mxG.K].alone=1;
mxG.D[mxG.K].createAll();
