layui.define(function(exports){
  var $ = layui.jquery
  ,laytpl = layui.laytpl
  ,element = layui.element
  ,device = layui.device()
  ,view = layui.view
  
  ,$win = $(window), $body = $('body')
  ,container = $('#qn_app')
  
  ,SHOW = 'layui-show', HIDE = 'layui-hide', THIS = 'layui-this', TEMP = 'template'
  ,APP_FLEXIBLE = 'qn_app_flexible', ICON_SHRINK = 'layui-icon-shrink-right', ICON_SPREAD = 'layui-icon-spread-left'
  ,SIDE_SHRINK = 'qn-side-shrink', SIDE_MENU1 = 'qn_menuItemElem', SIDE_MENU2 = 'qn_menuContentElem'
  
  //通用方法
  ,admin = {
    v: '1.1'
    
    //加载中
    ,loading: function(elem){
      elem.append(
        this.elemLoad = $('<i class="layui-anim layui-anim-rotate layui-anim-loop layui-icon layui-icon-loading qn-loading"></i>')
      );
    }
    
    //移除加载
    ,removeLoad: function(){
      this.elemLoad && this.elemLoad.remove();
    }
    
    //屏幕类型
    ,screen: function(){
      var width = $win.width()
      if(width >= 1200){
        return 3; //大屏幕
      } else if(width >= 992){
        return 2; //中屏幕
      } else if(width >= 768){
        return 1; //小屏幕
      } else {
        return 0; //超小屏幕
      }
    }
    
    //侧边伸缩
    ,sideFlexible: function(status, tpl){
      var app = $('#qn_app')
      ,iconElem =  $('#'+ APP_FLEXIBLE);
      
      //如果没有二级菜单，则阻止操作
      if(!tpl && admin.iconFlexible('length')) return;
      
      //设置状态
      if(status === 'spread'){
        if(admin.screen() < 2){
          app.addClass('qn-side-spread-sm');
        } else {
          app.removeClass(SIDE_SHRINK);
        }
        iconElem.removeClass(ICON_SPREAD).addClass(ICON_SHRINK);
      } else {
        if(admin.screen() < 2){
          app.removeClass('qn-side-spread-sm');
        }
        app.addClass(SIDE_SHRINK);
        iconElem.removeClass(ICON_SHRINK).addClass(ICON_SPREAD);
      }
      
      layui.event.call(this, 'admin', 'side', {
        status: status
      });
    }
    
    //通过检查是否有二级菜单，才显示/隐藏"伸缩的icon"
    ,iconFlexible: function(length){
      var iconFlexible = $('.qn-flexible')
      ,menuShow = $('#'+ SIDE_MENU2).find('.layui-menu-item.layui-show')
      ,notMenu3 = menuShow.find('.layui-nav-item').length === 0;
      
      length ||iconFlexible[notMenu3 ? 'addClass' : 'removeClass'](HIDE);
      return notMenu3;
    }
    
    //事件监听
    ,on: function(events, callback){
      return layui.onevent.call(this, 'admin', events, callback);
    }
    
    //弹出面板
    ,popup: function(options){
      var success = options.success
      ,skin = options.skin;
      
      delete options.success;
      delete options.skin;
      
      return layer.open($.extend({
        type: 1
        ,title: '提示'
        ,content: ''
        ,id: 'qn_adminPopup'
        ,skin: 'layui-layer-admin' + (skin ? ' ' + skin : '')
        ,shadeClose: true
        ,closeBtn: false
        ,success: function(layero, index){
          var elemClose = $('<i class="layui-icon" close>&#x1006;</i>');
          layero.append(elemClose);
          elemClose.on('click', function(){
            layer.close(index);
          });
          typeof success === 'function' && success.apply(this, arguments);
        }
      }, options));
    }
  }
  
  //事件
  var events = admin.events = {
    //伸缩
    flexible: function(othis){
      var iconElem = othis.find('#'+ APP_FLEXIBLE)
      ,isSpread = iconElem.hasClass(ICON_SPREAD);
      admin.sideFlexible(isSpread ? 'spread' : null);
    }
    //点击消息
    ,message: function(othis){
      othis.find('.layui-badge-dot').remove();
    }
    
    //便签
    ,note: function(othis){
      var mobile = admin.screen() < 2
      ,note = layui.data('qn_app').note;
      
      events.note.index = admin.popup({
        title: '便签'
        ,shade: 0
        ,offset: [
          '58px'
          ,(mobile ? null : (othis.offset().left - 250) + 'px')
        ]
        ,anim: -1
        ,id: 'qn_adminNote'
        ,skin: 'qn-note layui-anim layui-anim-upbit'
        ,content: '<textarea placeholder="内容"></textarea>'
        ,resize: false
        ,success: function(layero, index){
          var textarea = layero.find('textarea')
          ,value = note === undefined ? '便签中的内容会存储在本地，这样即便你关掉了浏览器，在下次打开时，依然会读取到上一次的记录。是个非常小巧实用的本地备忘录' : note;
          
          textarea.val(value).focus().on('keyup', function(){
            layui.data('qn_app', {
              key: 'note'
              ,value: this.value
            });
          });
        }
      })
    }

    //返回上一页
    ,back: function(){
      history.back();
    }
  };

  //初始
  !function(){
    //禁止水平滚动
    $body.addClass('layui-layout-body');
    
    //低版本IE提示
    if(device.ie && device.ie < 10){
      Error('IE'+ device.ie + '下访问可能不佳，推荐使用：Chrome / Firefox / Edge 等高级浏览器', {
        offset: 'auto'
        ,id: 'LAY_errorIE'
      });
    }
    
  }();
  
	//监听 hash 改变侧边状态
	admin.on('hash(side)', function(router){
		admin.view('TPL_layout').refresh(function(){
			element.render('nav', 'qn-side-child'); //重新渲染子菜单
		});
	});
  
	//左侧导航切换
	element.tab({
		headerElem: '#'+ SIDE_MENU1 +'>li'
		,bodyElem: '#'+ SIDE_MENU2 +'>.layui-menu-item'
	});
  
	//监听侧边一级菜单切换
	element.on('tab(qn-system-menu)', function(obj){
		if(admin.screen() < 2){
		  admin.sideFlexible('spread');
		  admin.iconFlexible();
		}
		var navthis = layui.data('qn_app').navthis;
		layui.data('qn_app', {
			key: 'navthis'
			,value: $(this).children('a').data('show')
		});
	});
	
	//监听二级菜单点击
	$('.layui-side-child dl.layui-nav-child dd a').click(function(){
		var datashow = $(this).parent().data('show');
		if(datashow){
			console.log(datashow);
			layui.data('qn_app', {
				key: 'navthis'
				,value: datashow
			});
		}
	});
  
	//自动显示当前hover页面高亮
	var navthis = layui.data('qn_app').navthis;
	if(navthis === undefined){
		$('.layui-side-menu li:first').addClass('layui-this').siblings().removeClass('layui-this');
		$(".layui-side-child div.layui-menu-item:first").addClass('layui-show').siblings().removeClass('layui-show');
	}else{
		if(navthis.indexOf('-')> 0){
			var navthis_arr = navthis.split('-');
			var nav_fir = navthis_arr[0];
			var nav_two = navthis_arr[1];
			var nav_thr = navthis_arr[2];
			$('.layui-side-menu li a[data-show="'+nav_fir+'"]').parent().addClass('layui-this').siblings().removeClass('layui-this');
			$(".layui-side-child div.layui-menu-item[data-show='"+nav_fir+"']").addClass('layui-show').siblings().removeClass('layui-show');
			
			$(".layui-side-child div.layui-menu-item[data-show='"+nav_fir+"'] .layui-nav-item[data-show='"+nav_fir+"-"+nav_two+"']").addClass('layui-this').siblings().removeClass('layui-this');
			if(!$(".layui-side-child div.layui-menu-item[data-show='"+nav_fir+"'] .layui-nav-item[data-show='"+nav_fir+"-"+nav_two+"']").hasClass('layui-nav-itemed')){
				$(".layui-side-child div.layui-menu-item[data-show='"+nav_fir+"'] .layui-nav-item[data-show='"+nav_fir+"-"+nav_two+"']").addClass('layui-nav-itemed');
			}
			$(".layui-side-child div.layui-menu-item[data-show='"+nav_fir+"'] .layui-nav-item[data-show='"+nav_fir+"-"+nav_two+"'] dd[data-show='"+nav_fir+"-"+nav_two+"-"+nav_thr+"']").addClass('layui-this').siblings().removeClass('layui-this');
		}else{
			$('.layui-side-menu li a[data-show="'+navthis+'"]').parent().addClass('layui-this').siblings().removeClass('layui-this');
			$(".layui-side-child div.layui-menu-item[data-show='"+navthis+"']").addClass('layui-show').siblings().removeClass('layui-show');
		}
		if(admin.screen() < 2){
			admin.sideFlexible();
			admin.iconFlexible();
		}
	}
	
  
  //页面跳转
  //$body.on('click', '*[lay-href]', function(){
  //  var othis = $(this)
  //  ,href = othis.attr('lay-href')
  //  ,router = layui.router();
//
  //  admin.prevRouter[router.path[0]] = router.href; //记录上一次各菜单的路由信息
  //  location.hash = '/' + href; //执行跳转
  //});
  
  //点击事件
  $body.on('click', '*[qn-event]', function(){
    var othis = $(this)
    ,attrEvent = othis.attr('qn-event');
    events[attrEvent] && events[attrEvent].call(this, othis);
  });
  
  //tips
  $body.on('mouseenter', '*[lay-tips]', function(){
    var othis = $(this)
    ,tips = othis.attr('lay-tips')
    ,offset = othis.attr('lay-offset') 
    ,index = layer.tips(tips, this, {
      tips: 1
      ,time: -1
      ,success: function(layero, index){
        if(offset){
          layero.css('margin-left', offset + 'px');
        }
      }
    });
    othis.data('index', index);
  }).on('mouseleave', '*[lay-tips]', function(){
    layer.close($(this).data('index'));
  });
  
  //窗口resize事件
  layui.data.resizeSystem = function(){
    //layer.close(events.note.index);
    layer.closeAll('tips');
    
    if(admin.screen() < 2){
      admin.sideFlexible()
    } else {
      admin.sideFlexible('spread')
    }
  }
  $win.on('resize', layui.data.resizeSystem);
  
  //接口输出
  exports('admin', admin);
});