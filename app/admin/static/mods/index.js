/**
 @Name: 靑年PHP后台主入口
 */
layui.define(['layer', 'laytpl', 'table', 'form', 'upload', 'util', 'element', 'laydate'], function(exports){

	var $ = layui.jquery
	,layer = layui.layer
	,laytpl = layui.laytpl
	,form = layui.form
	,util = layui.util
	,device = layui.device()
	,element = layui.element
	,laydate = layui.laydate
	,table = layui.table
	,upload = layui.upload; //导航的hover效果、二级菜单等功能，需要依赖element模块

	element.init();
	table.init();
	
	lay('.time_layui_element').each(function(){
		laydate.render({
		  elem: this
		  ,type: 'time'
		  ,trigger: 'click'
		});
	});
	
	lay('.datetime_layui_element').each(function(){
		laydate.render({
		  elem: this
		  ,type: 'datetime'
		  ,trigger: 'click'
		});
	});
	
	lay('.date_layui_element').each(function(){
		laydate.render({
		  elem: this
		  ,type: 'date'
		  ,trigger: 'click'
		});
	});

	//阻止IE7以下访问
	if(device.ie && device.ie < 8){
		layer.alert('如果您非得使用ie浏览，那么请使用ie8+');
	}

	layui.focusInsert = function(obj, str){
		var result, val = obj.value;
		obj.focus();
		if(document.selection){ //ie
		  result = document.selection.createRange(); 
		  document.selection.empty(); 
		  result.text = str; 
		} else {
		  result = [val.substring(0, obj.selectionStart), str, val.substr(obj.selectionEnd)];
		  obj.focus();
		  obj.value = result.join('');
		}
	};
	
	var _spinner = '<img src="public/images/loading.gif" class="layui-icon" width="16px"/>';
	var _btnOriginText = '';
	var beforeSend = function beforeSend(btn) {
		btn[['prop']]('disabled', true);
		_btnOriginText = btn[['html']]();
		btn[['html']](_spinner);
		element.init();
	};
	var finishRequest = function finishRequest(btn) {
		btn[['html']](_btnOriginText);
		btn[['prop']]('disabled', false);
	};
	
	var end = function(res, obj){
		if(res.action || res.url){
			location.href = res.action ? res.action : res.url;
		} else {
			finishRequest(obj);
		}
	};
	
	var success = function(res, obj){
		if(res.status == 0){
			layer.msg(res.info, {
				icon: 2,
				fixed: true, 
				offset: '80%',
				time: 1000,
			}, function(){
				end(res, obj);
			}); 
		}else if(res.status == 1){
			layer.msg(res.info, {
				icon: 1,
				fixed: true, 
				offset: '80%',
				time: 1000,
			}, function(){
				end(res, obj);
			});
		};
	};

	var gather = {
		json: function(url, data, obj, options){
			var that = this;
			options = options || {};
			data = data || {};
			$.ajax({
				type: options.type || 'post',
				dataType: options.dataType || 'json',
				data: data,
				url: url,
				beforeSend: beforeSend(obj),
				success: function(res){ success(res, obj); },
				error: function(e){
					options.error || layer.msg('请求异常，请重试', {shift: 6, icon: 2, fixed: true, offset: '80%', time: 1000});
				}
			});
		}

		//计算字符长度
		,charLen: function(val){
			var arr = val.split(''), len = 0;
			for(var i = 0; i <  val.length ; i++){
				arr[i].charCodeAt(0) < 299 ? len++ : len += 2;
			}
			return len;
		}

		,form: {}

		,escape: function(html){
			return String(html||'').replace(/&(?!#?[a-zA-Z0-9]+;)/g, '&amp;')
			.replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/'/g, '&#39;').replace(/"/g, '&quot;');
		}

		//内容转义
		,content: function(content){
			//支持的html标签
			var html = function(end){
				return new RegExp('\\['+ (end||'') +'(pre|div|table|thead|th|tbody|tr|td|ul|li|ol|li|dl|dt|dd|h2|h3|h4|h5)\\]\\n*', 'g');
			};
			content = gather.escape(content||'') //XSS
			.replace(/img\[([^\s]+?)\]/g, function(img){  //转义图片
				return '<img src="' + img.replace(/(^img\[)|(\]$)/g, '') + '">';
			}).replace(/@(\S+)(\s+?|$)/g, '@<a href="javascript:;" class="fly-aite">$1</a>$2') //转义@
			.replace(/face\[([^\s\[\]]+?)\]/g, function(face){  //转义表情
				var alt = face.replace(/^face/g, '');
				return '<img alt="'+ alt +'" title="'+ alt +'" src="' + gather.faces[alt] + '">';
			}).replace(/a\([\s\S]+?\)\[[\s\S]*?\]/g, function(str){ //转义链接
				var href = (str.match(/a\(([\s\S]+?)\)\[/)||[])[1];
				var text = (str.match(/\)\[([\s\S]*?)\]/)||[])[1];
				if(!href) return str;
				var rel =  /^(http(s)*:\/\/)\b(?!(\w+\.)*(sentsin.com|layui.com))\b/.test(href.replace(/\s/g, ''));
				return '<a href="'+ href +'" target="_blank"'+ (rel ? ' rel="nofollow"' : '') +'>'+ (text||href) +'</a>';
			}).replace(html(), '\<$1\>').replace(html('/'), '\</$1\>') //转移代码
			.replace(/\n/g, '<br>') //转义换行   
			return content;
		}

		,cookie: function(e,o,t){
			e=e||"";var n,i,r,a,c,p,s,d,u;if("undefined"==typeof o){if(p=null,document.cookie&&""!=document.cookie)for(s=document.cookie.split(";"),d=0;d<s.length;d++)if(u=$.trim(s[d]),u.substring(0,e.length+1)==e+"="){p=decodeURIComponent(u.substring(e.length+1));break}return p}t=t||{},null===o&&(o="",t.expires=-1),n="",t.expires&&("number"==typeof t.expires||t.expires.toUTCString)&&("number"==typeof t.expires?(i=new Date,i.setTime(i.getTime()+864e5*t.expires)):i=t.expires,n="; expires="+i.toUTCString()),r=t.path?"; path="+t.path:"",a=t.domain?"; domain="+t.domain:"",c=t.secure?"; secure":"",document.cookie=[e,"=",encodeURIComponent(o),n,r,a,c].join("");
		}

		,getcookie: function(n){
			var arr,reg=new RegExp("(^| )"+n+"=([^;]*)(;|$)");
			if(arr=document.cookie.match(reg))
				return unescape(arr[2]);
			else
				return null;
		}
	};
	gather.handleAjax = handleAjax;
	gather.U = U;
	gather.uploadPicture = uploadPicture;
	
	function handleAjax(msg) {
		//如果需要跳转的话，消息的末尾附上即将跳转字样
		if (msg.url) {
			msg.info += '，页面即将跳转～';
		}

		//弹出提示消息
		layer.msg(msg.info, {
		  icon: 2,
		  time: 1000,
		  fixed: true, 
		  offset: '80%',
		}, function(){
			$(that).removeClass('disabled').prop('disabled', false);
		});
		
		//需要跳转的话就跳转
		var interval = 1000;
		if (msg.url == "refresh") {
			setTimeout(function () {
				location.href = location.href;
			}, interval);
		} else if (msg.url) {
			setTimeout(function () {
				location.href = msg.url;
			}, interval);
		}
	}

	function U(url, params, rewrite) {
		if (window.Think.MODEL[0] == 2) {

			var website = window.Think.ROOT + '/';
			url = url.split('/');

			if (url[0] == '' || url[0] == '@')
				url[0] = APPNAME;
			if (!url[1])
				url[1] = 'Index';
			if (!url[2])
				url[2] = 'index';
			website = website + '' + url[0] + '/' + url[1] + '/' + url[2];

			if (params) {
				params = params.join('/');
				website = website + '/' + params;
			}
			if (!rewrite) {
				website = website + '.html';
			}

		} else {
			var website = window.Think.ROOT + '/index.php';
			url = url.split('/');
			if (url[0] == '' || url[0] == '@')
				url[0] = APPNAME;
			if (!url[1])
				url[1] = 'Index';
			if (!url[2])
				url[2] = 'index';
			website = website + '?s=/' + url[0] + '/' + url[1] + '/' + url[2];
			if (params) {
				params = params.join('/');
				website = website + '/' + params;
			}
			if (!rewrite) {
				website = website + '.html';
			}
		}

		if(typeof (window.Think.MODEL[1])!='undefined'){
			website=website.toLowerCase();
		}
		return website;
	}

	//图片相册
	layer.photos({
		photos: '.photos'
		,zIndex: 9999999999
	});

	//表单提交
	form.on('submit(*)', function(data){
		var action = $(data.form).attr('action'), button = $(data.elem);
		var serialdata = $(data.form).fixedSerialize();
		var that = $(this);
		
		gather.json(action, serialdata, that);
		return false;
	});
	
	//锁定屏幕
	$('#lock_screen').on('click', function(){
		layer.prompt({title: '请输入登录口令，并确认', formType: 0, btn: ['确定'], shadeClose: false, closeBtn: 0, shade: 0.9, fixed: true, scrollbar: false, move: false, maxlength: 140}, function(pass, index){
			layer.close(index);
		});
	});

	//加载特定模块
	//if(layui.cache.page && layui.cache.page !== 'index'){
	//	var extend = {};
	//	layui.use(layui.cache.page);
	//}

	//加载IM
	//if(!device.android && !device.ios){
	// layui.use('im');
	//}
	
	//顶部布局换色设置
	var bgColorSelectorColors = [{ c: '#981767', cName: '' }, { c: '#AD116B', cName: '' }, { c: '#B61944', cName: '' }, { c: '#AA1815', cName: '' }, { c: '#C4182D', cName: '' }, { c: '#D74641', cName: '' }, { c: '#ED6E4D', cName: '' }, { c: '#D78A67', cName: '' }, { c: '#F5A675', cName: '' }, { c: '#F8C888', cName: '' }, { c: '#F9D39B', cName: '' }, { c: '#F8DB87', cName: '' }, { c: '#FFD839', cName: '' }, { c: '#F9D12C', cName: '' }, { c: '#FABB3D', cName: '' }, { c: '#F8CB3C', cName: '' }, { c: '#F4E47E', cName: '' }, { c: '#F4ED87', cName: '' }, { c: '#DFE05E', cName: '' }, { c: '#CDCA5B', cName: '' }, { c: '#A8C03D', cName: '' }, { c: '#73A833', cName: '' }, { c: '#468E33', cName: '' }, { c: '#5CB147', cName: '' }, { c: '#6BB979', cName: '' }, { c: '#8EC89C', cName: '' }, { c: '#9AD0B9', cName: '' }, { c: '#97D3E3', cName: '' }, { c: '#7CCCEE', cName: '' }, { c: '#5AC3EC', cName: '' }, { c: '#16B8D8', cName: '' }, { c: '#49B4D6', cName: '' }, { c: '#6DB4E4', cName: '' }, { c: '#8DC2EA', cName: '' }, { c: '#BDB8DC', cName: '' }, { c: '#8381BD', cName: '' }, { c: '#7B6FB0', cName: '' }, { c: '#AA86BC', cName: '' }, { c: '#AA7AB3', cName: '' }, { c: '#935EA2', cName: '' }, { c: '#9D559C', cName: '' }, { c: '#C95C9D', cName: '' }, { c: '#DC75AB', cName: '' }, { c: '#EE7DAE', cName: '' }, { c: '#E6A5CA', cName: '' }, { c: '#EA94BE', cName: '' }, { c: '#D63F7D', cName: '' }, { c: '#C1374A', cName: '' }, { c: '#AB3255', cName: '' }, { c: '#A51263', cName: '' }, { c: '#7F285D', cName: ''}];
	$("#trace_show").click(function(){
		$("div.bgSelector").toggle(300, function() {
			if ($(this).html() == '') {
				$(this).sColor({
					colors: bgColorSelectorColors,  // 必填，所有颜色 c:色号（必填） cName:颜色名称（可空）
					colorsWidth: '50px',  // 必填，颜色的高度
					colorsHeight: '31px',  // 必填，颜色的高度
					curTop: '0', // 可选，颜色选择对象高偏移，默认0
					curImg: './app/admin/static/images/cur.png',  //必填，颜色选择对象图片路径
					form: 'drag', // 可选，切换方式，drag或click，默认drag
					keyEvent: true,  // 可选，开启键盘控制，默认true
					prevColor: true, // 可选，开启切换页面后背景色是上一页面所选背景色，如不填则换页后背景色是defaultItem，默认false
					defaultItem: ($.cookie('bgColorSelectorPosition') != null) ? $.cookie('bgColorSelectorPosition') : 22  // 可选，第几个颜色的索引作为初始颜色，默认第1个颜色
				});
			}
		});//切换显示
	});
	if ($.cookie('bgColorSelectorPosition') != null) {
		$('.layui-layout-admin .layui-header').css('background-color', bgColorSelectorColors[$.cookie('bgColorSelectorPosition')].c);
		$('.fbutton').css({'border-color': bgColorSelectorColors[$.cookie('bgColorSelectorPosition')].c ,'color': bgColorSelectorColors[$.cookie('bgColorSelectorPosition')].c});
		
	} else {
		//$('.main .subnav').css('background-color', bgColorSelectorColors[30].c);
		//$('.fbutton').css({'border-color': bgColorSelectorColors[30].c, 'color':bgColorSelectorColors[30].c});
	}
	
	//监听Hash改变
	window.onhashchange = function(){
		$("#qn_app_body").scrollTop(0);
		//resize
		layui.data.resize && $win.off('resize', layui.data.resize);
		delete layui.data.resize;
	
		layui.element.render();
		
		//容器 scroll 事件，剔除吸附层
		$("#qn_app_body").on('scroll', function(){
			var elemDate = $('.layui-laydate')
			,layerOpen = $('.layui-layer')[0];

			//关闭 layDate
			if(elemDate[0]){
				elemDate.each(function(){
					var othis = $(this);
					othis.hasClass('layui-laydate-static') || othis.remove();
				});
			}

			//关闭 Tips 层
			layerOpen && layer.closeAll('tips');
		});
	  
		//renderPage();
		 //执行 {admin}.hash 下的事件
		layui.event.call(this, 'admin', 'hash({*})', layui.router());
	};

	//右下角固定Bar
	//util.fixbar({
	//	bar1: '&#xe611;'
	//	,click: function(type){
	//	  if(type === 'bar1'){
	//		location.href="http://wpa.qq.com/msgrd?v=3&uin=411924848&site=qq&menu=yes";
	//	  }
	//	}
	//});
	
	//全选的实现
    $(".check-all").click(function () {
        $(".ids").prop("checked", this.checked);
    });
    $(".ids").click(function () {
        var option = $(".ids");
        option.each(function (i) {
            if (!this.checked) {
                $(".check-all").prop("checked", false);
                return false;
            } else {
                $(".check-all").prop("checked", true);
            }
        });
    });
	form.on('select(select_text)', function(data) {
		//$('#selectForm').submit();
		//e.preventDefault();
		var url = document.location.toString();
		var seperator = "?";
		var form2 = $('#selectForm').serialize();
		var action = $('#selectForm').attr('action');
		if(action == ''){
			 action = location.href;
		}
		form2 = form2+"&";
		var formarr = form2.split("&");
		for(var i=0;i < formarr.length-1;i++){
			var argarr = formarr[i].split("=");
			var arg = argarr[0];
			var arg_val = argarr[1];
			url = changeURLArg(url,arg,arg_val);
		}
		location.href = url;
		return false;
	});
	
	//全选
	form.on('checkbox(allChoose)', function(data){
		var child = $(data.elem).parents('table').find('tbody input[type="checkbox"]');
		child.each(function(index, item){
			item.checked = data.elem.checked;
			if(data.elem.checked){
				$(item).attr("checked", "checked");
			}else{
				$(item).attr("checked", false);
			}
		});
		form.render('checkbox');
	});
	form.on('checkbox(ids)', function(data){
		if(data.elem.checked){
			$(data.elem).attr("checked", "checked");
		}else{
			$(data.elem).attr("checked", false);
		}
		form.render('checkbox');
	});
	
	//切换搜索
	$(document).on('click', '.toggle_search', function(e){
		$('#search_form').toggle();
	});
	
	//搜索代码
	$(document).on('submit', '.form-dont-clear-url-param', function(e){
		e.preventDefault();
		var url = document.location.toString();
		var seperator = "?";
		var form = $(this).serialize();
		var action = $(this).attr('action');
		if(action == ''){
			 action = location.href;
		}
		form = form+"&";
		var formarr = form.split("&");
		for(var i=0;i < formarr.length-1;i++){
			var argarr = formarr[i].split("=");
			var arg = argarr[0];
			var arg_val = argarr[1];
			url = changeURLArg(url,arg,arg_val);
		}
		location.href = url;

		return false;
	});
	
	function changeURLArg(url,arg,arg_val){
		var pattern=arg+'=([^&]*)';
		var replaceText=arg+'='+arg_val;
		if(url.match(pattern)){
			var tmp='/('+ arg+'=)([^&]*)/gi';
			tmp=url.replace(eval(tmp),replaceText);
			return tmp;
		}else{
			if(url.match('[\?]')){
				return url+'&'+replaceText; 
			}else{
				return url+'?'+replaceText; 
			}
		}
		return url+'\n'+arg+'\n'+arg_val; 
	}
	//模拟弹窗
	$('[data-role="modal"]').click(function(){
		var target_url=$(this).attr('data-url');
		var data_title=$(this).attr('data-title');
		var target_form=$(this).attr('target-form');
		if(target_form!=undefined){
			//设置了参数时，把参数加入
			var form3=$('.'+target_form);

			if (form3.get(0) == undefined) {
				layer.msg('没有可操作数据', {icon: 2, fixed: true, offset: '80%', time:1000});
				return false;
			} else if (form3.get(0).nodeName == 'FORM') {
				query = form3.fixedSerialize();
			} else if (form3.get(0).nodeName == 'INPUT' || form3.get(0).nodeName == 'SELECT' || form3.get(0).nodeName == 'TEXTAREA') {
				query = form3.fixedSerialize();
			} else {
				query = form3.find('input,select,textarea').fixedSerialize();
			}
			if(!query.length){
				target_url=target_url;
			}else{
				target_url=target_url+'&'+query;
			}
		}
		layer.open({
		  type: 2,
		  title: data_title,
		  shadeClose: true,
		  shade: 0.8,
		  area: ['40%', '70%'],
		  content: target_url //iframe的url
		});
	});
	
	//确认删除
	$('.wzb-confirm').click(function(e){
		var text = $(this).attr('data-confirm');
		var result = confirm(text);
		if(result) {
			return true;
		} else {
			e.stopImmediatePropagation();
			e.stopPropagation();
			e.preventDefault();
			return false;
		}
	});
	
	//ul 排序选择
	$("ul.select").on("click","li",function(){
		$(this).addClass("selected").siblings().removeClass("selected");
	});
	
	//get批量获取列表页选中id再跳转请求
    $('.target-get').click(function () {
        var target, query, form;
        var target_form = $(this).attr('target-form');
        var that = this;
        var nead_confirm = false;
        if (($(this).attr('type') == 'submit') || (target = $(this).attr('href')) || (target = $(this).attr('url'))) {
        	
            form = $('.' + target_form);
            if ($(this).hasClass('confirm')) {
            	layer.confirm('皇上，确定要做这个操作吗？', {
				  btn: ['确定','取消'] //按钮
				  ,closeBtn: 0
				}, function(){
            		 if ($(this).attr('hide-data') === 'true') {//无数据时也可以使用的功能
                         form = $('.hide-data');
                         query = form.fixedSerialize();
                     } else if (form.get(0) == undefined) {
                         return false;
                     } else if (form.get(0).nodeName == 'FORM') {
                         if ($(this).attr('url') !== undefined) {
                             target = $(this).attr('url');
                         } else {
                             target = form.get(0).action;
                         }
                         query = form.fixedSerialize();
                     } else if (form.get(0).nodeName == 'INPUT' || form.get(0).nodeName == 'SELECT' || form.get(0).nodeName == 'TEXTAREA') {
                         query = form.fixedSerialize();
                     } else {
                         query = form.find('input,select,textarea').fixedSerialize();
                     }
					 location.href = target+'&'+query;
                }, function(index){
					layer.close(index);
					return false;
				});

            }else{
            	 if ($(this).attr('hide-data') === 'true') {//无数据时也可以使用的功能
                     form = $('.hide-data');
                     query = form.fixedSerialize();
                 } else if (form.get(0) == undefined) {
                     return false;
                 } else if (form.get(0).nodeName == 'FORM') {
                     if ($(this).attr('url') !== undefined) {
                         target = $(this).attr('url');
                     } else {
                         target = form.get(0).action;
                     }
                     //query = $("form:first").fixedSerialize();
					 query = form.fixedSerialize();
                 } else if (form.get(0).nodeName == 'INPUT' || form.get(0).nodeName == 'SELECT' || form.get(0).nodeName == 'TEXTAREA') {
                     query = form.fixedSerialize();
                 } else {
                     query = form.find('input,select,textarea').fixedSerialize();
                 }
				 //gather.json(target, query, $(that));
				 location.href = target+'&'+query;
            }
        }
        return false;
    });
    
    //ajax get请求
    $('.ajax-get').click(function () {
        var target;
        var that = this;
        
        if ($(this).hasClass('confirm')) {
			//询问框
			layer.confirm('皇上，确定要做这个操作吗？', {
			  btn: ['确定','取消'] //按钮
			  ,closeBtn: 0
			}, function(){
				if ((target = $(that).attr('href')) || (target = $(that).attr('url'))) {
					gather.json(target, '', $(that), {'type':'get'});
                    
                }else{
					layer.msg('未定义路径', {
					  icon: 2,
					  time: 1000,
					  fixed: true, 
					  offset: '80%',
					});
				}
			}, function(index){
				layer.close(index);
				return false;
			});
        	
        }else{
        	if ((target = $(that).attr('href')) || (target = $(that).attr('url'))) {
				gather.json(target, '', $(that), {'type':'get'});
            }
        }
        return false;
    });

    //ajax post submit请求
    $('.ajax-post').click(function () {
        var target, query, form;
        var target_form = $(this).attr('target-form');
        var that = this;
        var nead_confirm = false;
        if (($(this).attr('type') == 'submit') || (target = $(this).attr('href')) || (target = $(this).attr('url'))) {
        	
            form = $('.' + target_form);
            if ($(this).hasClass('confirm')) {
            	layer.confirm('皇上，确定要做这个操作吗？', {
				  btn: ['确定','取消'] //按钮
				  ,closeBtn: 0
				}, function(){
            		 if ($(this).attr('hide-data') === 'true') {//无数据时也可以使用的功能
                         form = $('.hide-data');
                         query = form.fixedSerialize();
                     } else if (form.get(0) == undefined) {
                         return false;
                     } else if (form.get(0).nodeName == 'FORM') {
                         if ($(this).attr('url') !== undefined) {
                             target = $(this).attr('url');
                         } else {
                             target = form.get(0).action;
                         }
                         query = form.fixedSerialize();
                     } else if (form.get(0).nodeName == 'INPUT' || form.get(0).nodeName == 'SELECT' || form.get(0).nodeName == 'TEXTAREA') {
                         query = form.fixedSerialize();
                     } else {
                         query = form.find('input,select,textarea').fixedSerialize();
                     }
                     console.log(query);
					 gather.json(target, query, $(that));
                }, function(index){
					layer.close(index);
					return false;
				});

            }else{
            	 if ($(this).attr('hide-data') === 'true') {//无数据时也可以使用的功能
                     form = $('.hide-data');
                     query = form.fixedSerialize();
                 } else if (form.get(0) == undefined) {
                     return false;
                 } else if (form.get(0).nodeName == 'FORM') {
                     if ($(this).attr('url') !== undefined) {
                         target = $(this).attr('url');
                     } else {
                         target = form.get(0).action;
                     }
                     //query = $("form:first").fixedSerialize();
					 query = form.fixedSerialize();
                 } else if (form.get(0).nodeName == 'INPUT' || form.get(0).nodeName == 'SELECT' || form.get(0).nodeName == 'TEXTAREA') {
                     query = form.fixedSerialize();
                 } else {
                     query = form.find('input,select,textarea').fixedSerialize();
                 }
                 console.log(query);
				 gather.json(target, query, $(that));
            }
        }
        return false;
    });
	//修复serialize checkbox全不选无值的问题
	$.fn.extend({
		"fixedSerialize": function () {
			var $f = $(this);
			var data = $(this).serialize();
			var $chks = $(this).find(":checkbox:not(:checked)");    //取得所有未选中的checkbox  

			if ($chks.length == 0) {
				return data;
			}
			var nameArr = [];
			var tempStr = "";
			$chks.each(function () {
				var chkName = $(this).attr("name");
				if ($.inArray(chkName, nameArr) == -1 && $f.find(":checkbox[name='" + chkName + "']:checked").length == 0) {
					nameArr.push(chkName);
					tempStr += "&" + chkName + "=";
				}
			});
			data += tempStr;
			return data;
		}
	});
	
	moduleManager = {
		'install': function (id) {
			$.post(U('admin/module/install'),{id:id},function(msg){
				handleAjax(msg);
			})
		},
		'uninstall': function (id) {
			$.post(U('admin/module/uninstall'),{id:id},function(msg){
				handleAjax(msg);
			})
		}
	}

	admin_image ={
	    removeImage: function (obj, attachId) {
	        // 移除附件ID数据
	        this.upAttachVal('del', attachId, obj);
	        obj.parents('.each').remove();

	    },
	    /**
	     * 更新附件表单值
	     */
	    upAttachVal: function (type, attachId,obj) {
	        var $attach_ids = obj.parents('.controls').find('.attach');
	        var attachVal = $attach_ids.val();
	        var attachArr = attachVal.split(',');
	        var newArr = [];
	        for (var i in attachArr) {
	            if (attachArr[i] !== '' && attachArr[i] !== attachId.toString()) {
	                newArr.push(attachArr[i]);
	            }
	        }
	        type === 'add' && newArr.push(attachId);
	        $attach_ids.val(newArr.join(','));
	        return newArr;
	    }
	}

	function uploadPicture(n,data,t){
    	var limit = 7;
		var data = eval("("+data+")");
		console.log(data);
		if(t == 0){
			$("#upload_"+n).attr('src',data.path);
			$("#upload_"+n).parent().attr('href',data.path);
			$("[name='"+n+"']").val(data.id);
		}else if(t == 1){
			var div = $('#imageview_'+n);
			admin_image.upAttachVal('add',data.id, $("[name='"+n+"_id']"));
			div.append('<div class="each"><a href="'+ data.path +'" title="点击查看大图"><img src="'+ data.path +'"></a><div class="text-center opacity del_btn" ></div><div onclick="admin_image.removeImage($(this),'+data.id +')"  class="text-center del_btn">删除</div></div>');
		}
	}

	layui.use('admin');
	layui.use('monitor');
	exports('qn', gather);

});