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
if (typeof mxG.G.prototype.createInfo=='undefined'){
mxG.Z.fr["Info"]="Info";
mxG.Z.fr["OK"]="OK";
mxG.Z.fr["Cancel"]="Annuler";
mxG.Z.fr["Event:"]="Évènement :";
mxG.Z.fr["Round:"]="Ronde :";
mxG.Z.fr["Black:"]="Noir :";
mxG.Z.fr["White:"]="Blanc :";
mxG.Z.fr["Rank:"]="Niveau :";
mxG.Z.fr["Komi:"]="Komi :";
mxG.Z.fr["Handicap:"]="Handicap :";
mxG.Z.fr["Result:"]="Résultat :";
mxG.Z.fr["Date:"]="Date :";
mxG.Z.fr["Place:"]="Lieu :";
mxG.Z.fr["Rules:"]="Règle :";
mxG.Z.fr["Time limits:"]="Temps :";
mxG.Z.fr["Overtime:"]="Byoyomi :";
mxG.Z.fr["Annotations:"]="Annotations :";
mxG.Z.fr["Copyright:"]="Copyright :";
mxG.Z.fr["Source:"]="Source :";
mxG.Z.fr["User:"]="Utilisateur :";
mxG.Z.fr["Black team:"]="Équipe de Noir :";
mxG.Z.fr["White team:"]="Équipe de Blanc :";
mxG.Z.fr["Game name:"]="Nom de la partie :";
mxG.Z.fr["Opening:"]="Ouverture :";
mxG.Z.fr["General comment:"]="Commentaire général :";
mxG.Z.fr["by resign"]="par abandon";
mxG.Z.fr["by time"]="au temps";
mxG.Z.fr["by forfeit"]="par forfait";
mxG.Z.fr["by"]="de";
mxG.Z.fr["on points"]="aux points";
mxG.Z.fr["suspended"]="suspendu";
mxG.Z.fr["Main"]="Informations principales";
mxG.Z.fr["Other"]="Autres informations";
mxG.Z.fr["Black"]="Noir";
mxG.Z.fr["White"]="Blanc";
mxG.Z.fr[" wins"]=" gagne";
mxG.Z.fr["no result"]="sans résultat";
mxG.Z.fr["draw"]="partie nulle";
mxG.Z.fr["unknown"]="inconnu";
mxG.G.prototype.popInfo=function(aPropName)
{
var aN;
aN=this.rN.KidOnFocus();
aN.TakeOff(aPropName,0);
};
mxG.G.prototype.decodeResult=function(a)
{
this.WN="";
this.HW="";
this.SC="";
if (a)
{
this.WN=a.substr(0,1);
if (this.WN=="0") this.WN="D";
if (a.substr(1,1)=="+")
{
this.WN+="+";
if (a.substr(2,1)=="R") this.HW="R";
else if (a.substr(2,1)=="T") this.HW="T";
else if (a.substr(2,1)=="F") this.HW="F";
else if (a.length>2) this.SC=a.substr(2);
}
}
};
mxG.G.prototype.changeInfoStatus=function(el,b)
{
var c=el.className.replace(" mxBadInput","");
if (b) el.className=c;else el.className=c+" mxBadInput";
};
mxG.G.prototype.checkRank=function(el,ev)
{
this.changeInfoStatus(el,(el.value+"").search(/^([0-9]+[kdp]?)?$/)==0);
this.doChangeInfo();
};
mxG.G.prototype.checkHandicap=function(el,ev)
{
this.changeInfoStatus(el,!el.value||(((el.value+"").search(/^[0-9]+$/)==0)&&(parseInt(el.value)>1)));
this.doChangeInfo();
};
mxG.G.prototype.checkReal=function(el,ev)
{
this.changeInfoStatus(el,(el.value+"").search(/^([0-9]+([.]([0-9]+)?)?)?$/)==0);
this.doChangeInfo();
};
mxG.G.prototype.encodeResult=function()
{
var e=this.getE("RE"),WN=this.getE("WN").value,HW;
if (WN=="D") e.value="Draw";else if (WN=="V") e.value="Void";else e.value=WN;
if ((WN=="B+")||(WN=="W+"))
{
if (HW=this.getE("HW").value) e.value+=HW;
else e.value+=this.getE("SC").value;
}
};
mxG.G.prototype.showMainInfoPage=function()
{
this.getE("MainInfoPage").style.display="block";
this.getE("OtherInfoPage").style.display="none";
this.getE("MainInfoBtn").className="mxInfoSelectedPageBtn";
this.getE("OtherInfoBtn").className="mxInfoPageBtn";
};
mxG.G.prototype.showOtherInfoPage=function()
{
this.getE("MainInfoPage").style.display="none";
this.getE("OtherInfoPage").style.display="block";
this.getE("MainInfoBtn").className="mxInfoPageBtn";
this.getE("OtherInfoBtn").className="mxInfoSelectedPageBtn";
};
mxG.G.prototype.buildInfo=function()
{
var s="";
this.decodeResult(this.getInfo("RE"));
s+="<div class=\"mxInfoPageMenuDiv\">";
s+="<button class=\"mxInfoSelectedPageBtn\" id=\""+this.n+"MainInfoBtn\" type=\"button\" onclick=\""+this.g+".showMainInfoPage();\">"+this.local("Main")+"</button>";
s+="<button class=\"mxInfoPageBtn\" id=\""+this.n+"OtherInfoBtn\" type=\"button\" onclick=\""+this.g+".showOtherInfoPage();\">"+this.local("Other")+"</button>";
s+="</div>\n";
s+="<div class=\"mxInfoPageDiv\" id=\""+this.n+"MainInfoPage\">";
s+=("<label class=\"mxEV\" for=\""+this.n+"EV\">"+this.local("Event:")+" </label><input class=\"mxEV\" onkeyup=\""+this.g+".doChangeInfo();\" id=\""+this.n+"EV\" type=\"text\" value=\""+"\"><br>");
s+=("<label class=\"mxRO\" for=\""+this.n+"RO\">"+this.local("Round:")+" </label><input class=\"mxRO\" onkeyup=\""+this.g+".doChangeInfo();\" id=\""+this.n+"RO\" type=\"text\" value=\""+"\"><br>");
s+=("<label class=\"mxDT\" for=\""+this.n+"DT\">"+this.local("Date:")+" </label><input class=\"mxDT\" onkeyup=\""+this.g+".doChangeInfo();\" id=\""+this.n+"DT\" type=\"text\" value=\""+"\"><br>");
s+=("<label class=\"mxPC\" for=\""+this.n+"PC\">"+this.local("Place:")+" </label><input class=\"mxPC\" onkeyup=\""+this.g+".doChangeInfo();\" id=\""+this.n+"PC\" type=\"text\" value=\""+"\"><br>");
s+=("<label class=\"mxPB\" for=\""+this.n+"PB\">"+this.local("Black:")+" </label><input class=\"mxPB\" onkeyup=\""+this.g+".doChangeInfo();\" id=\""+this.n+"PB\" type=\"text\" value=\""+"\">");
s+=("<label class=\"mxBR\" for=\""+this.n+"BR\">"+this.local("Rank:")+" </label><input class=\"mxBR\" onkeyup=\""+this.g+".checkRank(this,event);\" id=\""+this.n+"BR\" type=\"text\" value=\""+"\"><br>");
s+=("<label class=\"mxPW\" for=\""+this.n+"PW\">"+this.local("White:")+" </label><input class=\"mxPW\" onkeyup=\""+this.g+".doChangeInfo();\" id=\""+this.n+"PW\" type=\"text\" value=\""+"\">");
s+=("<label class=\"mxWR\" for=\""+this.n+"WR\">"+this.local("Rank:")+" </label><input class=\"mxWR\" onkeyup=\""+this.g+".checkRank(this,event);\" id=\""+this.n+"WR\" type=\"text\" value=\""+"\"><br>");
s+=("<label class=\"mxKM\" for=\""+this.n+"KM\">"+this.local("Komi:")+" </label><input class=\"mxKM\" onkeyup=\""+this.g+".checkReal(this,event);\" id=\""+this.n+"KM\" type=\"text\" value=\""+"\">");
s+=("<label class=\"mxHA\" for=\""+this.n+"HA\">"+this.local("Handicap:")+" </label><input class=\"mxHA\" onkeyup=\""+this.g+".checkHandicap(this,event);\" id=\""+this.n+"HA\" type=\"text\" value=\""+"\"><br>");
s+=("<label class=\"mxWN\" for=\""+this.n+"WN\">"+this.local("Result:")+" </label>");
s+=("<select class=\"mxWN\" onclick=\""+this.g+".doChangeInfo();\" id=\""+this.n+"WN\">");
s+=("<option value=\"\"></option>");
s+=("<option value=\"B+\""+">"+this.local("Black")+this.local(" wins")+"</option>");
s+=("<option value=\"W+\""+">"+this.local("White")+this.local(" wins")+"</option>");
s+=("<option value=\"D\""+">"+this.local("draw")+"</option>");
s+=("<option value=\"V\""+">"+this.local("no result")+"</option>");
s+=("<option value=\"?\""+">"+this.local("unknown")+"</option>");
s+=("</select>");
s+=("<select class=\"mxHW\" onclick=\""+this.g+".doChangeInfo();\" id=\""+this.n+"HW\">");
s+=("<option value=\"\"></option>");
s+=("<option value=\"R\""+">"+this.local("by resign")+"</option>");
s+=("<option value=\"T\""+">"+this.local("by time")+"</option>");
s+=("<option value=\"F\""+">"+this.local("by forfeit")+"</option>");
s+=("</select>");
s+=("<label class=\"mxSC\" for=\""+this.n+"SC\">"+this.local("by")+"</label><input class=\"mxSC\" id=\""+this.n+"SC\" onkeyup=\""+this.g+".checkReal(this,event);\" type=\"text\" value=\""+"\"><br>");
s+=("<input class=\"mxRE\" id=\""+this.n+"RE\" type=\"hidden\" value=\""+"\">");
s+=("<label class=\"mxGC\" for=\""+this.n+"GC\">"+this.local("General comment:")+" </label><br><textarea class=\"mxGC\" onkeyup=\""+this.g+".doChangeInfo();\" id=\""+this.n+"GC\">"+"</textarea><br>");
s+="</div>";
s+="<div class=\"mxInfoPageDiv\" style=\"display:none;\" id=\""+this.n+"OtherInfoPage\">";
s+=("<label class=\"mxGN\" for=\""+this.n+"GN\">"+this.local("Game name:")+" </label><input class=\"mxGN\" onkeyup=\""+this.g+".doChangeInfo();\" id=\""+this.n+"GN\" type=\"text\" value=\""+"\"><br>");
s+=("<label class=\"mxBT\" for=\""+this.n+"BT\">"+this.local("Black team:")+" </label><input class=\"mxBT\" onkeyup=\""+this.g+".doChangeInfo();\" id=\""+this.n+"BT\" type=\"text\" value=\""+"\"><br>");
s+=("<label class=\"mxWT\" for=\""+this.n+"WT\">"+this.local("White team:")+" </label><input class=\"mxWT\" onkeyup=\""+this.g+".doChangeInfo();\" id=\""+this.n+"WT\" type=\"text\" value=\""+"\"><br>");
s+=("<label class=\"mxRU\" for=\""+this.n+"RU\">"+this.local("Rules:")+" </label><input class=\"mxRU\" onkeyup=\""+this.g+".doChangeInfo();\" id=\""+this.n+"RU\" type=\"text\" value=\""+"\"><br>");
s+=("<label class=\"mxTM\" for=\""+this.n+"TM\">"+this.local("Time limits:")+" </label><input class=\"mxTM\" onkeyup=\""+this.g+".doChangeInfo();\" id=\""+this.n+"TM\" type=\"text\" value=\""+"\"><br>");
s+=("<label class=\"mxOT\" for=\""+this.n+"OT\">"+this.local("Overtime:")+" </label><input class=\"mxOT\" onkeyup=\""+this.g+".doChangeInfo();\" id=\""+this.n+"OT\" type=\"text\" value=\""+"\"><br>");
s+=("<label class=\"mxON\" for=\""+this.n+"ON\">"+this.local("Opening:")+" </label><input class=\"mxON\" onkeyup=\""+this.g+".doChangeInfo();\" id=\""+this.n+"ON\" type=\"text\" value=\""+"\"><br>");
s+=("<label class=\"mxAN\" for=\""+this.n+"AN\">"+this.local("Annotations:")+" </label><input class=\"mxAN\" onkeyup=\""+this.g+".doChangeInfo();\" id=\""+this.n+"AN\" type=\"text\" value=\""+"\"><br>");
s+=("<label class=\"mxCP\" for=\""+this.n+"CP\">"+this.local("Copyright:")+" </label><input class=\"mxCP\" onkeyup=\""+this.g+".doChangeInfo();\" id=\""+this.n+"CP\" type=\"text\" value=\""+"\"><br>");
s+=("<label class=\"mxSO\" for=\""+this.n+"SO\">"+this.local("Source:")+" </label><input class=\"mxSO\" onkeyup=\""+this.g+".doChangeInfo();\" id=\""+this.n+"SO\" type=\"text\" value=\""+"\"><br>");
s+=("<label class=\"mxUS\" for=\""+this.n+"US\">"+this.local("User:")+" </label><input class=\"mxUS\" onkeyup=\""+this.g+".doChangeInfo();\" id=\""+this.n+"US\" type=\"text\" value=\""+"\"><br>");
s+="</div>";
return s;
};
mxG.G.prototype.doChangeInfo=function()
{
if (this.infoBoxOn) this.getInfoFromBox();
}
mxG.G.prototype.putInfoInBox=function()
{
var p,pm,IX=["EV","RO","DT","PC","PB","BR","PW","WR","HA","KM","RE","GC","RU","TM","OT","AN","CP","SO","US","GN","BT","WT","ON"];
pm=IX.length;
for (p=0;p<pm;p++)
if (this.getE(IX[p]))
{
if (IX[p]=="RE")
{
this.decodeResult(this.getInfo("RE"));
this.getE("RE").value=this.getInfo("RE");
if (this.getE("WN")) this.getE("WN").value=this.WN;
if (this.getE("HW")) this.getE("HW").value=this.HW;
if (this.getE("SC")) this.getE("SC").value=this.SC;
}
else this.getE(IX[p]).value=this.getInfo(IX[p]);
}
};
mxG.G.prototype.getInfoFromBox=function()
{
var p,pm,v,IX=["EV","RO","DT","PC","PB","BR","PW","WR","HA","KM","RE","GC","RU","TM","OT","AN","CP","SO","US","GN","BT","WT","ON"];
pm=IX.length;
for (p=0;p<pm;p++)
{
if (IX[p]=="RE") this.encodeResult();
if (this.getE(IX[p])&&(v=this.getE(IX[p]).value)) this.rN.KidOnFocus().Set(IX[p],v);
else this.popInfo(IX[p]);
}
};
mxG.G.prototype.doInfoOK=function()
{
this.getInfoFromBox();
this.hideGBox("ShowInfo");
};
mxG.G.prototype.doInfo=function()
{
if (this.gBox=="ShowInfo") {this.hideGBox("ShowInfo");return;}
var f;
if (!this.getE("ShowInfoDiv"))
{
var s="<div class=\"mxShowContentDiv\" id=\""+this.n+"ShowInfoContentDiv\"></div>";
s+="<div class=\"mxOKDiv\">";
s+="<button type=\"button\" onclick=\""+this.g+".doInfoOK()\"><span>"+this.local("OK")+"</span></button>";
s+="<button type=\"button\" onclick=\""+this.g+".hideGBox('ShowInfo')\"><span>"+this.local("Cancel")+"</span></button>";
s+="</div>";
this.createGBox("ShowInfo").innerHTML=s;
this.getE("ShowInfoContentDiv").innerHTML=this.buildInfo();
f=1;
}
this.showGBox("ShowInfo");
this.putInfoInBox(); 
};
mxG.G.prototype.initInfo=function()
{
if (this.infoBoxOn) this.getE("InfoContentDiv").innerHTML=this.buildInfo();
};
mxG.G.prototype.updateInfo=function()
{
if (this.infoBoxOn) this.putInfoInBox();
this.refreshInfo();
};
mxG.G.prototype.refreshInfo=function()
{
if (this.infoBoxOn)
{
if (this.adjustInfoWidth) this.adjust("Info","Width",this.adjustInfoWidth);
if (this.adjustInfoHeight) this.adjust("Info","Height",this.adjustInfoHeight);
}
};
mxG.G.prototype.createInfo=function()
{
if (this.infoBoxOn||this.infoBtnOn)
{
this.write("<div class=\"mxInfoDiv\" id=\""+this.n+"InfoDiv\">");
if (this.infoBoxOn) this.write("<div class=\"mxInfoContentDiv\" id=\""+this.n+"InfoContentDiv\"></div>");
if (this.infoBtnOn) this.addBtn({n:"Info",v:this.label("Info","infoLabel")});
this.write("</div>");
}
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
if (typeof mxG.G.prototype.createFile=='undefined'){
mxG.Z.fr["New"]="Nouveau";
mxG.Z.fr["Open"]="Ouvrir";
mxG.Z.fr["Close"]="Fermer";
mxG.Z.fr["Save"]="Enregistrer";
mxG.Z.fr["Save on your device"]="Enregistrer sur votre machine";
mxG.Z.fr["Send"]="Envoyer";
mxG.Z.fr["Send by email"]="Envoyer par email";
mxG.Z.fr["Goban size"]="Taille du goban";
mxG.Z.fr["Email:"]="Email :";
mxG.Z.fr["Create"]="Créer";
mxG.Z.fr["Add"]="Ajouter";
mxG.Z.fr["OK"]="OK";
mxG.Z.fr["Cancel"]="Annuler";
mxG.Z.fr["Values between 5 and 19:"]="Valeurs entre 5 et 19 :";
mxG.Z.fr["Values between 1 and 52:"]="Valeurs entre 1 et 52 :";
mxG.Z.fr["Click here to open a sgf file"]="Cliquer ici pour ouvrir un fichier sgf";
mxG.Z.fr["File name:"]="Nom du fichier :";
mxG.Z.fr["Your browser cannot do this!"]="Votre navigateur ne peut pas faire ça !";
mxG.Z.fr["Error"]="Erreur";
mxG.Z.fr["Untitled"]="SansTitre";
mxG.Z.fr["This is not a sgf file!"]="Ce n'est pas un fichier sgf !";
mxG.G.prototype.cleanUpSZ=function(s)
{
var a,r;
s=s.replace(/\s/g,"");
a=s.match(/^([0-9]+)([x:]([0-9]+))?$/);
if (a)
{
r=Math.min(this.szMax,Math.max(this.szMin,a[1]-0));
if (a[3]) r+=":"+Math.min(52,Math.max(1,a[3]-0));
}
else r="19";
return r;
};
mxG.G.prototype.doNewOK=function(a)
{
var aST=this.getInfo("ST"),aSZ=this.getE("DimensionInput").value,aN;
if (a=="create")
{
if (this.getE("WindowMenuDiv"))
{
this.rN.cN=this.cN;
this.rNs.push(this.rN=new mxG.N(null,null,null));
}
else 
{
this.rN.Kid=[];
this.rN.Focus=0;
}
this.rN.sgf="";
}
aN=this.rN.N("FF","4");
aN.P["CA"]=["UTF-8"];
aN.P["GM"]=["1"];
aN.P["SZ"]=[this.cleanUpSZ(aSZ)];
aN.P["AP"]=["maxiGos:"+mxG.V];
if (parseInt(aST)) aN.P["ST"]=[aST];
this.backNode(aN);
if (this.hasC("Tree")) this.initTree();
this.hideGBox("New");
};
mxG.G.prototype.doNew=function()
{
if (this.hasC("Menu")) this.toggleMenu("File",0);
if (this.gBox=="New") {this.hideGBox("New");return;}
if (!this.getE("NewDiv"))
{
var s="";
s+="<div class=\"mxShowContentDiv\">";
s+="<h1>"+this.local("Goban size")+"</h1>";
s+="<div class=\"mxP\">";
s+="<label for=\""+this.n+"DimensionInput\">"+this.local("Values between "+this.szMin+" and "+this.szMax+":")+"</label>";
s+=" <input id=\""+this.n+"DimensionInput\" name=\""+this.n+"DimensionInput\" type=\"text\" value=\""+this.DX+"x"+this.DY+"\" size=\"5\">";
s+="</div>";
s+="</div>";
s+="<div class=\"mxOKDiv\">";
s+="<button type=\"button\" onclick=\""+this.g+".doNewOK('create')\"><span>"+this.local("Create")+"</span></button>";
s+="<button type=\"button\" onclick=\""+this.g+".doNewOK('add')\"><span>"+this.local("Add")+"</span></button>";
s+="<button type=\"button\" onclick=\""+this.g+".hideGBox('New')\"><span>"+this.local("Cancel")+"</span></button>";
s+="</div>";
this.createGBox("New").innerHTML=s;
}
else this.getE("DimensionInput").value=this.DX+"x"+this.DY;
this.showGBox("New");
};
mxG.G.prototype.doOpenOK=function()
{
var a,r,e=this.getE("SgfFileInputAlertDiv");
r=new FileReader();
r.gos=this;
r.f=this.getE("SgfFile").files[0];
if (e)
{
if ((r.f.name?r.f.name:r.f.fileName).match(/\.sgf$/)) e.innerHTML="";
else {e.innerHTML=this.local("This is not a sgf file!");return;}
}
r.onload=function(evt)
{
var s,m,c;
s=evt.target.result;
if (!this.c)
{
if (m=s.match(/CA\[([^\]]*)\]/)) c=m[1].toUpperCase();else c="ISO-8859-1";
if (c!="UTF-8")
{
this.c=c;
this.readAsText(this.f,c);
return;
}
}
if (this.gos.getE("WindowMenuDiv"))
{
this.gos.rN.cN=this.gos.cN;
this.gos.rNs.push(this.gos.rN=new mxG.N(null,null,null));
} 
this.mayHaveExtraTags=0;
new mxG.P(this.gos,s);
this.gos.backNode(this.gos.rN.KidOnFocus());
if (this.gos.hasC("Tree")) this.gos.initTree();
this.gos.rN.sgf=(this.f.name?this.f.name:this.f.fileName);
if (this.gos.openOnly) this.gos.updateAll();
else this.gos.hideGBox("Open");
};
r.readAsText(r.f);
};
mxG.G.prototype.doOpen=function()
{
var s="";
if (this.hasC("Menu")) this.toggleMenu("File",0);
if (this.gBox=="Open") {this.hideGBox("Open");return;}
if (!this.getE("OpenDiv")) this.createGBox("Open");
s+="<div class=\"mxShowContentDiv\">";
s+="<h1>"+this.local("Open")+"</h1>";
if (mxG.CanOpen())
{
s+="<div class=\"mxP\">";
s+="<div id=\""+this.n+"SgfFileInputAlertDiv\" class=\"mxErrorDiv\"></div>";
s+="<button type=\"button\" id=\""+this.n+"SgfFileInput\" onclick=\""+"document.getElementById('"+this.n+"SgfFile"+"').click();\"><span>"+this.local("Click here to open a sgf file")+"</span></button>";
s+="</div>";
s+="</div>";
s+="<div class=\"mxOKDiv\">";
s+="<input type=\"file\" style=\"visibility:hidden;position:fixed;\" id=\""+this.n+"SgfFile\" onchange=\""+this.g+".doOpenOK()\">";
s+="<button type=\"button\" onclick=\""+this.g+".hideGBox('Open')\"><span>"+this.local("Close")+"</span></button>";
s+="</div>";
}
else
{
s+="<div class=\"mxP\">";
s+=this.local("Your browser cannot do this!");
s+="</div>";
s+="</div>";
s+="<div class=\"mxOKDiv\">";
s+="<button type=\"button\" onclick=\""+this.g+".hideGBox('Open')\"><span>"+this.local("Close")+"</span></button>";
s+="</div>";
}
this.getE("OpenDiv").innerHTML=s; 
this.showGBox("Open");
};
mxG.G.prototype.doClose=function()
{
var k,km=(this.rNs?this.rNs.length:1),n=0;
if (this.hasC("Menu")) this.toggleMenu("File",0);
if (km==1)
{
this.mayHaveExtraTags=0;
new mxG.P(this,this.so);
this.rN.sgf="";
this.backNode(this.rN.KidOnFocus());
}
else
{
k=this.rNs.indexOf(this.rN);
if (k>-1) this.rNs.splice(k,1);
this.rN=this.rNs[0];
this.backNode(this.rN.cN);
}
if (this.hasC("Tree")) this.initTree();
this.updateAll();
};
mxG.G.prototype.doSaveOK=function()
{
var f=this.getE("SaveFileName").value;
if (f)
{
if (!f.match(/\.sgf$/)) f+=".sgf";
this.rN.sgf=f;
this.getE("SaveFileName").value=f;
this.downloadSgfLocally(f);
this.getE("SaveForm").submit(); 
}
this.hideGBox("Save");
};
mxG.G.prototype.doSave=function()
{
var e,s,k,km,i,m;
if (this.hasC("Menu")) this.toggleMenu("File",0);
if (!this.canSgfDownloadLocally()) {this.popupSgf();return;}
if (this.gBox=="Save") {this.hideGBox("Save");return;}
if (!this.getE("SaveDiv"))
{
s="<form id=\""+this.n+"SaveForm\" action=\"javascript:void(0)\" method=\"post\">";
s+="<div class=\"mxShowContentDiv\">";
s+="<h1>"+this.local("Save on your device")+"</h1>";
s+="<div class=\"mxP\"><label for=\""+this.n+"SaveFileName\">"+this.local("File name:")+" </label>";
s+="<input id=\""+this.n+"SaveFileName\" name=\"FileName\" type=\"text\" value=\"\" size=\"32\">";
s+="</div>";
s+="</div>";
s+="<div class=\"mxOKDiv\">";
s+="<button type=\"button\" onclick=\""+this.g+".doSaveOK()\"><span>"+this.local("OK")+"</span></button>";
s+="<button type=\"button\" onclick=\""+this.g+".hideGBox('Save')\"><span>"+this.local("Cancel")+"</span></button>";
s+="</div>";
s+="</form>";
this.createGBox("Save").innerHTML=s;
}
this.getE("SaveFileName").value=this.rN.sgf?this.rN.sgf:(this.local("Untitled")+".sgf");
this.showGBox("Save");
};
mxG.G.prototype.doSendOK=function()
{
var m='mailto:'+this.getE("SendEmail").value+'?subject=maxiGos&body='+encodeURIComponent(this.buildSgf());
window.location.href=m;this.hideGBox("Send");
this.getE("SendForm").submit(); 
};
mxG.G.prototype.doSend=function()
{
if (this.hasC("Menu")) this.toggleMenu("File",0);
if (this.gBox=="Send") {this.hideGBox("Send");return;}
if (!this.getE("SendDiv"))
{
var s="<form id=\""+this.n+"SendForm\" action=\"javascript:void(0)\" method=\"post\">";
s+="<div class=\"mxShowContentDiv\">";
s+="<h1>"+this.local("Send by email")+"</h1>";
s+="<div class=\"mxP\"><label for=\""+this.n+"Email\">"+this.local("Email:")+" </label>";
s+="<input id=\""+this.n+"SendEmail\" name=\""+this.n+"SendEmail\" type=\"text\" value=\"\" size=\"40\"></div>";
s+="</div>";
s+="<div class=\"mxOKDiv\">";
s+="<button type=\"button\" onclick=\""+this.g+".doSendOK()\"><span>"+this.local("OK")+"</span></button>";
s+="<button type=\"button\" onclick=\""+this.g+".hideGBox('Send')\"><span>"+this.local("Cancel")+"</span></button>";
s+="</div>";
s+="</form>";
this.createGBox("Send").innerHTML=s;
}
this.showGBox("Send");
};
mxG.G.prototype.addFileBtns=function()
{
if (!this.hideNew) this.addBtn({n:"New",v:this.local("New")});
if (!this.hideOpen) this.addBtn({n:"Open",v:this.local("Open")});
if (!this.hideClose) this.addBtn({n:"Close",v:this.local("Close")});
if (!this.hideSave) this.addBtn({n:"Save",v:this.local("Save")});
if (!this.hideSend) this.addBtn({n:"Send",v:this.local("Send")});
};
mxG.G.prototype.createFile=function()
{
var k=this.k;
if (!this.szMin) this.szMin=1;
if (!this.szMax) this.szMax=52;
if (this.fileBoxOn&&!(this.openOnly&&!mxG.CanOpen()))
{
this.write("<div class=\"mxFileDiv\" id=\""+this.n+"FileDiv\">");
if (this.openOnly)
{
this.write("<button type=\"button\" id=\""+this.n+"SgfFileInput\" onclick=\""+"document.getElementById('"+this.n+"SgfFile"+"').click();\"><span>"+this.local("Click here to open a sgf file")+"</span></button>");
this.write("<input type=\"file\" style=\"visibility:hidden;position:fixed;\" id=\""+this.n+"SgfFile\" onchange=\""+this.g+".doOpenOK()\">");
}
else this.addFileBtns();
this.write("</div>");
}
if (this.alone) window.addEventListener("unload",function(ev){if (mxG.D[k].sgfPopup&&!mxG.D[k].sgfPopup.closed) mxG.D[k].sgfPopup.close();},false);
};
}
if (typeof mxG.G.prototype.createView=='undefined'){
mxG.Z.fr["2d/3d"]="2d/3d";
mxG.Z.fr["Zoom+"]="Agrandir";
mxG.Z.fr["No zoom"]="Normal";
mxG.Z.fr["Zoom-"]="Réduire";
mxG.Z.fr["Colors"]="Couleurs";
mxG.Z.fr["Default goban background"]="Fond du goban par défault";
mxG.Z.fr["Customized goban background"]="Fond du goban personnalisé";
mxG.Z.fr["Goban background:"]="Fond du goban :";
mxG.Z.fr["Line color:"]="Couleur des lignes :";
mxG.Z.fr["Variation on focus color:"]="Couleur de la variation ayant le focus :";
mxG.G.prototype.doColorsOK=function()
{
var t,gbkt,gbk,lc,vofc;
gbkt=this.getE("GobanBkRadio1Input").checked?1:2;
if (gbkt==1) gbk=this.configGobanBk;
else
{
gbk=this.getE("GobanBkInput").value;
if (!gbk) gbk=this.configGobanBk;
}
lc=this.getE("LineColorInput").value;
vofc=this.getE("VariationOnFocusColorInput").value;
if (gbk!=this.gobanBk)
{
this.gobanBk=gbk;
t=gbk.match(/^url\(/)?"image":"color";
a=(t=="image")?"color":"image";
mxG.AddCssRule("#"+this.n+"GobanCanvas {background-"+a+":none;}");
mxG.AddCssRule("#"+this.n+"GobanCanvas {background-"+t+":"+gbk+";}");
}
this.lineColor=lc;
this.variationOnFocusColor=vofc;
this.hasToDrawWholeGoban=1;
this.hideGBox("Colors");
this.getE("ColorsForm").submit(); 
};
mxG.G.prototype.doColors=function()
{
var bk,s,e;
if (this.hasC("Menu")) this.toggleMenu("View",0);
if (this.gBox=="Colors") {this.hideGBox("Colors");return;}
if (!this.getE("ColorsDiv"))
{
s="<form id=\""+this.n+"ColorsForm\" action=\"javascript:void(0)\" method=\"post\">";
s+="<div class=\"mxShowContentDiv\">";
s+="<h1>"+this.local("Colors")+" (css)</h1>";
s+="<div class=\"mxP\">";
s+="<input class=\"mxGobanBkRadioInput\" name=\"GobanBkRadioInput\" value=\"1\" type=\"radio\" id=\""+this.n+"GobanBkRadio1Input\">";
s+="<label class=\"mxGobanBkRadioInput\" for=\""+this.n+"GobanBkRadio1Input\">"+this.local("Default goban background")+" </label>";
s+="<br>";
s+="<input class=\"mxGobanBkRadioInput\" name=\"GobanBkRadioInput\" value=\"2\" type=\"radio\" id=\""+this.n+"GobanBkRadio2Input\">";
s+="<label class=\"mxGobanBkRadioInput\" for=\""+this.n+"GobanBkRadio2Input\">"+this.local("Customized goban background")+" </label>";
s+="<br><br>";
s+="<label for=\""+this.n+"GobanBkInput\">"+this.local("Goban background:")+" </label>";
s+="<input type=\"text\" id=\""+this.n+"GobanBkInput\">";
s+="<br><br>";
s+="<label for=\""+this.n+"LineColorInput\">"+this.local("Line color:")+" </label>";
s+="<input type=\"text\" id=\""+this.n+"LineColorInput\">";
s+="<br><br>";
s+="<label for=\""+this.n+"VariationOnFocusColorInput\">"+this.local("Variation on focus color:")+" </label>";
s+="<input type=\"text\" id=\""+this.n+"VariationOnFocusColorInput\">";
s+="</div>";
s+="</div>";
s+="<div class=\"mxOKDiv\">";
s+="<button type=\"button\" onclick=\""+this.g+".doColorsOK()\"><span>"+this.local("OK")+"</span></button>";
s+="<button type=\"button\" onclick=\""+this.g+".hideGBox('Colors')\"><span>"+this.local("Cancel")+"</span></button>";
s+="</div>";
s+="</form>";
this.createGBox("Colors").innerHTML=s;
}
if (this.configGobanBk===undefined)
{
bk=mxG.GetStyle(this.gcn,"backgroundImage");
if (!bk||(bk=="none")) bk=mxG.GetStyle(this.gcn,"backgroundColor");
this.configGobanBk=bk;
this.gobanBk=bk;
}
if (e=this.getE("GobanBkRadio1Input")) e.checked=(this.gobanBk==this.configGobanBk);
if (e=this.getE("GobanBkRadio2Input")) e.checked=(this.gobanBk!=this.configGobanBk);
this.getE("GobanBkInput").value=((this.gobanBk==this.configGobanBk)?"":this.gobanBk);
this.getE("LineColorInput").value=this.lineColor;
this.getE("VariationOnFocusColorInput").value=(this.variationOnFocusColor?this.variationOnFocusColor:"");
this.showGBox("Colors");
};
mxG.G.prototype.doZoom=function(s)
{
var e=this.gcn,n=5,d=this.d,d2;
if (this.hasC("Menu")) this.toggleMenu("View",0);
if (!this.d0) this.d0=d;
do
{
n++;
e.style.fontSize=n+"px";
d2=2*Math.floor(mxG.GetPxStyle(e,"fontSize")*3/4)+1;
if ((s=="+")&&((d+2)<=d2)) break;
if ((s=="-")&&((d-2)<=d2)) break;
if ((s=="=")&&(this.d0<=d2)) break;
} while (n<42);
this.refreshAll();
};
mxG.G.prototype.doZoomPlus=function(){this.doZoom("+");};
mxG.G.prototype.doNoZoom=function(){this.doZoom("=");};
mxG.G.prototype.doZoomMinus=function(){this.doZoom("-");};
mxG.G.prototype.doIn3d=function()
{
if (this.hasC("Menu")) this.toggleMenu("View",0);
this.in3dOn=this.in3dOn?0:1;
var e=this.getE("GlobalBoxDiv");
e.className=e.className.replace((this.in3dOn?"mxIn2d":"mxIn3d"),(this.in3dOn?"mxIn3d":"mxIn2d"));
this.hasToDrawWholeGoban=1;
this.exD=0;
this.d=0;
this.setD();
if (this.hasC("Tree")) {this.hasToDrawTree=this.hasToDrawTree|1;this.dT=0;}
if (this.hasC("Edit")) this.exEts=0;
this.refreshAll();
};
mxG.G.prototype.addViewBtns=function()
{
if (!this.hideViewIn3dBtn) this.addBtn({n:"In3d",v:this.local("2d/3d")});
if (!this.hideViewZoomPlusBtn) this.addBtn({n:"ZoomPlus",v:this.local("Zoom+")});
if (!this.hideViewNoZoomBtn) this.addBtn({n:"NoZoom",v:this.local("No zoom")});
if (!this.hideViewZoomMinusBtn) this.addBtn({n:"ZoomMinus",v:this.local("Zoom-")});
if (!this.hideViewColorsBtn) this.addBtn({n:"Colors",v:this.local("Colors")});
};
mxG.G.prototype.createView=function()
{
if (this.viewBoxOn)
{
this.write("<div class=\"mxViewDiv\" id=\""+this.n+"ViewDiv\">");
this.addViewBtns();
this.write("</div>");
}
};
}
if (typeof mxG.G.prototype.createMenu=='undefined'){
mxG.Z.fr["File"]="Fichier";
mxG.Z.fr["Edit"]="Édition";
mxG.Z.fr["Cut"]="Couper";
mxG.Z.fr["Copy"]="Copier";
mxG.Z.fr["Paste"]="Coller";
mxG.Z.fr["View"]="Affichage";
mxG.Z.fr["Window"]="Fenêtre";
mxG.Z.fr["Untitled"]="SansTitre";
mxG.G.prototype.toggleMenu=function(m,s)
{
if (this.toggleMenuTimeout) {clearTimeout(this.toggleMenuTimeout);this.toggleMenuTimeout=0;}
if (s)
{
this.currentMenu=m;this.getE(m+"SubMenuDiv").style.display="block";
this.toggleMenuTimeout=setTimeout("mxG.D["+this.k+"].toggleMenu(\""+m+"\",0)",9999);
}
else
{
this.currentMenu="";
this.getE(m+"SubMenuDiv").style.display="none";
}
};
mxG.G.prototype.doMenu=function(m)
{
var c=this.currentMenu;
if (this.gBox) this.hideGBox(this.gBox);
if (c) {this.toggleMenu(c,0);if (m==c) return;}
this.toggleMenu(m,1);
};
mxG.G.prototype.doFile=function(){this.doMenu("File");};
mxG.G.prototype.doEdit=function(){this.doMenu("Edit");};
mxG.G.prototype.doView=function(){this.doMenu("View");};
mxG.G.prototype.doSwitchWindow=function(k)
{
this.toggleMenu("Window",0);
this.rN.cN=this.cN;
this.rN=this.rNs[k];
this.backNode(this.rN.cN?this.rN.cN:this.rN.Kid[0]);
if (this.hasC("Tree")) this.initTree();
this.updateAll();
};
mxG.G.prototype.doWindow=function()
{
var k,km=this.rNs.length,s="",b={};
for (k=0;k<km;k++)
{
b.n="Win"+k;
if (this.rNs[k].sgf) b.v=this.rNs[k].sgf.replace(/\.sgf$/,"");else b.v=this.local("Untitled");
s+="<button class=\"mxBtn"+((this.rNs[k]==this.rN)?" mxCoched":" mxCochable")+"\" type=\"button\" autocomplete=\"off\" id=\""+this.n+b.n+"Btn\" onclick=\""+this.g+".doSwitchWindow("+k+");\">";
s+="<span>"+b.v+"</span>";
s+="</button>";
}
this.getE("WindowSubMenuDiv").innerHTML=s;
this.doMenu("Window");
};
mxG.G.prototype.createMenu=function()
{
var a=(this.menus?this.menus.split(/[\s]*[,][\s]*/):[]),m,k,km=a.length;
this.rNs=[this.rN];
this.write("<div class=\"mxMenuDiv\" id=\""+this.n+"MenuDiv\">");
for (k=0;k<km;k++)
{
m=a[k];
this.write("<div class=\"mxOneMenuDiv\" id=\""+this.n+m+"MenuDiv\">");
this.addBtn({n:m,v:this.local(m)});
this.write("<div style=\"z-index:20;\" class=\"mxSubMenuDiv\" id=\""+this.n+m+"SubMenuDiv\">");
if (this["add"+m+"Btns"]) this["add"+m+"Btns"]();
this.write("</div></div>");
}
this.write("</div>");
};
}
if (typeof mxG.G.prototype.createPass=='undefined'){
mxG.Z.fr["Pass"]="Passe";
mxG.G.prototype.doPass=function()
{
if (this.hasC("Edit")) this.checkEdit(0,0);
else if (this.hasC("Variations")&&this.canPlaceVariation) this.checkVariation(0,0);
else if (this.hasC("Guess")) this.checkGuess(0,0);
};
mxG.G.prototype.isPass=function(aN)
{
var s,x,y;
if (aN.P["B"]||aN.P["W"])
{
s=(aN.P["B"]?aN.P["B"][0]:aN.P["W"][0]);
if (s.length==2)
{
x=s.c2n(0);
y=s.c2n(1);
if ((x<1)||(y<1)||(x>this.dimX)||(y>this.dimY)) {x=0;y=0;}
}
else {x=0;y=0;}
return !(x||y);
}
return 0;
};
mxG.G.prototype.updatePass=function()
{
var aN=0,k,km,status,look=0,s,e=this.getE("PassBtn");
if (!e) return;
status=this.isPass(this.cN)?1:0;
if (!(this.styleMode&2))
{
if (this.styleMode&1) aN=this.cN.Dad;
else aN=this.cN;
}
if (aN)
{
km=aN.Kid.length;
if (km)
{
if (this.styleMode&1) {if (km>1) look=1;}
else look=1;
}
}
if (look) for (k=0;k<km;k++) if (this.isPass(aN.Kid[k])) status=status|2;
aN=this.cN.KidOnFocus();
if (aN&&this.isPass(aN)) status=status|4;
if (this.canPassOnlyIfPassInSgf)
{
if (status&2) this.enableBtn("Pass");
else this.disableBtn("Pass");
}
s="mxBtn mxPassBtn";
if (status&1) s+=" mxJustPlayedPassBtn";
if (status&2) s+=" mxOnVariationPassBtn";
if (status&4) s+=" mxOnFocusPassBtn";
e.className=s;
if (this.gBox) this.disableBtn("Pass");else this.enableBtn("Pass");
};
mxG.G.prototype.createPass=function()
{
if (this.passBtnOn)
{
this.write("<div class=\"mxPassDiv\" id=\""+this.n+"PassDiv\">");
this.addBtn({n:"Pass",v:this.label("Pass","passLabel")});
this.write("</div>");
}
};
}
if (typeof mxG.G.prototype.createImage=='undefined'){
mxG.Z.fr["Image"]="Image";
mxG.G.prototype.showImage=function()
{
if (this.im4Show&&!this.im4Show.OK) {setTimeout(this.g+".showImage()",100);return;}
var aW,aH,html,hs=0;
html='<!DOCTYPE HTML>\n';
html+='<html><head>';
html+='<title>maxiGos-Image</title>';
html+='</head><body style="margin:16px;padding:0;text-align:center;">';
html+='<img id="gcni" style="display:block;margin:0 auto;" alt="maxiGos" src="'+this.gcni.toDataURL("image/png")+'">';
html+='</body></html>';
aW=this.gobanCnWidth()+48+(hs?16:0);
aH=this.gobanCnHeight()+48+(hs?16:0)+hs;
if (this.imagePopup&&!this.imagePopup.closed) this.imagePopup.close();
this.imagePopup=window.open('','maxiGosImage','width='+aW+',height='+aH+',scrollbars=no');
this.imagePopup.document.open();
this.imagePopup.document.write(html);
this.imagePopup.document.close();
};
mxG.G.prototype.setImageWithBkColor=function(c)
{
this.im4Show="";
this["gcxi"].save();
this["gcxi"].fillStyle=c;
this["gcxi"].fillRect(0,0,this["gcni"].width,this["gcni"].height);
this["gcxi"].restore(); 
this["gcxi"].drawImage(this.gcn,0,0);
};
mxG.G.prototype.setImageWithBkPattern=function(bk)
{
var gw=this["gcni"].width,gh=this["gcni"].height,c,s;
this.im4Show=new Image();
this.im4Show.OK=0;
this.im4Show.gos=this;
this.im4Show.onload=function()
{
this.OK=1;
this.gos["gcxi"].save();
this.gos["gcxi"].drawImage(this,0,0,this.gos.gcn.width,this.gos.gcn.height);
this.gos["gcxi"].restore(); 
this.gos["gcxi"].drawImage(this.gos.gcn,0,0);
};
this.im4Show.src=bk;
};
mxG.G.prototype.doImage=function()
{
var aW,aH,html,st,bk,bs,w,cn,cx,bkp=0;
bk=mxG.GetStyle(this.gcn,"backgroundImage");
if (!bk||(bk=="none")) bk=mxG.GetStyle(this.gcn,"backgroundColor");
if (bk.match(/^url\((.*)\)$/))
{
bk=bk.replace(/^url\((.*)\)$/,"$1");
bk=bk.replace(/\"/g,"");
bkp=1;
}
this.gcni=document.createElement('canvas');
this.gcxi=this.gcni.getContext('2d');
this.gcni.width=this.gcn.width;
this.gcni.height=this.gcn.height;
if (bkp) this.setImageWithBkPattern(bk);
else this.setImageWithBkColor(bk);
this.showImage();
};
mxG.G.prototype.updateImage=function()
{
if (this.getE("ImageBtn"))
{
if (this.gBox) this.disableBtn("Image");else this.enableBtn("Image");
}
};
mxG.G.prototype.createImage=function()
{
var k=this.k;
if (this.imageBtnOn)
{
this.write("<div class=\"mxImageDiv\" id=\""+this.n+"ImageDiv\">");
this.addBtn({n:"Image",v:this.local("Image")});
this.write("</div>");
}
window.addEventListener("unload",function(ev){if (mxG.D[k].imagePopup&&!mxG.D[k].imagePopup.closed) mxG.D[k].imagePopup.close();},false);
};
}
if (typeof mxG.G.prototype.createSgf=='undefined'){
mxG.Z.fr[" Close "]="Fermer";
mxG.nl2br=function(s)
{
return (s+'').replace(/\r\n|\n\r|\r|\n/g,'<br>');
};
mxG.sgfEsc=function(s)
{
return (s+'').replace(/([^\\\]]?)(\\|])/g,'$1'+"\\"+'$2');
};
mxG.G.prototype.canSgfDownloadLocally=function()
{
var a;
if (this.downloadLocallyIs===undefined)
{
if (this.toCharset!="UTF-8") this.downloadLocallyIs=0;
else this.downloadLocallyIs=(typeof document.createElement('a').download==='string')?1:0;
}
return this.downloadLocallyIs;
};
mxG.G.prototype.mirror=function(s)
{
var c1=s.substr(0,1),c2=s.substr(1,1);
return c1+String.fromCharCode(96+20-c2.c2n());
};
mxG.G.prototype.buildAllSgf=function(aN,only,c)
{
var rc="\n",k,x,y,ym,aText="",first,keep;
if (c===undefined) c=0;
if ((aN.Dad&&(aN.Dad==this.rN))||(aN.Dad&&(aN.Dad.Kid.length>1)))
{
if (only&4) {if ((aN.Dad==this.rN)&&(aN==aN.Dad.KidOnFocus())) aText+="(";}
else if (only&2) {if ((aN.Dad==this.rN)&&(aN==aN.Dad.Kid[0])) aText+="(";}
else if ((aN.Dad==this.rN)&&(aN==aN.Dad.Kid[0])) aText+="(";
else {aText+=(rc+"(");c=0;}
}
if (aN!=this.rN)
{
if (aText[aText.length-1]!="(")
{
if (aN.Dad&&aN.Dad.Dad&&(aN.Dad.Dad==this.rN)) {aText+=rc;c=0;}
else if (c>3) {aText+=rc;c=0;} else c++;
}
first=1;
for (x in aN.P)
{
if (x.match(/^[A-Z]+$/))
{
if (only&1)
{
if ((x=="B")||(x=="W")||(x=="AB")||(x=="AW")||(x=="AE")
||(x=="FF")||(x=="CA")||(x=="GM")||(x=="SZ")
||(x=="EV")||(x=="RO")||(x=="DT")||(x=="PC")
||(x=="PB")||(x=="BR")||(x=="BT")||(x=="PW")||(x=="WR")||(x=="WT")
||(x=="RU")||(x=="TM")||(x=="OT")||(x=="HA")||(x=="KM")||(x=="RE")||(x=="VW"))
keep=1;
else keep=0;
}
else keep=1;
if (keep)
{
if (first) {aText+=";";first=0;} 
if (aN.Dad&&(aN.Dad==this.rN)) {aText+=rc;c=0;}
aText+=x;
ym=aN.P[x].length;
for (y=0;y<ym;y++) aText+=("["+mxG.sgfEsc(aN.P[x][y])+"]");
}
}
}
}
if (aN.Kid&&aN.Kid.length)
{
if (only&4) {if (aN!=this.cN) aText+=this.buildAllSgf(aN.KidOnFocus(),only,c);}
else if (only&2) aText+=this.buildAllSgf(aN.Kid[0],only,c);
else for (k=0;k<aN.Kid.length;k++) aText+=this.buildAllSgf(aN.Kid[k],only,c);
}
if (only&4) {if ((aN.Dad==this.rN)&&(aN==aN.Dad.KidOnFocus())) aText+=")";}
else if (only&2) {if ((aN.Dad==this.rN)&&(aN==aN.Dad.Kid[0])) aText+=")";}
else {if ((aN.Dad&&(aN.Dad==this.rN))||(aN.Dad&&(aN.Dad.Kid.length>1))) aText+=")";}
return aText;
};
mxG.G.prototype.sgfMandatory=function()
{
var p,km=this.rN.Kid.length;
for (var k=0;k<km;k++)
{
p=this.rN.Kid[k].P;
p.FF=["4"];
p.CA=[this.toCharset];
p.GM=["1"];
p.AP=["maxiGos:"+mxG.V];
}
};
mxG.G.prototype.buildSomeSgf=function(only)
{
this.sgfMandatory();
return this.buildAllSgf(this.rN,only,0);
};
mxG.G.prototype.buildSgf=function()
{
this.sgfMandatory();
return this.buildAllSgf(this.rN,(this.sgfSaveCoreOnly?1:0)+(this.sgfSaveMainOnly?2:0),0);
};
mxG.G.prototype.popupSgf=function()
{
if (this.sgfPopup&&!this.sgfPopup.closed) this.sgfPopup.close();
this.sgfPopup=window.open();
this.sgfPopup.document.open();
this.sgfPopup.document.write("<!DOCTYPE html><html><body><pre>\n");
this.sgfPopup.document.write(this.buildSgf());
this.sgfPopup.document.write("\n</pre></body></html>");
this.sgfPopup.document.close();
this.sgfPopup.document.title="Sgf"; 
};
mxG.G.prototype.downloadSgfLocally=function(f)
{
var u,a;
if (this.canSgfDownloadLocally())
{
u="data:application/octet-stream;charset=utf-8,"+encodeURIComponent(this.buildSgf());
a=document.createElement('a');
document.body.appendChild(a); 
a.download=f;
a.href=u;
a.click();
document.body.removeChild(a);
}
else this.popupSgf(); 
};
mxG.G.prototype.doReplaceFromSgf=function()
{
var s=this.getE("ShowSgfTextArea").value;
if (s!=this.sgfBeforeEdit)
{
this.mayHaveExtraTags=0;
new mxG.P(this,this.getE("ShowSgfTextArea").value);
this.backNode(this.rN.KidOnFocus());
if (this.hasC("Tree")) this.initTree();
this.updateAll();
}
this.hideGBox("ShowSgf");
};
mxG.G.prototype.doEditSgf=function()
{
if (this.gBox=="ShowSgf") {this.hideGBox("ShowSgf");return;}
if (!this.getE("ShowSgfDiv"))
{
var s="";
s+="<div class=\"mxShowContentDiv\">";
s+="<textarea id=\""+this.n+"ShowSgfTextArea\"></textarea>";
s+="</div>";
s+="<div class=\"mxOKDiv\">";
s+="<button type=\"button\" onclick=\""+this.g+".doReplaceFromSgf()\"><span>"+this.local("OK")+"</span></button>";
s+="<button type=\"button\" onclick=\""+this.g+".hideGBox('ShowSgf')\"><span>"+this.local("Cancel")+"</span></button>";
s+="</div>";
this.createGBox("ShowSgf").innerHTML=s;
}
this.sgfBeforeEdit=this.buildSgf();
this.getE("ShowSgfTextArea").value=this.sgfBeforeEdit;
this.showGBox("ShowSgf");
};
mxG.G.prototype.doSgf=function()
{
if (this.noSgfDialog)
{
this.downloadSgfLocally(this.rN.sgf?this.rN.sgf:"maxiGos.sgf");
}
else if (this.allowEditSgf) this.doEditSgf();
else
{
if (this.gBox=="ShowSgf") {this.hideGBox("ShowSgf");return;}
if (!this.getE("ShowSgfDiv"))
{
var s="";
s+="<div class=\"mxShowContentDiv\">";
s+="<div class=\"mxP\" id=\""+this.n+"ShowSgfP\"></div>";
s+="</div>";
s+="<div class=\"mxOKDiv\">";
s+="<button type=\"button\" onclick=\""+this.g+".hideGBox('ShowSgf')\"><span>"+this.local(" Close ")+"</span></button>";
s+="</div>";
this.createGBox("ShowSgf").innerHTML=s;
}
this.getE("ShowSgfP").innerHTML=mxG.nl2br(this.htmlProtect(this.buildSgf()));
this.showGBox("ShowSgf");
}
};
mxG.G.prototype.addSgfBtn=function()
{
this.addBtn({n:"Sgf",v:this.label("SGF","sgfLabel")});
};
mxG.G.prototype.createSgf=function()
{
var p,fromCharset,toCharset;
if (this.toCharset===undefined) this.toCharset="UTF-8";
if (this.sgfBtnOn)
{
this.write("<div class=\"mxSgfDiv\" id=\""+this.n+"SgfDiv\">");
this.addSgfBtn();
this.write("</div>");
}
};
}
if (typeof mxG.G.prototype.createScore=='undefined'){
mxG.Z.fr["Score"]="Score";
mxG.Z.fr["Black:"]="Noir :";
mxG.Z.fr["White:"]="Blanc :";
mxG.Z.fr["chinese rules"]="règle chinoise";
mxG.Z.fr["AGA rules"]="règle AGA";
mxG.Z.fr["Ing rules"]="règle Ing";
mxG.Z.fr["New-Zeland rules"]="règle néo-zélandaise";
mxG.Z.fr["japanese rules"]="règle japonaise";
mxG.Z.fr["territory"]="territoire";
mxG.Z.fr["prisoners"]="prisonniers";
mxG.Z.fr["pass"]="passe";
mxG.Z.fr["stones"]="pierres";
mxG.Z.fr["last move"]="dernier coup";
mxG.Z.fr["komi"]="komi ";
mxG.G.prototype.getOwner=function(x,y)
{
var xy;
if (this.gor.inGoban(x,y))
{
xy=this.xy(x,y);
if (this.visited4GetOwner[xy]) return 0;
this.visited4GetOwner[xy]=1;
if (this.scoreBan[x][y].modified=="E")
{
if (this.scoreBan[x][y].forced=="B") return 1;
else if (this.scoreBan[x][y].forced=="W") return 2;
else return this.getOwner(x-1,y)|this.getOwner(x+1,y)|this.getOwner(x,y-1)|this.getOwner(x,y+1);
}
if (this.scoreBan[x][y].modified=="B") return 1;
if (this.scoreBan[x][y].modified=="W") return 2;
}
return 0;
};
mxG.G.prototype.toggleFriends=function(nat,x,y,enable)
{
var xy,onat;
onat=(nat=="B")?"W":"B";
if (this.gor.inGoban(x,y))
{
xy=this.xy(x,y);
if (this.visited4ToggleFriends[xy]) return;
this.visited4ToggleFriends[xy]=1;
if (this.scoreBan[x][y].initial==onat) return;
if (this.scoreBan[x][y].forced==onat) return;
if (this.scoreBan[x][y].forced==nat) return;
if (this.scoreBan[x][y].initial==nat) this.scoreBan[x][y].modified=(enable?nat:"E");
this.toggleFriends(nat,x-1,y,enable);
this.toggleFriends(nat,x+1,y,enable);
this.toggleFriends(nat,x,y-1,enable);
this.toggleFriends(nat,x,y+1,enable);
}
};
mxG.G.prototype.getTX=function()
{
var TX=["TB","TW"];
var aN,k,aLen,s,x,y,x1,y1,x2,y2,z;
aN=this.cN4Score;
for (z=0;z<7;z++)
{
if (aN.P[TX[z]]) aLen=aN.P[TX[z]].length;else aLen=0;
for (k=0;k<aLen;k++)
{
s=aN.P[TX[z]][k];
if (s.length==2)
{
x=s.c2n(0);
y=s.c2n(1);
this.scoreBan[x][y].marked=(TX[z]=="TB")?"B":"W";
}
else if (s.length==5)
{
x1=s.c2n(0);
y1=s.c2n(1);
x2=s.c2n(3);
y2=s.c2n(4);
for (x=x1;x<=x2;x++)
for (y=y1;y<=y2;y++)
{
this.scoreBan[x][y].marked=(TX[z]=="TB")?"B":"W";
}
}
}
}
};
mxG.G.prototype.removeTX=function(a,b)
{
var k,km,kp,TX=["TB","TW"],aN,v;
aN=this.cN4Score;
v=this.xy2s(a,b);
for (kp=0;kp<TX.length;kp++)
{
if (aN.P[TX[kp]])
{
km=aN.P[TX[kp]].length;
for (k=0;k<km;k++) if (aN.P[TX[kp]][k]==v) break;
if (k<km) aN.TakeOff(TX[kp],k);
}
}
};
mxG.G.prototype.addTX=function(tx,a,b)
{
var aN,v;
aN=this.cN4Score;
v=this.xy2s(a,b);
this.removeTX(a,b);
if (aN.P[tx]) aN.P[tx].push(v);
else aN.P[tx]=[v];
};
mxG.G.prototype.setTX=function()
{
var i,j;
for (i=1;i<=this.DX;i++)
for (j=1;j<=this.DY;j++)
{
switch(this.scoreBan[i][j].computed)
{
case "B":this.addTX("TB",i,j);break;
case "W":this.addTX("TW",i,j);break;
default:this.removeTX(i,j);
}
}
};
mxG.G.prototype.resetTX=function()
{
var i,j;
for (i=1;i<=this.DX;i++)
for (j=1;j<=this.DY;j++)
{
switch(this.scoreBan[i][j].marked)
{
case "B":this.addTX("TB",i,j);break;
case "W":this.addTX("TW",i,j);break;
default:this.removeTX(i,j);
}
}
};
mxG.G.prototype.initScoreBan=function()
{
var i,j,nat;
this.scoreBan=[];
for (i=1;i<=this.DX;i++)
{
this.scoreBan[i]=[];
for (j=1;j<=this.DY;j++)
{
nat=this.gor.getBanNat(i,j);
this.scoreBan[i][j]={initial:nat,modified:nat};
}
}
this.getTX();
this.justInitializedScoreBan=1;
};
mxG.G.prototype.setComputedScoreBan=function()
{
var i,j,r;
if (this.ephemeralScoreOn)
{
for (i=1;i<=this.DX;i++)
for (j=1;j<=this.DY;j++)
{
if (this.scoreBan[i][j].forced) {this.scoreBan[i][j].computed=this.scoreBan[i][j].forced;}
else if (this.scoreBan[i][j].modified!="E") this.scoreBan[i][j].computed="E";
else
{
this.visited4GetOwner=[];
r=this.getOwner(i,j);
if ((r==1)||(r==2))
{
if (this.scoreBan[i][j].forced) {this.scoreBan[i][j].computed=this.scoreBan[i][j].forced;}
else
{
if (r==1) this.scoreBan[i][j].computed="B";
else this.scoreBan[i][j].computed="W";
}
}
else
{
if (this.scoreBan[i][j].forced) {this.scoreBan[i][j].computed=this.scoreBan[i][j].forced;}
else if (this.scoreBan[i][j].initial=="B") this.scoreBan[i][j].computed="W";
else if (this.scoreBan[i][j].initial=="W") this.scoreBan[i][j].computed="B";
else this.scoreBan[i][j].computed="E";
}
}
if (this.justInitializedScoreBan&&this.scoreBan[i][j].marked)
{
if (this.scoreBan[i][j].marked!=this.scoreBan[i][j].computed)
{
this.scoreBan[i][j].forced=this.scoreBan[i][j].marked;
this.scoreBan[i][j].computed=this.scoreBan[i][j].forced;
}
}
}
}
else
{
for (i=1;i<=this.DX;i++)
for (j=1;j<=this.DY;j++)
{
if (this.justInitializedScoreBan&&this.scoreBan[i][j].marked)
this.scoreBan[i][j].forced=this.scoreBan[i][j].marked;
if (this.scoreBan[i][j].forced) {this.scoreBan[i][j].computed=this.scoreBan[i][j].forced;}
else this.scoreBan[i][j].computed="E";
}
}
this.justInitializedScoreBan=0;
};
mxG.G.prototype.computeScore=function()
{
if (!this.ephemeralScoreOn) return;
var sw,sb,i,j,k,s,r,rs,komi,rules,pb,pw,ub,uw,nat,ib,iw,cb,cw,noPrisoner,ew;
sb=0;
sw=0;
ib=0;
iw=0;
for (i=1;i<=this.DX;i++)
for (j=1;j<=this.DY;j++)
{
switch(this.scoreBan[i][j].computed)
{
case "B":sb++;break;
case "W":sw++;break;
}
switch(this.scoreBan[i][j].modified)
{
case "B":ib++;break;
case "W":iw++;break;
}
}
komi=this.getInfo("KM");
if (komi) komi=parseFloat(komi); else komi=5.5;
ub=0;
uw=0;
pw=0;
pb=0;
noPrisoner=0;
for (k=1;k<=this.gor.play;k++)
{
nat=this.gor.nat[k];
if (nat=="B")
{
ub++;
if (!this.gor.x[k]&&!this.gor.y[k]) pb++;
}
else if (nat=="W")
{
uw++;
if (!this.gor.x[k]&&!this.gor.y[k]) pw++;
if (this.gor.act[k]=="A") noPrisoner=1;
}
else ;
}
rules=this.getInfo("RU").toLowerCase();
if ((rules=="chinese")||(rules=="ing")||(rules=="goe")||(rules=="nz")) r="C";
else if (rules=="aga") r="A";
else r="J";
ew=0;
if (noPrisoner)
{
cb=0;
cw=0;
pw=0;
pb=0;
}
else
{
cb=uw-iw-pw;
if ((r=="A")&&this.gor.play&&(this.gor.act[this.gor.play]=="")&&(this.gor.nat[this.gor.play]=="B")) ew=1;
cw=ub-ib-pb;
}
switch(rules)
{
case "chinese":rs=this.local("chinese rules");break;
case "aga":rs=this.local("AGA rules");break;
case "ing":
case "goe":rs=this.local("Ing rules");break;
case "nz":rs=this.local("New-Zeland rules");break;
default:rs=this.local("japanese rules");
}
s="<h1>"+this.local("Score")+" ("+rs+")</h1><div>";
if (r=="J")
{
s+=this.local("Black:")+" "+sb+" ("+this.local("territory")+") "+" + "+cb+" ("+this.local("prisoners")+") "+" = "+(sb+cb);
s+="<br>";
s+=this.local("White:")+" "+sw+" ("+this.local("territory")+") "+" + "+cw+" ("+this.local("prisoners")+") "+" + "+komi+" ("+this.local("komi")+") "+" = "+(sw+cw+komi);
}
else if (r=="A")
{
s+=this.local("Black:")+" "+sb+" ("+this.local("territory")+") "+" + "+cb+" ("+this.local("prisoners")+") "+" + "+pw+" ("+this.local("pass")+") "+" + "+ew+" ("+this.local("last move")+") "+" = "+(sb+cb+pw+ew);
s+="<br>";
s+=this.local("White:")+" "+sw+" ("+this.local("territory")+") "+" + "+cw+" ("+this.local("prisoners")+") "+" + "+pb+" ("+this.local("pass")+") "+" + "+komi+" ("+this.local("komi")+") "+" = "+(sw+cw+pb+komi);
}
else 
{
s+=this.local("Black:")+" "+sb+" ("+this.local("territory")+") "+" + "+ib+" ("+this.local("stones")+") "+" = "+(sb+ib);
s+="<br>";
s+=this.local("White:")+" "+sw+" ("+this.local("territory")+") "+" + "+iw+" ("+this.local("stones")+") "+" + "+komi+" ("+this.local("komi")+") "+" = "+(sw+iw+komi);
}
s+="</div>";
this.scoreComment=s;
};
mxG.G.prototype.getComment4Score=function()
{
return this.scoreComment;
};
mxG.G.prototype.toggleCanPlaceScore=function()
{
var b;
if (this.canPlaceScore)
{
if (this.ephemeralScoreOn)
{
this.getComment=this.exGetComment4Score;
this.resetTX(this.cN4Score);
}
this.cN4Score=0;
this.canPlaceScore=0;
this.canPlaceVariation=this.initialCanPlaceVariationForScore;
this.canPlaceGuess=this.initialCanPlaceGuessForScore;
this.canPlaceSolve=this.initialCanPlaceSolveForScore;
this.canPlaceEdit=this.initialCanPlaceEditForScore;
this.marksAndLabelsOn=this.initialmarksAndLabelsOnForScore;
b=this.getE("ScoreBtn");
if (b) b.classList.remove("mxActivatedScoreBtn");
}
else
{
b=this.getE("ScoreBtn");
if (b) b.classList.add("mxActivatedScoreBtn");
this.cN4Score=this.cN;
if (this.ephemeralScoreOn)
{
this.exGetComment4Score=this.getComment;
this.getComment=this.getComment4Score;
}
this.canPlaceScore=1;
this.initialCanPlaceVariationForScore=(this.canPlaceVariation?1:0);
this.initialCanPlaceGuessForScore=(this.canPlaceGuess?1:0);
this.initialCanPlaceSolveForScore=(this.canPlaceSolve?1:0);
this.initialCanPlaceEditForScore=(this.canPlaceEdit?1:0);
this.initialmarksAndLabelsOnForScore=(this.marksAndLabelsOn?1:0);
this.canPlaceVariation=0;
this.canPlaceGuess=0;
this.canPlaceSolve=0;
this.canPlaceEdit=0;
this.marksAndLabelsOn=1;
this.initScoreBan();
this.setComputedScoreBan();
this.setTX();
}
};
mxG.G.prototype.doScore=function()
{
this.toggleCanPlaceScore();
if (this.canPlaceScore) this.computeScore();
this.updateAll();
};
mxG.G.prototype.possibleOwner=function(x,y)
{
var b,w;
b=0;
w=0;
if (this.gor.inGoban(x-1,y))
{
if ((this.scoreBan[x-1][y].modified=="B")||(this.scoreBan[x-1][y].forced=="B"))  b+=3;
else if ((this.scoreBan[x-1][y].modified=="W")||(this.scoreBan[x-1][y].forced=="W"))  w+=3;
}
if (this.gor.inGoban(x+1,y))
{
if ((this.scoreBan[x+1][y].modified=="B")||(this.scoreBan[x+1][y].forced=="B"))  b+=3;
else if ((this.scoreBan[x+1][y].modified=="W")||(this.scoreBan[x+1][y].forced=="W"))  w+=3;
}
if (this.gor.inGoban(x,y-1))
{
if ((this.scoreBan[x][y-1].modified=="B")||(this.scoreBan[x][y-1].forced=="B"))  b+=3;
else if ((this.scoreBan[x][y-1].modified=="W")||(this.scoreBan[x][y-1].forced=="W"))  w+=3;
}
if (this.gor.inGoban(x,y+1))
{
if ((this.scoreBan[x][y+1].modified=="B")||(this.scoreBan[x][y+1].forced=="B"))  b+=3;
else if ((this.scoreBan[x][y+1].modified=="W")||(this.scoreBan[x][y+1].forced=="W"))  w+=3;
}
if (this.gor.inGoban(x-1,y-1))
{
if ((this.scoreBan[x-1][y-1].modified=="B")||(this.scoreBan[x-1][y-1].forced=="B"))  b++;
else if ((this.scoreBan[x-1][y-1].modified=="W")||(this.scoreBan[x-1][y-1].forced=="W"))  w++;
}
if (this.gor.inGoban(x+1,y-1))
{
if ((this.scoreBan[x+1][y-1].modified=="B")||(this.scoreBan[x+1][y-1].forced=="B"))  b++;
else if ((this.scoreBan[x+1][y-1].modified=="W")||(this.scoreBan[x+1][y-1].forced=="W"))  w++;
}
if (this.gor.inGoban(x-1,y+1))
{
if ((this.scoreBan[x-1][y+1].modified=="B")||(this.scoreBan[x-1][y+1].forced=="B"))  b++;
else if ((this.scoreBan[x-1][y+1].modified=="W")||(this.scoreBan[x-1][y+1].forced=="W"))  w++;
}
if (this.gor.inGoban(x+1,y+1))
{
if ((this.scoreBan[x+1][y+1].modified=="B")||(this.scoreBan[x+1][y+1].forced=="B"))  b++;
else if ((this.scoreBan[x+1][y+1].modified=="W")||(this.scoreBan[x+1][y+1].forced=="W"))  w++;
}
if (b>w) return "B";
else if (w>b) return "W";
else return "B";
};
mxG.G.prototype.checkScore=function(a,b)
{
var po,opo;
if ((this.scoreBan[a][b].initial=="E")||!this.ephemeralScoreOn)
{
if (this.scoreBan[a][b].initial=="E") po=this.possibleOwner(a,b);
else po=(this.scoreBan[a][b].initial=="B")?"W":"B";
opo=(po=="B")?"W":"B";
if (this.scoreBan[a][b].forced)
{
if (this.scoreBan[a][b].computed==po) this.scoreBan[a][b].forced=opo;
else if (this.scoreBan[a][b].computed==opo) this.scoreBan[a][b].forced=0;
else this.scoreBan[a][b].forced=po;
}
else
{
if (this.scoreBan[a][b].computed==po) this.scoreBan[a][b].forced="E";
else if (this.scoreBan[a][b].computed==opo) this.scoreBan[a][b].forced="E";
else this.scoreBan[a][b].forced=po;
}
}
else
{
this.visited4ToggleFriends=[];
if (this.scoreBan[a][b].modified=="E") this.toggleFriends(this.scoreBan[a][b].initial,a,b,1);
else this.toggleFriends(this.scoreBan[a][b].initial,a,b,0);
}
this.setComputedScoreBan();
this.setTX();
this.computeScore();
this.updateAll();
};
mxG.G.prototype.doClickScore=function(ev)
{
if (this.isGobanDisabled()) return;
if (this.canPlaceScore)
{
var c=this.getC(ev);
if (!this.inView(c.x,c.y)) {this.plonk();return;}
this.checkScore(c.x,c.y);
}
};
mxG.G.prototype.doKeydownGobanForScore=function(ev)
{
var c;
if (this.isGobanDisabled()) return;
if (this.canPlaceScore&&this.gobanFocusVisible)
{
c=mxG.GetKCode(ev);
if ((c==13)||(c==32))
{
this.checkScore(this.xFocus,this.yFocus);
ev.preventDefault();
}
}
};
mxG.G.prototype.initScore=function()
{
var e=this.gcn,k=this.k;
e.getMClick=mxG.GetMClick;
e.addEventListener("click",function(ev){mxG.D[k].doClickScore(ev);},false);
if (this.gobanFocus) this.go.addEventListener("keydown",function(ev){mxG.D[k].doKeydownGobanForScore(ev);},false);
};
mxG.G.prototype.updateScore=function()
{
if (this.canPlaceScore&&(this.cN!=this.cN4Score)) this.toggleCanPlaceScore();
if (this.getE("ScoreBtn"))
{
if (this.gBox) this.disableBtn("Score");else this.enableBtn("Score");
}
};
mxG.G.prototype.createScore=function()
{
this.canPlaceScore=0; 
if (this.scoreBtnOn)
{
this.write("<div class=\"mxScoreDiv\" id=\""+this.n+"ScoreDiv\">");
this.addBtn({n:"Score",v:this.local("Score")});
this.write("</div>");
}
};
}
if (typeof mxG.G.prototype.createHelp=='undefined'){
mxG.Z.fr[" Close "]="Fermer";
mxG.Z.fr["Help"]="Aide";
mxG.Z.fr["Help not available!"]="Aide non disponible !";
mxG.Z.fr["Error"]="Erreur";
mxG.G.prototype.downloadHelp=function(L_)
{
var xhr=new XMLHttpRequest(),f,a="helpSource_";
xhr.L_=L_;
xhr.msg="<h1>"+this.local("Help")+"</h1><p>"+this.local("Help not available!")+"</p>";
xhr.gos=this;
xhr.onreadystatechange=function()
{
if (xhr.readyState==4)
{
var e=xhr.gos.getE("ShowHelpContentDiv");
if (xhr.status==200) {e.innerHTML=xhr.responseText;}
else if (xhr.L_=="en") {e.innerHTML=xhr.msg;}
else xhr.gos.downloadHelp("en");
}
};
f=(this[a+L_]||this[a+"en"]||"").split('?')[0];
if (!f||!f.split("/").reverse()[0].match(/help[^\/]*$/))
{
this.getE("ShowHelpContentDiv").innerHTML=xhr.msg;
return;
}
xhr.open("GET",this.path+f,true);
if (xhr.overrideMimeType) xhr.overrideMimeType("text/plain;charset=UTF-8");
xhr.send(null);
};
mxG.G.prototype.buildHelp=function()
{
var h=this["helpData_"+this.l_]||this["helpData_en"];
if (h) this.getE("ShowHelpContentDiv").innerHTML=h;
else this.downloadHelp(this.l_);
};
mxG.G.prototype.doHelp=function()
{
if (this.gBox=="ShowHelp") {this.hideGBox("ShowHelp");return;}
if (!this.getE("ShowHelpDiv"))
{
var b,s="<div class=\"mxShowContentDiv\" id=\""+this.n+"ShowHelpContentDiv\"></div>";
s+="<div class=\"mxOKDiv\">";
s+="<button type=\"button\" onclick=\""+this.g+".hideGBox('ShowHelp')\"><span>"+this.local(" Close ")+"</span></button>";
s+="</div>";
this.createGBox("ShowHelp").innerHTML=s;
this.buildHelp();
}
this.showGBox("ShowHelp");
};
mxG.G.prototype.createHelp=function()
{
if (this.alone&&!this["helpData_"+this.l_]&&!this["helpData_en"]) return;
if (this.helpBtnOn)
{
this.write("<div class=\"mxHelpDiv\" id=\""+this.n+"HelpDiv\">");
this.addBtn({n:"Help",v:this.local("Help")});
this.write("</div>");
}
};
}
if (typeof mxG.G.prototype.createButtons=='undefined'){
mxG.G.prototype.refreshButtons=function()
{
if (this.adjustButtonsWidth) this.adjust("Buttons","Width",this.adjustButtonsWidth);
if (this.adjustButtonsHeight) this.adjust("Buttons","Height",this.adjustButtonsHeight);
};
mxG.G.prototype.createButtons=function()
{
var a=(this.buttons?this.buttons.split(/[\s]*[,][\s]*/):[]),m,k,km=a.length;
this.write("<div class=\"mxButtonsDiv\" id=\""+this.n+"ButtonsDiv\">");
for (k=0;k<km;k++)
{
m=a[k];
if (this["add"+m+"Btn"]) this["add"+m+"Btn"]();
else this.addBtn({n:m,v:this.local(m)});
}
this.write("</div>");
};
}
if (typeof mxG.G.prototype.createEdit=='undefined'){
mxG.Z.fr["Selection"]="Sélection";
mxG.Z.fr["Full/partial view"]="Vue partielle/totale";
mxG.Z.fr["Place a move"]="Placer un coup";
mxG.Z.fr["Add/remove a stone"]="Ajouter/retirer une pierre";
mxG.Z.fr["Cut branch"]="Couper une branche";
mxG.Z.fr["Copy branch"]="Copier une branche";
mxG.Z.fr["Paste branch"]="Coller une branche";
mxG.Z.fr["Label"]="Etiquette";
mxG.Z.fr["Mark"]="Marque";
mxG.Z.fr["Circle"]="Cercle";
mxG.Z.fr["Square"]="Carré;";
mxG.Z.fr["Triangle"]="Triangle";
mxG.Z.fr["Numbering"]="Numérotation";
mxG.Z.fr["As in book"]="Comme dans les livres";
mxG.Z.fr["Indices"]="Indices";
mxG.Z.fr["Variation marks"]="Marques sur les variations";
mxG.Z.fr["Variation style"]="Style des variations";
mxG.Z.fr["Marks and labels"]="Marques et étiquettes";
mxG.Z.fr["Header"]="Entête";
mxG.Z.fr["B"]="L";
mxG.Z.fr["I"]="I";
mxG.Z.fr["V"]="V";
mxG.Z.fr["H"]="E";
mxG.Z.fr["S"]="S";
mxG.Z.fr["OK"]="OK";
mxG.Z.fr["Cancel"]="Annuler";
mxG.Z.fr["New (from this point):"]="Nouvelle (à partir de cette position) :";
mxG.Z.fr["Modify"]="Modifier (seulement pour cette partie de l'arbre des coups)";
mxG.Z.fr["Remove"]="Supprimer (seulement pour cette partie de l'arbre des coups)";
mxG.Z.fr["Start numbering with:"]="Numéroter en commençant par :";
mxG.Z.fr["No numbering"]="Ne pas numéroter";
mxG.Z.fr["Good move"]="Bon coup";
mxG.Z.fr["Bad move"]="Mauvais coup";
mxG.Z.fr["Doubtful move"]="Douteux";
mxG.Z.fr["Interesting move"]="intéressant";
mxG.Z.fr["Good for Black"]="Bon pour Noir";
mxG.Z.fr["Good for White"]="Bon pour Blanc";
mxG.Z.fr["Even"]="Équilibré";
mxG.Z.fr["Unclear"]="Pas clair";
mxG.Z.fr["Add turn in Sgf"]="Ajouter le trait dans le Sgf";
if (this.mxG.CanCn())
{
CanvasRenderingContext2D.prototype.dashedLine=function(x1,y1,x2,y2,e)
{
var dX=x2-x1,dY=y2-y1,da=Math.floor(Math.sqrt(dX*dX+dY*dY)/(e?e:2)),daX=dX/da,daY=dY/da,k=0;
this.beginPath();
this.moveTo(x1,y1);
while (k++<da)
{
x1+=daX;
y1+=daY;
this[k%2==0?'moveTo':'lineTo'](x1,y1);
}
this[k%2==0?'moveTo':'lineTo'](x2,y2);
this.stroke();
};
}
mxG.G.prototype.setViewFromSelection=function()
{
var aN,s,xl,yt,xr,yb,exXl,exYt,exXr,exYb,exXls,exYts,exXrs,exYbs;
if (this.selection)
{
xl=((this.editXrs>this.editXls)?this.editXls:this.editXrs);
yt=((this.editYbs>this.editYts)?this.editYts:this.editYbs);
xr=((this.editXrs>this.editXls)?this.editXrs:this.editXls);
yb=((this.editYbs>this.editYts)?this.editYbs:this.editYts);
if (xl<1) xl=1;
if (yt<1) yt=1;
if (xr>this.DX) xr=this.DX;
if (yb>this.DY) yb=this.DY;
this.inSelect=0;
this.unselectView();
}
else
{
xl=1;
yt=1;
xr=this.DX;
yb=this.DY;
}
if ((xl==1)&&(yt==1)&&(xr==this.DX)&&(yb==this.DY)) s="";
else s=this.xy2s(xl,yt)+":"+this.xy2s(xr,yb);
aN=this.cN;
if (aN.P.VW)
{
aN.TakeOff("VW",-1);
if (s) aN.P.VW=[s];
}
else aN.P.VW=[s];
this.updateAll();
};
mxG.G.prototype.selectPoint=function(x,y)
{
var cx=this.gcx;
var d=this.d,r=d/2,z=this.z,d2=this.d2,d3=(d2&1?1:0);
var a=(x-this.xli)*d+z,b=(y-this.yti)*(d+d2)+(d2>>1)+d3+z;
var dxl=0,dyt=0,dxr=0,dyb=0;
if (x==this.xl) dxl=z;
if (y==this.yt) dyt=z;
if (x==this.xr) dxr=z;
if (y==this.yb) dyb=z;
if (x==0) a=a-z;
if (y==0) {b=b-z;dyb=dyb-d3;}
if (x==(this.DX+1)) a=a+z;
if (y==(this.DY+1)) {b=b+z+d3;dyb=dyb-d3;}
if (this.hasToDrawWholeGoban&&!this.lastSelectLine&&(y>0)&&(y<this.DY)) {dyb=dyb-d3;}
if (!this.hasToDrawWholeGoban) this.unselectPoint(x,y); 
cx.fillStyle=this.lineColor;
cx.globalAlpha=0.2;
cx.fillRect(a-dxl,b-(d2>>1)-d3-dyt,d+dxl+dxr,d+d2+d3+dyt+dyb);
cx.globalAlpha=1;
};
mxG.G.prototype.unselectPoint=function(x,y)
{
if (this.inView(x,y))
{
k=this.xy(x,y);
this.drawPoint(this.gcx,x,y,this.vNat[k],this.vStr[k]);
}
else if (this.indicesOn) this.drawPoint(this.gcx,x,y,"O",this.getIndices(x,y));
};
mxG.G.prototype.unselectTool=function(tool)
{
this.getE(tool+"Tool").className="mxUnselectedEditTool";
};
mxG.G.prototype.selectTool=function(tool)
{
this.getE(tool+"Tool").className="mxSelectedEditTool";
};
mxG.G.prototype.superSelectTool=function(tool)
{
this.getE(tool+"Tool").className="mxSuperSelectedEditTool";
};
mxG.G.prototype.disableTool=function(tool)
{
if (tool=="Comment") this.getE("CommentToolText").disabled=true;
else this.getE(tool+"Tool").disabled=true;
};
mxG.G.prototype.enableTool=function(tool)
{
if (tool=="Comment") this.getE("CommentToolText").disabled=false;
else this.getE(tool+"Tool").disabled=false;
};
mxG.G.prototype.disableTools=function()
{
var k,km=this.tools.length;
for (k=0;k<km;k++) this.disableTool(this.tools[k][0]);
this.disableTool("Comment");
};
mxG.G.prototype.enableTools=function()
{
var k,km=this.tools.length;
for (k=0;k<km;k++) this.enableTool(this.tools[k][0]);
this.enableTool("Comment");
};
mxG.G.prototype.changeSelectedTool=function(newTool)
{
if (this.editTool&&(this.editTool!="ShowInfo")&&(this.editTool!="Numbering")) this.unselectTool(this.editTool);
this.editTool=newTool;
if ((newTool!="ShowInfo")&&(newTool!="Numbering")) this.selectTool(newTool);
};
mxG.G.prototype.doCut=function()
{
var aN,SZ,ST;
if (this.hasC("Menu")) this.toggleMenu("Edit",0);
this.selectTool("Cut");
this.zN=this.cN;
aN=this.zN.Dad;
this.zN.Dad=null;
if ((aN==this.rN)&&(aN.Kid.length==1))
{
SZ=this.getInfo("SZ");
ST=this.getInfo("ST");
}
aN.Kid.splice(aN.Focus-1,1);
aN.Focus=aN.Kid.length?1:0;
if (aN==this.rN)
{
if (aN.Focus) aN=aN.Kid[0];
else
{
aN=aN.N("FF",4);
aN.P.GM=["1"];
aN.P.CA=["UTF-8"];
aN.P.SZ=[SZ];
aN.P.ST=[ST];
}
}
this.backNode(aN);
if (this.hasC("Tree")) this.initTree();
this.updateAll();
setTimeout(this.g+".unselectTool(\"Cut\")",200);
};
mxG.G.prototype.doCopy=function()
{
if (this.hasC("Menu")) this.toggleMenu("Edit",0);
this.selectTool("Copy");
this.zN=this.cN.Clone(null);
this.zN.Dad=null;
setTimeout(this.g+".unselectTool(\"Copy\")",200);
};
mxG.G.prototype.doPaste=function()
{
if (this.hasC("Menu")) this.toggleMenu("Edit",0);
this.selectTool("Paste");
if (this.zN)
{
if (this.zN.P.SZ) this.cN=this.rN;
this.zN.Dad=this.cN;
this.cN.Kid[this.cN.Kid.length]=this.zN;
this.zN=this.zN.Clone(null);
this.cN.Focus=this.cN.Kid.length;
this.backNode((this.cN==this.rN)?this.cN.KidOnFocus():this.cN);
if (this.hasC("Tree")) this.initTree();
this.updateAll();
}
setTimeout(this.g+".unselectTool(\"Paste\")",200);
};
mxG.G.prototype.doAsInBook=function()
{
var aN=this.cN,sN=this.rN.KidOnFocus(),exFig=0,newFig,newAsInBookOn=(this.asInBookOn?0:1);
while (aN!=this.rN)
{
if (aN.P.FG) {exFig=parseInt(aN.P.FG[0]);break;}
aN=aN.Dad;
}
if (aN==this.rN) aN=sN;
newFig=(newAsInBookOn?(exFig|256):(exFig&~256));
if ((aN==sN)&&!newFig) aN.TakeOff("FG",0);
else aN.Set("FG",newFig);
this.updateAll();
};
mxG.G.prototype.doIndices=function()
{
var aN=this.cN,sN=this.rN.KidOnFocus(),exFig=0,newFig,newIndicesOn=(this.indicesOn?0:1);
while (aN!=this.rN)
{
if (aN.P.FG) {exFig=parseInt(aN.P.FG[0]);break;}
aN=aN.Dad;
}
if (aN==this.rN) aN=sN;
newFig=newIndicesOn?(exFig&~1):(exFig|1);
if ((aN==sN)&&!newFig) aN.TakeOff("FG",0);
else aN.Set("FG",newFig);
this.updateAll();
};
mxG.G.prototype.doNumberingOK=function()
{
var aN;
if (this.getE("NewFigureBox")&&this.getE("NewFigureBox").checked) aN=this.cN;
else
{
aN=this.cN;
while ((aN.Dad!=this.rN)&&!(aN.P.FG)) aN=aN.Dad;
}
if (this.getE("FigureOrNot2Input")&&this.getE("FigureOrNot2Input").checked)
{
aN.TakeOff("FG",0);
aN.TakeOff("PM",0);
aN.TakeOff("MN",0);
}
else
{
var newNumberingOn=(this.getE("NumberingOrNot1Input").checked?1:0);
var newNumWith=parseInt(this.getE("NumWithTextInput").value);
var newAsInBookOn=(this.getE("AsInBookInput").checked?1:0);
var newIndicesOn=(this.getE("IndicesInput").checked?1:0);
var newFigure=((newAsInBookOn?256:0)|(newIndicesOn?0:1));
if (aN==this.rN.KidOnFocus())
{
if (newFigure) aN.Set("FG",newFigure);
else aN.TakeOff("FG",0);
if ((newNumWith>1)&&newNumberingOn) aN.Set("MN",newNumWith);
else aN.TakeOff("MN",0);
if (newNumberingOn!=1) aN.Set("PM",newNumberingOn);
else aN.TakeOff("PM",0);
}
else
{
aN.Set("FG",newFigure);
aN.Set("PM",newNumberingOn);
if (newNumberingOn) aN.Set("MN",newNumWith);
else aN.TakeOff("MN",0);
}
}
if (this.hasC("Tree")) this.hasToDrawTree=this.hasToDrawTree|1;
this.hideGBox("Numbering");
};
mxG.G.prototype.switchFigureOrNot=function()
{
var e;
if (this.getE("NewFigureBox").checked)
{
if (e=this.getE("FigureOrNot1P")) e.style.display="none";
else if (e=this.getE("FigureOrNot2P")) e.style.display="none";
}
else
{
if (e=this.getE("FigureOrNot1P")) e.style.display="block";
else if (e=this.getE("FigureOrNot2P")) e.style.display="block";
}
};
mxG.G.prototype.doNumbering=function()
{
if (this.gBox=="Numbering") {this.hideGBox("Numbering");return;}
if (!this.getE("NumberingDiv")) this.createGBox("Numbering");
var aN=this.cN,s="";
while ((aN.Dad!=this.rN)&&!aN.P.FG) aN=aN.Dad;
s+="<div class=\"mxShowContentDiv\">";
s+="<h1>"+this.local("Numbering")+"</h1>";
if (aN!=this.cN)
{
s+="<div class=\"mxP\"><label for=\""+this.n+"NewFigureBox\">"+this.local("New (from this point):")+" </label>";
s+="<input type=\"checkbox\" "+"id=\""+this.n+"NewFigureBox\" onclick=\""+this.g+".switchFigureOrNot()\"></div>";
}
if ((aN.Dad!=this.rN)&&aN.P.FG) 
{
s+="<div class=\"mxP\" id=\""+this.n+"FigureOrNot1P\"><input type=\"radio\" id=\""+this.n+"FigureOrNot1Input\" name=\"figureOrNot\" checked=\"checked\" value=\"1\">";
s+="<label for=\""+this.n+"FigureOrNot1Input\">"+this.local("Modify")+"</label></div>";
}
s+="<div class=\"mxP mxTabNumberingP\">";
s+="<input type=\"radio\" id=\""+this.n+"NumberingOrNot1Input\" name=\"numberingOrNot\" ";
s+=(this.numberingOn?"checked=\"checked\" ":"");
s+="value=\"1\">";
s+="<label for=\""+this.n+"NumberingOrNot1Input\">"+this.local("Start numbering with:")+" </label>";
s+="<input type=\"text\" id=\""+this.n+"NumWithTextInput\" size=\"3\" maxlength=\"3\" value=\""+1+"\"><br>";
s+="<input type=\"radio\" id=\""+this.n+"NumberingOrNot2Input\" name=\"numberingOrNot\" ";
s+=(!this.numberingOn?"checked=\"checked\" ":"");
s+="value=\"2\">";
s+="<label for=\""+this.n+"NumberingOrNot2Input\">"+this.local("No numbering")+"</label><br><br>";
s+="<input type=\"checkbox\" "+(this.asInBookOn?"checked=\"checked\" ":"")+"id=\""+this.n+"AsInBookInput\"> "+this.local("As in book")+"<br>";
s+="<input type=\"checkbox\" "+(this.indicesOn?"checked=\"checked\" ":"")+"id=\""+this.n+"IndicesInput\"> "+this.local("Indices")+"<br>";
s+="</div>";
if ((aN.Dad!=this.rN)&&aN.P.FG)
{
s+="<div class=\"mxP\" id=\""+this.n+"FigureOrNot2P\"><input type=\"radio\" id=\""+this.n+"FigureOrNot2Input\" name=\"figureOrNot\" value=\"2\">";
s+="<label for=\""+this.n+"FigureOrNot2Input\">"+this.local("Remove")+"</label></div>";
}
s+="</div>";
s+="<div class=\"mxOKDiv\">";
s+="<button type=\"button\" onclick=\""+this.g+".doNumberingOK()\"><span>"+this.local("OK")+"</span></button>";
s+="<button type=\"button\" onclick=\""+this.g+".hideGBox('Numbering')\"><span>"+this.local("Cancel")+"</span></button>";
s+="</div>";
this.getE("NumberingDiv").innerHTML=s;
this.showGBox("Numbering");
};
mxG.G.prototype.doVariations=function()
{
if (this.styleMode&2) this.styleMode-=2;else this.styleMode+=2;
this.rN.KidOnFocus().Set("ST",this.styleMode&~4);
this.updateAll();
};
mxG.G.prototype.doStyle=function()
{
if (this.styleMode&1) this.styleMode-=1;else this.styleMode+=1;
this.rN.KidOnFocus().Set("ST",this.styleMode&~4);
this.updateAll();
};
mxG.G.prototype.doPropertySwitch=function(tool)
{
var z;
if ((tool!="DO")&&(tool!="IT")) z=2;else z=1;
if (this.cN.P&&this.cN.P[tool])
{
if (((this.cN.P[tool][0]+"")=="1")&&(z>1)) this.cN.P[tool][0]="2";
else this.cN.TakeOff(tool,0);
}
else
{
if ((tool=="GB")||(tool=="GW")||(tool=="DM")||(tool=="UC"))
{
if ((tool!="GB")&&this.cN.P&&this.cN.P.GB) this.cN.TakeOff("GB",0);
if ((tool!="GW")&&this.cN.P&&this.cN.P.GW) this.cN.TakeOff("GW",0);
if ((tool!="DM")&&this.cN.P&&this.cN.P.DM) this.cN.TakeOff("DM",0);
if ((tool!="UC")&&this.cN.P&&this.cN.P.UC) this.cN.TakeOff("UC",0);
}
if ((tool=="TE")||(tool=="BM")||(tool=="DO")||(tool=="IT"))
{
if ((tool!="TE")&&this.cN.P&&this.cN.P.TE) this.cN.TakeOff("TE",0);
if ((tool!="BM")&&this.cN.P&&this.cN.P.BM) this.cN.TakeOff("BM",0);
if ((tool!="DO")&&this.cN.P&&this.cN.P.DO) this.cN.TakeOff("DO",0);
if ((tool!="IT")&&this.cN.P&&this.cN.P.IT) this.cN.TakeOff("IT",0);
}
this.cN.Set(tool,(z>1)?"1":"");
}
this.updateAll();
};
mxG.G.prototype.doPL=function()
{
if (this.cN.P&&this.cN.P.PL) this.cN.TakeOff("PL",0);
else this.cN.Set("PL",this.editNextNat);
this.updateAll();
};
mxG.G.prototype.doEditTool=function(newTool)
{
if (this.gBox) {if (newTool==this.gBox) this.hideGBox(newTool);return;}
if (newTool=="ShowInfo") {this.doInfo();return;}
if (newTool=="Numbering") {this.doNumbering();return;}
if (newTool=="Cut") {this.doCut();return;}
if (newTool=="Copy") {this.doCopy();return;}
if (newTool=="Paste") {this.doPaste();return;}
if (newTool=="AsInBook") {this.doAsInBook();return;}
if (newTool=="Indices") {this.doIndices();return;}
if (newTool=="Variations") {this.doVariations();return;}
if (newTool=="Style") {this.doStyle();return;}
if ((newTool=="GB")
||(newTool=="GW")
||(newTool=="DM")
||(newTool=="UC")
||(newTool=="TE")
||(newTool=="BM")
||(newTool=="DO")
||(newTool=="IT")) {this.doPropertySwitch(newTool);return;}
if (newTool=="PL") {this.doPL();return;}
if (newTool=="View")
{
this.selectTool(newTool);
this.setViewFromSelection();
setTimeout(this.g+".unselectTool(\""+newTool+"\")",200);
if (this.editTool=="Select") this.changeSelectedTool("Play");
return;
}
if (this.selection) {this.inSelect=0;this.unselectView();}
if ((newTool=="Play")&&(this.editTool=="Play"))
{
if (this.editNextNat=="B") {this.editNextNat="W";this.setStoneTool("W");}
else if (this.editNextNat=="W") {this.editNextNat="B";this.setStoneTool("B");}
return;
}
if ((newTool=="Setup")&&(this.editTool=="Setup"))
{
if (this.editAX=="AB") {this.editAX="AW";this.setStoneTool("AW");}
else if (this.editAX=="AW") {this.editAX="AB";this.setStoneTool("AB");}
return;
}
this.changeSelectedTool(newTool);
};
mxG.G.prototype.doEditCommentTool=function()
{
var s=this.getE("CommentToolText").value;
if (s) this.cN.Set("C",s);else this.cN.TakeOff("C",0);
};
mxG.G.prototype.getNextEditNat=function()
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
mxG.G.prototype.addPlay=function(p,x,y)
{
var aN,v=this.xy2s(x,y);
aN=this.cN.N(p,v);
aN.Add=1;
this.cN.Focus=this.cN.Kid.length;
if (this.playOn)
{
if (p=="B") {this.blackID=this.scribeID;this.blackName=this.scribeName;this.rN.KidOnFocus().Set("PB",this.scribeName);}
if (p=="W") {this.whiteID=this.scribeID;this.whiteName=this.scribeName;this.rN.KidOnFocus().Set("PW",this.scribeName);}
}
};
mxG.G.prototype.checkEdit=function(a,b)
{
var nextNat=this.editNextNat;
if (!nextNat) {this.plonk();return;}
if (a||b)
{
if ((this.checkRulesOn==2)&&!this.gor.isValid(nextNat,a,b)) {this.plonk();return;}
if ((this.checkRulesOn==1)&&this.gor.isOccupied(a,b)) {this.plonk();return;}
}
var s,aN,x,y,nat,k=0,km=this.cN.Kid.length;
while (k<km)
{
aN=this.cN.Kid[k];
x=-1;
y=-1;
nat="O";
s="";
if (aN.P.B) {s=aN.P.B[0];nat="B";}
else if (aN.P.W) {s=aN.P.W[0];nat="W";}
if (s.length==2) {x=s.c2n(0);y=s.c2n(1);}
else if (s.length==0) {x=0;y=0;}
if ((x==a)&&(y==b)&&(nat==nextNat)) 
{
this.cN.Focus=k+1;
this.backNode(this.cN); 
this.placeNode();
this.updateAll();
return;
}
else k++;
}
this.addPlay(nextNat,a,b);
this.placeNode();
if (this.hasC("Tree")) this.addNodeInTree(this.cN);
this.updateAll();
};
mxG.G.prototype.doClickEditPlay=function(x,y)
{
if (!this.inView(x,y)) {this.plonk();return;}
this.checkEdit(x,y);
};
mxG.G.prototype.doClickEditSetup=function(x,y)
{
var aN,p,v,k,km,kp;
var AX=["AB","AW","AE"];
if (!this.inView(x,y)) return;
if (this.gor.getBanNat(x,y)!="E") p="AE";else p=this.editAX;
v=this.xy2s(x,y);
if (this.cN.P.B||this.cN.P.W)
{
aN=this.cN.N(p,v);
this.cN.Focus=this.cN.Kid.length;
this.placeNode();
if (this.hasC("Tree")) this.initTree();
this.updateAll();
this.changeSelectedTool("Setup");
}
else
{
aN=this.cN;
for (kp=0;kp<3;kp++)
{
if (aN.P[AX[kp]])
{
km=aN.P[AX[kp]].length;
for (k=0;k<km;k++) if (aN.P[AX[kp]][k]==v) break;
if (k<km) aN.TakeOff(AX[kp],k);
}
}
this.backNode(aN.Dad);
aExNat=this.gor.getBanNat(x,y);
if (aExNat!=p.substr(1,1))
{
if (aN.P[p]) aN.P[p].push(v);
else aN.P[p]=[v];
}
this.placeNode(aN);
this.updateAll();
}
};
mxG.G.prototype.selectGobanArea=function(x,y)
{
if ((this.editTool=="Select")&&this.inSelect&&((x!=this.editXrs)||(y!=this.editYbs)))
{
var id,i,j,xl,yt,xr,yb,xl1,yt1,xr1,yb1,xl2,yt2,xr2,yb2;
xl1=Math.min(this.editXls,this.editXrs);
yt1=Math.min(this.editYts,this.editYbs);
xr1=Math.max(this.editXls,this.editXrs);
yb1=Math.max(this.editYts,this.editYbs);
if (this.editXls==0) this.editXrs=((x==0)?1:((x==this.DX)?this.DX+1:x));
else if (this.editXls==(this.DX+1))  this.editXrs=((x==1)?0:((x==(this.DX+1))?this.DX:x));
else  this.editXrs=((x==1)?0:((x==this.DX)?this.DX+1:x));
if (this.editYts==0) this.editYbs=((y==0)?1:((y==this.DY)?this.DY+1:y));
else if (this.editYts==(this.DY+1))  this.editYbs=((y==1)?0:((y==(this.DY+1))?this.DY:y));
else  this.editYbs=((y==1)?0:((y==this.DY)?this.DY+1:y));
xl2=Math.min(this.editXls,this.editXrs);
yt2=Math.min(this.editYts,this.editYbs);
xr2=Math.max(this.editXls,this.editXrs);
yb2=Math.max(this.editYts,this.editYbs);
xl=Math.min(xl1,xl2);
yt=Math.min(yt1,yt2);
xr=Math.max(xr1,xr2);
yb=Math.max(yb1,yb2);
for (i=xl;i<=xr;i++)
for (j=yt;j<=yb;j++)
if ((i>=xl2)&&(i<=xr2)&&(j>=yt2)&&(j<=yb2))
{
if ((i<xl1)||(i>xr1)||(j<yt1)||(j>yb1)) this.selectPoint(i,j);
}
else if ((i>=xl1)&&(i<=xr1)&&(j>=yt1)&&(j<=yb1)) this.unselectPoint(i,j);
}
};
mxG.G.prototype.unselectView=function()
{
var i,j,xl,yt,xr,yb;
this.selection=0;
xl=Math.max(this.xli,Math.min(this.editXls,this.editXrs));
yt=Math.max(this.yti,Math.min(this.editYts,this.editYbs));
xr=Math.min(this.xri,Math.max(this.editXls,this.editXrs));
yb=Math.min(this.ybi,Math.max(this.editYts,this.editYbs));
for (i=xl;i<=xr;i++)
for (j=yt;j<=yb;j++)
this.unselectPoint(i,j);
};
mxG.G.prototype.selectView=function()
{
var i,j,xl,yt,xr,yb;
this.selection=1;
xl=Math.max(this.xli,Math.min(this.editXls,this.editXrs));
yt=Math.max(this.yti,Math.min(this.editYts,this.editYbs));
xr=Math.min(this.xri,Math.max(this.editXls,this.editXrs));
yb=Math.min(this.ybi,Math.max(this.editYts,this.editYbs));
for (i=xl;i<=xr;i++)
for (j=yt;j<=yb;j++)
{
if (j==yb) this.lastSelectLine=1;
this.selectPoint(i,j);
if (j==yb) this.lastSelectLine=0;
}
};
mxG.G.prototype.doClickEditMarkOrLabel=function(x,y,m)
{
var v=0,k,km,kp,aLB,bLB="",MX=["MA","TR","SQ","CR","LB"];
if (!this.inView(x,y)) return;
v=this.xy2s(x,y);
if (m=="LB")
{
aLB=this.getE("LabelTool").value;
v+=(":"+aLB);
}
for (kp=0;kp<5;kp++)
{
if (this.cN.P[MX[kp]])
{
km=this.cN.P[MX[kp]].length;
for (k=0;k<km;k++) if (this.cN.P[MX[kp]][k].substr(0,2)==v.substr(0,2)) break;
if ((k==km)&&(MX[kp]==m))
{
if ((m=="LB")&&(aLB.length==1)&&aLB.match(/[A-Za-z1-9]/))
{
if (aLB=="Z") bLB="A";
else if (aLB=="z") bLB="a";
else if (aLB=="9") bLB="1";
else bLB=String.fromCharCode(aLB.charCodeAt(0)+1);
}
this.cN.P[m][km]=v;
}
else if (k<km)
{
if (MX[kp]=="LB") bLB=this.cN.P[MX[kp]][k].substr(3);
this.cN.TakeOff(MX[kp],k);
}
}
else if (MX[kp]==m)
{
if ((m=="LB")&&(aLB.length==1)&&aLB.match(/[A-Za-z1-9]/))
{
if (aLB=="Z") bLB="A";
else if (aLB=="z") bLB="a";
else if (aLB=="9") bLB="1";
else bLB=String.fromCharCode(aLB.charCodeAt(0)+1);
}
this.cN.P[m]=[v];
}
}
if ((m=="LB")&&bLB) this.getE("LabelTool").value=bLB;
this.backNode(this.cN);
this.updateAll();
};
mxG.G.prototype.doMouseMoveEdit=function(ev)
{
if ((this.editTool=="Select")&&(this.inSelect==1)&&!mxG.IsAndroid)
{
if (ev.preventDefault) ev.preventDefault();
var c=this.getC(ev);
this.selectGobanArea(c.x,c.y);
}
};
mxG.G.prototype.doMouseDownEditSelect=function(x,y)
{
if (this.inSelect==1)
{
if (mxG.IsAndroid()) this.selectGobanArea(x,y);
this.inSelect=0;
}
else
{
this.inSelect=1;
if (this.selection) this.unselectView();
this.editXls=((x==1)?0:((x==this.DX)?this.DX+1:x));
this.editYts=((y==1)?0:((y==this.DY)?this.DY+1:y));
this.editXrs=((x==(this.DX+1))?this.DX:((x==0)?1:x));
this.editYbs=((y==(this.DY+1))?this.DY:((y==0)?1:y));
this.selectView();
this.selectGobanArea(x,y);
}
};
mxG.G.prototype.doMouseDownEdit=function(ev)
{
if ((this.editTool=="Select")&&!mxG.IsAndroid)
{
var c=this.getC(ev);
this.doMouseDownEditSelect(c.x,c.y);
}
};
mxG.G.prototype.doMouseUpEditSelect=function(x,y)
{
var xo,yo,x1,y1;
if (this.editXls==0) xo=1;else if (this.editXls==(this.DX+1)) xo=this.DX;else xo=this.editXls;
if (this.editYts==0) yo=1;else if (this.editYts==(this.DY+1)) yo=this.DY;else yo=this.editYts;
if (x==0) x1=1;else if (x==(this.DX+1)) x1=this.DX;else x1=x;
if (y==0) y1=1;else if (x==(this.DY+1)) y1=this.DY;else y1=y;
if ((xo!=x1)&&(yo!=y1)) this.inSelect=0;
};
mxG.G.prototype.doMouseUpEdit=function(ev)
{
if ((this.editTool=="Select")&&!mxG.IsAndroid)
{
var c=this.getC(ev);
this.doMouseUpEditSelect(c.x,c.y);
}
};
mxG.G.prototype.doMouseOutEdit=function(ev)
{
if ((this.editTool=="Select")&&!mxG.IsAndroid) this.inSelect=0;
};
mxG.G.prototype.doKeydownSelect=function(x,y)
{
var xo,yo,x1,y1;
if (this.inSelect==2) this.inSelect=0;
else
{
this.inSelect=2;
if (this.selection) this.unselectView();
this.editXls=((x==1)?0:((x==this.DX)?this.DX+1:x));
this.editYts=((y==1)?0:((y==this.DY)?this.DY+1:y));
this.editXrs=((x==(this.DX+1))?this.DX:((x==0)?1:x));
this.editYbs=((y==(this.DY+1))?this.DY:((y==0)?1:y));
this.selectView();
this.selectGobanArea(x,y);
}
};
mxG.G.prototype.doXYEdit=function(x,y)
{
switch(this.editTool)
{
case "Play": this.doClickEditPlay(x,y);break;
case "Setup": this.doClickEditSetup(x,y);break;
case "Mark": this.doClickEditMarkOrLabel(x,y,"MA");break;
case "Triangle": this.doClickEditMarkOrLabel(x,y,"TR");break;
case "Circle": this.doClickEditMarkOrLabel(x,y,"CR");break;
case "Square": this.doClickEditMarkOrLabel(x,y,"SQ");break;
case "Label": this.doClickEditMarkOrLabel(x,y,"LB");break;
case "Select": if (mxG.IsAndroid) this.doMouseDownEditSelect(x,y);break;
}
};
mxG.G.prototype.doClickEdit=function(ev)
{
if (this.canPlaceEdit)
{
var c=this.getC(ev),x=c.x,y=c.y;
this.doXYEdit(x,y);
}
};
mxG.G.prototype.doKeydownGobanForEdit=function(ev)
{
var c;
if (this.gBox&&(this[gBox+"Parent"]=="Goban")) return;
if (this.canPlaceEdit&&this.gobanFocusVisible)
{
c=mxG.GetKCode(ev);
if ((c==13)||(c==32))
{
if (this.editTool=="Select") this.doKeydownSelect(this.xFocus,this.yFocus);
else this.doXYEdit(this.xFocus,this.yFocus);
ev.preventDefault();
}
}
};
mxG.G.prototype.toolSpacing=function()
{
var el=this.getE("PlayTool");
return mxG.GetPxStyle(el,"marginLeft")+mxG.GetPxStyle(el,"marginRight");
};
mxG.G.prototype.toolBorders=function()
{
var el=this.getE("PlayTool");
return mxG.GetPxStyle(el,"borderLeftWidth")+mxG.GetPxStyle(el,"borderRightWidth");
};
mxG.G.prototype.toolSize=function()
{
return (((this.d*7)>>3)<<1)+1;
};
mxG.G.prototype.toolBarWidth=function()
{
var n=(this.toolBarLines?this.toolBarLines:this.extraEditToolsOn?3:2);
return (Math.ceil(this.tools.length/n))*(this.Ets+this.toolBorders()+this.toolSpacing());
};
mxG.G.prototype.drawToolStone=function(cx,nat,d)
{
var a=this.et,b=2*a;
if ((nat=="B")||(nat=="W")) cx.drawImage(this.imgBig[nat],a,a,d-b,d-b);
else
{
var img1=((nat=="AB")?this.imgBig.B:this.imgBig.W),
img2=((nat=="AB")?this.imgBig.W:this.imgBig.B);
var w1=img1.width,h1=img1.height,w2=img2.width,h2=img2.height;
cx.drawImage(img1,0,0,w1/2,h1,a,a,d/2-a,d-b);
cx.drawImage(img2,w2/2,0,w2/2,h2,d/2,a,d/2-a,d-b);
}
};
mxG.G.prototype.drawToolSelect=function(cx,x,y,d)
{
cx.dashedLine(x+(d>>2)-0.5,y+(d>>2)-0.5,x+(d>>2)-0.5,y+d-(d>>2)+0.5,2);
cx.dashedLine(x+(d>>2)-0.5,y+d-(d>>2)+0.5,x+d-(d>>2)+0.5,y+d-(d>>2)+0.5,2);
cx.dashedLine(x+d-(d>>2)+0.5,y+d-(d>>2)+0.5,x+d-(d>>2)+0.5,y+(d>>2)-0.5,2);
cx.dashedLine(x+d-(d>>2)+0.5,y+(d>>2)-0.5,x+(d>>2)-0.5,y+(d>>2)-0.5,2);
};
mxG.G.prototype.drawToolView=function(cx,x,y,d)
{
cx.strokeRect(x+(d>>2)-0.5,y+(d>>2)-0.5,d-((d>>2)<<1)+1,d-((d>>2)<<1)+1);
cx.strokeRect(x+(d>>1)-0.5,y+(d>>2)-0.5,d-(d>>1)-(d>>2)+1,d-(d>>1)-(d>>2)+1);
};
mxG.G.prototype.setStoneTool=function(nat)
{
if (!this.imgBig.B.canDraw||!this.imgBig.W.canDraw) {setTimeout(this.g+".setStoneTool(\""+nat+"\")",100);return;}
var tool,d=this.Ets,cn,cx;
if ((nat=="B")||(nat=="W")) tool="Play";else tool="Setup";
cn=this.getE(tool+"ToolCn");
cn.width=d;
cn.height=d;
cx=cn.getContext("2d");
cx.strokeStyle="black";
this.drawToolStone(cx,nat,d);
};
mxG.G.prototype.drawCanvasTool=function(tool,pos)
{
var d=this.Ets,el=this.getE(tool+"Tool"),cn=this.getE(tool+"ToolCn"),cx=cn.getContext("2d"),c=mxG.GetStyle(cn,"color");
el.style.height=(d+this.toolBorders())+"px";
el.style.width=(d+this.toolBorders())+"px";
cn.width=d;
cn.height=d;
cx.fillStyle=c;
cx.strokeStyle=c;
cx.clearRect(0,0,d,d);
switch(tool)
{
case "Select": this.drawToolSelect(cx,0,0,d);break;
case "View": this.drawToolView(cx,0,0,d);break;
case "Play": this.drawToolStone(cx,this.editNextNat,d);break;
case "Setup": this.drawToolStone(cx,this.editAX,d);break;
case "Mark": this.drawMark(cx,0,0,d);break;
case "Triangle": this.drawTriangle(cx,0,0,d);break;
case "Circle": this.drawCircle(cx,0,0,d,d/4);break;
case "Square": this.drawSquare(cx,0,0,d);break;
case "Numbering": this.drawToolStone(cx,"W",d);this.drawText(cx,2,2,d-4,"5",{});break;
case "AsInBook": this.drawText(cx,2,2,d-4,this.local("B"),{});break;
case "Indices": this.drawText(cx,2,2,d-4,this.local("I"),{});break;
case "Variations": this.drawText(cx,2,2,d-4,this.local("V"),{});break;
case "Style": this.drawText(cx,2,2,d-4,this.local("S"),{});break;
case "ShowInfo": this.drawText(cx,2,2,d-4,this.local("H"),{});break;
case "GB": this.drawText(cx,2,2,d-4,this.local("●+"),{});break;
case "GW": this.drawText(cx,2,2,d-4,this.local("○+"),{});break;
case "DM": this.drawText(cx,2,2,d-4,this.local("="),{});break;
case "UC": this.drawText(cx,2,2,d-4,this.local("~"),{});break;
case "TE": this.drawText(cx,2,2,d-4,this.local("!"),{});break;
case "BM": this.drawText(cx,2,2,d-4,this.local("?"),{});break;
case "DO": this.drawText(cx,2,2,d-4,this.local("?!"),{});break;
case "IT": this.drawText(cx,2,2,d-4,this.local("!?"),{});break;
case "PL": this.drawText(cx,2,2,d-4,this.local("T"),{});break;
}
};
mxG.G.prototype.drawImageTool=function(tool,pos)
{
var el=this.getE(tool+"Tool"),im=this.getE(tool+"ToolImg"),d=this.Ets;
el.style.height=(d+this.toolBorders())+"px";
el.style.width=(d+this.toolBorders())+"px";
im.height=d;
im.width=d;
};
mxG.G.prototype.drawInputTool=function(tool,pos)
{
var el=this.getE(tool+"Tool"),d=this.Ets,fs=1;
el.style.height=d+"px";
el.style.width=d+"px";
el.style.fontSize=fs+"px";
while ((fs<99)&&(d/mxG.GetPxStyle(el,"fontSize")>1.8)) {fs++;el.style.fontSize=fs+"px";}
while ((fs>2)&&(d/mxG.GetPxStyle(el,"fontSize")<1.6)) {fs--;el.style.fontSize=fs+"px";}
};
mxG.G.prototype.drawEditTools=function()
{
if (!this.imgBig.B.canDraw||!this.imgBig.W.canDraw) {setTimeout(this.g+".drawEditTools()",100);return;}
var k,km=this.tools.length,t;
this.Ets=this.toolSize();
this.exEts=this.Ets;
this.getE("EditToolBarDiv").style.maxWidth=this.toolBarWidth()+"px";
for (k=0;k<km;k++)
{
t=this.tools[k][0];
switch(this.tools[k][1])
{
case "canvas": this.drawCanvasTool(t,k);break;
case "img": this.drawImageTool(t,k);break;
case "input": this.drawInputTool(t,k);break;
}
}
};
mxG.G.prototype.initEdit=function()
{
var e,k
if (this.editXls===undefined) this.editXls=((this.xl==1)?0:this.xl);
if (this.editYts===undefined) this.editYts=((this.yt==1)?0:this.yt);
if (this.editXrs===undefined) this.editXrs=((this.xr==this.DX)?this.DX+1:this.xr);
if (this.editYbs===undefined) this.editYbs=((this.yb==this.DY)?this.DY+1:this.yb);
this.editAX="AB";
this.editNextNat="B";
this.drawEditTools();
this.getE("CommentToolText").value="";
if (!this.editTool) this.changeSelectedTool("Play");
this.pN=this.cN; 
e=this.gcn;
k=this.k;
e.getMClick=mxG.GetMClick;
e.addEventListener("click",function(ev){mxG.D[k].doClickEdit(ev);},false);
e.addEventListener("mousemove",function(ev){mxG.D[k].doMouseMoveEdit(ev);},false);
e.addEventListener("mouseup",function(ev){mxG.D[k].doMouseUpEdit(ev);},false);
e.addEventListener("mousedown",function(ev){mxG.D[k].doMouseDownEdit(ev);},false);
e.addEventListener("mouseout",function(ev){mxG.D[k].doMouseOutEdit(ev);},false);
if (this.gobanFocus) this.go.addEventListener("keydown",function(ev){mxG.D[k].doKeydownGobanForEdit(ev);},false);
};
mxG.G.prototype.selectDouble=function(tool)
{
if (this.cN.P&&this.cN.P[tool])
{
if ((this.cN.P[tool][0]+"")=="2") this.superSelectTool(tool);
else this.selectTool(tool);
}
else this.unselectTool(tool);
};
mxG.G.prototype.selectSingle=function(tool)
{
if (this.cN.P&&this.cN.P[tool]) this.selectTool(tool);else this.unselectTool(tool);
};
mxG.G.prototype.updateEdit=function()
{
if (this.gBox)
{
this.disableTools();
if ((this.gBox=="Numbering")||(this.gBox=="ShowInfo")) this.enableTool(this.gBox);
}
else this.enableTools();
this.editNextNat=this.getNextEditNat();
this.setStoneTool(this.editNextNat);
if (this.pN!=this.cN)
{
this.getE("LabelTool").value=this.getE("LabelTool").value.match(/^([a-z])$/)?"a":"A";
this.changeSelectedTool("Play");
this.pN=this.cN;
}
if (this.indicesOn) this.selectTool("Indices");else this.unselectTool("Indices");
if (this.styleMode&2) this.unselectTool("Variations");else this.selectTool("Variations");
if (this.styleMode&1) this.unselectTool("Style");else this.selectTool("Style");
if (this.asInBookOn) this.selectTool("AsInBook");else this.unselectTool("AsInBook");
if (this.extraEditToolsOn)
{
this.selectDouble("GB");
this.selectDouble("GW");
this.selectDouble("DM");
this.selectDouble("UC");
this.selectDouble("TE");
this.selectDouble("BM");
this.selectSingle("DO");
this.selectSingle("IT");
this.selectSingle("PL");
}
this.getE("CommentToolText").value=this.cN.P.C?this.cN.P.C[0]:"";
};
mxG.G.prototype.refreshEdit=function()
{
if (this.toolSize()!=this.exEts) this.drawEditTools();
};
mxG.G.prototype.doKeydownLabel=function(ev)
{
if (mxG.GetKCode(ev)==13) this.changeSelectedTool("Label");
};
mxG.G.prototype.createTool=function(a)
{
var s=" title=\""+this.local(a[2])+"\"",id=this.n+a[0]+"Tool";
s+=" class=\"mxUnselectedEditTool\"";
s+=" onclick=\""+this.g+".doEditTool('"+a[0]+"')\"";
s+=" id=\""+id+"\"";
switch (a[1])
{
case "canvas":s="<button "+s+"><canvas width=\"0\" height=\"0\" id=\""+id+"Cn\"></canvas></button>";break;
case "img":s="<button "+s+"><img id=\""+id+"Img\" width=\"0\" height=\"0\" "+" src=\""+this[a[0].toLowerCase()+"Img"]+"\"></button>";break;
case "input":s="<input "+s+" onkeydown=\""+this.g+".doKeydownLabel(event)\""+" type=\"text\" value=\"A\">";break;
}
this.write(s);
};
mxG.G.prototype.addEditBtns=function()
{
this.addBtn({n:"Cut",v:this.local("Cut")});
this.addBtn({n:"Copy",v:this.local("Copy")});
this.addBtn({n:"Paste",v:this.local("Paste")});
};
mxG.G.prototype.createEdit=function()
{
var k=0,km;
this.canPlaceEdit=1;
this.et=1; 
this.zN=null; 
this.cutImg="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAYAAABXAvmHAAAABmJLR0QAAAAAAAD5Q7t/AAAACXBIWXMAAAsTAAALEwEAmpwYAAAAB3RJTUUH3gYSEisqI9n+6gAAA3NJREFUaN7t2U+I1VUUB/DPnTfWIkJKMBFDm4IKoTYuUkgKAs1cOFEbK1sZ/VkELaIWQUQFgdRALYoWQUSLoiASLIJWNaGDlkqGVELkoqKhGKzE+XPbnDf8uv3em+f80Tfw+8Hl3Xt/93fv+d7zPfecc1/KOVvOz4Bl/jQAGgANgAZAA2BBz2BdZ0ppACmaOec8068A0nL3xIM1u78Sj2EHWvga+3LOp+J9yv2EOuc8W7AVE5jG0RD+LDIeqY7tl1IVfl0IegLXFf2H4t2uvgOAgai8EkJuifYgVkR9NX7B2EUQsIVWNwCtqBzGN1FfUTPw2QDYWmQBB9uHSbSHMIx38GOsOYMx7O0G4Bh+iHp1wvZJ9UxMNrBEOz2M1/FHrNOpHO5EoZEYsLkUPuqf4MAiC70BL+BIIeQ5TBV9M/gSuzsZ8VAMPImbi4Xui3ebaoRIwdPUicNFez3ux8Ga3Z3GZCH0CezDUNdTKCZ/oPLx23gaH1X6Pse94SPuwjXnsds7Q8vjc1Akx5gR7Oz5GK0sdAveLyacDLW2d+mfqE/gO7xcp52K8R+qmW+6RvCDoZ313bTYFUANNQZqyo14CqOFAKPYhtvwHv6uoci5ou8YnseGBTmyBRjioz1Qos3navvd0iAvKICq4eLBgh5tipRCnwo7W1X6gvnKMa98IKWUimBwvMc840pcizVd5px/MDdPTeyIMON8KfRZRL0bl4xCsZOzhhx9K3EHngzH0hboTJxet2NL1P+qATFZOKlx7Mc9uHTBAELNz4UDqS5+tjj6psLtf4yHu2zC43E6nSm+narR0gRexEZcXmxkbQjzn4wspXQrDuCyCB2+CA+9HWtj2BP4Cb/j25zzeI92sxl3h0O7oYdPRvEh9uecT/aa0EzEOb296F+LryI/7hpNdqBhKjS8u8a5VSlW1fRv+BR3zpUPvBQfDHcI5m6KyVZfpGAu41e8VdBrNpw+ip87eeX4PY5X2zu7yJHpLrzRIcSoljFc1SmhORL1SzoschrfL9T5zJHQXI89wf/xQvCHulHotTgp1pQpZRGpjiyFBuaIx1q9JPVXV5BuKibaU+Hkur68lQhBt1VUdjzK6Wj/ia39divxv5u5lNIV2BuecVVY/gd4M+c80VwtXuDL3dSmVL9e8Kbmb9YGQAOgAdAAaAAs5+dfpTbEtaHP/JcAAAAASUVORK5CYII=";
this.copyImg="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAYAAABXAvmHAAAAfklEQVRoge2T2w3AIBDDsv/S7QgF7rhXbYk/RGIpSAAAcJHH+YSDgBCwMUog843U8DIC7SeEQHB3aTM8peAXowR2JlJmeghUEli5i8BNgdMJeWS7PPJbgfYTQsAicEqZP5AZXkag/YQQCO4uOYUjYGHUhBAI7i4ZyyIAAADLvGyYfZ+H/TacAAAAAElFTkSuQmCC";
this.pasteImg="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAYAAABXAvmHAAAAkElEQVRoge3UQQqAMAxE0d7/0nUvQYoZO435H1y4afogOgbREc3gKVN0+VIIAO5KAZ4uu/pYA+C68P1dAdgC+g1gBu8ZwLaV+mqQBZAdavmoFStj/Su1AxxXW8AxawYAAIA8QDFbckhbQPkVAuAGKGZLDmkLKL9CANwAxWzJISUBiuHWFXobgAEgV3kAEdFaF8ttHg2WkXOyAAAAAElFTkSuQmCC";
this.inSelect=0;
this.selection=0;
this.editTool=0;
this.write("<div class=\"mxEditDiv\" id=\""+this.n+"EditDiv\">");
this.write("<div tabindex=\"-1\" style=\"outline:none;\" class=\"mxEditToolBarDiv\" id=\""+this.n+"EditToolBarDiv\">");
this.tools=[];
this.tools[k]=["Select","canvas","Selection"];k++;
this.tools[k]=["View","canvas","Full/partial view"];k++;
this.tools[k]=["Play","canvas","Place a move"];k++;
this.tools[k]=["Setup","canvas","Add/remove a stone"];k++;
this.tools[k]=["Cut","img","Cut branch"];k++;
this.tools[k]=["Copy","img","Copy branch"];k++;
this.tools[k]=["Paste","img","Paste branch"];k++;
this.tools[k]=["Numbering","canvas","Numbering"];k++;
this.tools[k]=["ShowInfo","canvas","Header"];k++;
this.tools[k]=["Label","input","Label"];k++;
this.tools[k]=["Mark","canvas","Mark"];k++;
this.tools[k]=["Circle","canvas","Circle"];k++;
this.tools[k]=["Square","canvas","Square"];k++;
this.tools[k]=["Triangle","canvas","Triangle"];k++;
this.tools[k]=["AsInBook","canvas","As in book"];k++;
this.tools[k]=["Indices","canvas","Indices"];k++;
this.tools[k]=["Variations","canvas","Variation marks"];k++;
this.tools[k]=["Style","canvas","Variation style"];k++;
if (this.extraEditToolsOn=1)
{
this.tools[k]=["GB","canvas","Good for Black"];k++;
this.tools[k]=["GW","canvas","Good for White"];k++;
this.tools[k]=["DM","canvas","Even"];k++;
this.tools[k]=["UC","canvas","Unclear"];k++;
this.tools[k]=["TE","canvas","Good move"];k++;
this.tools[k]=["BM","canvas","Bad move"];k++;
this.tools[k]=["DO","canvas","Doubtful move"];k++;
this.tools[k]=["IT","canvas","Interesting move"];k++;
this.tools[k]=["PL","canvas","Add turn in Sgf"];k++;
}
km=k;
for (k=0;k<km;k++) this.createTool(this.tools[k]);
this.write("</div><div class=\"mxEditCommentToolDiv\" id=\""+this.n+"CommentTool\" onclick=\""+this.g+".doEditCommentTool()\">");
this.write("<textarea id=\""+this.n+"CommentToolText\" value=\"\" onchange=\""+this.g+".doEditCommentTool()\"></textarea>");
this.write("</div></div>");
};
}
if (typeof mxG.G.prototype.createTree=='undefined'){
mxG.G.prototype.idt=function(x,y)
{
return x+"_"+y;
};
mxG.G.prototype.stopDrawTreeIfAny=function()
{
if (this.treeIntervalId) {clearInterval(this.treeIntervalId);this.treeIntervalId=0;}
this.treeIntervalK=0;
};
mxG.G.prototype.getCT=function(ev,xo,yo)
{
var x,y,c=this.getE("TreePointCanvas"+this.idt(xo,yo)).getMClick(ev),d=this.getTreeD();
x=xo+Math.floor((c.x-1)/d);
y=yo+Math.floor((c.y-1)/d);
return {x:x,y:y}
};
mxG.G.prototype.doClickTree=function(ev,xo,yo)
{
var aN,c,x,y;
if (this.isTreeDisabled()) return;
if (this.hasC("Navigation")) this.getE("NavigationDiv").focus();
c=this.getCT(ev,xo,yo);
x=c.x;
y=c.y;
if ((this.tree[y]!=undefined)&&(this.tree[y][x]!=undefined))
{
aN=this.tree[y][x];
this.backNode(aN);
this.updateAll();
}
};
mxG.G.prototype.getTreeD=function()
{
var d=this.dT+2;
return d+((d>>2)<<1)-1;
};
mxG.G.prototype.addPointToTree=function(treeDiv,x,y)
{
if (this.getE("TreePointCanvas"+this.idt(x,y))) return;
var left,top,cn,d=this.getTreeD(),k,n=this.treeN,m=this.treeM;
left=d*x-1;
top=d*y-1;
cn=document.createElement("canvas");
cn.id=this.n+"TreePointCanvas"+this.idt(x,y);
cn.height=n*d+2;
cn.width=m*d+2;
cn.style.display="block";
cn.style.position="absolute";
cn.style.left=left+"px";
cn.style.top=top+"px";
k=this.k;
cn.getMClick=mxG.GetMClick;
if (cn.addEventListener)
cn.addEventListener("click",function(ev){mxG.D[k].doClickTree(ev,x,y);},false);
treeDiv.appendChild(cn);
return cn;
};
mxG.G.prototype.computeTreeD=function()
{
var d,dmin,s;
if (!this.treeDScale&&!this.treeDMin) return this.d;
dmin=(this.treeDMin?this.treeDMin:19);
dscale=(this.treeDScale?this.treeDScale:1);
d=Math.max(this.d*dscale,dmin);
d=((d>>1)<<1)+1;
return d;
};
mxG.G.prototype.addPointsToTree=function()
{
var treeDiv=this.getE("TreeContentDiv"),i,j,n=this.treeN,m=this.treeM;
this.stopDrawTreeIfAny();
this.dT=this.computeTreeD();
this.imgTree={B:this.setImg("B",this.dT),W:this.setImg("W",this.dT)};
while (treeDiv.firstChild) treeDiv.removeChild(treeDiv.firstChild);
for (j=0;j<this.treeRowMax;j=j+n)
for (i=0;i<this.treeColumnMax;i=i+m)
this.addPointToTree(treeDiv,i,j);
this.hasToDrawTree=2;
};
mxG.G.prototype.drawTreeLine=function(s,x,y,c,lw)
{
var d=this.dT+2,r=(d>>1),r2=(d>>2),cn,cx,z,xx,yy,dd,xo,yo,n=this.treeN,m=this.treeM;
xx=Math.floor(x/m)*m;
yy=Math.floor(y/n)*n;
dd=this.getTreeD();
xo=(x-xx)*dd;
yo=(y-yy)*dd;
cn=this.getE("TreePointCanvas"+this.idt(xx,yy));
if (!cn) cn=this.addPointToTree(this.getE("TreeContentDiv"),xx,yy);
cx=cn.getContext("2d");
cx.strokeStyle=(c?c:this.treeLineColor);
cx.lineWidth=(lw?lw:this.treeLineWidth);
z=(this.tree[y][x]==this.cN)?0:1;
if (s=="H2L")
{
cx.beginPath();
cx.moveTo(xo,yo+r2+r+0.5);
cx.lineTo(xo+r2+z,yo+r2+r+0.5);
cx.stroke();
}
if (s=="D2TL")
{
cx.beginPath();
cx.moveTo(xo+0.5,yo+0.5);
cx.lineTo(xo+r2+(z?0.15:0)*d+0.5,yo+r2+(z?0.15:0)*d+0.5);
cx.stroke();
}
else if (s=="H2R")
{
cx.beginPath();
cx.moveTo(xo-z+d+r2,yo+r2+r+0.5);
cx.lineTo(xo+d+(r2<<1),yo+r2+r+0.5);
cx.stroke();
}
else if (s=="D2BR")
{
cx.beginPath();
cx.moveTo(xo+r2+(z?0.85:1)*d-0.5,yo+r2+(z?0.85:1)*d-0.5);
cx.lineTo(xo+(r2<<1)+d-0.5,yo+(r2<<1)+d-0.5);
cx.stroke();
}
else if (s=="V2B")
{
cx.beginPath();
cx.moveTo(xo+r2+r+0.5,yo+r2+d-z);
cx.lineTo(xo+r2+r+0.5,yo+(r2<<1)+d);
cx.stroke();
}
else if (s=="V1")
{
cx.beginPath();
cx.moveTo(xo+r2+r+0.5,yo);
cx.lineTo(xo+r2+r+0.5,yo+(r2<<1)+d);
cx.stroke();
}
else if (s=="A1")
{
cx.beginPath();
cx.moveTo(xo+r2+r+0.5,yo);
cx.lineTo(xo+r2+r+0.5,yo+r2+r+0.5);
cx.lineTo(xo+(r2<<1)+d-0.5,yo+(r2<<1)+d-0.5);
cx.stroke();
}
else if (s=="T1")
{
cx.beginPath();
cx.moveTo(xo+r2+r+0.5,yo);
cx.lineTo(xo+r2+r+0.5,yo+(r2<<1)+d);
cx.moveTo(xo+r2+r+0.5,yo+r2+r+0.5);
cx.lineTo(xo+(r2<<1)+d-0.5,yo+(r2<<1)+d-0.5);
cx.stroke();
}
};
mxG.G.prototype.drawTreeEmphasis=function(cx,xo,yo,d,c)
{
var r=d/2;
cx.fillStyle=c;
if (this.circularTreeEmphasis)
{
cx.beginPath();
cx.arc(xo+r,yo+r,r+1,0,Math.PI*2,false);
cx.fill();
}
else cx.fillRect(xo,yo,d,d);
};
mxG.G.prototype.hasEmphasis=function(aN)
{
if (aN==this.cN) return this.treeFocusColor;
else return 0;
};
mxG.G.prototype.drawTreePoint=function(aN)
{
var d=this.dT+2,r=(d>>1),r2=(d>>2),nat,s="",x,y,cn,cx,xo,yo,xx,yy,dd;
var n=this.treeN,m=this.treeM,c,bN,lw=this.treeLineWidth;
if (aN.P["B"]) nat="B";else if (aN.P["W"]) nat="W";else nat="KA";
if (!this.hideTreeNumbering&&((nat=="B")||(nat=="W")))
{
if (aN.P.C&&this.markCommentOnTree) s="?";
else if (this.hasC("Diagram")) s=this.getAsInTreeNum(aN);
}
x=aN.iTree;
y=aN.jTree;
xx=Math.floor(x/m)*m;
yy=Math.floor(y/n)*n;
dd=this.getTreeD();
xo=(x-xx)*dd;
yo=(y-yy)*dd;
cn=this.getE("TreePointCanvas"+this.idt(xx,yy));
if (!cn) cn=this.addPointToTree(this.getE("TreeContentDiv"),xx,yy);
cx=cn.getContext("2d");
cx.clearRect(xo+0.5,yo+0.5,d+(r2<<1)-2,d+(r2<<1)-2);
cx.clearRect(xo+0.5+d+(r2<<1)-2,yo+0.5+d+(r2<<1)-2,1.5,1.5);
if (c=this.hasEmphasis(aN)) this.drawTreeEmphasis(cx,xo+r2,yo+r2,d,c);
if ((nat=="B")||(nat=="W"))
{
cx.drawImage(this.imgTree[nat],xo+r2+1,yo+r2+1,d-2,d-2);
if (s) this.drawText(cx,xo+r2+1,yo+r2+1,d-2,s,{c:(nat=="B")?"#fff":"#000"});
}
else {cx.strokeStyle=this.onTreeEmptyColor;this.drawTriangle(cx,xo+1+r2,yo+1+r2,d-2);}
if (x)
{
c=this.getEmphasisColor(aN.Good);
if (this.tree[y][x-1]==aN.Dad) this.drawTreeLine("H2L",x,y,c,lw);
else this.drawTreeLine("D2TL",x,y,c,lw);
}
if (aN.Kid&&aN.Kid.length)
{
if ((this.tree[y][x+1]!=undefined)&&(this.tree[y][x+1]!=undefined)&&(this.tree[y][x+1].Dad==aN))
{
c=this.getEmphasisColor(this.tree[y][x+1].Good);
this.drawTreeLine("H2R",x,y,c,lw);
}
if ((this.tree[y+1]!=undefined)&&(this.tree[y+1][x+1]!=undefined)&&(this.tree[y+1][x+1].Dad==aN))
{
c=this.getEmphasisColor(this.tree[y+1][x+1].Good);
this.drawTreeLine("D2BR",x,y,c,lw);
}
if ((this.tree[y+1]!=undefined)&&(this.tree[y+1][x]!=undefined)
&&((this.tree[y+1][x].Shape==-1)||(this.tree[y+1][x].Shape==-2)||(this.tree[y+1][x].Shape==-3)))
{
c=this.getEmphasisColor(this.tree[y+1][x].Good);
this.drawTreeLine("V2B",x,y,c,lw);
}
}
};
mxG.G.prototype.computeGoodness=function(aN,good) {return 0;}; 
mxG.G.prototype.buildTree=function(aN,io,jo)
{
var i=io,j=jo,k,km=aN.Kid.length,l,good=0,path,p,pm;
if (!this.uC) this.setPl();
if (j==this.treeRowMax) {this.tree[j]=[];this.treeRowMax++;}
this.tree[j][i]=aN;
aN.iTree=i;
aN.jTree=j;
for (k=0;k<km;k++)
{
path=[];
while ((this.tree[j]!==undefined)&&(this.tree[j][i+1]!==undefined))
{
if (this.tree[j][i]===undefined)
{
if ((this.tree[j+1]===undefined)||(this.tree[j+1][i+1]===undefined))
{
if (k==(km-1))
{
this.tree[j][i]={Shape:-1,Good:0}; 
path.push({i:i,j:j});
}
else
{
this.tree[j][i]={Shape:-2,Good:0}; 
path.push({i:i,j:j});
}
}
else
{
this.tree[j][i]={Shape:-3,Good:0}; 
path.push({i:i,j:j});
}
}
j++;
}
good=good|this.buildTree(aN.Kid[k],i+1,j);
pm=path.length;
for (p=0;p<pm;p++) {this.tree[path[p].j][path[p].i].Good=aN.Kid[k].Good;}
}
this.treeColumnMax=Math.max(this.treeColumnMax,i+1);
return aN.Good=this.computeGoodness(aN,good);
};
mxG.G.prototype.getTreeNumOfVisibleLines=function()
{
var e=this.getE("TreeDiv");
if (e.clientHeight===undefined) return 20;
return Math.floor(e.clientHeight/this.getTreeD())+1;
};
mxG.G.prototype.drawMTreeLine=function(k,h2dt,nv)
{
var i,j,jm,c,lw=this.treeLineWidth;
jm=Math.min(k+nv,this.treeRowMax);
for (j=k;j<jm;j++)
{
if (!this.treeCheck[j])
{
this.treeCheck[j]=1;
for (i=0;i<this.treeColumnMax;i++)
if ((this.tree[j]!=undefined)&&(this.tree[j][i]!=undefined))
{
if (this.tree[j][i]&&this.tree[j][i].Dad) this.drawTreePoint(this.tree[j][i]);
else
{
if (this.tree[j][i]) c=this.getEmphasisColor(this.tree[j][i].Good);
if (this.tree[j][i]&&(this.tree[j][i].Shape==-1)) this.drawTreeLine("A1",i,j,c,lw);
else if (this.tree[j][i]&&(this.tree[j][i].Shape==-2)) this.drawTreeLine("T1",i,j,c,lw);
else if (this.tree[j][i]&&(this.tree[j][i].Shape==-3)) this.drawTreeLine("V1",i,j,c,lw);
}
}
}
}
};
mxG.G.prototype.drawMTreeLineAsync=function(h2dt,nv)
{
var i,j,jm,k;
k=this.treeIntervalK;
this.drawMTreeLine(k,h2dt,nv);
k=k+this.treeN;
if (k>=this.treeRowMax)
{
clearInterval(this.treeIntervalId);
this.treeIntervalId=0;
this.treeIntervalK=0;
}
else this.treeIntervalK=k;
};
mxG.G.prototype.afterDrawTree=function()
{
this.treeNodeOnFocus=this.cN;
this.scrollTreeToShowFocus();
};
mxG.G.prototype.drawTree=function(h2dt)
{
var j,jo,dt,da,n=this.treeN,nv;
if (!this.img.B.canDraw||!this.img.W.canDraw)
{
setTimeout(this.g+".drawTree("+h2dt+")",100);
return;
}
this.stopDrawTreeIfAny();
h2dt=h2dt|this.hasToDrawTree;
this.treeCheck=[];
nv=this.getTreeNumOfVisibleLines();
jo=Math.max(0,this.cN.jTree-nv);
dt=new Date();
this.drawMTreeLine(jo,h2dt,nv*2);
da=Math.round((new Date().getTime()-dt.getTime())/(nv*2));
if (nv<this.treeRowMax)
this.treeIntervalId=setInterval(this.g+".drawMTreeLineAsync("+h2dt+","+n+")",5*n*da);
this.hasToDrawTree=0;
this.afterDrawTree();
};
mxG.G.prototype.addNodeInTree=function(aN)
{
var i,j,bN=aN.Dad;
if (bN&&(bN!=this.rN))
{
i=bN.iTree;
j=bN.jTree;
if ((this.tree[j][i+1]===undefined)
||(this.tree[j+1]===undefined)
||(this.tree[j+1][i+1]===undefined))
{
if (this.tree[j][i+1]===undefined) i++;
else
{
if (this.tree[j+1]===undefined) {this.tree[this.treeRowMax]=[];this.treeRowMax++;}
i++;j++;
}
this.tree[j][i]=aN;
aN.iTree=i;
aN.jTree=j;
if (this.goodnessCode) aN.Good=this.goodnessCode.OffPath;
this.treeColumnMax=Math.max(this.treeColumnMax,i+1);
}
else this.initTree();
}
else this.initTree();
};
mxG.G.prototype.initTree=function()
{
var k,km=this.rN.Kid.length,aN;
if (this.scrollInTreeContent) this.treeScrollable=this.getE("TreeContentDiv");
else this.treeScrollable=this.getE("TreeDiv");
if (!this.treeLineColor) this.treeLineColor=mxG.GetStyle(this.getE("TreeContentDiv"),"color");
this.stopDrawTreeIfAny();
this.tree=[];
this.treeRowMax=0;
this.treeColumnMax=0;
for (k=0;k<km;k++)
{
aN=this.rN.Kid[k];
while (aN.KidOnFocus()) aN=aN.KidOnFocus();
this.treeCurrentLast=aN;
this.buildTree(this.rN.Kid[k],0,this.treeRowMax);
}
this.addPointsToTree();
};
mxG.G.prototype.scrollTreeToShowFocus=function()
{
var i,j,left,top,right,bottom,width,height,scrollLeft,scrollTop,e,d;
if (!this.treeNodeOnFocus) return;
i=this.treeNodeOnFocus.iTree;
j=this.treeNodeOnFocus.jTree;
d=this.getTreeD();
left=d*i;
top=d*j;
right=left+d+1;
bottom=top+d+1;
e=this.treeScrollable;
if (e.clientWidth===undefined) return;
width=e.clientWidth; 
if (!width) return;
if (e.clientHeight===undefined) return;
height=e.clientHeight; 
if (!height) return;
if (e.scrollLeft===undefined) return;
scrollLeft=e.scrollLeft;
if (e.scrollTop===undefined) return;
scrollTop=e.scrollTop;
if (left<scrollLeft) e.scrollLeft=left;
else if ((right-width)>scrollLeft) e.scrollLeft=right-width;
if (top<scrollTop) e.scrollTop=top;
else if ((bottom-height)>scrollTop) e.scrollTop=bottom-height;
};
mxG.G.prototype.disableTree=function()
{
var e=this.getE("TreeDiv");
if (!e.hasAttribute("data-maxigos-disabled"))
{
e.setAttribute("data-maxigos-disabled","1");
if (!mxG.IsFirefox) e.setAttribute("tabindex","-1");
}
};
mxG.G.prototype.enableTree=function()
{
var e=this.getE("TreeDiv");
if (e.hasAttribute("data-maxigos-disabled"))
{
e.removeAttribute("data-maxigos-disabled");
if (!mxG.IsFirefox) e.setAttribute("tabindex","0");
}
};
mxG.G.prototype.isTreeDisabled=function()
{
return this.getE("TreeDiv").hasAttribute("data-maxigos-disabled");
};
mxG.G.prototype.setTreeSize=function()
{
var e,htp,hgp;
if (this.adjustTreeWidth) this.adjust("Tree","Width",this.adjustTreeWidth);
if (this.adjustTreeHeight)
{
if (this.adjustTreeHeight==2)
{
e=this.getE("TreeDiv");
htp=mxG.GetPxStyle(e.parentNode,"height");
hgp=mxG.GetPxStyle(this.gop,"height");
if (htp!=hgp) {e.style.height=mxG.GetPxStyle(e,"height")+(hgp-htp)+"px"};
}
else this.adjust("Tree","Height",this.adjustTreeHeight);
}
};
mxG.G.prototype.updateTree=function()
{
var aN;
this.setTreeSize();
if (this.hasToDrawTree) {this.drawTree(this.hasToDrawTree);}
else
{
if ((aN=this.treeNodeOnFocus)&&(aN!=this.rN)&&(aN!=this.cN)) this.drawTreePoint(aN);
if ((aN=this.cN)&&(aN!=this.rN)&&(aN!=this.treeNodeOnFocus)) this.drawTreePoint(aN);
this.afterDrawTree();
}
if (this.gBox) this.disableTree();else this.enableTree();
};
mxG.G.prototype.refreshTree=function()
{
var e,j,nv;
this.hasToAfterDrawTree=0;
this.setTreeSize();
if (this.computeTreeD()!=this.dT) {this.addPointsToTree();this.drawTree(this.hasToDrawTree);}
else if (this.hasToAfterDrawTree) this.afterDrawTree();
e=this.treeScrollable;
if (e.scrollTop===undefined) return;
j=Math.max(0,Math.floor(e.scrollTop/this.getTreeD()));
nv=this.getTreeNumOfVisibleLines();
if ((j!=this.treeJ)||(nv!=this.treeNV)) {this.treeJ=j;this.treeNV=nv;this.drawMTreeLine(j,2,nv);}
};
mxG.G.prototype.createTree=function()
{
var a,s;
if (!this.treeN) this.treeN=10; 
if (!this.treeM) this.treeM=10; 
if (!this.treeLineWidth) this.treeLineWidth=1;
if (!this.emphasisTreeLineWidth) this.emphasisTreeLineWidth=this.treeLineWidth*2;
if (!this.treeFocusColor) this.treeFocusColor="#f00"; 
if (!this.onTreeBlackColor) this.onTreeBlackColor="white";
if (!this.onTreeWhiteColor) this.onTreeWhiteColor="black";
if (!this.onTreeEmptyColor) this.onTreeEmptyColor=this.treeLineColor;
s=" style=\"position:relative;text-align:left;\"";
a=mxG.IsFirefox?"":" tabindex=\"0\"";
this.write("<div class=\"mxTreeDiv\" id=\""+this.n+"TreeDiv\""+a+s+">");
this.write("<div class=\"mxTreeContentDiv\" id=\""+this.n+"TreeContentDiv\"></div>");
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
a+="div.mxNeoClassicWaitDiv {text-align:center;}"
a+="div.mxNeoClassicGlobalBoxDiv {line-height:1.4em;}"
a+="div.mxNeoClassicGlobalBoxDiv div.mxGobanDiv {margin:0 auto;position:relative;}"
a+="div.mxNeoClassicGlobalBoxDiv div.mxGobanDiv canvas{background-image:url(data:image/jpg;base64,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Q+pig1BXbS+0OLWQ9RcZ2iDjR/kG40pUGm202TrfS4cD7WEDx8RJQr0I7Rtm7lugOeBvnCM8fyAF3yjMbU8b4cBm4aJFJ/eYyLyx0qMpbx5gdFjPeoBqVpNht7TJoblhFWPcGgH1f5AyV6j3CFgU06f2BtgWC52I0X2o/k2PDvLFEBEu1ehDEiGdM8v2NC3L36mhkWm3BmLC2uXygkuNQ4RzjQeZP7CMkhPCKHgzQW3uT6goAjAQjuHESQnXyhsghiOSEyD4ahOdZc6E/wCwQBDJDkzFkQsX4iorB8I7A2HrAvQzrlMi2F1gBAiNAOHylXLJfqG0i8dz/ZydRD2vgTD80mQke1Bpxmuenv8A4OH2sGXAwZcfX/DIcvMycP8AjWNvGaOfqHIlC+2TBxPn/pvYvMOeI+4M8jGvh7n14wCxxeDA60PkIfhumXMeBMPA+Z82+YHh4J8G4zRwHueSdungTV80l+Yepb6ZncnwJn+1M+POeiUH5idifEz8ph81hz/5/9oADAMBAAIAAwAAABBkMFpsEAENslhJkgskshtgtJJAhgoglttEhlJlpIAlkshpBtotJJENVoBttIMhhFltEMkhEFtptMttNJploptrNMohMllBgkllhpptNNtNNJtMFtgIMptMlEhEEtlpppttppptt1sstsKEglAkkFEsphttttttptNN9MFklplkkkskgtkpptNppNtptNtJsEggthNgNkEIltBtFJNJptottt9NkgsIsIkMpJFltttJNNJtphpNtNMkgIMgAkIttkhtttBJNJNNppttptEhNohMtNJtkptttBJtINtphNpdllgkAhktNptstthtBJNpNtpJNtttgpFgoktttJAptgpgAJgIthJNpptkNEEslttNNgttgJhANoNtAENNltltBEAhNNJNttJgNAANBMhAAtNJtlthEElNttNNptgBAhAAIJAINIFlltEtllptNtNttkFgkEBFlkANJFthtMtttttNtptpEJhAEIMpoAMpFtltssttttttppolBgMEgEFsgNINNltEtsJtttttosIhgIAgIhFAMplthtEttttttNphIIJgMEIMAAANhltltktgttttNpttpJAEBhFBAJFsNtlpkttpttNtttlgFAEJIAIEAsF8NFtEotttpNNtpJgBAMEgIlAJoBlNttkstNttNNtpNoJAEAgIEkhkMtJthsstttNNNttkABBBAAlgMEgA9JNpsNptNJtttpskBBFEIIgEEsolhNptttNsFpJttgIggAEAAANEgAtgNttttpttpNptgFBIAAJBAkFgEtAtstltpsJtttsghhpBFkIEEEgAkJNptlNoNttttpgMhoABkEhkEsAlJNstpttMNNNttgpEpABhgBgEoIsJNtttttttNNNsoAhkFFkgkEFoAlhtptNttNpNtphogBoIFsBkFEhAlFtptlttNNtpppogAooNtBkEEhIlBNpNFtpsItNsNgggBAFsplAMpIltNttFNNtN9ttNgEgBAFsBgIJgJNpNtthttsttpsNhAoJgJEFlMgoANpNpNltpthNptNBAgAAFkEgEghANpNptlNtsBtpJJJJAJhBsgAMAEJFJNptFNpstttoNgJAhghtggMElgMNNsNltttMNptNhBAoEAMsAAhBIdtJttttpsJNpkNoIBogAtpgBAlItpNtNtNposNhNJsBgoIkslBAgoBFNpoNFttgiNoMIJBAsIgsIggAEINNNtNNpttgNptNsAAoBggAgAggBFtNttoNtJpNJNJgkgBIhlBgAghsJNJttgttBIhtMJgosoIhsJgAogNlJNptttpNgANsBAAggABoNggogNtJtpttttptBtoBgkhAElIJgBgJNNtNltktttphtNpggAEklJpgIApJNNMtNktttpptsJooAAABJpgIAptJtsENsttttttttsgBAAgNpoAAppptskNklltttttMtABgAAtpkgJJpptkktkhltNJtNstBFJAENpoJJJJptkktklFtNptlssoJBMFtpoFJtJJtkglEkFtJtslMtJtJJNtpsFJNtpoFslEkltJNstNtRPNEtttoFttt/wD/xAAeEQABBAIDAQAAAAAAAAAAAAABEWBwgDBAECBQkP/aAAgBAwEBPxDOEAOiiCjmAQIAAAAAEAAAAAIAgJ3Am+ACAAAgxgEAAAAAAAAUAA2CAAAAABkACQAAAAAAE9sAAAAAAAAAAABhkAAAAAQAAABAAAAAAAIAEAAAAAAACAICOEAAAAEAAACAAEAAgAAAAAAAAAQAACAAAAAASACUvAAAAAAAAAAAAAAAAAAAAAAAAAIAIAAAAAAAIAAAAAAAAAAAAAIAAAAEAAAAAAAQAAAAMAAAAAAAIAAAAAAH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IAAAABAAFHhoAAAAAQABAAAAAAAIAIAABAQQABCAAQEBBAEAwAAAAAACAAAgAgACCAAoACAAAAAAAoAAAAAAAEAAACEAAAAAAAgAAFAQEAAAgGRXACgAABAABAAAIAAABAAAQAYAAECoAAgAAAAAAAEAEABABAAgAAQD0vgAAIwAQAAAAAAAAQAAAAQAQAAAAYAAAAAAAAEEAAABAIF0EAAIABAAAAADAECAQCAAAAAAgAgAwAgACACAMAIAAAIECJVgjAEAQAAAAAAAAAEAAAAQANDAAAAAAAAAEEAAAAAAAABAwAAAgAAAAgAAAAAAgAoAQAABAAAAAIIBIAIAgBAAACAAAACAYAAAAEAQIAAAAoBAAABAAAAAAAAQABAAARpJgABAAAAAQAgAAAAgAGAAIAAAAIAAAAAAEAUAAAAIAQAAAACAgCAIAAAAAAMAAAAIAAAAAAIAgAAIAQAAAAAgAAAAAAAAgAAAAYACACAAIAEAAAADAAMUAAAwAAABAAEAAAACAABACAgYEAIAAAAMAGAAAAAACAAAgAADXAAIFAwBAAAAQAAAAAAgAAAIABgAgAMAAAGAAAIAAAgAAQCAAAABACBgDAAgAAAAAAAEAAB4WoEAAAAABAgCAAAAAAAIAAEAAAAZDwAABACgAAAIAIAAgAQAAAwAACAYAAAAAAEAAgIAAAAwAAAgAAAACCAAAABAAAABACgBAAAAACAIAgAIBAAAAAAEjRAIADAAwAQAAAEAAAwAIAEAAAAAAEAQAAIABAFAUAAAAAAAAAAAAUCCAAAAAAAAAEAAAAgAAACAICAAMAIAAAAAAACAAAAYAIACAAQAQAAAEAIAAAQQAAAG4AMAqwAQAAQACAABCAAECTswAoAMCAAAAAwGAAAICAABACAYAAAACACAGAAAAEACAAMAAAQAABAAIAIBAAEAIAwAACAAwBAQAAAAAAQAAgAAAQAgAIAgAAAEAAAAAAgAgAgAEAAAAQAQAAAAIAEACABAAACAAABAAAoAUAAABQQgAAAACAAKGxgAAAAAIEAYAAIBAQQAAAAIAACBAEAAABAAKAAAAAQAQGAAAGAAAAAAAAAAAAAEAQAoAEAAIAABAAABAIAAAAAAAAAAgABAGAAAAIAAAAAADAAAAAAAACAAAAAAAAACAAAIABAAgAAAAABABQAQABAEAACB4VgCAQAUAAgAAABAACABAwKAAAAAQAQAAAH0xgAAAYAAgAAIBAEAAAAAAAAAIAACAAwAEAAAACAAGAIAAAIAgEAAACAAJ5AAABgAAAAAQQAEAAQACgAYAAIABAAAFAAAAgAAAgAgAICAAEAAAAAAAAgBAAQEABAAAAgBAEAGAEAgAFEAAAAAIANkDIoAEAAQAAAAQANoAKAQAAMAAAAAAAIAAAAAgAgABgAAEAAAIAIACAAAAAAAAAEAQAgCAEAIAAABAAABABAAAAQAAAAAAAAAAAAgIAAAAEAAgCAAQAAAEEAAADAAAIACAQAAGABAAAAFAACCAAQAgAQgAAQICgAgAQEAgAAAKmkAAAUAIAEABAAAAAAAIAABAAAAAAAQAAAEAEAEAAAIBAFAAAAFAAAAgCAIAAICABCAwAAAIABAAAABAAAAAAABABAEAAAQEAAABAgIAAAAAAAAEJoAIAQAIAAAAAAAQAgAAAAKAAUAAIABAAAEAAoAgAYAAQEBAAgACCAACEAQAAAACABAtAQAgACAMCAAEACgAAAEAgCAgAAQCEAAgCA9gCAAgAAAAAAAAQAAAAAAACAgAAAAECAAIAAAQAIAAABAEAAAAAAgMABACAAAAAAABAQAIAYCAAACAIAgAQQEAIBAMAIAgEAAAYDOAABABAEAAAAAwFEAAEAAAACCAIAAEQAAAAAAAAAGBAAAAAAAAIBAgQCQ2gAACAAIAADACACgAAABgAMACAAAIAMAgABAAAAABAAQACABACABiAFAEABAAQAEAQABQIAAB4PAEAQAICAAAAAAABAJAAAAGAQAiAEAAAgGABAAAAAAAAAAAAACAAABAARAYACAFAAAAAAAoAIBCAAAAQAAABAYAAAAgQQAAEBlAACAABAAEAEAQAAAgAAwEDAAAYAQAAAAABAAIAAIAYAIAABAQBAABAEgKCAACbkBAAABgBAAAAgAAQBAAgFABCAQAAAAAACAAAAEAAACAAgUAEAAMAQAAAIAAEAEAAAAAEACkAAAYAAAAAAgAEw+gGAEAAABAAABQAAAwAAABAIAAAAAADACAAACAAAIAQMAAIAIBAAgABAKACADAAAAABAEAAAABAIBQAgABAAgABAAIAMAIQDAIEEAgMisAQAgQCAAAAAAAAwAAAQBQAaagAgAQABACEAAAACABEAAEAABABgAoAAAAAAAAADAAAAAAAAAAQAABAIAgAgAAAEACAABgAAAMAAAMBAAACABQAUQAIAQAUAABAAIAQIAEAwAQAAAgCADAAAAQABAAAAAABJB7BAAwAAAAQAIAEDAAEAAAACAAIAAAAAAgBAQCAAAAAQAAARAAABBAACAGAoAAAIAEAAAAAIAAAAAAYBAAABgIAIAAgAACAJsa4AAIQDAFAgAAACAEBAMAAIAAQCAAAIBAAAAAQCAQIBQQAQAAAEAIAAAAACDAQAwCAQAAAgAAEAIAAACAIAABAAAAEAAApAgAAhAAQBQARAAAAAAAAAAACABAAAAQAABAIAoIAEAAACAAAAAAIDaABAEAAAEAACAQAAEAEACACAIAAAYABBAAAKAQAIAIAAIAgBAEAEAAAAAAAAAAAEGAIMChpoAIAoAMAIAAABAAAAAUAAIAAIAAAAQAQACAAAIAACAAAAAAIAgAQCkAAEAAQACAwAAYAAAgACEAAAAAAAAAAAAAAQBQFAAAADAAAAAAAABAgBAQAAAAABAAAEAAIjOCAEAEIAAAwACAEABAAMAAQCAIQAAAAAQAAAIQBCAEAAAAAAABAIAAAA5ADAQAgBAAAEAABgABAAAQAAAAAADAAgAABAAAACAAAAgEAAAAAAAgAAAAAIEEABAAAIBAAACgAwXwAgAAABAwCAAYAAAAAAABAICgAAAAABAQCAAAwAAQCAIAAJhUAAAAQAACAAAAAAAQIAAAQAAAgCAAAAAAACAACQoAAIGlgGAABAAAAAAAAAABAQGACAAICAAAAABAACABAYACEAAICBAAAAEAAAAAEAAAAgEAAAABgBgAEADAAAABAEABAYAAEAQEAQAACAAoACIEAAAgAAoAIAgD9QFmYAPIEBHcAAEB5oYCGIUCvIEQC5QJNjoABAeAoBKAAQ7kQAAAAMALM5CAHEB6GY+wEYPqAIAEAuwRHD/2Q==);background-size:cover;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxIn3d div.mxInnerGobanDiv {box-shadow:0 0.1rem 0.1rem rgba(0,0,0,0.1);}"
a+="div.mxNeoClassicGlobalBoxDiv.mxIn3d div.mxInnerGobanDiv[data-maxigos-disabled] {box-shadow:none;}"
a+="div.mxNeoClassicGlobalBoxDiv button:hover {cursor:pointer;}"
a+="div.mxNeoClassicGlobalBoxDiv button[disabled]:hover {cursor:default;}"
a+="div.mxNeoClassicGlobalBoxDiv button::-moz-focus-inner {padding:0;}"
a+="div.mxNeoClassicGlobalBoxDiv button {-webkit-appearance:none;}"
a+="div.mxNeoClassicGlobalBoxDiv div.mxWaitDiv {border:0.125em solid #c33;color:#c33;background:#fff;font-size:2em;}"
a+="div.mxNeoClassicGlobalBoxDiv div.mxVersionDiv{margin:0 auto;text-align:center;padding-top:0.5rem;padding-bottom:0.5rem;}";
e.type='text/css';
if (e.styleSheet) e.styleSheet.cssText=a;
else e.appendChild(document.createTextNode(a));
document.getElementsByTagName('head')[0].appendChild(e);
})();
(function(){var a="",e=document.createElement("style");
a+="div.mxNeoClassicGlobalBoxDiv div.mxNavigationDiv{padding-top:0.5rem;padding-bottom:0.5rem;text-align:center;line-height:0;margin:0 auto;}"
a+="div.mxNeoClassicGlobalBoxDiv div.mxNavigationDiv button{font-size:1em;width:2em;height:1em;min-height:0;background-color:transparent;background-image:none;box-shadow:none;border:0;padding:0;margin:0 0.5em;vertical-align:middle;}"
a+="div.mxNeoClassicGlobalBoxDiv div.mxNavigationDiv input{font-family:Arial,sans-serif;font-size:0.75em;width:2em;height:1em;min-height:0;vertical-align:middle;text-align:center;margin:0;padding:0.125em;border:1px solid rgba(0,0,0,0.3);background:transparent;border-radius:0;}"
a+="div.mxNeoClassicGlobalBoxDiv div.mxNavigationDiv button div{display:block;position:relative;top:0;height:1em;width:0;margin:0 auto;}"
a+="div.mxNeoClassicGlobalBoxDiv div.mxNavigationDiv button div span {display:none;}"
a+="div.mxNeoClassicGlobalBoxDiv div.mxNavigationDiv button div:before,div.mxNeoClassicGlobalBoxDiv div.mxNavigationDiv button div:after{top:0;position:absolute;content:\"\";border-width:0;border-style:solid;border-color:transparent #000;}"
a+="div.mxNeoClassicGlobalBoxDiv div.mxNavigationDiv button:focus div:before,div.mxNeoClassicGlobalBoxDiv div.mxNavigationDiv button:focus div:after{border-color:transparent #c33;}"
a+="div.mxNeoClassicGlobalBoxDiv div.mxNavigationDiv button[disabled] div:before,div.mxNeoClassicGlobalBoxDiv div.mxNavigationDiv button[disabled] div:after{border-color:transparent rgba(0,0,0,0.3);}"
a+="div.mxNeoClassicGlobalBoxDiv div.mxNavigationDiv .mxFirstBtn div:before{height:1em;left:-0.3125em;border-width:0 0 0 0.125em;}"
a+="div.mxNeoClassicGlobalBoxDiv div.mxNavigationDiv .mxFirstBtn div:after{height:0;right:-0.3125em;border-width:0.5em 0.5em 0.5em 0; }"
a+="div.mxNeoClassicGlobalBoxDiv div.mxNavigationDiv .mxTenPredBtn div:before{height:0;left:-0.5em;border-width:0.5em 0.5em 0.5em 0; }"
a+="div.mxNeoClassicGlobalBoxDiv div.mxNavigationDiv .mxTenPredBtn div:after{height:0;right:-0.5em;border-width:0.5em 0.5em 0.5em 0; }"
a+="div.mxNeoClassicGlobalBoxDiv div.mxNavigationDiv .mxPredBtn div:after{height:0;left:-0.25em;border-width:0.5em 0.5em 0.5em 0; }"
a+="div.mxNeoClassicGlobalBoxDiv div.mxNavigationDiv .mxNextBtn div:before{height:0;left:-0.25em;border-width:0.5em 0 0.5em 0.5em;}"
a+="div.mxNeoClassicGlobalBoxDiv div.mxNavigationDiv .mxTenNextBtn div:before{height:0;left:-0.5em;border-width:0.5em 0 0.5em 0.5em;}"
a+="div.mxNeoClassicGlobalBoxDiv div.mxNavigationDiv .mxTenNextBtn div:after{height:0;right:-0.5em;border-width:0.5em 0 0.5em 0.5em;}"
a+="div.mxNeoClassicGlobalBoxDiv div.mxNavigationDiv .mxLastBtn div:before{height:0;left:-0.3125em;border-width:0.5em 0 0.5em 0.5em;}"
a+="div.mxNeoClassicGlobalBoxDiv div.mxNavigationDiv .mxLastBtn div:after{height:1em;right:-0.3125em;border-width:0 0.125em 0 0;}"
a+="div.mxNeoClassicGlobalBoxDiv div.mxNavigationDiv .mxLoopBtn div:before{height:0;left:-0.625em;border-width:0.5em 0.5em 0.5em 0; }"
a+="div.mxNeoClassicGlobalBoxDiv div.mxNavigationDiv .mxLoopBtn div:after{height:0;right:-0.625em;border-width:0.5em 0 0.5em 0.5em;}"
a+="div.mxNeoClassicGlobalBoxDiv div.mxNavigationDiv .mxPauseBtn div:before{height:1em;left:0.25em;border-width:0 0 0 0.125em;}"
a+="div.mxNeoClassicGlobalBoxDiv div.mxNavigationDiv .mxPauseBtn div:after{height:1em;right:0.25em;border-width:0 0.125em 0 0;}"
a+="div.mxNeoClassicGlobalBoxDiv div.mxNavigationDiv button::-moz-focus-inner {padding:0;border:0;}"
a+="div.mxNeoClassicGlobalBoxDiv div.mxNavigationDiv{-khtml-user-select: none;-webkit-user-select: none;-moz-user-select: -moz-none;-ms-user-select: none;user-select: none;}";
e.type='text/css';
if (e.styleSheet) e.styleSheet.cssText=a;
else e.appendChild(document.createTextNode(a));
document.getElementsByTagName('head')[0].appendChild(e);
})();
(function(){var a="",e=document.createElement("style");
a+="div.mxNeoClassicWaitDiv.mxEditWaitDiv {text-align:center;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv {text-align:center;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxErrorDiv {text-align:center;color:#c33;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv h1 {text-align:center;font-size:1.5em;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv h2 {font-size:1.3em;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv h3 {font-size:1.1em;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mx1BoxDiv,.mxEditGlobalBoxDiv div.mx2BoxDiv{display:inline-block;vertical-align:top;padding-top:0.5em;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mx2BoxDiv {padding-left:0.5em;padding-right:0.5em;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxGobanDiv canvas{font-size:0.75em;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxVersionDiv{display:flex;padding:0.25em;margin:0;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxVersionDiv span{flex:1;margin:auto;}"
a+="@media screen and (max-width:42em){div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mx2BoxDiv {padding-left:0;padding-right:0;}"
a+="}/* buttons */div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv button,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv input[type=button]{font-size:1em;line-height:1.125em;margin:0 2px;padding-left:0.25em;padding-right:0.25em;border-radius:0;background:#fff;color:#000;-webkit-appearance:none;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxEditToolBarDiv input[type=text]{border-radius:0;-webkit-appearance:none;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxEditToolBarDiv textarea{-webkit-appearance:none;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxPassDiv  .mxJustPlayedPassBtn{color:#000;border-color:#000;background-color:#fff;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxPassDiv  .mxOnVariationPassBtn{color:#000;border-color:#000;background-color:#fff;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxPassDiv  .mxOnFocusPassBtn{color:#c33;border-color: #000;background-color:#fff;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv button:hover {cursor:pointer;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxEditToolBarDiv button canvas,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxEditToolBarDiv button img {display:block;margin:0;padding:0;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxPassDiv button[disabled]:hover,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxEditToolBarDiv input[disabled]:hover,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxEditToolBarDiv button[disabled]:hover {cursor:default;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxPassDiv button[disabled] span,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxEditToolBarDiv button[disabled] canvas,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxEditToolBarDiv button[disabled] img {opacity:0.3;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxEditToolBarDiv button::-moz-focus-inner {padding:0;border:0;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv.mxIn3d div.mxEditToolBarDiv button:focus,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv.mxIn3d div.mxEditToolBarDiv input[type=text]:focus,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv.mxIn2d div.mxEditToolBarDiv button:focus,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv.mxIn2d div.mxEditToolBarDiv input[type=text]:focus {outline:none;border:2px solid #c33;}"
a+="/* Edit tools */div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxEditToolBarDiv{margin:0 auto;white-space:normal;position:relative;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxEditToolBarDiv button,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxEditToolBarDiv input[type=text]{text-align:center;cursor:pointer;display:inline-block;padding:0;vertical-align:middle;background-color:#fff;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxEditToolBarDiv .mxSelectedEditTool,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxEditToolBarDiv input[type=text].mxSelectedEditTool {background-color:#54bafc;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxEditToolBarDiv .mxSuperSelectedEditTool {background-color:#00f;color:#fff;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxEditCommentToolDiv{background-color:#fff;margin:0.5em 0;padding:0.25em;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxEditCommentToolDiv textarea{height:7.5em;width:100%;margin:0;padding:0;border:0;color:#000;font-weight:normal;resize: none;}"
a+="/* info */div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxShowInfoDiv{text-align:left;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxShowInfoDiv div.mxShowContentDiv{line-height:1.7em;margin-top:0;padding-top:0;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxShowInfoDiv label{display:inline-block;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxShowInfoDiv input[type=text],div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxShowInfoDiv select,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxShowInfoDiv textarea{border:1px solid #999;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxShowInfoDiv input[type=text],div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxShowInfoDiv select{font-size:0.9em;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv label.mxEV,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv label.mxRO,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv label.mxDT,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv label.mxPC{width:30%;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv label.mxPB,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv label.mxPW,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv label.mxKM{width:30%;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv label.mxBR,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv label.mxWR{text-align:right;width:19%;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv label.mxHA{text-align:right;width:27%;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv label.mxSC{text-align:center;width:8%;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv label.mxAN,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv label.mxCP,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv label.mxSO,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv label.mxUS,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv label.mxRU,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv label.mxTM,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv label.mxOT,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv label.mxON,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv label.mxBT,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv label.mxWT,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv label.mxGN{width:38%;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv label.mxWN,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv label.mxGC{width:98%;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv input.mxPB,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv input.mxPW{width:37%;margin-right:2%;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv input.mxBR,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv input.mxWR{width:7%;margin-left:2%;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv input.mxKM,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv input.mxHA{width:18%;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv input.mxHA{margin-left:2%;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv input.mxKM{margin-right:2%;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv select.mxWN,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv select.mxHW{width:35%;margin-right:2%;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv input.mxSC{width:14%;margin-left:2%;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv input.mxEV,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv input.mxRO,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv input.mxDT,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv input.mxPC{width:68%;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv input.mxAN,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv input.mxCP,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv input.mxSO,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv input.mxUS,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv input.mxRU,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv input.mxTM,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv input.mxOT,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv input.mxON,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv input.mxBT,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv input.mxWT,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv input.mxGN{width:60%;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv textarea.mxGC{height:5em;width:98%;resize:none;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxInfoPageDiv{margin:0 auto;width:96%;padding-top:0.25em;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxInfoPageMenuDiv{border-bottom:1px solid #999;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv .mxInfoPageBtn,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv .mxInfoSelectedPageBtn{font-size:0.9em;min-width:25%;white-space:nowrap;border:1px solid #999;border-bottom:0;margin:0;padding:0 0.5em;vertical-align:bottom;border-top-left-radius:0.5em;border-top-right-radius:0.5em;box-shadow:none;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv .mxInfoPageMenuDiv button:first-of-type{margin-right:0.25em;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv .mxInfoPageBtn{background-color:#fff;color:#000;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv .mxInfoSelectedPageBtn{background-color:#999;color:#fff;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv .mxBadInput {color:red;}"
a+="/* Others */div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxMenuDiv,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxButtonsDiv{height:1.75em;margin:0 auto;text-align:center;white-space:nowrap;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxOneMenuDiv{display:inline-block;text-align:left;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxSubMenuDiv{position:absolute;z-index:2;display:none;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxOneMenuDiv button{text-align:left;margin:0 0.125em 0.25em 0.125em;white-space:nowrap;background:#fff;display:block;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxSubMenuDiv button{margin:0 0.125em 0 0.125em;padding-right:0.5em;width:100%;width:-moz-available; /* work-around for firefox bug  (at least v16 on mac) */}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxSubMenuDiv button.mxCoched,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxSubMenuDiv button.mxCochable{padding-left:1.25em;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxSubMenuDiv button:hover {background:#54bafc;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxSubMenuDiv button.mxCoched span:before {position:absolute;left:0.5em;content:\"✓\";}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxNumberingDiv,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxNewDiv,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxOpenDiv,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxSaveDiv,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxSendDiv,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxColorsDiv,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxShowSgfDiv,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxShowHelpDiv,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxShowInfoDiv{background-color:rgba(255,255,255,1);padding:0 0 3em 0;cursor:default;white-space:normal;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxOpenDiv div.mxP button{box-shadow:none;border:0;text-decoration:underline;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxShowContentDiv{position:relative;height:100%;overflow:auto;padding:0.25em;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxShowSgfDiv div.mxShowContentDiv textarea{height:99%;width:99%;border:0;margin:0 auto;padding:0;background:transparent;resize:none;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxOKDiv{text-align:center;width:100%;position:absolute;bottom:0;background:#eee;padding-top:0.5em;padding-bottom:0.5em;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxP{padding-bottom:0.5em;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxSaveDiv div.mxP input,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxSendDiv div.mxP input{width:80%;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxShowSgfDiv div.mxP{text-align:left;font-family:monospace;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxColorsDiv div.mxP{text-align:left;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxColorsDiv label,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxColorsDiv input[type=text]{display:inline-block;width:90%;margin-left:1em;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxColorsDiv input.mxGobanBkRadioInput[type=text],div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxColorsDiv label.mxGobanBkRadioInput{width:auto;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxColorsDiv input.mxGobanBkRadioInput[type=radio]{margin-left:1em;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxShowHelpDiv,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxNumberingDiv{text-align:justify;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxNewDiv,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxOpenDiv,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxSaveDiv,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxSendDiv{text-align:center;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxOpenDiv label{display:block;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxTreeDiv{padding:0.5em;background-color:#fff;overflow:auto;min-height:2em;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxTreeDiv[data-maxigos-disabled] {opacity:0.3;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxShowHelpDiv h1 {counter-reset:h2;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxShowHelpDiv h2:before {counter-increment:h2;content:counter(h2) \". \";}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxShowHelpDiv h2 {counter-reset:h3;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxShowHelpDiv h3:before {counter-increment:h3;content:counter(h2) \".\" counter(h3) \". \";}"
a+="/* borders */div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxShowSgfDiv,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxNumberingDiv,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxShowHelpDiv,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxNewDiv,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxOpenDiv,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxSaveDiv,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxSendDiv,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxColorsDiv,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxTreeDiv,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxShowInfoDiv,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxEditCommentToolDiv,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv button,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv input[type=button]{border:1px solid #999;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxEditToolBarDiv button,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxEditToolBarDiv input[type=text]{border:2px solid transparent;outline:1px solid #999;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxEditToolBarDiv button,div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxEditToolBarDiv input{margin:2px;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxEditGlobalBoxDiv div.mxButtonsDiv button.mxActivatedScoreBtn{color:#c33;}";
e.type='text/css';
if (e.styleSheet) e.styleSheet.cssText=a;
else e.appendChild(document.createTextNode(a));
document.getElementsByTagName('head')[0].appendChild(e);
})();
mxG.K++;
mxG.D[mxG.K]=new mxG.G(mxG.K);
mxG.D[mxG.K].path=mxG.GetDir()+"../../../";
mxG.D[mxG.K].theme="NeoClassic";
mxG.D[mxG.K].config="Edit";
mxG.D[mxG.K].b[0]={n:"1Box",c:["File","View","Menu","Diagram","Goban","Info","Navigation","Goto","Variations"]};
mxG.D[mxG.K].b[1]={n:"2Box",c:["Pass","Image","Sgf","Score","Help","Buttons","Edit","Tree","Version"]};
mxG.D[mxG.K]["helpData_fr"]="<h1>Aide</h1> <h2>Principe général</h2> <p> Cet éditeur est composé d\'une barre de menus, d\'un goban, d\'une barre de navigation, d\'une barre d\'outils,  d\'une boite de saisie et d\'un arbre.  Cliquez sur le goban pour y placer des pierres, des marques ou des étiquettes.  Utiliser le menu pour afficher un nouveau goban, ouvrir un fichier sgf, l\'enregistrer, ou l\'envoyer par email.  Utilisez la barre de navigation sous le goban pour parcourir l\'arbre des coups.  Utiliser la barre d\'outils sous la barre de navigation pour changer d\'action.  Utiliser la boite de saisie pour commenter la position affichée sur le goban.  Cliquez sur un noeud de l\'arbre pour afficher la position correspondante à ce noeud. </p> <h2>Les menus</h2> <h3>Le menu \"Fichier\"</h3> <p> \"Nouveau\" : affiche un nouveau goban de n\'importe quelle taille pas forcément carré.  On peut soit remplacer l\'affichage précédent par ce nouveau goban,  soit l\'ajouter afin de pouvoir enregistrer plusieurs parties dans un même fichier  (cependant il faut savoir que si tel est le cas, beaucoup de lecteurs ne pourront pas lire ces parties). </p> <p> \"Ouvrir\" : ouvre un fichier sgf stocké sur votre machine (ce n\'est pas toujours possible car cela dépend des machines : certains téléphones en particulier vous interdiront cette possibilité). </p> <p> \"Fermer\" : ferme le fichier sgf en cours. </p> <p> \"Enregistrer\" : enregistre votre saisie sous forme de fichier sgf sur votre machine (ce n\'est pas toujours possible car cela dépend des machines : certains téléphones en particulier vous interdiront cette possibilité). </p> <p> \"Envoyer\" : envoie par email votre saisie sous forme de fichier sgf (utile pour les machines ne permettant pas d\'y enregistrer un fichier). </p> <h3>Le menu \"Édition\"</h3> <p> \"Couper\" : coupe une variation. Voir aussi le paragraphe \"Couper une variante\" ci-dessous pour plus de détails. </p> <p> \"Copier\" : copie une variation. Voir aussi le paragraphe \"Copier une variante\" ci-dessous pour plus de détails. </p> <p> \"Coller\" : colle une variation. Voir aussi le paragraphe \"Coller une variante\" ci-dessous pour plus de détails. </p> <h3>Le menu \"Affichage\"</h3> <p> \"2d/3d\" : affiche le lecteur en 2d ou en 3d. </p> <p> \"Agrandir\" : agrandit la taille du goban. </p> <p> \"Normal\" : ramène le goban à sa taille initiale. </p> <p> \"Réduire\" : réduit la taille du goban. </p> <p> \"Couleurs\" : permet de changer la couleur du fond du goban et de ses lignes. </p> <h3>Le menu \"Fenêtre\"</h3> <p>Permet de changer le fichier sgf en cours d\'édition lorsque plusieurs fichiers sont ouverts.</p> <h2>La barre de navigation</h2> <p> Elle permet de se déplacer dans l\'arbre des coups. </p> <p> Le numéro du coup courant y est affiché. On peut se déplacer à un autre coup en changeant simplement ce numéro. </p> <p> Pour passer, il suffit de cliquer sur le bouton \"Passe\". Ce bouton change de couleur lorsque le coup courant est un passe. Il change aussi de couleur si le coup suivant est un passe quand gos affiche par ailleurs des marques sur les variations. </p> <h2>Autres boutons</h2> <p> Le bouton \"Image\" permet de fabriquer et afficher une image (format PNG) représentant la position affichée sur le goban.  Cette image peut par exemple être utlisée comme illustration dans vos pages web. </p> <p> Le bouton \"Sgf\" permet d\'afficher la description de ce qui a été saisi au format sgf. </p> <p> On peut aussi modifier le sgf directement dans la fenêtre qui s\'affiche. </p> <h2>Vue partielle du goban</h2> <p> Pour afficher une vue partielle du goban, cliquez sur l\'outil \"Sélection\" (un carré en pointillé dans la barre d\'outils),  sélectionnez une partie du goban avec la souris ou son équivalent sur votre machine,  en cliquant sur le coin supérieur gauche puis sur le coin inférieur droit de ce que vous voulez sélectionner  (ne pas maintenir le bouton de la souris enfoncé entre les deux clicks).  Ensuite, cliquez sur l\'outil \"Vue partielle/totale\" (un petit carré à l\'intérieur d\'un grand carré dans la barre d\'outils) pour réduire le goban à la partie que vous avez sélectionnée. </p> <p> Pour désélectionner la sélection sans réduire le goban, cliquez sur l\'outil \"Sélection\" lorsque celui-ci est déjà sélectionné. </p> <p> Pour réafficher en entier le goban, cliquez sur l\'outil \"Vue partielle/totale\" lorsqu\'aucune sélection n\'est effectuée. </p> <h2>Placer un coup et ajouter/retirer une pierre</h2> <p> On a deux outils permettant d\'ajouter ou retirer une pierre sur le goban : l\'outil \"Placer un coup\" et l\'outil \"Ajouter/retirer une pierre\".  L\'outil \"Placer un coup\" permet de placer une succession de coups éventuellement numérotés,  tandis que l\'outil \"Ajouter/retirer une pierre\" permet de construire une position  (ceci sert par exemple à placer des pierres de handicap ou construire la position initiale d\'un problème). </p> <h3>L\'outil \"Placer un coup\"</h3> <p> Lorsque l\'outil \"Placer un coup\" (une pierre noire ou blanche dans la barre d\'outils) est sélectionné, on peut placer des coups sur le goban.  Si des pierres se retrouvent sans liberté, elles sont capturées automatiquement. </p> <p> L\'éditeur essaie en permanence de déterminer la couleur du prochain coup qui sera placé.  Il affiche alors une pierre noire ou une pierre blanche sur cet outil suivant le résultat de cette détermination. </p> <p> Il est possible de changer la couleur du prochain coup qui sera posé en cliquant sur l\'outil \"Placer un coup\" lorque celui-ci est déjà sélectionné  (il est donc possible d\'afficher deux coups de suite de la même couleur). </p> <h3>L\'outil \"Ajouter/retirer une pierre\"</h3> <p> Lorsque l\'outil \"Ajouter/retirer une pierre\" (une pierre moitié noire et moitié blanche dans la barre d\'outils) est sélectionné, on peut ajouter ou retirer des pierres sur le goban. Aucune capture n\'est effectuée.  Si on clique sur une intersection inoccupée, on y ajoute une noire ou une blanche  (si l\'image sur l\'outil a une demi-pierre noire à gauche, on ajoute une pierre noire,  et si elle a une demi-pierre blanche à gauche, on ajoute une pierre blanche).  Enfin, si on clique sur une intersection occupée, on retire la pierre qui s\'y trouve. </p> <p> L\'utilisation de cet outil sur une position obtenue après avoir placé une série de coups a pour effet de réinitialiser la numérotation des coups.  Les numéros des coups déjà placés sont retirés et le premier coup placé à partir de cette position aura le numéro 1. </p> <p>  Il est possible de changer la couleur de la prochaine pierre qui sera posée en cliquant sur l\'outil \"Ajouter/retirer une pierre\" lorque celui-ci est déjà sélectionné. </p> <h2>Couper une variation</h2> <p> Pour couper une variation, afficher le premier coup de la variation sur le goban  (en naviguant dans l\'arbre des coups via la barre de navigation,  ou en cliquant sur la pierre correspondant au coup dans l\'arbre des coups lui-même),  puis cliquez sur l\'outil \"Couper une branche\" (une paire de ciseaux dans la barre d\'outils). </p> <h2>Copier une variation</h2> <p> Pour copier une variation, afficher le premier coup de la variation sur le goban  (en naviguant dans l\'arbre des coups via la barre de navigation,  ou en cliquant sur la pierre correspondant au coup dans l\'arbre des coups lui-même),  puis cliquez sur l\'outil \"Copier une branche\" (deux feuilles se superposant dans la barre d\'outils). </p> <h2>Coller une variation</h2> <p> Pour coller une variation précédemment coupée ou copiée, afficher le dernier coup précédant la variation sur le goban  (en naviguant dans l\'arbre des coups via la barre de navigation, ou en cliquant sur la pierre correspondant  au coup dans l\'arbre des coups lui-même), puis cliquez sur l\'outil \"Coller une branche\" (une feuille venant en superposition sur un support dans la barre d\'outils). </p> <p class=\"important\">MaxiGos ne fait aucune vérification de cohérence de ce qui sera collé.</p> <p>Cette fonction peut être utile quand on s\'aperçoit a posteriori qu\'on a oublié de placer un échange de coups.  Il convient alors d\'aller au coup suivant l\'échange, couper la branche, placer les coups manquants,  et coller la branche précédemment coupée.</p> <p>Cette fonction peut aussi être utile quand on s\'aperçoit a posteriori qu\'on a placé un échange de coups en trop.  Il convient alors d\'aller au coup suivant l\'échange, copier la branche,  revenir au coup précédent l\'échange, coller la branche précédemment copiée,  revenir sur le premier coup de l\'échange à supprimer et couper la branche.</p> <h2>Marques et étiquettes</h2> <p> Pour ajouter ou retirer une marque ou étiquette, sélectionnez l\'un des outils \"Etiquette\" (une lettre dans la barre d\'outils), \"Marque\" (une croix dans la barre d\'outils), \"Triangle\" (une triangle dans la barre d\'outils), \"Cercle\"  (un cercle dans la barre d\'outils) ou \"Carré\" (un carré dans la barre d\'outils),  puis cliquez sur l\'intersection où vous souhaitez l\'ajouter ou la retirer.  Il est possible de changer le texte de la prochaine étiquette qui sera placée en cliquant sur l\'outil \"Etiquette\", et en entrant au clavier les caractères souhaités.  L\'étiquette peut être constituée de plusieurs caractères, mais en pratique, il est préférable de se limiter à des étiquettes de un à trois caractères. </p> <h2>Autres outils</h2> <h3>L\'outil \"Numérotation\"</h3> <p> L\'outil \"Numérotation\" (la pierre numérotée dans la barre d\'outils) permet d\'afficher ou cacher  la numérotation des pierres placées à l\'aide de l\'outil \"Placer un coup\". </p> <p> On peut ne modifier la numérotation qu\'à partir de la position courante si on le souhaite.  Bien qu\'en théorie, on puisse le faire à n\'importe quel coup, il est conseillé de ne le faire qu\'en début de variation. </p> <p> On peut aussi via cet outil afficher ou non les indices, et afficher ou non les pierres capturées comme dans les livres. </p> <h3>L\'outil \"Entête\"</h3> <p> L\'outil \"Entête\" (\"E\" dans la barre d\'outils), permet d\'afficher un formulaire de saisie des propriétés d\'entête des fichiers sgf  (évènement, ronde, nom de noir, niveau de noir, nom de blanc, niveau de blanc, ...). </p> <p> Pour quitter le formulaire et réafficher le goban en prenant en compte vos éventuelles modifications,  cliquez sur le bouton \"OK\" en bas du formulaire. </p> <p> Pour quitter le formulaire et réafficher le goban sans prendre en compte vos éventuelles modifications,  cliquez sur \"E\" dans la barre d\'outils, ou sur le bouton \"Annuler\" en bas du formulaire. </p> <h3>L\'outil \"Comme dans les livres\"</h3> <p> L\'outil \"Comme dans les livres\" (\"L\" dans la barre d\'outils) permet de changer le mode d\'affichage des pierres capturées.  Soit on affiche le goban tel qu\'il serait en partie réelle, soit on affiche le goban en laissant les pierres capturées par des coups numérotés comme dans les livres.  Pour passer de l\'un à l\'autre mode, il suffit de cliquer sur l\'outil. </p> <p>Note : quand aucune pierre numérotée n\'est visible, cet outil est sans effet.</p> <h3>L\'outil \"Indices\"</h3> <p> L\'outil \"Indices\" (\"I\" dans la barre d\'outils) permet d\'afficher ou cacher des indices autour du goban.  En cas de découpe du goban, les indices ne sont affichés que sur les bords visibles.  En cas de sélection d\'une partie du goban contenant des bords avec des indices, ceux-ci sont ajoutés automatiquement à la sélection. </p> <h3>L\'outil \"Marque sur les variations\"</h3> <p> L\'outil \"Marque sur les variations\" (\"V\" dans la barre d\'outils) permet d\'afficher ou cacher les marques sur les variations.  Ces marques sont là uniquement pour vous aider à visualiser la liste des variations possibles à partir d\'une position donnée.  Il ne faut pas y faire référence dans le commentaire car elles peuvent ne pas être affichables ou avoir des libellés différents d\'un logiciel à l\'autre.  Lorsque vous avez besoin de faire référence à une intersection dans le commentaire,  placez plutôt sur le goban des marques et étiquettes à l\'aide de l\'un des outils \"Etiquette\", \"Marque\", \"Triangle\", \"Cercle\" ou \"Carré\" (méthode à privilégier),  ou éventuellement utilisez les indices sur le pourtour du goban.  Lorsqu\'une intersection a à la fois une marque de variation et une marque ou étiquette placée à l\'aide de l\'un des outils\"Etiquette\", \"Marque\", \"Triangle\", \"Cercle\" ou \"Carré\",  c\'est cette dernière qui est affichée, mais avec le même style que celui d\'une marque de variation. </p> <h3>L\'outil \"Style\"</h3> <p> L\'outil \"Style\" (\"S\" dans la barre d\'outils) permet de changer le style d\'affichage des variations. Soit on affiche les alternatives au coup courant, soit on affiche les alternatives au coup suivant. Pour voir les marques sur les variations, n\'oubliez pas d\'activer aussi le mode \"Marque sur les variations\". </p> <h3>Les outils d\'annotation</h3> <p> Permet d\'ajouter des annotations diverses au coup courant (propriétés sgf GB, GW, DM, UC, TE, BM, DO et IT). </p> <h3>L\'outil \"Trait\"</h3> <p> Il permet d\'indiquer qui a le trait au coup suivant (propriété sgf PL). </p> <h2>L\'arbre des coups</h2> <p> Il permet de visualiser l\'ensemble des coups  (en cliquant sur une pierre de l\'arbre, le coup correspondant est affiché sur le goban). </p> <h2>Quitter cette aide</h2> <p> Pour quitter cette aide et réafficher le goban, cliquez le bouton \"Aide\" ou sur le bouton \"Fermer\" en bas de ce texte. </p>";
mxG.D[mxG.K]["helpData_en"]="<h1>Help</h1> <h2>Overview</h2> <p> With this tool, you can edit a go game or diagram using the sgf file format. </p> <h2>Menus</h2> <h3>\"File\" menu</h3> <p>Use it to create, open, save or send by email a sgf file.</p> <p> \"New\" button: display a goban of any size (not necessarily a square).  You can replace the current data or add new data to them. </p> <p> \"Open\": open a sgf file stored on your device (not always possible with some devices). </p> <p> \"Close\": close the current sgf file. </p> <p> \"Save\": save what you edit in a sgf file on your device (not always possible with some devices). </p> <p> \"Send\": send by email what you edit (useful if you cannot save what you edit on your device). </p> <h3>\"Edit\" menu</h3> <p>\"Cut\", \"Copy\" or \"Paste\" a branch of a game tree (see also \"Cut a branch\", \"Copy a branch\" and \"Paste a branch\" below.</p> <h3>\"View\" menu</h3> <p>Change view (2d/3d effect, zoom, colors). <h3>\"Window\" menu</h3> <p>Change the current sgf file.</p> <h2>Navigation bar</h2> <p> Use it to navigate in the game tree. </p> <p> It shows the number of the current move. Change this number to go to another move. </p> <p> Click on \"Pass\" button to pass. This button color changes when the current move is a pass, and when the next move is a pass (if variation marks are shown). </p> <h2>Other buttons</h2> <p> \"Image\" button: display a png image of the current position. </p> <p> \"Sgf\" button: display and edit the sgf. </p> <h2>Partial view of the goban</h2> <p> To display a part of the goban only, click on \"Selection\" tool (a dashed square in the tool bar), select it with the mouse (ot its equivalent) by clicking on its top left and bottom right corners. (don\'t keep mouse button down between the two clicks). Then click on \"Partial/full view\" tool (a small sqaure inside a bigger one in the tool bar) to finish the job. </p> <p> To unselect the selection, click on \"Selection\" tool again. </p> <p> To display the full goban again, on \"Partial/full view\" tool when no part of the goban is selected. </p> <h2>Place a move or add/remove a stone</h2> <p> There are two tools that allow to add/remove stones on the goban: \"Place a move\" and \"Add/remove a stone\" tools.</p> </p> <h3>\"Place a move\" tool</h3> <p> \"Place a move\" tool (a black stone or white stone in the tool bar) allows to place a serie of moves possibly numbered. </p> If some stones are without liberty, they are removed automatically from the goban. </p> <p> The editor tries to guess what will be the color of the next move, and changes the color of the stone displayed in the tool accordingly. </p> <p> It is possible to change the color of the next move just by clicking on \"Place a move\" tool  (thus it is possible to place two moves of the same color in succession). </p> <h3>\"Add/remove a stone\" tool</h3> <p> \"Add/remove a stone\" tool (a half white/half black stone) allows to add or remove a stone from the goban to setup a position  (for instance to place handicap stones or setup the initial position of a problem). </p> <p>The color of the next stone will be the color of the left half of the tool. If one clicks on an occupied intersection, the stone is removed.</p> <p> It is possible to change the color of the next stone just by clicking on \"Add/remove a stone\" tool. </p> <p> Warning: the numerotation restarts to 0 when such a stone is added. </p> <h2>Cut/copy/paste a branch</h2> <p> One can cut/copy/paste a branch of the tree when one of the \"Cut a branch\", \"Copy a branch\" or \"Paste a branch\" is selected. </p> <h2>Marks and labels</h2> <p> Click on one of the \"Label\" (a letter in the tool bar), \"Mark\" (a cross in the tool bar),  \"Triangle\" (a triangle in the tool bar), \"Cercle\"  (a circle in the tool bar) or \"Square\" (a square in the tool bar), then click on an intersecion to add/remove the corresponding mark or label. The next label that will be add is incrementing automatically (from \"A\" to \"Z\", ...),  but can be force to any text by clicking on the \"Label\" tool and replacing the letter in it. </p> <h2>Other tools</h2> <h3>\"Numbering\" tool</h3> <p> \"Numbering\" tool (a numbered stone in the tool bar) shows/hides numbering. </p> <h3>\"Header\" tool</h3> <p> \"Header\" tool (\"H\" in the tool bar) allows to edit game information properties  (event, round, name of black player, name of white player, ...). </p> <h3>\"As in book\" tool</h3> <p> \"As in book\" tool (\"B\" in the tool bar) adds/removes captured stones on the goban as in book/as in real life. </p> <h3>\"Indices\" tool</h3> <p> \"Indices\" tool (\"I\" in the tool bar) shows/hides indices arround the goban. </p> <h3>\"Mark on variation\" tool</h3> <p> \"Mark on variation\" tool (\"V\" in the tool bar) shows/hides mark on variation.</p> <h3>\"Style\" tool</h3> <p> \"Style\" tool (\"S\" in the tool bar) changes variation style. One can display variations of the current move (siblings mode) or variations of the next move (children mode). To see corresponding variation marks, don\'t forget to enable \"Mark on variation\" mode too. </p> <h3>Annotation tools</h3> <p> They add various annotations to the current move (sgf properties GB, GW, DM, UC, TE, BM, DO and IT). </p> <h3>\"Turn\" tool</h3> <p> It allows to indicate the turn for the next move (PL sgf property). </p> <h2>Tree</h2> <p> It allows to see all the nodes of the game tree (by clicking on a stone of the tree, one returns to the position when the corresponding move was played). </p> <h2>Quit this help</h2> <p> To quit this help, and display again the goban, click on \"Help\" button or \"Close\" button at the bottom of this text. </p>";
mxG.D[mxG.K].markOnLastOn=0;
mxG.D[mxG.K].marksAndLabelsOn=1;
mxG.D[mxG.K].in3dOn=1;
mxG.D[mxG.K].stretchOn=1;
mxG.D[mxG.K].initMethod="first";
mxG.D[mxG.K].maximizeGobanHeight=1;
mxG.D[mxG.K].maximizeGobanWidth=1;
mxG.D[mxG.K].buttons="Pass,Image,Sgf,Score,Help";
mxG.D[mxG.K].gotoInputOn=1;
mxG.D[mxG.K].focusColor="#c33";
mxG.D[mxG.K].variationOnFocusColor="#c33";
mxG.D[mxG.K].treeFocusColor="#c33";
mxG.D[mxG.K].adjustTreeHeight=2;
mxG.D[mxG.K].adjustNavigationWidth="Goban";
mxG.D[mxG.K].adjustVersionHeight="Navigation";
mxG.D[mxG.K].checkRulesOn=1;
mxG.D[mxG.K].menus="File,Edit,View,Window";
mxG.D[mxG.K].doubleEditToolBar=1;
mxG.D[mxG.K].allowEditSgf=1;
mxG.D[mxG.K].fitParent=3;
mxG.D[mxG.K].alone=1;
mxG.D[mxG.K].createAll();
