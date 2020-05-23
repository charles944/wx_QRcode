/**
  install.js
**/

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
 
	//……
	//你的代码都应该写在这里面
	$('.step1').on('click', function(){
		layer.open({
			type: 1
			,title: false //不显示标题栏
			,closeBtn: false
			,area: '50%;'
			,shade: 0.8
			,id: 'LAY_layuipro' //设定一个id，防止重复弹出
			,resize: false
			,btn: ['朕同意了', '朕表示不爽']
			,moveType: 1 //拖拽模式，0或者1
			,content: $("#agreement").html()
			,success: function(layero){
				var btn = layero.find('.layui-layer-btn');
				btn.css('text-align', 'center');
				btn.find('.layui-layer-btn0').attr({
				  href: '?s=/install/step1.html'
				  //,target: '_blank'
				});
			}
		});
	});
	
	$('#view-list').on('click', function(){
		layer.open({
			type: 1
			,title: false //不显示标题栏
			,closeBtn: false
			,area: ['50%','50%']
			,shade: 0.8
			,id: 'LAY_layuipro' //设定一个id，防止重复弹出
			,resize: false
			,btn: ['下一步', '关闭']
			,moveType: 1 //拖拽模式，0或者1
			,content: $('#show-list').html()
			,success: function(layero){
				var btn = layero.find('.layui-layer-btn');
				btn.css('text-align', 'center');
				btn.find('.layui-layer-btn0').attr({
				  href: '?s=/index/complete'
				  //,target: '_blank'
				});
			}
		});
	});
	
	var gather = {
		//Ajax
		json: function(url, data, success, options){
		  var that = this;
		  options = options || {};
		  data = data || {};
		  return $.ajax({
			type: options.type || 'post',
			dataType: options.dataType || 'json',
			data: data,
			url: url,
			success: function(res){
			  if(res.status === 1) {
				success && success(res);
			  } else {
				layer.msg(res.info||res.code, {shift: 6,icon: 2, fixed: true, offset: '80%', time:1000});
			  }
			}, error: function(e){
			  options.error || layer.msg('请求异常，请重试', {shift: 6, icon:2, fixed: true, offset: '80%', time:1000});
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
		//,content: function(value){
		//  layedit.sync(editIndex);
		//}
	});

	//表单提交
	form.on('submit(install)', function(data){
		var action = $(data.form).attr('action'), button = $(data.elem);
		//console.log(JSON.stringify(data));
		gather.json(action, data.field, function(res){
		  var end = function(){
			if(res.url){
			  location.href = res.url;
			} else {
			  gather.form[action](data.field, data.form);
			}
		  };
		  if(res.status == 0){
			layer.msg(res.info, {
			  icon: 1,
			  fixed: true, 
			  offset: '80%',
			  time: 10*1000,
			  end: end
			})
		  }else if(res.status == 1){
			layer.msg(res.info, {
			  icon: 1,
			  fixed: true, 
			  offset: '80%',
			  time: 1000,
			  end: end
			})
		  };
		});
		return false;
	});
	
	exports('qncore', gather);
});