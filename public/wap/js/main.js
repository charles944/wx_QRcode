function fastClick() {
    var n = function() {
            try {
                return document.createEvent("TouchEvent"), !0
            } catch (n) {
                return !1
            }
        }(),
        e = $.fn.on;
    $.fn.on = function() {
        if (/click/.test(arguments[0]) && "function" == typeof arguments[1] && n) {
            var t, i = arguments[1];
            e.apply(this, ["touchstart",
                function(n) {
                    t = n.changedTouches[0].clientY
                }
            ]), e.apply(this, ["touchend",
                function(n) {
                    Math.abs(n.changedTouches[0].clientY - t) > 10 || (n.preventDefault(), i.apply(this, [n]))
                }
            ])
        } else e.apply(this, arguments);
        return this
    }
}

function androidInputBugFix() {
    /Android/gi.test(navigator.userAgent) && window.addEventListener("resize", function() {
        "INPUT" != document.activeElement.tagName && "TEXTAREA" != document.activeElement.tagName || window.setTimeout(function() {
            document.activeElement.scrollIntoViewIfNeeded()
        }, 0)
    })
}

function setJSAPI() {
    var n = {
        title: "WeUI, 为微信 Web 服务量身设计",
        desc: "WeUI, 为微信 Web 服务量身设计",
        link: "https://weui.io",
        imgUrl: "https://mmbiz.qpic.cn/mmemoticon/ajNVdqHZLLA16apETUPXh9Q5GLpSic7lGuiaic0jqMt4UY8P4KHSBpEWgM7uMlbxxnVR7596b3NPjUfwg7cFbfCtA/0"
    };
    $.getJSON("https://weui.io/api/sign?url=" + encodeURIComponent(location.href.split("#")[0]), function(e) {
        wx.config({
            beta: !0,
            debug: !1,
            appId: e.appid,
            timestamp: e.timestamp,
            nonceStr: e.nonceStr,
            signature: e.signature,
            jsApiList: ["onMenuShareTimeline", "onMenuShareAppMessage", "onMenuShareQQ", "onMenuShareWeibo", "onMenuShareQZone", "setBounceBackground"]
        }), wx.ready(function() {
            wx.invoke("setBounceBackground", {
                backgroundColor: "#F8F8F8",
                footerBounceColor: "#F8F8F8"
            }), wx.onMenuShareTimeline(n), wx.onMenuShareQQ(n), wx.onMenuShareAppMessage({
                title: "WeUI",
                desc: "为微信 Web 服务量身设计",
                link: location.href,
                imgUrl: "https://mmbiz.qpic.cn/mmemoticon/ajNVdqHZLLA16apETUPXh9Q5GLpSic7lGuiaic0jqMt4UY8P4KHSBpEWgM7uMlbxxnVR7596b3NPjUfwg7cFbfCtA/0"
            })
        })
    })
}

function setPageManager() {
    for (var n = {}, e = $("template"), t = $(window).height(), i = 0, a = e.length; i < a; ++i) {
        var o = e[i],
            s = o.id.replace(/tpl_/, "");
        n[s] = {
            name: s,
            url: "#" + s,
            template: "#" + o.id
        }
    }
    n.home.url = "#";
    for (var r in n) pageManager.push(n[r]);
    pageManager.setPageAppend(function(n) {
        var e = n.find(".page__ft");
        e.length < 1 || (e.position().top + e.height() < t ? e.addClass("j_bottom") : e.removeClass("j_bottom"))
    }).setDefault("home").init()
}
//,
function init() {
    fastClick(), androidInputBugFix(), setPageManager(), window.pageManager = pageManager,  window.home = function() {
        location.hash = ""
    }
}
var pageManager = {
    $container: $("#container"),
    _pageStack: [],
    _configs: [],
    _pageAppend: function() {},
    _defaultPage: null,
    _pageIndex: 1,
    setDefault: function(n) {
        return this._defaultPage = this._find("name", n), this
    },
    setPageAppend: function(n) {
        return this._pageAppend = n, this
    },
    init: function() {
        var n = this;
        $(window).on("hashchange", function() {
            var e = history.state || {},
                t = 0 === location.hash.indexOf("#") ? location.hash : "#",
                m = t.indexOf("-") ? t.split('-') : t;
                if(Object.prototype.toString.call(m) === '[object Array]'){
                    i = n._find("url", m[0]) || n._defaultPage;
                    if(m[1]){
                        tmpId = m[1];
                    }
                }else{
                    i = n._find("url", m) || n._defaultPage;
                }
            e._pageIndex <= n._pageIndex || n._findInStack(t) ? n._back(i) : n._go(i)
        }), history.state && history.state._pageIndex && (this._pageIndex = history.state._pageIndex), this._pageIndex--;
        var e = 0 === location.hash.indexOf("#") ? location.hash : "#",
            m = e.indexOf("-") ? e.split('-') : e;
            if(Object.prototype.toString.call(m) === '[object Array]'){
                t = n._find("url", m[0]) || n._defaultPage;
                if(m[1]){
                    tmpId = m[1];
                }
            }else{
                t = n._find("url", m) || n._defaultPage;
            }
        return this._go(t), this
    },
    push: function(n) {
        return this._configs.push(n), this
    },
    go: function(n) {
        var m = n.indexOf("-") ? n.split('-') : n;
        if(Object.prototype.toString.call(m) === '[object Array]'){
            var e = this._find("name", m[0]);
            if(m[1]){
                e && (location.hash = e.url+'-'+m[1])
            }else{
                e && (location.hash = e.url)
            }
        }
    },
    _go: function(n) {
        this._pageIndex++, history.replaceState && history.replaceState({
            _pageIndex: this._pageIndex
        }, "", location.href);
        var e = $(n.template).html(),
            t = $(e).addClass(n.name);
        return t.addClass('js_show'), this.$container.append(t), this._pageAppend.call(this, t), this._pageStack.push({
            config: n,
            dom: t
        }), n.isBind || this._bind(n), this
    },
    back: function() {
        history.back()
    },
    _back: function(n) {
        var h = 0 === location.hash.indexOf("#") ? location.hash : "#";
        var m = h.indexOf("-") ? h.split('-') : h;
        var u = null;
        if(Object.prototype.toString.call(m) === '[object Array]'){
            h = m[0];
            if(m[1]){
                tmpId = m[1];
            }
            for (var e = null, t = this._pageStack.length-1, i = 0; t >= i; t--) {
                var a = this._pageStack[t];
                if (a.config.url === h) {
                    e = a;
                    break
                }else if(h == '#home' && a.config.url == '#'){
                    e = a;
                    break
                }
                if (!e){
                    this._pageStack.pop();
                    a.dom.remove();
                    this._pageIndex--;
                }
            }
            if(!e){
                h == '#home' ? u = '#' : u = h;
                var b = this._find("url", u);
                if(b){
                    var a = $(b.template).html(),
                        o = $(a).addClass("js_show").addClass(b.name);
                    this.$container.append(o), b.isBind || this._bind(b), this._pageStack.push({
                        config: b,
                        dom: o
                    })
                }
            }
        }
        clearInterval(timergame);
        clearInterval(timertask);
        return this
    },
    _findInStack: function(n) {
        for (var e = null, t = 0, i = this._pageStack.length; t < i; t++) {
            var a = this._pageStack[t];
            if (a.config.url === n) {
                e = a;
                break
            }else if(n == '#home' && a.config.url == '#'){
                e = a;
                break
            }
        }
        return e
    },
    _find: function(n, e) {
        for (var t = null, i = 0, a = this._configs.length; i < a; i++)
            if (this._configs[i][n] === e) {
                t = this._configs[i];
                break
            }
        return t
    },
    _bind: function(n) {
        var e = n.events || {};
        for (var t in e)
            for (var i in e[t]) this.$container.on(i, t, e[t][i]);
        n.isBind = !0
    }
};
init();