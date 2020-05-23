/**
  login & reg js
**/
layui.define(['qn'], function(exports){
	var $ = layui.jquery;
	var layer = layui.layer;
	var util = layui.util;
	var laytpl = layui.laytpl;
	var form = layui.form;
	var laypage = layui.laypage;
	var user = layui.cache.user;
	var qn = layui.qn;

	//自定义验证规则
	form.verify({
		title: function(value){
		  if(value.length < 5){
			return '皇上，用户名至少得5个字符才行啊';
		  }
		}
		,pass: [/(.+){6,12}$/, '皇上，密码必须6到12位哦~']
		,registerusername: function(value, item){
			var checkuserurl = $(item).attr("check-url");
			var atype = "username";
			var result = qn.json(checkuserurl, {account: value, type: atype}, function(res){});
			if(result.status == 0){
				return result.info;
			}
		}
		,registeremail: function(value, item){
			var checkemailurl = $(item).attr("check-url");
			var atype = "email";
			var result = qn.json(checkemailurl, {account: value, type: atype}, function(res){});
			if(result.status == 0){
				return result.info;
			}
		}
		,registermobile: function(value, item){
			var checkmobileurl = $(item).attr("check-url");
			var atype = "mobile";
			var result = qn.json(checkmobileurl, {account: value, type: atype}, function(res){});
			if(result.status == 0){
				return result.info;
			}
		}
		,registernickname: function(value, item){
			var checknickurl = $(item).attr("check-url");
			var result = qn.json(checknickurl, {nickname: value}, function(res){});
			if(result.status == 0){
				return result.info;
			}
		}
		,registerpassword: [/(.+){6,12}$/, '密码必须6到12位哦~']
	});

	//表单提交
	form.on('submit(register_username)', function(data){
		console.log(data);
		var action = $(data.form).attr('action'), button = $(data.elem);
		//console.log(JSON.stringify(data));
		var res = qn.json(action, data.field, function(res){}, {'obj': button});
		if(res.status == 0){
			layer.msg(res.info, {
			  icon: 2,
			  fixed: true, 
			  offset: '80%',
			  time: 1000,
			}, function(){
				qn.normal_end(res, button);
			});
		}else if(res.status == 1){
			layer.msg('恭喜您，注册成功！', {
			  icon: 1,
			  fixed: true, 
			  offset: '80%',
			  time: 1000,
			  
			},function(){
				end: end(res)
			});
		};
		function end(res){
			var index = parent.layer.getFrameIndex(window.name);
			parent.layer.close(index);
			if(res.url){
				parent.location.href = res.url;
			} else {
				parent.location.reload();
				//qn.form[action](data.field, data.form);
			}
		}
		return false;
	});

	//表单提交
	form.on('submit(register_email)', function(data){
		var action = $(data.form).attr('action'), button = $(data.elem);
		//console.log(JSON.stringify(data));
		var res = qn.json(action, data.field, function(res){}, {'obj': button});
		if(res.status == 0){
			layer.msg(res.info, {
			  icon: 2,
			  fixed: true, 
			  offset: '80%',
			  time: 1000,
			}, function(){
				qn.normal_end(res, button);
			});
		}else if(res.status == 1){
			layer.msg('恭喜您，注册成功！', {
			  icon: 1,
			  fixed: true, 
			  offset: '80%',
			  time: 1000,
			},function(){
				end: end(res);
			});
		};
		function end(res){
			var index = parent.layer.getFrameIndex(window.name);
			parent.layer.close(index);
			if(res.url){
				parent.location.href = res.url;
			} else {
				parent.location.reload();
				//qn.form[action](data.field, data.form);
			}
		}
		return false;
	});

	//表单提交
	form.on('submit(register_mobile)', function(data){
		var action = $(data.form).attr('action'), button = $(data.elem);
		//button.html('提交中..');
		//console.log(JSON.stringify(data));
		var res = qn.json(action, data.field, function(res){}, {'obj': button});
		if(res.status == 0){
			layer.msg(res.info, {
			  icon: 2,
			  fixed: true, 
			  offset: '80%',
			  time: 1000,
			}, function(){
				qn.normal_end(res, button);
			});
		}else if(res.status == 1){
			layer.msg('恭喜您，注册成功！', {
			  icon: 1,
			  fixed: true, 
			  offset: '80%',
			  time: 1000,
			}, function(){
				end: end(res);
			});
		};
		function end(res){
			var index = parent.layer.getFrameIndex(window.name);
			parent.layer.close(index);
			if(res.url){
				parent.location.href = res.url;
			} else {
				parent.location.reload();
				//qn.form[action](data.field, data.form);
			}
		}
		return false;
	});

	//登陆表单提交
	form.on('submit(login)', function(data){
		var action = $(data.form).attr('action'), button = $(data.elem);
		//console.log(JSON.stringify(data));
		var res = qn.json(action, data.field, function(res){}, {'obj': button});
		if(res.status == 0){
			parent.layer.msg(res.info, {
			  icon: 2,
			  fixed: true, 
			  offset: '80%',
			  time: 1000,
			}, function(){
				qn.normal_end(res, button);
			});
			
		}else if(res.status == 1){
			layer.msg('登陆成功！', {
			  icon: 1,
			  fixed: true, 
			  offset: '80%',
			  time: 2000,
			}, function(){
				end: end(res);
			})
		};
		function end(res){
			var index = parent.layer.getFrameIndex(window.name);
			parent.layer.close(index);
			if(res.url){
				parent.location.href = res.url;
			} else {
				parent.location.reload();
				//qn.form[action](data.field, data.form);
			}
		};
		return false;
	});
	
	
	/**
	 * 根据条件选择登录方式(弹窗/跳转登录页面)
	 */
	$('[data-role="do_login"]').on('click',function() {
		var index = parent.layer.getFrameIndex(window.name);
		layer.close(index);
		if (!user.uid) {
			if (OPEN_QUICK_LOGIN == 1) {
				layer.open({
					type: 2,
					//title: '快捷登录',
					title: false,
					closeBtn: 0,
					shadeClose: true,
					shade: ['0.85','#0d0a31'],
					skin: 'qn-layer',
					area: 'auto',
					scrollbar: false,
					content: [U('home/member/quicklogin'),'no'], //iframe的url
					success: function(layero, index){
						layer.iframeAuto(index);
						var topoffset = ($(window).height() - $("#layui-layer-iframe"+index).height())/2;
						parent.$("#layui-layer"+index).css({'top': topoffset, 'overflow': 'hidden'});
					}
				});
			} else {
				window.location.href = U('home/member/login');
			}
		}
	});

	$('[data-role="do_register"]').on('click',function() {	
		var index = parent.layer.getFrameIndex(window.name);
		parent.layer.close(index);
		if (!user.uid) {
			if (ONLY_OPEN_REGISTER == "1") {
				layer.open({
					type: 2,
					title: '邀请用户才能注册！',
					shadeClose: false,
					shade: ['0.85','#0d0a31'],
					skin: 'qn-layer',
					area: 'auto',
					content: U('home/member/inCode'), //iframe的url
					success: function(layero, index){
						layer.iframeAuto(index);
						var topoffset = ($(window).height() - $("#layui-layer-iframe"+index).height())/2;
						parent.$("#layui-layer"+index).css({'top': topoffset, 'overflow': 'hidden'});
					}
				});
			} else if(OPEN_QUICK_REGISTER == "1") {
				layer.open({
					type: 2,
					title: false,
					shadeClose: true,
					id: 'quickregister',
					closeBtn: 0,
					shade: ['0.85','#0d0a31'],
					skin: 'qn-layer',
					area: 'auto',
					scrollbar: false,
					content: [U('home/member/quickregister'),'no'], //iframe的url
					success: function(layero, index){
						layer.iframeAuto(index);
						var topoffset = ($(window).height() - $("#layui-layer-iframe"+index).height())/2;
						parent.$("#layui-layer"+index).css({'top': topoffset, 'overflow': 'hidden'});
					}
				});
			} else {
				location.href = U('home/member/register');
			}
		}
	});
	
	$('[data-toggle="modal"]').on('click',function () {
		var url = $(this).attr('data-url');
		var title = $(this).attr('data-title');
		layer.open({
			type: 2,
			title: false,
			shadeClose: true,
			id: 'quickregister',
			closeBtn: 0,
			shade: ['0.85','#0d0a31'],
			skin: 'qn-layer',
			area: 'auto',
			scrollbar: false,
			content: [url,'no'], //iframe的url
			success: function(layero, index){
				layer.iframeAuto(index);
				var topoffset = ($(window).height() - $("#layui-layer-iframe"+index).height())/2;
				parent.$("#layui-layer"+index).css({'top': topoffset, 'overflow': 'hidden'});
			}
		});
	});
	
	$('[data-toggle="incode"]').on('click',function () {
		var url = $(this).attr('data-url');
		var title = $(this).attr('data-title');
		layer.prompt({title: title, formType: 0}, function(val, index){
			var res = qn.json(url, {'code': val}, function(res){}, {'obj': $(this)});
			
			if(res.status == 0){
				parent.layer.msg(res.info, {
				  icon: 2,
				  fixed: true, 
				  offset: '80%',
				  time: 1000,
				}, function(){
					layer.close(index);
				});
				
			}else if(res.status == 1){
				layer.close(index);
				location.href = res.url;
			};
			//layer.msg('得到了'+val);
			
		});
	});
	 

	/**
	 * 绑定登出事件
	 */

	$('[event-node="logout"]').on('click',function () {
		var res = qn.json(U('home/member/logout'), '', function(res){}, {'obj': $(this)});
		if(res.status == 0){
			layer.msg(res.info, {
				icon: 2,
				fixed: true, 
				offset: '80%',
				time: 1000,
			},function(){
				end: end(res)
			})
		}else if(res.status == 1){
			layer.msg(res.info, {
				icon: 1,
				fixed: true, 
				offset: '80%',
				time: 1000,
			}, function(){
				end: end(res)
			})
		};
		function end(res){
			if(res.url){
				location.href = res.url;
			} else {
				location.reload();
				//qn.form[action](data.field, data.form);
			}
		};
	});
	/*登录end*/
	
	exports('user', null);
});