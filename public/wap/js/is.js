function m(e) {
    console.log(e)
}

function $post(e, o, a, i) {
    commonAjax("post", e, o, a, i)
}

function $get(e, o, a, i) {
    commonAjax("get", e, o, a, i)
}

function back(){
    window.pageManager.back()
}

function tip(e) {
    $t.html(e), isTipHide() && ($t.show(), setTimeout(function() {
        $t.hide()
    }, 2e3))
}

function isTipHide() {
    return "none" === $t.css("display")
}

function openLoading(e) {
    var o = $("#loadingToast");
    0 === o.length && $(e).append('<div id="loadingToast"><div class="weui-mask_transparent"></div><div class="weui-toast"><i class="weui-loading weui-icon_toast"></i><p class="weui-toast__content">数据加载中</p></div></div>')
}

function closeLoading() {
    $("#loadingToast").remove()
}

function showSuccess(e, o) {
    closeLoading(), $(e).append('<div class="is-show-success"><div class="weui-mask_transparent"></div><div class="weui-toast"><i class="weui-icon-success-no-circle weui-icon_toast"></i><p class="weui-toast__content">已完成</p></div></div>'), setTimeout(function() {
        $(".is-show-success").remove(), o && o()
    }, 1111)
}

function set(e, o) {
    localStorage.setItem(e, JSON.stringify(o))
}

function get(e) {
    var b = localStorage.getItem(e);
    return JSON.parse(b);
}

function isLogin(e) {
    proxy = get("proxy");
    var i = window.location.hash;
    if($.isEmptyObject(proxy)){
        if(i == ""){
            window.pageManager.go("login");
        }else if(i == "#register"){
            //window.pageManager.go("register");
            e();
        }else{
            window.pageManager.go("login");
        }
    }else{
        e();
    }
}

function isManager() {
    //return 1 == proxy.level
    return 1;
}

function getRoomCard() {
    return isManager() ? "无限" : proxy.room_card
}

function getLevel() {
    return syscode.level[proxy.level]
}

function setRoomCard(e) {
    isManager() || (proxy.room_card = proxy.room_card + e, set("proxy", proxy))
}

function loadMore(e, o) {
    $(e).append('<div class="weui-cell" id="load-more"><div class="weui-cell__bd"><p class="page__desc is-text-center">加载更多</p></div></div>'), $("#load-more").on("click", function() {
        $(this).remove(), o()
    })
}

function loadMoreBegin(e) {
    $(e).append('<div class="weui-loadmore" id="load-more-begin"><i class="weui-loading"></i><span class="weui-loadmore__tips">正在加载</span></div>')
}

function loadMoreNo(e) {
    $("#load-more").remove(), $("#load-more-begin").remove(), $(e).append('<div class="weui-loadmore weui-loadmore_line" id="load-more-no"><span class="weui-loadmore__tips">暂无更多数据</span></div>')
}

function initHome() {
    proxy = get("proxy");
    //$.isEmptyObject(proxy) ? ($("#money_data").html("")) : ($("#money_data").html(proxy.money));
    return 0;
    //($("#home-next-proxy-count").html(""), $("#home-proxy-level").html(""), $("#home-room-card").html(""))
     //($("#home-next-proxy-count").html(proxy.next_proxy_count), $("#home-proxy-level").html(getLevel()), $("#home-room-card").html(getRoomCard()))
}

function dialog(e, o) {
    var a = '<div class="js_dialog dialog-tip" id="show-dialog"><div class="weui-mask"></div><div class="weui-dialog"><div class="weui-dialog__bd">' + e + '</div><div class="weui-dialog__ft"><a href="javascript:;" class="weui-dialog__btn weui-dialog__btn_primary" id="show-dialog-confirm">确认</a></div></div></div>';
    $("body").append(a), $("#show-dialog-confirm").on("click", function() {
        $("#show-dialog").remove(), o && o()
    })
}
var timergame = null, timertask = null, tmpId, damaId, gameId, taskId, newsId, regs = {
    username: {
        reg: /^[-_a-zA-Z0-9]{2,19}$/,
        msg: "仅支持3-20个字母、数字、下划线或减号"
    },
    password: {
        reg: /^\w{6,30}$/,
        msg: "密码格式错误"
    },
    imgCode: {
        reg: /^\w{4,6}$/,
        msg: "请输入图形验证码，长度4-6位"
    },
    mobileCode: {
        reg: /^\w{4,6}$/,
        msg: "请输入手机验证码，长度4-6位"
    },
    realname: {
        reg: /^.{1,10}$/,
        msg: "姓名长度1-10"
    },
    nickname: {
        reg: /^.{1,10}$/,
        msg: "昵称长度1-10"
    },
    mobile: {
        reg: /^\d{11}$/,
        msg: "手机号格式错误"
    }
};
! function(e, o) {
    var a = "/tiyanphp1/";
    //e.roomCardRecordPage = a + "api/user/getgamelist", e.proxyPage = a + "api/user/getgamelist",e.requestPage = a + "api/user/getgamelist",
    e.imageCode = a + "api/user/getloginverifyimg", e.requestInitHome = a + "api/user/getinithome", e.login = a + "api/user/login", e.reg = a + "api/user/register", e.getmobileverify = a + "home/verify/sendVerify", e.requestGameList = a + "api/user/getgamelist", e.requestGameDetail = a + "api/user/getgamedetail", e.requestGameLevel = a + "api/user/getgamelevel", e.requestGameWard = a + "api/user/getgameward", e.requestTaskList = a + "api/user/gettasklist", e.requestTaskDetail = a + "api/user/gettaskdetail", e.requestTaskBind = a + "api/user/gettaskbind", e.requestNewsList = a + "api/user/getnewslist", e.requestNewsDetail = a + "api/user/getnewsdetail", e.requestMsgList = a + "api/user/getmsglist", e.requestUserScore = a + "api/user/getuserscore", e.requestDamaList = a + "api/user/getdamalist", e.requestDamaDetail = a + "api/user/getdamadetail", e.requestDamaSign = a + "api/user/getdamasign", e.resetPwd = a + "proxy/resetPwd.do", e.playerRecharge = a + "proxy/playerRecharge.do", e.esc = a + "api/user/loginout", e.sms = a + "sms/send.do", e.requestProxy = a + "proxy/request.do",  e.requestDetail = a + "proxy/requestDetail.do", e.updateStatus = a + "proxy/updateStatus.do", e.detail = a + "proxy/detail.do", o.gameType = {
        1: "麻将",
        2: "字牌",
        3: "纸牌"
    }, o.type = {
        0: "上卡",
        1: "下卡",
        2: "充值"
    }, o.status = {
        0: "禁用",
        1: "正常",
        2: "审核中",
        3: "已拒绝"
    }, o.level = {
        0: "顶级",
        1: "1级",
        2: "2级",
        3: "3级"
    }
}(window.paths = {}, window.syscode = {});
var commonAjax = function(e, o, a, i, t) {
        $.ajax({
            url: o + "?t=" + (new Date).getTime(),
            data: a,
            dataType: "json",
            type: e,
            timeout: 1e4,
            success: function(e) {
                0 === e.status ? i(e) : "401" == e.error_code ? (localStorage.clear(), tip(e.message), window.pageManager.go("login")) : t ? t(e) : tip(e.message), closeLoading()
            },
            error: function(e, o, a) {
                tip("网络不佳"), closeLoading()
            }
        })
    },
    $t = $(".js_tooltips"),
    gameList = function(e) {
        var o, a, i, t = function() {
                o = 0, a = 20, i = 0, $(e).html(""), r()
            },
            r = function() {
                var o = {
                    limit: a
                };
                o.offset = (++i - 1) * a, loadMoreBegin(e), $get(paths.requestGameList, o, function(o) {
                    $("#game_list #load-more-begin").remove();
                    for(var i = 0; i < o.result.category.length; i++){
                        $("#game_list #nav_module").append('<li class="dis_inbl pr_1em line_h_26em text_12em text_al_c" data-id='+o.result.category[i].id+'>'+o.result.category[i].title+'</li>');
                    }
                    for (var i = 0; i < o.result.rows.length; i++) {
                        var t = o.result.rows[i],
                            s = $("#game_list #game-list-template").html();
                        s = s.replace(/{id}/g, t.id), s = s.replace(/{category}/g, t.category_id), s = s.replace(/{num}/g, t.view_count), s = s.replace(/{img}/g, t.class_img_path), s = s.replace(/{name}/g, t.title+t.title_sub), s = s.replace(/{gold}/g, (t.sum_gold_coin/10000)+'万'+o.result.game_score_type_name), $(e).append(s)
                    }
                    o.result.rows.length < a ? loadMoreNo(e) : loadMore(e, function() {
                        r()
                    }), $(e).find("a[data-id]").on("click", function() {
                        gameId = $(this).data("id"), window.pageManager.go("game_detail_list-"+gameId)
                    })
                })
            };
        return {
            reload: t
        }
    }("#game-list"),
    taskList = function(e) {
        var o, a, i, t = function() {
                o = 0, a = 20, i = 0, $(e).html(""), r()
            },
            r = function() {
                var o = {
                    limit: a
                };
                o.offset = (++i - 1) * a, loadMoreBegin(e), $get(paths.requestTaskList, o, function(o) {
                    $("#task_list #load-more-begin").remove();
                    for(var i = 0; i < o.result.category.length; i++){
                        $("#task_list #nav_module").append('<li class="dis_inbl pr_1em line_h_26em text_12em text_al_c" data-id='+o.result.category[i].id+'>'+o.result.category[i].title+'</li>');
                    }
                    for (var i = 0; i < o.result.rows.length; i++) {
                        var t = o.result.rows[i],
                        s = $("#task_list #task-list-template").html();
                    s = s.replace(/{id}/g, t.id), s = s.replace(/{category}/g, t.category_id), s = s.replace(/{num}/g, t.view_count), s = s.replace(/{img}/g, t.cover_path), s = s.replace(/{name}/g, t.title), s = s.replace(/{gold}/g, (t.task_gold_coin/10000)+'万'+o.result.task_score_type_name), $(e).append(s)
                    }
                    o.result.rows.length < a ? loadMoreNo(e) : loadMore(e, function() {
                        r()
                    }), $(e).find("a[data-id]").on("click", function() {
                        taskId = $(this).data("id"), window.pageManager.go("task_detail_list-"+taskId)
                    })
                })
            };
        return {
            reload: t
        }
    }("#task-list"),
    damaList = function(e) {
        var o, a, i, t = function() {
                o = 0, a = 20, i = 0, $(e).html(""), r()
            },
            r = function() {
                var o = {
                    limit: a
                };
                o.offset = (++i - 1) * a, loadMoreBegin(e), $get(paths.requestDamaList, o, function(o) {
                    $("#dama_list #load-more-begin").remove();
                    for(var i = 0; i < o.result.category.length; i++){
                        $("#dama_list #nav_module").append('<li class="dis_inbl pr_1em line_h_26em text_12em text_al_c" data-id='+o.result.category[i].id+'>'+o.result.category[i].title+'</li>');
                    }
                    for (var i = 0; i < o.result.rows.length; i++) {
                        var t = o.result.rows[i],
                        s = $("#dama_list #dama-list-template").html();
                    s = s.replace(/{id}/g, t.id), s = s.replace(/{category}/g, t.category_id), s = s.replace(/{num}/g, t.view_count), s = s.replace(/{img}/g, t.cover_path), s = s.replace(/{name}/g, t.title), s = s.replace(/{gold}/g, (t.code_gold_coin)+o.result.dama_score_type_name), $(e).append(s)
                    }
                    o.result.rows.length < a ? loadMoreNo(e) : loadMore(e, function() {
                        r()
                    }), $(e).find("a[data-id]").on("click", function() {
                        damaId = $(this).data("id"), window.pageManager.go("dama_detail_list-"+damaId)
                    })
                })
            };
        return {
            reload: t
        }
    }("#dama-list"),
    newsList = function(e) {
        var o, a, i, t = function() {
                o = 0, a = 5, i = 0, $(e).html(""), r()
            },
            r = function() {
                var o = {
                    limit: a
                };
                o.offset = (++i - 1) * a, loadMoreBegin(e), $get(paths.requestNewsList, o, function(o) {
                    $("#news_list #load-more-begin").remove();
                    for (var i = 0; i < o.result.rows.length; i++) {
                        var t = o.result.rows[i],
                            s = $("#news_list #news-list-template").html();
                        s = s.replace(/{id}/g, t.id), s = s.replace(/{title}/g, t.title), s = s.replace(/{desc}/g, t.description), s = s.replace(/{addtime}/g, t.addtime), s = s.replace(/{nickname}/g, t.nickname), $(e).append(s)
                    }
                    o.result.rows.length < a ? loadMoreNo(e) : loadMore(e, function() {
                        r()
                    }), $(e).find("a[data-id]").on("click", function() {
                        newsId = $(this).data("id"), window.pageManager.go("news_detail_list-"+newsId)
                    })
                })
            };
        return {
            reload: t
        }
    }("#news-list"),
    msgList = function(e) {
        var o, a, i, t = function() {
                o = 0, a = 5, i = 0, $(e).html(""), r()
            },
            r = function() {
                var o = {
                    limit: a
                };
                o.offset = (++i - 1) * a, loadMoreBegin(e), $get(paths.requestMsgList, o, function(o) {
                    $("#msg_list #load-more-begin").remove();
                    for (var i = 0; i < o.result.rows.length; i++) {
                        var t = o.result.rows[i],
                            s = $("#msg_list #msg-list-template").html();
                        s = s.replace(/{id}/g, t.id), s = s.replace(/{title}/g, t.content.title), s = s.replace(/{desc}/g, t.content.content), $(e).append(s)
                    }
                    o.result.rows.length < a ? loadMoreNo(e) : loadMore(e, function() {
                        r()
                    }), $(e).find("a[data-id]").on("click", function() {
                        newsId = $(this).data("id"), window.pageManager.go("msg_detail_list")
                    })
                })
            };
        return {
            reload: t
        }
    }("#msg-list"),
    homePage = function(e) {
        a = function() {
            //console.log(initHome());
            // if ($('.index-banner-slider').length>0){
            //     var indexSlider = slider('.index-banner-slider', {
            //         speed: 1000,
            //         autoplay: 3000,
            //         loop: true,
            //         passiveListeners: false,
            //         pagination:'.slider-pagination'
            //     });
            // }
            if (initHome()) {
                //console.log(1111);
            }
            loadMoreBegin(e), $get(paths.requestInitHome, '', function(o) {
                $(e).find("#load-more-begin").remove();
                var t = o.result;
                $(e).find(".gamecount").html(t.game);
                $(e).find(".taskcount").html(t.task);
                $(e).find(".damacount").html(t.dama);
            }), $(e).find("a[data-id]").on("click", function() {
                window.pageManager.go($(this).data("id"));
            }), $(e).find(".esc").on("click", function() {
                $.get(window.paths.esc), localStorage.clear(), window.pageManager.go("login")
            }), $(e).find(".back").on("click", function(){
                window.pageManager.back();
            })
        };
        return {
            init: a
        }
    }("#home");
$(window).on("popstate", function() {
    var e = location.hash,
        o = e.split("#!/"),
        a = o[1];
    if ("" !== a) {
        var i = window.location.hash;
        switch (i) {
            //case "":
                //isLogin(homePage.init);
                //$("#weui-footer").show();
                //break;
        }
    }
});