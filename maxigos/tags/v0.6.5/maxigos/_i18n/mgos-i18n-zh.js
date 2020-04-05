// begin of localization
if (typeof mxG=='undefined') mxG={};
if (typeof mxG.Z=='undefined') mxG.Z=[];
if (!mxG.Z.zh) mxG.Z.zh=[];

// mgos.js
mxG.Z.zh["Require HTML5!"]="需要HTML5";
mxG.Z.zh["Loading..."]="下载...";

// mgosBackToMain.js
mxG.Z.zh["Back to game"]="回到游戏";

// mgosCartouche.js
mxG.Z.zh["Caps"]="提子";
mxG.Z.zh["Rank"]="等级";

// mgosComment.js
mxG.Z.zh["buildMove"]=function(k) {return k+"手";};

// mgosCut.js
mxG.Z.zh["Cut"]="剪切";

// mgosEdit.js
mxG.Z.zh["Selection"]="选择";
mxG.Z.zh["Full/partial view"]="全部/部分选中";
mxG.Z.zh["Place a move"]="黑/白走子";
mxG.Z.zh["Add/remove a stone"]="增加/删除棋子";
mxG.Z.zh["Cut branch"]="剪切分支";
mxG.Z.zh["Copy branch"]="复制分支";
mxG.Z.zh["Paste branch"]="粘贴分支";
mxG.Z.zh["Label"]="标签";
mxG.Z.zh["Mark"]="记号";
mxG.Z.zh["Circle"]="圆形";
mxG.Z.zh["Square"]="方块";
mxG.Z.zh["Triangle"]="三角";
mxG.Z.zh["Numbering"]="编号";
mxG.Z.zh["As in book"]="如在书";
mxG.Z.zh["Indices"]="坐标";
mxG.Z.zh["Variation marks"]="变化标记";
mxG.Z.zh["Variation style"]="分支显示风格";
mxG.Z.zh["Mark on last"]="标记最后一手";
mxG.Z.zh["Marks and labels"]="标记和标签";
mxG.Z.zh["Header"]="信息";
mxG.Z.zh["B"]="B";
mxG.Z.zh["I"]="I";
mxG.Z.zh["V"]="V";
mxG.Z.zh["H"]="H";
mxG.Z.zh["S"]="S";
mxG.Z.zh["OK"]="确定";
mxG.Z.zh["Cancel"]="取消";
mxG.Z.zh["New (from this point):"]="新编号:";
mxG.Z.zh["Modify"]="修改";
mxG.Z.zh["Remove"]="删除";
mxG.Z.zh["Start numbering with:"]="编号为:";
mxG.Z.zh["No numbering"]="无编号";

// mgosFile.js
mxG.Z.zh["New"]="新建";
mxG.Z.zh["Open"]="打开";
mxG.Z.zh["Close"]="关闭";
mxG.Z.zh["Save"]="保存";
mxG.Z.zh["Save on your device"]="保存";
mxG.Z.zh["Send"]="发送";
mxG.Z.zh["Send by email"]="寄电子邮件";
mxG.Z.zh["Goban size"]="棋盘大小";
mxG.Z.zh["Email:"]="电子邮件";
mxG.Z.zh["Create"]="创建";
mxG.Z.zh["Add"]="添加";
//mxG.Z.zh["OK"]="确定";
//mxG.Z.zh["Cancel"]="取消";
mxG.Z.zh["Values between 5 and 19:"]="5到19中选";
mxG.Z.zh["Values between 1 and 52:"]="1到52中选";
mxG.Z.zh["Click here to open a sgf file"]="点击这里打开一个SGF文件";
mxG.Z.zh["File name:"]="文件名:";
mxG.Z.zh["Your browser cannot do this!"]="不可能";
mxG.Z.zh["Error"]="错误";
mxG.Z.zh["Untitled"]="无题";
mxG.Z.zh["This is not a sgf file!"]="这不是SGF文件";

// mgosGoto.js
mxG.Z.zh["Guess-o-meter"]="猜-O-米";

// mgosHeader.js
//mxG.Z.zh["Header"]="信息";
mxG.Z.zh[" "]=" ";
mxG.Z.zh[", "]=", ";
mxG.Z.zh[": "]="：";
mxG.Z.zh["."]=".";
mxG.Z.zh["-"]="〜";
mxG.Z.zh["Black"]="黑";
mxG.Z.zh["White"]="白";
mxG.Z.zh[" wins"]="胜";
mxG.Z.zh["Date"]="日期";
mxG.Z.zh["Place"]="地点";
mxG.Z.zh["Rules"]="规则";
mxG.Z.zh["Time limits"]="时限";
mxG.Z.zh["Handicap"]="授子";
mxG.Z.zh["Result"]="结果";
mxG.Z.zh["none"]="没有";
mxG.Z.zh[" by resign"]="中盘";
mxG.Z.zh[" by time"]="超时";
mxG.Z.zh[" by forfeit"]="弃权";
mxG.Z.zh[" by "]="";
mxG.Z.zh["game with no result"]="无胜负";
mxG.Z.zh["draw"]="持棋";
mxG.Z.zh["unknown result"]="未知结果";
mxG.Z.zh["Komi"]="贴目";
mxG.Z.zh[" point"]="目";
mxG.Z.zh[" points"]="目";
mxG.Z.zh[" Close "]="确定";
mxG.Z.zh["h"]="点";
mxG.Z.zh["mn"]="分";
mxG.Z.zh["s"]="秒";
mxG.Z.zh[" per player"]="";
mxG.Z.zh["Japanese"]="日本";
mxG.Z.zh["Chinese"]="中国";
mxG.Z.zh["Korean"]="韩国";
mxG.Z.zh["GOE"]="GOE";
mxG.Z.zh["AGA"]="AGA";
mxG.Z.zh[" move"]="手";
mxG.Z.zh[" moves"]="手";
mxG.Z.zh["Number of moves"]="手数";

mxG.Z.zh["buildDay"]=function(a)
{
	return a.replace(/,([0-9]{2})/g,mxG.Z.zh["-"]+"$1").replace(/0([1-9])/g,"$1")+"日";
};

mxG.Z.zh["buildMonth"]=function(a)
{
	return a.replace(/0([1-9])/g,"$1")+"月";
};

mxG.Z.zh["buildDate2"]=function(s)
{
	var r,reg=/(^\s*([0-9]{2})(-([0-9]{2}(,[0-9]{2})*))?)(([^-])(.*))*\s*$/g;
	if (s.match(reg))
	{
		r=s.replace(reg,"$8");
		m=s.replace(reg,"$2");
		d=s.replace(reg,"$4");
		return mxG.Z.zh["buildMonth"](m)+(d?mxG.Z.zh["buildDay"](d):"")+(r?mxG.Z.zh[", "]+mxG.Z.zh["buildDate2"](r):"");
	}
	return s;
};

mxG.Z.zh["buildDate"]=function(s)
{
	var r,y,m,reg=/(^\s*([0-9]{4})(-([^\.]*))*)(\.)?(.*)\s*$/g;
	s=s.replace(/,([0-9]{4})/g,".$1");
	if (s.match(reg))
	{
		r=s.replace(reg,"$6");
		y=s.replace(reg,"$2");
		m=s.replace(reg,"$4");
		return (y+"年")+(m?mxG.Z.zh["buildDate2"](m):"")+(r?mxG.Z.zh[", "]+mxG.Z.zh["buildDate"](r):"");
	}
	return s;
};

mxG.Z.zh["buildRank"]=function(a)
{
	var b=a;
	if (b.match(/^[0-9]+[kdp]$/))
	{
		b=b.replace(/[dp]/,"段");
		b=b.replace("k","级");
		b=b.replace("10","十");
		b=b.replace("1","一");
		b=b.replace("2","二");
		b=b.replace("3","三");
		b=b.replace("4","四");
		b=b.replace("5","五");
		b=b.replace("6","六");
		b=b.replace("7","七");
		b=b.replace("8","八");
		b=b.replace("9","九");
	}
	return b;
};

mxG.Z.zh["buildNumOfMoves"]=function(a) {return "共"+a+"手";};

mxG.Z.zh["buildResult"]=function(a)
{
	var b="";
	if (a.substr(0,1)=="B") b=mxG.Z.zh["Black"];
	else if (a.substr(0,1)=="W") b=mxG.Z.zh["White"];
	else if (a.substr(0,1)=="V") return mxG.Z.zh["game with no result"];
	else if (a.substr(0,1)=="D") return mxG.Z.zh["draw"];
	else if (a.substr(0,1)=="0") return mxG.Z.zh["draw"];
	else if (a.substr(0,1)=="?") return mxG.Z.zh["unknown result"];
	else return a;
	b+=mxG.Z.zh[" wins"];
	if (a.substr(1,1)=="+")
	{
		if (a.substr(2,1)=="R") b+=mxG.Z.zh[" by resign"];
		else if (a.substr(2,1)=="T") b+=mxG.Z.zh[" by time"];
		else if (a.substr(2,1)=="F") b+=mxG.Z.zh[" by forfeit"];
		else if (a.length>2) b+=parseFloat(a.substr(2).replace(",","."))+mxG.Z.zh[" point"];
	}
	return b;
};

// mgosHelp.js
//mxG.Z.zh[" Close "]="确定";
mxG.Z.zh["Help"]="帮助";
mxG.Z.zh["Help not available!"]="帮助不可用";
//mxG.Z.zh["Error"]="错误";

// mgosImage.js
mxG.Z.zh["Image"]="图片";

// mgosInfo.js
mxG.Z.zh["Info"]="信息";
//mxG.Z.zh["OK"]="确定";
//mxG.Z.zh["Cancel"]="取消";
mxG.Z.zh["Event:"]="赛事名称:";
mxG.Z.zh["Round:"]="轮次:";
mxG.Z.zh["Black:"]="黑:";
mxG.Z.zh["White:"]="白:";
mxG.Z.zh["Rank:"]="等级:";
mxG.Z.zh["Komi:"]="贴目:";
mxG.Z.zh["Handicap:"]="授子:";
mxG.Z.zh["Result:"]="结果:";
mxG.Z.zh["Date:"]="日期:";
mxG.Z.zh["Place:"]="地点:";
mxG.Z.zh["Rules:"]="规则:";
mxG.Z.zh["Time limits:"]="时限:";
mxG.Z.zh["Overtime:"]="读秒方式:";
mxG.Z.zh["Annotations:"]="注解者:";
mxG.Z.zh["Copyright:"]="版权:";
mxG.Z.zh["Source:"]="棋谱来源:";
mxG.Z.zh["User:"]="录入者:";
mxG.Z.zh["Black team:"]="执黑者队名:";
mxG.Z.zh["White team:"]="执白者队名:";
mxG.Z.zh["Game name:"]="对局名称:";
mxG.Z.zh["Opening:"]="开局名称:";
mxG.Z.zh["General comment:"]="对局评论:";
mxG.Z.zh["by resign"]="中盘";
mxG.Z.zh["by time"]="超时";
mxG.Z.zh["by forfeit"]="弃权";
mxG.Z.zh["by"]="目:";
mxG.Z.zh["on points"]="目";
mxG.Z.zh["suspended"]="游戏暂停";
mxG.Z.zh["Main"]="主要";
mxG.Z.zh["Other"]="其它";
//mxG.Z.zh["Black"]="黑";
//mxG.Z.zh["White"]="白";
//mxG.Z.zh[" wins"]="胜";
//mxG.Z.zh["draw"]="持棋";
mxG.Z.zh["no result"]="无胜负";
mxG.Z.zh["unknown"]="未知";

// mgosKifu.js
mxG.Z.zh["Kifu"]="棋谱";

// mgosMenu.js
mxG.Z.zh["File"]="文件";
mxG.Z.zh["Edit"]="编辑";
//mxG.Z.zh["Cut"]="剪切";
mxG.Z.zh["Copy"]="复制";
mxG.Z.zh["Paste"]="粘贴";
mxG.Z.zh["View"]="视图";
mxG.Z.zh["Window"]="窗口";
//mxG.Z.zh["Untitled"]="无题";

// mgosMoveInfo.js
// mgosNotSeen.js
mxG.Z.zh[" elsewhere"]=" 脱先";
mxG.Z.zh[" pass"]=" 虚手";
mxG.Z.zh[" at "]=" => ";

// mgosOption.js
mxG.Z.zh["Options"]="选项";
//mxG.Z.zh["OK"]="确定";
//mxG.Z.zh["Cancel"]="取消";
//mxG.Z.zh["Mark on last"]="标记最后一手";
//mxG.Z.zh["Indices"]="坐标";
//mxG.Z.zh["As in book"]="如在书";
//mxG.Z.zh["Numbering"]="编号";
//mxG.Z.zh["Marks and labels"]="标记和标签";
//mxG.Z.zh["Variation marks"]="变化标记";
mxG.Z.zh["Show variations of current move instead of next move"]="兄弟分支显示风格";
mxG.Z.zh["In 3d"]="3D";
mxG.Z.zh["When clicking on the goban"]="当点击在棋盘上";
mxG.Z.zh["place a variation"]="一个地方的变化";
mxG.Z.zh["try to guess the next move"]="试图去猜测下一步棋";
mxG.Z.zh["from"]="第";
mxG.Z.zh["with"]="手 为";
mxG.Z.zh["Loop time:"]="循环时间：";
mxG.Z.zh["Animated stone"]="石动画";
mxG.Z.zh["Animated stone time:"]="石动画时间：";
mxG.Z.zh["Eval score"]="得分";

// mgosPass.js
mxG.Z.zh["Pass"]="虚手";

// mgosSgf.js
mxG.Z.zh["SGF"]="ＳＧＦ";
//mxG.Z.zh[" Close "]="确定";
	
// mgosScore.js
mxG.Z.zh["Score"]="得分";
mxG.Z.zh["Black:"]="黑：";
mxG.Z.zh["White:"]="白：";
mxG.Z.zh["chinese rules"]="中国规则";
mxG.Z.zh["AGA rules"]="美国规则";
mxG.Z.zh["Ing rules"]="应氏规则";
mxG.Z.zh["New-Zeland rules"]="新西兰规则";
mxG.Z.zh["japanese rules"]="日本规则";
mxG.Z.zh["territory"]="地";
mxG.Z.zh["prisoners"]="提子";
mxG.Z.zh["pass"]="虚手";
mxG.Z.zh["stones"]="子";
mxG.Z.zh["last move"]="最後一手";
mxG.Z.zh["komi"]="贴目";

// mgosSolve.js
mxG.Z.zh["Retry"]="重试";
mxG.Z.zh["Undo"]="取消";
mxG.Z.zh["Continuation"]="往最后";

// mgosTitle.js
//mxG.Z.zh[", "]=", ";

// mgosVariations.js
mxG.Z.zh["Variations: "]="变化: ";
mxG.Z.zh["no variation"]="没有变化";

// mgosView.js
mxG.Z.zh["2d/3d"]="2D/3D";
mxG.Z.zh["Zoom+"]="放大";
mxG.Z.zh["No zoom"]="正常";
mxG.Z.zh["Zoom-"]="缩小";
mxG.Z.zh["Colors"]="颜色";
mxG.Z.zh["Goban background:"]="棋盘背景";
mxG.Z.zh["Line color:"]="线颜色";
mxG.Z.zh["Variation on focus color:"]="焦点变化颜色";

// end of localization
