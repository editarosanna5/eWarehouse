if(!_.surface){_.surface=1;(function($){var b6=function(){$.Gy.call(this);this.j=[];this.G=[]},Kpa=function(a,b,c){var d=a.j.slice(),e=a.G.slice(),f=a.g("length"),h=a.g("stroke"),k=$.ap(h),l=d[0]*(1-b)+b*e[0];b=d[1]*(1-b)+b*e[1]+c/2;b=$.O(b,1)-.5;l=$.O(l,k);a.path.moveTo(l,b);a.path.lineTo(l,b+f);a.path.stroke(h)},c6=function(a,b){a*=Math.PI/180;b*=Math.PI/180;var c=Math.sin(a),d=Math.cos(a),e=Math.sin(b),f=Math.cos(b);return[d*f,-c*f,e,c,d,0,-e*d,c*e,f]},d6=function(a,b){return[b[0]*a[0]+b[1]*a[1]+b[2]*a[2],b[0]*a[3]+b[1]*a[4]+b[2]*a[5],
b[0]*a[6]+b[1]*a[7]+b[2]*a[8]]},e6=function(a,b){var c=d6(c6(0,a),[-1,0,0]),d=c[0],e=c[1];c=c[2];for(var f=d*d+e*e+c*c,h=0,k=0;k<b.length;k++){var l=b[k];h+=Math.abs(d*l[0]+e*l[1]+c*l[2]-f)/Math.sqrt(f)}return h/=b.length},Lpa=function(a,b){var c=a[0],d=a[1],e=a[2],f=c*c+d*d+e*e;return Math.abs(c*b[0]+d*b[1]+e*b[2]-f)/Math.sqrt(f)},f6=function(a,b){a=a.slice();a[1]=b.left+a[1]*b.width/1.48+b.width/2;a[2]=b.top+a[2]*b.height/1.75+b.height/2;return a},g6=function(a,b){a=a.slice();for(var c=0;c<a.length;c++)a[c]=
f6(a[c],b);return a},h6=function(){$.Iy.call(this);this.aa=[[-.5,-.5,.5],[-.5,.5,.5],[.5,.5,.5],[.5,-.5,.5]];this.b=[]},i6=function(a){a.b=c6(a.K,a.i)},j6=function(a){i6(a);for(var b=[],c=[0,0,0],d=[0,0,0],e=0;e<a.aa.length;e++){var f=a.aa[e];b.push(d6(a.b,f));b[e][1]<d[1]&&(c=f,d=b[e])}return c},k6=function(a){i6(a);for(var b=[],c=window.Infinity,d=0,e=0;e<a.aa.length;e++){var f=e6(a.i,[d6(a.b,a.aa[e])]);f<c&&(c=f,d=e);b.push(f)}return a.aa[d]},l6=function(a,b,c,d){b=d6(a.b,b);c=d6(a.b,c);a.j=b.slice();
a.G=c.slice();var e=g6([b,c],d),f=e[0][1],h=e[0][2];d=e[1][1];e=e[1][2];b=a.zIndex()+2-e6(a.i,[b,c]);c=a.fF();c.clear();c.moveTo(f,h).lineTo(d,e);c=a.Ua();a=a.vb();$.qr(c,a);var k=[f,h],l=[d,e];$.n(k)&&$.n(l)&&(c.j=k,c.G=l,c.va(1));c.zIndex(b);f=[f,h];d=[d,e];$.n(f)&&$.n(d)&&(a.j=f,a.G=d,a.va(1));a.zIndex(b);$.rr(c,a)},m6=function(){$.az.call(this);this.G=null;this.P={LX:[],aT:[]};this.ba=!1},n6=function(a){if(0==a.P.aT.length){var b=$.pk();b.parent(a.O()).zIndex(a.zIndex());b.stroke("none");a.P.LX.push(b);
return b}b=a.P.aT.pop();a.P.LX.push(b);b.parent(a.O());return b},Mpa=function(a,b){return $.n(b)?(a.ba=!!b,a):a.ba},o6=function(a){a.G=a.G?a.G:$.pk();return a.G},Npa=function(a,b){var c=a.ja(),d=c6(a.K,a.i),e=[];e[0]=d6(d,b[0]);e[1]=d6(d,b[1]);if(e6(a.i,[e[0]])>e6(a.i,[e[1]])){var f=b[1];b[1]=b[0];b[0]=f;f=e[1];e[1]=e[0];e[0]=f}b[2]=[b[1][0],b[1][1],-.5];e[2]=d6(d,b[2]);c=g6(e,c);a.j.moveTo(c[0][1],c[0][2]);a.j.lineTo(c[1][1],c[1][2]);a.G.moveTo(c[1][1],c[1][2]);a.G.lineTo(c[2][1],c[2][2])},p6=function(a,
b){$.Y.call(this);this.jb=a;this.f=b;this.La=$.nk();this.Uc=$.pk();this.f.O(this.La);this.Uc.parent(this.La);this.Uc.ta("mousemove",this.kq,!1,this);this.Uc.ta("mouseout",this.kq,!1,this);this.Uc.ta("mouseover",this.kq,!1,this)},q6=function(a){$.Y.call(this);this.jb=a;this.Uc=$.pk()},r6=function(){$.U.call(this);this.ua={};$.R(this.ua,[["enabled",0,32768],["stroke",0,8192]])},s6=function(a){$.Y.call(this);this.sa=a;this.Gc=[];this.Yc={x:[0,"x"],y:[1,"y"],z:[2,"z"]};this.La=$.nk();$.R(this.ua,[["enabled",
0,1],["size",0,1],["fill",0,1],["stroke",0,1],["type",0,1]])},Opa=function(a,b,c){var d=b.data(),e=d6(a.sa.Ug,t6(a.sa,d)),f=f6(e,c);f&&(b.b=f);b.zIndex(Lpa(a.sa.w_,e));d=d6(a.sa.Ug,t6(a.sa,[d[0],d[1],a.sa.Pu().ph()]));c=f6(d,c);a.zS();b.f.b={from:f,ef:c}},Ppa=function(a){if(!a.Gc.length&&a.ka)for(var b=a.ka.$();b.advance();){var c=b.get("x"),d=b.get("y"),e=b.get("z");$.ea(c)&&$.ea(d)&&$.ea(e)&&(c=a.ap({index:b.la(),data:[c,d,e]}),a.Gc.push(c))}return a.Gc},Qpa=function(a){var b=a.index();a=a.data();
var c={};c.index={type:"number",value:b};c.x={type:"number",value:a[0]};c.y={type:"number",value:a[1]};c.z={type:"number",value:a[2]};return c},Rpa=function(a,b,c){a.Ii||(a.Ii=new $.xw);b=b.index();var d=a.$();d.select(b);a.Ii.gg(d);return $.gv(a.Ii,c)},u6=function(a,b){function c(){var a=this.g("rotationZ"),b=this.g("rotationY"),c=$.pr(this,"xGrid"),h=$.pr(this,"yGrid"),k=$.pr(this,"zGrid"),l=$.pr(this,"xAxis"),m=$.pr(this,"yAxis"),p=$.pr(this,"zAxis");$.qr(l,m,p,c,h,k);c&&c.Cq(a).Bq(b);h&&h.Cq(a).Bq(b);
k&&k.Cq(a).Bq(b);p&&p.Cq(a).Bq(b);l&&l.Cq(a).Bq(b);m&&m.Cq(a).Bq(b);$.sr(l,m,p,c,h,k)}$.ux.call(this);this.Fa("surface");this.Ar=["z","value"];this.NX=[];this.dz=[];this.OX=[];this.za=[];this.Gz=[];this.data(a||null,b);$.R(this.ua,[["rotationZ",10485776,1,null,c],["rotationY",10485776,1,null,c],["box",16,1],["stroke",16,1]])},t6=function(a,b){var c=a.Xa().transform(b[0])-.5,d=a.bb().transform(b[1])-.5,e=1-a.Pu().transform(b[2])-.5;return[c,d,e]},v6=function(a,b){for(var c=0,d=0;d<b.length;d++)c+=
Lpa(a.w_,b[d]);c/=b.length;return 2-c},Spa=function(a){for(var b=0,c=0,d=0,e=0,f=-window.Infinity,h=0,k=window.Infinity,l=0,m=0;m<a.length;m++){a[m][1]<b&&(b=a[m][1],c=m);a[m][1]>d&&(d=a[m][1],e=m);var p=a[m][0];f<p&&(f=a[m][0],h=m);k>p&&(k=a[m][0],l=m)}return{left:a[c],right:a[e],Xoa:a[h],l3:a[l]}},w6=function(a,b,c,d){var e=$.oa(b.scale());$.Aa(a,e)||(d[c].scale=b.scale().F())},x6=function(a,b,c){c in b&&(a[c](b[c]),"scale"in b[c]&&a[c]().scale(b[c].scale))},Tpa=function(a,b){var c=new u6(a,b);
c.jd(c.pf("colorScale"));return c};$.H(b6,$.Gy);$.g=b6.prototype;$.g.oa=$.Gy.prototype.oa;$.g.qa=$.Gy.prototype.qa|16;$.g.uS=function(a,b,c,d){b=this.g("length");c=this.j.slice();var e=this.j[1],f=this.G[1],h=Math.abs(e-f);c[1]=Math.min(e,f)+h-h*a;a=this.g("stroke");e=$.ap(a);d=c[0]-d/2;d=$.O(d,1)+.5;c=c[1];c=$.O(c,e);this.path.moveTo(d,c);this.path.lineTo(d-b,c);this.path.stroke(a)};$.g.qS=function(a,b,c,d){Kpa(this,a,d)};$.g.wS=function(a,b,c,d){Kpa(this,a,d)};$.H(h6,$.Iy);$.g=h6.prototype;$.g.oa=$.Iy.prototype.oa;$.g.qa=$.Iy.prototype.qa;$.g.q0=function(){var a=$.pr(this,"title");if(a){var b=this.fF().rb().clone(),c=this.ja();switch(this.g("orientation")){case "left":$.Wv(a,"left");break;case "bottom":$.Wv(a,"bottom");b.height=c.Sa()-b.top;break;case "right":$.Wv(a,"bottom"),b.height=c.Sa()-b.top}a.ja(b);a.W()}};$.g.dF=function(a){i6(this);var b=j6(this);l6(this,b,[b[0],b[1],-.5],a)};
$.g.eF=function(a){i6(this);var b=k6(this),c=[-.5==b[0]?.5:-.5,b[1],b[2]];if(-.5==c[0]){var d=c;c=b;b=d}l6(this,b,c,a)};$.g.cF=function(a){i6(this);var b=k6(this),c=[b[0],-.5==b[1]?.5:-.5,b[2]];if(-.5==c[1]){var d=c;c=b;b=d}l6(this,b,c,a)};$.g.tQ=function(){h6.u.tQ.call(this);var a=this.zIndex()+2-e6(this.i,[this.j,this.G]);this.line.zIndex(a);this.Ua().zIndex(a);this.vb().zIndex(a)};
$.g.Yp=function(){var a=this.g("orientation");if("left"==a)var b=this.dF;else"right"==a?b=this.eF:"bottom"==a&&(b=this.cF);b.call(this,this.ja(),0,1,0,0);this.line.stroke(this.g("stroke"))};
$.g.g1=function(a,b,c,d,e,f,h){e=this.ja();h=h?this.labels():this.ob();var k=0,l=0;c=this.zIndex();var m=this.g("orientation");f=k6(this);switch(m){case "right":k=[-.5==f[0]?.5:-.5,f[1],f[2]];-.5==k[0]&&(c=f,f=k,k=c);f=d6(this.b,f);k=d6(this.b,k);c=this.zIndex()+(0>this.i?2:-2);l=g6([f,k],e);e=l[0][1];f=l[0][2];k=l[1][1];l=l[1][2];k=e*(1-a)+a*k;l=f*(1-a)+a*l+d+b.height/2;break;case "bottom":k=[f[0],-.5==f[1]?.5:-.5,f[2]];-.5==k[1]&&(c=f,f=k,k=c);f=d6(this.b,f);k=d6(this.b,k);c=1-a;l=[];l.push(a*k[0]+
c*f[0]);l.push(a*k[1]+c*f[1]);l.push(a*k[2]+c*f[2]);c=this.zIndex()+(0>this.i?2:-2);l=g6([f,k],e);e=l[0][1];f=l[0][2];k=l[1][1];l=l[1][2];k=e*(1-a)+a*k;l=f*(1-a)+a*l+d+b.height/2;break;case "left":f=j6(this),k=[f[0],f[1],-.5],f=d6(this.b,f),k=d6(this.b,k),c=this.zIndex()+2,l=g6([f,k],e),e=l[0][1],f=l[0][2],l=l[1][2],k=Math.abs(f-l),l=Math.min(f,l)+k-k*a,k=e-d-b.width/2}h.zIndex(c);return{x:k,y:l}};$.g.Bq=function(a){a=Number(a);$.n(a)&&this.i!=a&&(this.i=a,this.B(16|this.Ce,9),this.$d())};
$.g.Cq=function(a){a=Number(a);return $.n(a)?(this.K!=a&&(this.K=a,this.B(16|this.Ce,9),this.$d()),this):this.K};$.g.jl=function(){return new b6};
$.g.h1=function(a,b,c,d){a=this.ja();var e=0,f=0;this.zIndex();var h=this.g("orientation");c=k6(this);switch(h){case "right":e=[-.5==c[0]?.5:-.5,c[1],c[2]];-.5==e[0]&&(f=c,c=e,e=f);c=d6(this.b,c);e=d6(this.b,e);this.zIndex();f=g6([c,e],a);a=f[0][1];c=f[0][2];e=f[1][1];f=f[1][2];e=a*(1-b)+b*e;f=c*(1-b)+b*f+d;break;case "bottom":e=[c[0],-.5==c[1]?.5:-.5,c[2]];-.5==e[1]&&(f=c,c=e,e=f);c=d6(this.b,c);e=d6(this.b,e);f=1-b;h=[];h.push(b*e[0]+f*c[0]);h.push(b*e[1]+f*c[1]);h.push(b*e[2]+f*c[2]);this.zIndex();
f=g6([c,e],a);a=f[0][1];c=f[0][2];e=f[1][1];f=f[1][2];e=a*(1-b)+b*e;f=c*(1-b)+b*f+d;break;case "left":c=j6(this),e=[c[0],c[1],-.5],c=d6(this.b,c),e=d6(this.b,e),this.zIndex(),f=g6([c,e],a),a=f[0][1],c=f[0][2],f=f[1][2],e=Math.abs(c-f),f=Math.min(c,f)+e-e*b,e=a-d}return{x:e,y:f}};$.H(m6,$.az);$.g=m6.prototype;$.g.Iz=function(){m6.u.Iz.call(this);for(var a;a=this.P.LX.pop();)a.clear(),this.P.aT.push(a)};$.g.MJ=function(){m6.u.MJ.call(this);o6(this).stroke(this.g("stroke"))};$.g.mK=function(){m6.u.mK.call(this);var a=this.O();o6(this).parent(a)};$.g.aC=function(){var a=this.zIndex()-1,b=0>=this.i?a+2:a;this.Oh().zIndex(b);o6(this).zIndex(a);$.Dc(this.D,function(a){a.zIndex(b)})};$.g.remove=function(){m6.u.remove.call(this);o6(this).parent(null)};
$.g.Fj=function(){o6(this).clear();m6.u.Fj.call(this)};
$.g.iG=function(a){a-=.5;if(this.ba){a=-a;var b=[[-.5,-.5,a],[-.5,.5,a],[.5,.5,a],[.5,-.5,a]];a=c6(this.K,this.i);for(var c=[],d=-window.Infinity,e=0,f=0;f<b.length;f++){c[f]=d6(a,b[f]);var h=e6(this.i,[c[f]]);h>d&&(d=h,e=f)}c=b[e];c=[[-c[0],c[1],c[2]],[c[0],c[1],c[2]],[c[0],-c[1],c[2]]];for(f=0;f<c.length;f++)c[f]=d6(a,c[f]);a=this.ja();a=g6(c,a);this.G.moveTo(a[0][1],a[0][2]);this.G.lineTo(a[1][1],a[1][2]);this.G.lineTo(a[2][1],a[2][2])}else a=[[a,-.5,.5],[a,.5,.5]],Npa(this,a)};
$.g.jG=function(a){a-=.5;Npa(this,[[-.5,a,.5],[.5,a,.5]])};
$.g.fG=function(a,b,c){if(Mpa(this)){var d=n6(this),e=-(a-.5);a=-(b-.5);var f=[[-.5,-.5,e],[-.5,.5,e],[.5,.5,e],[.5,-.5,e]];e=c6(this.K,this.i);b=[];for(var h=-window.Infinity,k=0,l=0;l<f.length;l++){b[l]=d6(e,f[l]);var m=e6(this.i,[b[l]]);m>h&&(h=m,k=l)}l=f[k];b=[[-l[0],l[1],l[2]],[l[0],l[1],l[2]],[l[0],-l[1],l[2]]];a=[[-l[0],l[1],a],[l[0],l[1],a],[l[0],-l[1],a]];for(l=0;l<b.length;l++)b[l]=d6(e,b[l]),a[l]=d6(e,a[l]);e=this.ja();b=g6(b,e);a=g6(a,e);d.moveTo(b[0][1],b[0][2]);d.lineTo(b[1][1],b[1][2]);
d.lineTo(b[2][1],b[2][2]);d.lineTo(a[2][1],a[2][2]);d.lineTo(a[1][1],a[1][2]);d.lineTo(a[0][1],a[0][2]);d.close();d.fill(c.fill());d.zIndex(20)}else{d=c6(this.K,this.i);a-=.5;b-=.5;a=[[a,-.5,.5],[a,.5,.5]];a.push([b,.5,.5]);a.push([b,-.5,.5]);f=[];for(h=0;h<a.length;h++)f.push(d6(d,a[h]));e=this.ja();l=g6(f,e);c.moveTo(l[0][1],l[0][2]);c.lineTo(l[1][1],l[1][2]);c.lineTo(l[2][1],l[2][2]);c.lineTo(l[3][1],l[3][2]);c.close();l=n6(this);k=[];f[0][0]>f[1][0]?k.push(a[0]):k.push(a[1]);k.push([k[0][0],k[0][1],
-.5]);k.push([b,k[0][1],-.5]);k.push([b,k[0][1],.5]);b=[];for(h=0;h<a.length;h++)b.push(d6(d,k[h]));d=g6(b,e);l.moveTo(d[0][1],d[0][2]);l.lineTo(d[1][1],d[1][2]);l.lineTo(d[2][1],d[2][2]);l.lineTo(d[3][1],d[3][2]);l.close();l.fill(c.fill());l.zIndex(20)}};
$.g.gG=function(a,b,c){var d=c6(this.K,this.i);a-=.5;b-=.5;a=[[-.5,a,.5],[.5,a,.5]];a.push([.5,b,.5]);a.push([-.5,b,.5]);for(var e=[],f=0;f<a.length;f++)e.push(d6(d,a[f]));var h=this.ja(),k=g6(e,h);c.moveTo(k[0][1],k[0][2]);c.lineTo(k[1][1],k[1][2]);c.lineTo(k[2][1],k[2][2]);c.lineTo(k[3][1],k[3][2]);c.close();k=n6(this);k.clear();var l=[];e[0][0]>e[1][0]?l.push(a[0]):l.push(a[1]);l.push([l[0][0],l[0][1],-.5]);l.push([l[0][0],b,-.5]);l.push([l[0][0],b,.5]);b=[];for(f=0;f<a.length;f++)b.push(d6(d,
l[f]));d=g6(b,h);k.moveTo(d[0][1],d[0][2]);k.lineTo(d[1][1],d[1][2]);k.lineTo(d[2][1],d[2][2]);k.lineTo(d[3][1],d[3][2]);k.close();k.fill(c.fill());k.zIndex(20)};$.g.Cq=function(a){return $.n(a)?(this.K=+a,this.B(28,1),this):this.K};$.g.Bq=function(a){$.n(a)&&(this.i=+a,this.B(28,1))};$.g.R=function(){$.od(this.G);this.G=null;m6.u.R.call(this)};$.H(p6,$.Y);$.g=p6.prototype;$.g.W=function(){if(this.jb.g("enabled")){this.La.parent(this.O());this.f.W();var a=$.vp(this.jb.qc(this,"type")),b=this.jb.qc(this,"size");this.Uc.clear();a(this.Uc,this.b[1],this.b[2],b,b);this.Uc.fill(this.jb.qc(this,"fill"));this.Uc.stroke(this.jb.qc(this,"stroke"))}else this.La.parent(null)};$.g.kq=function(a){var b=this.jb;"mousemove"===a.type||"mouseover"===a.type?$.Gw(b.Ra(),a.clientX,a.clientY,Rpa(b,this,Qpa(this))):"mouseout"===a.type&&b.Ra().Ic()};
$.g.data=function(a){return a?(this.ka=a,this):this.ka};$.g.index=function(a){return $.n(a)?(this.zd=a,this):this.zd};$.g.R=function(){$.sd(this.La,this.Uc,this.f);this.Uc=this.La=this.f=null;p6.u.R.call(this)};$.H(q6,$.Y);q6.prototype.Px=function(){this.Uc.moveTo(this.b.from[1],this.b.from[2]);this.Uc.lineTo(this.b.ef[1],this.b.ef[2])};q6.prototype.W=function(){this.jb.g("enabled")?(this.Uc.clear(),this.Uc.parent(this.O()),this.Px(),this.Uc.stroke(this.jb.xp(this))):this.Uc.parent(null)};q6.prototype.R=function(){$.od(this.Uc);this.Uc=null;q6.u.R.call(this)};$.H(r6,$.U);r6.prototype.oa=40960;var y6={};$.nq(y6,[[0,"enabled",$.yq],[1,"stroke",$.Jq]]);$.S(r6,y6);r6.prototype.xp=function(){return this.g("stroke")};r6.prototype.F=function(){var a={};$.Nq(this,y6,a);return a};r6.prototype.U=function(a,b){$.Fq(this,y6,a,b)};$.H(s6,$.Y);s6.prototype.oa=17;var z6={};$.nq(z6,[[0,"enabled",$.yq],[0,"type",$.Xq],[0,"size",$.wq],[1,"fill",$.Hq],[1,"stroke",$.Gq]]);$.S(s6,z6);$.g=s6.prototype;$.g.Mfa=function(){this.va(1)};$.g.zS=function(a){this.b||(this.b=new r6,$.W(this,"droplines",this.b),$.K(this.b,this.Mfa,this));return $.n(a)?(this.b.N(a),this):this.b};$.g.ap=function(a){var b=new p6(this,new q6(this.zS()));b.data(a.data);b.index(a.index);return b};
$.g.W=function(a){this.J(2)&&(this.La.parent(this.O()),this.I(2));for(var b,c=Ppa(this),d=0;d<c.length;d++)b=c[d],Opa(this,b,a);c.sort(function(a,b){return b.zIndex()-a.zIndex()});for(d=0;d<c.length;d++)b=c[d],b.O(this.La),b.W()};$.g.$=function(){return this.ka.$()};$.g.bd=function(){$.sd(this.Gc);this.Gc.length=0;this.va(16)};
$.g.data=function(a,b){return $.n(a)?(this.Pf!==a&&(this.Pf=a,this.ka&&$.dr(this.ka,this.bd,this),$.sd(this.ka,this.Xc),$.J(a,$.vr)?this.ka=a.Li():this.ka=$.J(a,$.Fr)?a.Xd(this.Yc):(this.Xc=new $.Fr($.A(a)||$.z(a)?a:null,b)).Xd(this.Yc),$.K(this.ka,this.bd,this),this.bd()),this):this.ka};$.g.qc=function(a,b){var c=this.$();c.select(a.index());c=c.get(b)||this.g(b);if($.E(c)){var d=Qpa(a);d.sourceColor={type:"",value:this.sa.xp(a.data()[2])};d=Rpa(this,a,d);return c.call(d,d)}return c};
$.g.Ra=function(a){this.fb||(this.fb=new $.ow(0),this.fb.$e(),$.W(this,"tooltip",this.fb),this.fb.parent(this.sa.Ra()),this.fb.Ca(this.sa));return $.n(a)?(this.fb.N(a),this):this.fb};$.g.F=function(){var a={};$.Nq(this,z6,a);var b=this.data();b&&(a.data=b.F());return a};$.g.U=function(a,b){$.Fq(this,z6,a,b);var c=a.data;c&&this.data(c)};$.g.R=function(){$.sd(this.Gc,this.b,this.La);this.Gc.length=0;this.sa=null};var A6=s6.prototype;A6.droplines=A6.zS;A6.tooltip=A6.Ra;$.H(u6,$.ux);var B6=function(){var a={};$.nq(a,[[0,"rotationZ",$.wq],[0,"rotationY",function(a){a=Number(a);return $.Za(a,-90,90)}],[1,"box",$.Jq],[1,"stroke",$.Jq]]);return a}();$.S(u6,B6);$.g=u6.prototype;$.g.oa=$.ux.prototype.oa;$.g.qa=$.ux.prototype.qa|10580496;$.g.cY=function(a){this.Ou||(this.Ou=new h6,$.K(this.Ou,this.po,this),this.Ou.Cq(this.g("rotationZ")),this.Ou.Bq(this.g("rotationY")),$.W(this,"zAxis",this.Ou));return $.n(a)?(this.Ou.N(a),this):this.Ou};
$.g.Uh=function(a){this.kc||(this.kc=new h6,$.K(this.kc,this.po,this),this.kc.Cq(this.g("rotationZ")),this.kc.Bq(this.g("rotationY")),$.W(this,"xAxis",this.kc));return $.n(a)?(this.kc.N(a),this):this.kc};$.g.dj=function(a){this.$k||(this.$k=new h6,$.K(this.$k,this.po,this),this.$k.Cq(this.g("rotationZ")),this.$k.Bq(this.g("rotationY")),$.W(this,"yAxis",this.$k));return $.n(a)?(this.$k.N(a),this):this.$k};$.g.po=function(){this.B(2097152,1)};$.g.Kf=function(){return this.ka.$()};
$.g.zc=function(){return this.rd=this.ka.$()};$.g.$=function(){return this.rd||(this.rd=this.ka.$())};$.g.sj=function(){return!this.ka.$().Ib()};$.g.data=function(a,b){return $.n(a)?(this.Pf!==a&&(this.Pf=a,this.ka&&$.dr(this.ka,this.bd,this),$.od(this.ka),$.od(this.Xc),$.J(a,$.vr)?this.ka=a.Li():this.ka=$.J(a,$.Fr)?a.Xd():(this.Xc=new $.Fr($.A(a)||$.z(a)?a:null,b)).Xd(),$.K(this.ka,this.bd,this),this.B(4608,1)),this):this.ka};$.g.bd=function(){this.B(4352,1)};
$.g.MS=function(a,b,c){return(0,$.za)(c,a)===b};
$.g.pb=function(){if(this.J(4096)){this.B(16384);var a=this.data().$();a.reset();for(var b=[],c=[],d=[];a.advance();){var e=$.N(a.get("x")),f=$.N(a.get("y")),h=$.N(a.get("z"));b.push(e);c.push(f);d.push(h)}b=(0,$.Ye)(b,this.MS);c=(0,$.Ye)(c,this.MS);d=(0,$.Ye)(d,this.MS);this.NX=b;this.dz=c;this.OX=d;a=a.Ib();3E3<a&&$.hl(800,null,[a],!0);(b.length*c.length!=a||4>a)&&0!=a?($.fl(400),this.U3=!0):this.U3=!1;this.I(4096)}c=this.Xa();a=this.bb();d=this.Pu();b=!1;c.Of()&&c.xg();a.Of()&&a.xg();d.Of()&&d.xg();
e=[];f=[];h=[];var k=$.pr(this,"markers");if(k){k=Ppa(k);for(var l=0;l<k.length;l++){var m=k[l].data();e.push(m[0]);f.push(m[1]);h.push(m[2])}}c.Vc.apply(c,$.Ga(this.NX,e));a.Vc.apply(a,$.Ga(this.dz,f));d.Vc.apply(d,$.Ga(this.OX,h));c.Of()&&(b|=c.Dg());a.Of()&&(b|=a.Dg());d.Of()&&(b|=d.Dg());this.J(16384)&&this.tb&&(c=$.Ga(this.OX,h),this.tb.Of()?(this.tb.xg(),this.tb.Vc.apply(this.tb,c),this.tb.Dg()):(this.tb.vo(),this.tb.Vc.apply(this.tb,c)),$.J(this.tb,$.Zy)&&$.Ht(this.tb.Ua()),this.B(16),this.I(16384));
b&&this.B(65536)};$.g.Il=function(){this.pb();for(var a=this.Pu().Ua().get(),b=[],c=0;c<a.length;c++){var d=this.xp(a[c]);d={index:0,text:a[c],iconEnabled:!0,iconType:"square",iconStroke:$.Rl(d,1),iconFill:d,disabled:!this.enabled()};b.push(d)}return b};$.g.Qe=function(){return[]};$.g.Xa=function(a){if($.n(a)){if(a=$.ct(this.cb,a,null,15))this.cb=a,this.cb.ea(!1),this.B(16,1);$.K(this.cb,this.KD,this);return this}this.cb||(this.cb=$.Ys(),$.K(this.cb,this.KD,this));return this.cb};
$.g.bb=function(a){if($.n(a)){if(a=$.ct(this.Cc,a,null,15))this.Cc=a,this.Cc.ea(!1),this.B(16,1);$.K(this.Cc,this.KD,this);return this}this.Cc||(this.Cc=$.Ys(),$.K(this.Cc,this.KD,this));return this.Cc};$.g.Pu=function(a){if($.n(a)){if(a=$.ct(this.kz,a,null,15))this.kz=a,this.kz.ea(!1),this.B(16,1);$.K(this.kz,this.KD,this);return this}this.kz||(this.kz=$.Ys(),$.K(this.kz,this.KD,this));return this.kz};
$.g.jd=function(a){if($.n(a)){if(null===a&&this.tb)this.tb=null,this.B(16400,1);else if(a=$.ct(this.tb,a,null,48,null,this.Mp,this)){var b=this.tb==a;this.tb=a;this.tb.ea(b);b||this.B(16400,1)}return this}return this.tb};$.g.Mp=function(a){$.X(a,6)&&this.B(16896,1)};$.g.KD=function(){this.B(16,1)};$.g.xp=function(a){var b=this.jd(),c;b?c=b.Jq(a):c=this.$b().oc(0);return c};
$.g.Gi=function(a){this.Kb||(this.Kb=new $.vN,$.W(this,"colorRange",this.Kb),this.Kb.fa(!0,this.Kb.na),this.Kb.ur().Fa("defaultMarkerFactory","surface.colorRange.marker"),this.Kb.ur().fa(!0,this.Kb.ur().na),this.Kb.ib(this),this.Kb.O(this.Ma),$.K(this.Kb,this.Jz,this),this.B(8196,1));return $.n(a)?(this.Kb.N(a),this):this.Kb};$.g.Jz=function(a){var b=0,c=0;$.X(a,1)&&(b|=8208,c|=1);$.X(a,8)&&(b|=4,c|=8);this.B(b,c)};
$.g.wq=function(a){a.button==$.Oi&&(this.k3=!0,this.xja=a.clientX,this.yja=a.clientY,this.Aja=this.g("rotationY"),this.Bja=this.g("rotationZ"))};$.g.zh=function(a){"mouseup"==a.type&&(this.k3=!1)};$.g.zf=function(a){if(this.k3){$.V(this);var b=this.nb();this.rotationY(this.Aja-(this.yja-a.clientY)/b.height*110);this.rotationZ(this.Bja+(this.xja-a.clientX)/b.width*110);this.ea(!0)}};
$.g.Kh=function(a){if(!this.kf()){var b=this.g("rotationZ"),c=this.g("rotationY"),d=[-1,0,0];this.w_=d=d6(c6(0,c),d);b*=Math.PI/180;c*=Math.PI/180;d=Math.sin(b);b=Math.cos(b);var e=Math.sin(c);c=Math.cos(c);this.Ug=[b*c,-d*c,e,d,b,0,-e*b,d*e,c];c=$.pr(this,"xGrid");d=$.pr(this,"yGrid");b=$.pr(this,"zGrid");e=$.pr(this,"xAxis");var f=$.pr(this,"yAxis"),h=$.pr(this,"zAxis");this.La||(this.La=this.Ma.Ad(),this.La.zIndex(36),this.CE=this.La.Ad(),this.CE.zIndex(36));var k=$.pr(this,"colorRange");this.pb();
this.J(65536)&&(h&&!h.scale()&&h.scale(this.Pu()),e&&!e.scale()&&e.scale(this.Xa()),f&&!f.scale()&&f.scale(this.bb()),c&&c.scale(this.Xa()),d&&d.scale(this.bb()),b&&b.scale(this.Pu()),this.I(65536));this.J(8192)&&k&&($.V(k),k.scale(this.jd()),k.target(this),k.ea(!1),this.B(4));this.J(4)&&(k?(k.ja(a.clone()),this.gf=k.xd()):this.gf=a.clone(),this.gf.left+=10,this.gf.width-=10,h&&h.ja(this.gf),f&&f.ja(this.gf),e&&e.ja(this.gf),c&&c.ja(this.gf),d&&d.ja(this.gf),b&&b.ja(this.gf),this.B(512),this.I(4));
this.J(8192)&&(k&&($.V(k),k.O(this.Ma),k.W(),k.ea(!1)),this.I(8192));a=this.gf;var l=[[-.5,-.5,.5],[-.5,.5,.5],[.5,.5,.5],[.5,-.5,.5]];h=[[-.5,-.5,-.5],[-.5,.5,-.5],[.5,.5,-.5],[.5,-.5,-.5]];if(0==this.Gz.length)for(f=0;12>f;f++)this.Gz[f]=this.La.path();else for(f=0;f<this.Gz.length;f++)this.Gz[f].clear();e=this.g("box");var m=[],p=[];for(f=0;f<l.length;f++)m[f]=d6(this.Ug,l[f]),p[f]=d6(this.Ug,h[f]);f=Spa(m);h=f.l3;k=Spa(p).l3;var q=f.left,r=[];for(f=0;f<m.length;f++){var t=f==m.length-1?0:f+1,
u=m[t],v=m[f],w=Math.abs(l[f][0]-l[t][0]),x=Math.abs(l[f][1]-l[t][1]),y=$.pr(this,"xAxis"),B=$.pr(this,"yAxis");t=$.pr(this,"zAxis");(y&&y.enabled(),B&&B.enabled(),y&&y.enabled()&&w>x&&0<=[v,u].indexOf(h))||B&&B.enabled()&&x>w&&0<=[v,u].indexOf(h)||r.push([m[f],u])}for(f=0;f<p.length;f++)u=f==p.length-1?p[0]:p[f+1],r.push([p[f],u]);for(f=0;f<m.length;f++)t&&t.enabled()&&m[f]==q||r.push([m[f],p[f]]);for(f=0;f<r.length;f++){t=r[f][0];l=r[f][1];m=Math.min(v6(this,[t]),v6(this,[l]));if(t==h||l==h||t==
k||l==k)m=100;this.Yp(this.Gz[f],g6(r[f],a),m,e)}this.J(8388608)&&(c&&(c.O(this.Ma),c.W()),d&&(d.O(this.Ma),d.W()),b&&(b.O(this.Ma),b.W()),this.I(8388608));if(this.J(16)){t=this.gf;c=this.data().$();c.reset();d=this.g("stroke");this.CE.Wi();if(!this.U3){this.La.suspend();for(b=0;b<this.NX.length-1;b++)for(a=0;a<this.dz.length-1;a++){f=b*(this.dz.length-1)+a;(e=this.za[f])?(e.parent(this.CE),e.clear()):(e=this.za[f]=this.CE.path(),e.stroke(null));c.select(b*this.dz.length+a);f=[$.N(c.get("x")),$.N(c.get("y")),
$.N(c.get("z"))];c.select((b+1)*this.dz.length+a);h=[$.N(c.get("x")),$.N(c.get("y")),$.N(c.get("z"))];c.select((b+1)*this.dz.length+a+1);k=[$.N(c.get("x")),$.N(c.get("y")),$.N(c.get("z"))];c.select(b*this.dz.length+a+1);r=[$.N(c.get("x")),$.N(c.get("y")),$.N(c.get("z"))];l=[f,h,k,r];p=this.Pu();m=p.ph();p=p.ai();u=this.bb();q=u.ph();u=u.ai();w=this.Xa();v=w.ph();w=w.ai();x=0;y=[];for(B=0;B<l.length;B++){var G=l[B];if(!(G[2]>=m&&G[2]<=p&&G[1]<=u&&G[1]>=q&&G[0]>=v&&G[0]<=w)){x++;if(G[2]<m||G[2]>p)G[2]=
Math.max(Math.min(G[2],p),m);if(G[1]<u||G[1]>q)G[1]=Math.max(Math.min(G[1],u),q);if(G[0]>v||G[0]<w)G[0]=Math.max(Math.min(G[0],w),v)}y.push(G)}if(l=x==l.length?null:y)m=d6(this.Ug,t6(this,l[0])),p=d6(this.Ug,t6(this,l[1])),q=d6(this.Ug,t6(this,l[2])),u=d6(this.Ug,t6(this,l[3])),l=36+v6(this,[m,p,q,u]),m=g6([m,p,q,u],t),f=this.xp((f[2]+h[2]+k[2]+r[2])/4),this.Yp(e,m,l,d),e.close(),e.fill(f||"white")}this.La.resume()}if(t=$.pr(this,"markers"))t.O(this.La),t.W(this.gf);this.I(16)}this.J(2097152)&&(t=
$.pr(this,"xAxis"),c=$.pr(this,"yAxis"),d=$.pr(this,"zAxis"),t&&t.O(this.Ma).W(),c&&c.O(this.Ma).W(),d&&d.O(this.Ma).W(),this.I(2097152))}};$.g.Yp=function(a,b,c,d){a.clear();a.moveTo(b[0][1],b[0][2]);for(var e=1;e<b.length;e++)a.lineTo(b[e][1],b[e][2]);a.zIndex(c);a.stroke(d)};
$.g.gm=function(a){this.jt||(this.jt=new m6,this.jt.Tj=this,$.K(this.jt,this.Fy,this),this.jt.Cq(this.g("rotationZ")),this.jt.Bq(this.g("rotationY")),$.W(this,"xGrid",this.jt),this.B(8388608,1));return $.n(a)?(this.jt.N(a),this):this.jt};$.g.im=function(a){this.Kr||(this.Kr=new m6,this.Kr.Tj=this,$.K(this.Kr,this.Fy,this),this.Kr.Cq(this.g("rotationZ")),this.Kr.Bq(this.g("rotationY")),$.cz(this.Kr,"vertical"),$.W(this,"yGrid",this.Kr),this.B(8388608,1));return $.n(a)?(this.Kr.N(a),this):this.Kr};
$.g.wJ=function(a){this.Oq||(this.Oq=new m6,this.Oq.Tj=this,$.K(this.Oq,this.Fy,this),this.Oq.Cq(this.g("rotationZ")),this.Oq.Bq(this.g("rotationY")),$.cz(this.Oq,"horizontal"),Mpa(this.Oq,!0),$.W(this,"zGrid",this.Oq),this.B(8388608,1));return $.n(a)?(this.Oq.N(a),this):this.Oq};$.g.Fy=function(){this.B(8388624,1)};$.g.En=function(a){var b=8388624;$.X(a,16)&&(b|=65536);this.gm().B(this.gm().qa);this.im().B(this.im().qa);this.wJ().B(this.wJ().qa);this.B(b,1)};
$.g.Db=function(a){this.Gc||(this.Gc=new s6(this),$.W(this,"markers",this.Gc),$.K(this.Gc,this.En,this));return $.n(a)?(this.Gc.N(a),this):this.Gc};$.g.nc=function(){return this};$.g.ae=function(){return null};$.g.$b=function(a){if($.J(a,$.Es))return this.Ec($.Es,a),this;if($.J(a,$.Bs))return this.Ec($.Bs,a),this;$.C(a)&&"range"==a.type?this.Ec($.Es):($.C(a)||null==this.Ea)&&this.Ec($.Bs);return $.n(a)?(this.Ea.N(a),this):this.Ea};
$.g.Ec=function(a,b){if($.J(this.Ea,a))b&&this.Ea.N(b);else{var c=!!this.Ea;$.od(this.Ea);this.Ea=new a;$.W(this,"palette",this.Ea);this.Ea.yp();b&&this.Ea.N(b);$.K(this.Ea,this.Ff,this);c&&this.B(16,1)}};$.g.Ff=function(a){$.X(a,2)&&this.B(16,1)};$.g.Na=function(){return"surface"};
$.g.F=function(){var a=u6.u.F.call(this);this.ka&&(a.data=this.data().F());var b=[];this.cb&&(a.xScale=this.Xa().F(),b.push($.oa(this.Xa())));this.Cc&&(a.yScale=this.bb().F(),b.push($.oa(this.bb())));this.kz&&(a.zScale=this.Pu().F(),b.push($.oa(this.Pu())));this.kc&&(a.xAxis=this.Uh().F(),w6(b,this.Uh(),"xAxis",a));this.$k&&(a.yAxis=this.dj().F(),w6(b,this.dj(),"yAxis",a));this.Ou&&(a.zAxis=this.cY().F(),w6(b,this.cY(),"zAxis",a));this.jt&&(a.xGrid=this.gm().F(),w6(b,this.gm(),"xGrid",a));this.Kr&&
(a.yGrid=this.im().F(),w6(b,this.im(),"yGrid",a));this.Oq&&(a.zGrid=this.wJ().F(),w6(b,this.wJ(),"zGrid",a));a.colorScale=this.tb?this.jd().F():null;this.Kb&&(a.colorRange=this.Gi().F());(b=$.pr(this,"markers"))&&(a.markers=b.F());$.Nq(this,B6,a);return{chart:a}};
$.g.U=function(a,b){u6.u.U.call(this,a,b);"data"in a&&this.data(a.data);x6(this,a,"xScale");x6(this,a,"yScale");x6(this,a,"zScale");x6(this,a,"xAxis");x6(this,a,"yAxis");x6(this,a,"zAxis");x6(this,a,"xGrid");x6(this,a,"yGrid");x6(this,a,"zGrid");"colorScale"in a&&this.jd(a.colorScale);"colorRange"in a&&this.Gi(a.colorRange);"markers"in a&&this.Db(a.markers);$.Fq(this,B6,a,b)};
$.g.R=function(){$.sd(this.Gc,this.kc,this.$k,this.Ou,this.za,this.jt,this.Kr,this.Oq,this.Gz,this.CE,this.La,this.Kb,this.Ea,this.ka,this.Xc);this.Gz.length=0;this.za.length=0;this.Oq=this.Kr=this.jt=this.Ou=this.$k=this.kc=this.Xc=this.ka=this.Ea=this.Kb=this.La=this.CE=null;u6.u.R.call(this)};$.g.ns=function(){return[this]};$.g.QC=function(){return["x","y","z"]};var C6=u6.prototype;C6.colorScale=C6.jd;C6.colorRange=C6.Gi;C6.xGrid=C6.gm;C6.yGrid=C6.im;C6.zGrid=C6.wJ;C6.xAxis=C6.Uh;C6.yAxis=C6.dj;
C6.zAxis=C6.cY;C6.xScale=C6.Xa;C6.yScale=C6.bb;C6.zScale=C6.Pu;C6.getType=C6.Na;C6.palette=C6.$b;C6.markers=C6.Db;$.Op.surface=Tpa;$.F("anychart.surface",Tpa);}).call(this,$)}