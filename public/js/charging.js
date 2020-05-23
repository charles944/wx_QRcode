(function() {
    void 0 == this.qke && (this.qke = {});
    var a = function() {};
    this.qke.api_base_url = "https://www.qkepay.com/api/gateway/", "undefined" != typeof QKE_API_URL && (this.qke.api_base_url = QKE_API_URL), this.qke.$ = function(a, b, c) {
        var d = {
                "#": "getElementById",
                ".": "getElementsByClassName",
                "@": "getElementsByName",
                "=": "getElementsByTagName",
                "*": "querySelectorAll"
            }[a[0]],
            e = (b === c ? document : b)[d](a.slice(1));
        return e.length < 2 ? e[0] : e
    }, this.qke.obj = function(b) {
        var c = {
            handler: {
                complete: a,
                success: a,
                error: a
            },
            params: b
        };
        return c.complete = function(a) {
            return c.handler.complete = a.bind(c), c
        }, c.success = function(a) {
            return c.handler.success = a.bind(c), c
        }, c.error = function(a) {
            return c.handler.error = a.bind(c), c
        }, c
    }
}).call(this),
    function() {
        var charge = function(data) {
                if ("string" == typeof data && "{" == data[0] && (data = JSON.parse(data)), data.status && data.result && data.result.action) switch (data.result.action.type) {
                    case "form":
                        form_submit(data.result.action.url, "POST", data.result.action.params);
                        break;
                    case "js":
                        eval(data.result.action.params);
                        break;
                    case "url":
                        window.location = data.result.action.url;
                    default:
                        console && console.error && console.error("undefined action:" + data.result.action.type)
                } else alert(data.error_msg), console && console.error && console.error(data)
            },
            ChargeWizard = function(a) {
                a.charge_id || alert("emtpy charge_id?"), a.platform = a.platform ? a.platform : "pc", "mobile" == a.platform ? load_charge_panel_mobile(a) : window.location = trim_p(window.qke.api_base_url) + "/app/checkout/pc?id=" + a.charge_id + "&list=" + a.payments
            },
            trim_p = function(a) {
                return "/" == a.substr(a.length - 1, 1) && (a = a.substr(0, a.length - 1)), a
            },
            load_charge_panel_mobile = function(a) {
                var b = document.createElement("IFRAME");
                b.setAttribute("src", trim_p(window.qke.api_base_url) + "/app/checkout/mobile?id=" + a.charge_id + "&list=" + a.payments), b.style.width = "100%", b.style.position = "fixed", b.style.bottom = "0", b.style.left = "0", b.style.zIndex = "100", b.style.border = "none", b.style.visibility = "hidden", b.onload = function() {
                    b.style.visibility = "inherit"
                }, document.body.appendChild(b);
                var c = document.createElement("div");
                document.body.appendChild(c), c.style.width = "100%", c.style.height = document.documentElement.clientHeight + "px", c.style.background = "rgba(0,0,0,.3)", c.style.position = "absolute", c.style.left = "0", c.style.top = "0", c.style.zIndex = "99";
                document.getElementById("pay-btns");
                document.body.style.height = document.documentElement.clientHeight + "px", document.body.style.overflow = "hidden", event.stopPropagation();
                var d = function() {
                    b.remove(), c.remove(), document.removeEventListener("click", d, !1)
                };
                document.addEventListener("click", d, !1)
            },
            form_submit = function(a, b, c) {
                var d = window.document.createElement("form");
                d.setAttribute("method", b), d.setAttribute("action", a), d.setAttribute("target", "_top");
                for (var e in c)
                    if (c.hasOwnProperty(e)) {
                        var f = window.document.createElement("input");
                        f.setAttribute("type", "hidden"), f.setAttribute("name", e), f.setAttribute("value", c[e]), d.appendChild(f)
                    }
                document.body.appendChild(d), d.submit()
            };
        this.charge = charge, this.ChargeWizard = ChargeWizard
    }.call(this.qke),
    function() {
        var a = function(a) {
                var c = window.qke.obj(a);
                return this.JSONP({
                    keep: !0,
                    url: b,
                    data: a,
                    complete: function(b) {
                        a.url = window.qke.api_base_url + "/app/checkout/do", a.channel = b, window.qke.charge(a)
                    }
                }), c
            },
            b = function() {
                var a = window.navigator.userAgent.toLowerCase();
                return "micromessenger" == a.match(/MicroMessenger/i) ? window.qke.api_base_url + "/app/checkout/show_wx" : window.qke.api_base_url + "/app/checkout"
            };
        this.checkout = a
    }.call(this.qke),
    function() {
        var a, b, c, d, e, f, g, h;
        c = function(a) {
            return window.document.createElement(a)
        }, d = window.encodeURIComponent, g = Math.random, a = function(a) {
            var d, f, g, i, j, k, l;
            if (a = a ? a : {}, k = {
                keep: a.keep || !1,
                data: a.data || {},
                error: a.error || e,
                success: a.success || e,
                beforeSend: a.beforeSend || e,
                complete: a.complete || e,
                url: a.url || ""
            }, k.computedUrl = b(k), 0 === k.url.length) throw new Error("MissingUrl");
            return i = !1, k.beforeSend({}, k) !== !1 && (g = a.callbackName || "callback", f = a.callbackFunc || "jsonp_" + h(15), d = k.data[g] = f, window[d] = function(a) {
                return k.keep || (window[d] = null), k.success(a, k), k.complete(a, k)
            }, l = c("script"), l.src = b(k), l.async = !0, l.onerror = function(a) {
                return k.error({
                    url: l.src,
                    event: a
                }), k.complete({
                    url: l.src,
                    event: a
                }, k)
            }, l.onload = l.onreadystatechange = function() {
                return i || this.readyState && "loaded" !== this.readyState && "complete" !== this.readyState ? void 0 : (i = !0, l.onload = l.onreadystatechange = null, l && l.parentNode && l.parentNode.removeChild(l), l = null)
            }, j = j || window.document.getElementsByTagName("head")[0] || window.document.documentElement, j.insertBefore(l, j.firstChild)), {
                abort: function() {
                    return window[d] = function() {
                        return window[d] = null
                    }, i = !0, l && l.parentNode ? (l.onload = l.onreadystatechange = null, l && l.parentNode && l.parentNode.removeChild(l), l = null) : void 0
                }
            }
        }, e = function() {
            return void 0
        }, b = function(a) {
            var b;
            return b = a.url, b += a.url.indexOf("?") < 0 ? "?" : "&", b += f(a.data)
        }, h = function(a) {
            var b;
            for (b = ""; b.length < a;) b += g().toString(36)[2];
            return b
        }, f = function(a) {
            var b, c, e;
            b = [];
            for (c in a) e = a[c], b.push(d(c) + "=" + d(e));
            return b.join("&")
        }, this.JSONP = a, this.createElement = c
    }.call(this.qke);