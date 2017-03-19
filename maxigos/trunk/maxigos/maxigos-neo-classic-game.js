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
a+="div.mxNeoClassicGlobalBoxDiv.mxGameGlobalBoxDiv div.mxHeaderDiv{padding-top:0.5rem;padding-bottom:0.5rem;text-align:left;margin:0 auto;}"
a+="div.mxNeoClassicGlobalBoxDiv.mxGameGlobalBoxDiv div.mxHeaderDiv h1{text-align:left;font-size:1em;margin:0;padding:0;}";
e.type='text/css';
if (e.styleSheet) e.styleSheet.cssText=a;
else e.appendChild(document.createTextNode(a));
document.getElementsByTagName('head')[0].appendChild(e);
})();
mxG.K++;
mxG.D[mxG.K]=new mxG.G(mxG.K);
mxG.D[mxG.K].path=mxG.GetDir()+"../../../";
mxG.D[mxG.K].theme="NeoClassic";
mxG.D[mxG.K].config="Game";
mxG.D[mxG.K].b[0]={n:"HeaderBox",c:["Title","Header"]};
mxG.D[mxG.K].b[1]={n:"MainBox",c:["Goban","Navigation","Loop","Variations"]};
mxG.D[mxG.K].b[2]={n:"VersionBox",c:["Version"]};
mxG.D[mxG.K].markOnLastOn=1;
mxG.D[mxG.K].markOnLastColor="#c33";
mxG.D[mxG.K].in3dOn=1;
mxG.D[mxG.K].stretchOn=1;
mxG.D[mxG.K].initMethod="last";
mxG.D[mxG.K].headerBoxOn=1;
mxG.D[mxG.K].hidePlace=1;
mxG.D[mxG.K].hideTimeLimits=1;
mxG.D[mxG.K].hideRules=1;
mxG.D[mxG.K].hideGeneralComment=1;
mxG.D[mxG.K].concatTeamToPlayer=1;
mxG.D[mxG.K].concatNumOfMovesToResult=1;
mxG.D[mxG.K].hideNumOfMovesLabel=1;
mxG.D[mxG.K].hideNumOfMoves=0;
mxG.D[mxG.K].hideResultLabel=1;
mxG.D[mxG.K].variationsBoxOn=0;
mxG.D[mxG.K].hideSingleVariationMarkOn=1;
mxG.D[mxG.K].variationMarksOn=1;
mxG.D[mxG.K].variationOnFocusColor="#c33";
mxG.D[mxG.K].focusColor="#c33";
mxG.D[mxG.K].canPlaceVariation=1;
mxG.D[mxG.K].navigations="First,TenPred,Pred,Loop,Next,TenNext,Last";
mxG.D[mxG.K].maximizeGobanWidth=1;
mxG.D[mxG.K].adjustHeaderWidth=1;
mxG.D[mxG.K].adjustNavigationWidth=1;
mxG.D[mxG.K].fitParent=3;
mxG.D[mxG.K].alone=1;
mxG.D[mxG.K].createAll();
