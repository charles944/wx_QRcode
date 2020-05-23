/**
 @Name: 靑年PHP后台登陆主入口
 */
layui.define(['layer', 'form', 'element'], function(exports){
	var $ = layui.jquery
	,layer = layui.layer
	,form = layui.form
	,element = layui.element
	,device = layui.device();
  
	//阻止IE7以下访问
	if(device.ie && device.ie < 8){
		layer.alert('如果您非得使用靑年PHP，那么请使用ie8+');
	}
	
	
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
		if(res.url){
			location.href = res.url;
			//finishRequest(obj);
		} else {
			finishRequest(obj);
			//location.reload();
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
		
		,form: {}
		
		,cookie: function(e,o,t){
			e=e||"";var n,i,r,a,c,p,s,d,u;if("undefined"==typeof o){if(p=null,document.cookie&&""!=document.cookie)for(s=document.cookie.split(";"),d=0;d<s.length;d++)if(u=$.trim(s[d]),u.substring(0,e.length+1)==e+"="){p=decodeURIComponent(u.substring(e.length+1));break}return p}t=t||{},null===o&&(o="",t.expires=-1),n="",t.expires&&("number"==typeof t.expires||t.expires.toUTCString)&&("number"==typeof t.expires?(i=new Date,i.setTime(i.getTime()+864e5*t.expires)):i=t.expires,n="; expires="+i.toUTCString()),r=t.path?"; path="+t.path:"",a=t.domain?"; domain="+t.domain:"",c=t.secure?"; secure":"",document.cookie=[e,"=",encodeURIComponent(o),n,r,a,c].join("");
		}
    };
	
	//自定义验证规则
	form.verify({
		title: function(value){
			if(value.length < 5){
				return '皇上，用户名至少得5个字符才行啊';
			}
		}
		,pass: [/(.+){6,12}$/, '皇上，密码必须6到12位哦~']
	});

	//表单提交
	form.on('submit(*)', function(data){
		var action = $(data.form).attr('action'), button = $(data.elem);
		var serialdata = $(data.form).serialize();
		var that = $(this);
		
		gather.json(action, serialdata, that);
		return false;
	});
	
	exports('login', gather);
});