function m(e) {
    console.log(e)
}

function Loading(a){
	if(!window.plus){
		if(!a){a = '数据加载中'}
		hui.loading(a);
	}else{
		if(!a){a = '加载中...'}
		hui.h5Loading(false, a,{round:2, padding:'20px', textalign:'right'});
	}
}

function closeLoading(){
	if(!window.plus){
    	hui.closeLoading();
	}else{
    	hui.h5Loading(true);
	}
}

function showSuccess(e, o) {
    if(e.status){
        hui.upToast(e.status,'long');
    }else{
        hui.upToast(e.message,'long');
    }
    setTimeout(function() {
       o && o()
    }, 1111)
}

function set(e, o) {
    localStorage.setItem(e, JSON.stringify(o))
}

function get(e) {
    var b = localStorage.getItem(e);
    return JSON.parse(b);
}

var hasOwnProperty = Object.prototype.hasOwnProperty;

function isEmpty(obj) {
    // 本身为空直接返回true
    if (obj == null) return true;
    // 然后可以根据长度判断，在低版本的ie浏览器中无法这样判断。
    if (obj.length > 0)    return false;
    if (obj.length === 0)  return true;
    //最后通过属性长度判断。
    for (var key in obj) {
        if (hasOwnProperty.call(obj, key)) return false;
    }

    return true;
}

function isNullObj(obj){
  for(var i in obj){
    if(obj.hasOwnProperty(i)){
      return false;
    }
  }
  return true;
}

function isLogin(e) {
    qnresult = get("qnresult");
    if(isNullObj(qnresult)){
        hui.open('./login.html');
    }else{
        e();
    }
}

function $post(e, o, a, i) {
    commonAjax("post", e, o, a, i)
}

function $get(e, o, a, i) {
    commonAjax("get", e, o, a, i)
}

var commonAjax = function(e, o, a, i, t) {
    $.ajax({
        url: o + "&t=" + (new Date).getTime(),
        data: a,
        backType: "JSON",
        dataType: "JSON",
        type: e,
        timeout: 1e4,
        beforeSend: function(){Loading();},
    	complete: function(){closeLoading();},
        success: function(e) {
            0 === e.status ? i(e) : "401" == e.error_code ? (localStorage.clear(), hui.upToast(e.message), hui.open("./login.html")) : t ? t(e) : hui.upToast(e.message)
        },
        error: function(e, o, a) {
            hui.upToast("网络不佳")
        }
    })
}
var $_GET = (function(){
    var url = window.document.location.href.toString();
    var u = url.split("?");
    if(typeof(u[1]) == "string"){
        u = u[1].split("&");
        var get = {};
        for(var i in u){
            if(typeof(u[i]) == "string"){
                var j = u[i].split("=");
                get[j[0]] = j[1];
            }
        }
        return get;
    } else {
        return {};
    }
})()
var qnresult;
var host = window.location.host;
! function(e, o) {
    var a = "http://"+host+"/";
    e.requestmsgList = a + "api/vc/msglist", e.login = a + "api/user/login", e.reg = a + "api/user/register", e.getmobileverify = a + "home/verify/sendVerify", e.esc = a + "api/user/loginout", e.requestdomain = a, e.requestmsgdetail = a + "api/user/getmsgdetail",e.requestmsglist = a + "api/user/getmsglist", e.requestuserList = a + "api/user/getuserinfo", e.imageCode = a + "api/user/getloginverifyimg";
}(window.paths = {}, window.syscode = {});