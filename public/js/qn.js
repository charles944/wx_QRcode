/**
  qn.js
**/
layui.define(['layer', 'laytpl', 'form', 'upload', 'util', 'laypage','element','flow'], function(exports){
	var $ = layui.jquery
	,layer = layui.layer
	,form = layui.form
	,util = layui.util
	,device = layui.device()
	,laytpl = layui.laytpl
	,laypage = layui.laypage
	,user = layui.cache.user
	,element = layui.element
	,upload = layui.upload
	,flow = layui.flow;

	//阻止IE7以下访问
	if(device.ie && device.ie < 8){
		layer.alert('如果您非得使用靑年PHP，那么请使用ie8+');
	}
	
	flow.lazyimg(); //带有lay-src的img元素开启了懒加载

	var _spinner = '<img src="/public/images/loading.gif" class="" width="16px"/>';
	var _btnOriginText = '';
		
	var gather = {
		//Ajax
		json: function(url, data, success, options){
			var result = false;
			options = options || {};
			data = data || {};
			var obj = options.obj || $(this);
			$.ajax({
				type: options.type || 'post',
				dataType: options.dataType || 'json',
				data: data,
				url: url,
				async:false,
				beforeSend: gather.beforeSend(obj),
				success: function(res){
					result = res;
				}, error: function(e){
				   options.error || layer.msg('请求异常，请重试', {shift: 6, icon: 2, fixed: true, offset: '80%', time: 1000});
				}
			});
			return result;
		}
		
		,beforeSend: function beforeSend(btn) {
			btn[['prop']]('disabled', true);
			_btnOriginText = btn[['html']]();
			//console.log(_btnOriginText);
			btn[['html']](_spinner);
			element.init();
		}
		
		,finishRequest: function finishRequest(btn) {
			btn[['html']](_btnOriginText);
			btn[['prop']]('disabled', false);
		}
		
		,normal_end: function(res, obj){
			if(res.action || res.url){
				location.href = res.action ? res.action : res.url;
			} else {
				gather.finishRequest(obj);
			}
		}
		
		,special_end: function(res, obj){
			var index = parent.layer.getFrameIndex(window.name);
			if(typeof(index) == 'undefined'){
				if(res.url){
					location.href = res.url;
				} else {
					gather.finishRequest(obj);
					location.reload();
				}
				return false;
			}else{
				parent.layer.close(index);
				if(res.url){
					parent.location.href = res.url;
				} else {
					gather.finishRequest(obj);
					parent.location.reload();
				}
			}
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
			return new RegExp('\\['+ (end||'') +'(div|table|thead|th|tbody|tr|td|ul|li|ol|li|dl|dt|dd|h2|h3|h4|h5)\\]\\n*', 'g');
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
		  .replace(/\n/g, '<br/>') //转义换行   //20170213换行符bug
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
		
		,handleAjax: function (msg) {
			//如果需要跳转的话，消息的末尾附上即将跳转字样
			if (msg.url) {
				msg.info += '，页面即将跳转～';
			}

			//弹出提示消息
			layer.msg(msg.info, {
			  icon: 1,
			  time: 1000,
			  fixed: true, 
			  offset: '80%',
			}, function(){
				//$(that).removeClass('disabled').prop('disabled', false);
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
    };

	//系统常规
	$(".nav ul li:has(ul)").hover(function() {
		$(this).children("a").css({
			color: "#fff"
		});
		0 < $(this).find("li").length && $(this).children("ul").stop(!0, !0).slideDown(100)
	},
	function() {
		$(this).children("a").css({
			color: "#FFF"
		});
		$(this).children("ul").stop(!0, !0).slideUp("fast")
	});
	$(".toggle-search").click(function() {
		$(".toggle-search").toggleClass("active");
		$(".search-expand").fadeToggle(300)
	});
	$(".navbar-toggle").click(function() {
		$(".navbar-toggle").toggleClass("active");
		$(".navbar-collapse").toggle(300);
		$(".nav ul li ul").show()
	});
	$(".totop").hide();
	$(window).scroll(function() {
		0 < $(window).scrollTop() ? $(".totop").fadeIn(200) : $(".totop").fadeOut(200)
	});
	$(".totop").click(function() {
		$("html,body").animate({
			scrollTop: "0px"
		},
		400)
	});

	//确认删除
	$('.qn-confirm').click(function(e){
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

	//ajax get请求
    $('.ajax-get').click(function () {
        var target;
        var that = this;
        button = $(this);
        if ($(this).hasClass('confirm')) {
			//询问框
			layer.confirm('皇上，确定要做这个操作吗？', {
			  btn: ['确定','取消'] //按钮
			  ,closeBtn: 0
			}, function(){
				if ((target = $(that).attr('href')) || (target = $(that).attr('url'))) {
					var res = gather.json(target, '', function(res){}, {'obj': button});
                    if(res.status == 0){
						layer.msg(res.info, {
							icon: 2,
							fixed: true, 
							offset: '80%',
							time: 1000,
						},function(){
							gather.special_end(res, that);
						})
					}else if(res.status == 1){
						layer.msg(res.info, {
							icon: 1,
							fixed: true, 
							offset: '80%',
							time: 1000,
						},function(){
							gather.special_end(res, that);
							if(res.url == 'refresh'){
								location.reload();
							}
						})
					};
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
				var res = gather.json(target, '', function(res){}, {'obj': button});
				if(res.status == 0){
					layer.msg(res.info, {
						icon: 2,
						fixed: true, 
						offset: '80%',
						time: 1000,
					},function(){
						gather.special_end(res, that);
					})
				}else if(res.status == 1){
					layer.msg(res.info, {
						icon: 1,
						fixed: true, 
						offset: '80%',
						time: 1000,
					},function(){
						gather.special_end(res, that);
						if(res.url == 'refresh'){
							location.reload();
						}
					})
				};
            }
        }
        return false;
    });

	//表单提交
	form.on('submit(*)', function(data){
		var action = $(data.form).attr('action'), button = $(data.elem);
		data.field.content = gather.content(data.field.content);
		var serialdata = $(data.form).serialize();
		var that = $(this);
		var res = gather.json(action, serialdata, function(res){}, {'obj': button});
		if(res.status == 0){
			layer.msg(res.info, {
				icon: 2,
				fixed: true, 
				offset: '80%',
				time: 1000,
			},function(){
				gather.special_end(res, that);
			})
		}else if(res.status == 1){
			layer.msg(res.info, {
				icon: 1,
				fixed: true, 
				offset: '80%',
				time: 1000,
			},function(){
				gather.special_end(res, that);
				if(res.url == 'refresh'){
					location.reload();
				}
			})
		};
		
		return false;
	});

	/**
	 * 检查是否有新的消息
	 */
	function checkMessage() {
		if(user.uid){
			$.get(U('home/public/getmsg'), {}, function (msg) {
				msg = eval(msg);
				if (msg.messages) {
					var msgarr = msg.messages.pop();
					layer.msg(msgarr.msg, {icon:1, fixed: true, offset: '80%', time:1500}, function(){tip_message(msg.messages)});
				}
			}, 'json');
		}
	}
	checkMessage();//检查一次消息

	function tip_message(data){
		if(data.length > 1){
			var msgarr = data.pop();
			layer.msg(msgarr.msg, {icon:1, fixed: true, offset: '80%', time:1500}, function(){tip_message(data)});
		}else if(data.length == 1){
			var msgarr = data.pop();
			layer.msg(msgarr.msg, {icon:1, fixed: true, offset: '80%', time:1500});
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

	//加载特定模块
	layui.use('user');
	
	if(layui.cache.page && layui.cache.page !== 'index'){
		var extend = {};
		layui.use(layui.cache.page);
	}

	
	exports('qn', gather);
});