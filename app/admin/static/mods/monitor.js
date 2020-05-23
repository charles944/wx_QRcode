;
layui.define(function(e) {
    layui.use(["admin", "carousel"],
    function() {
        var e = layui.$,
        a = (layui.admin, layui.carousel),
        t = layui.element,
        i = layui.device();
        e(".qn-carousel").each(function() {
            var t = e(this);
            a.render({
                elem: this,
                width: "100%",
                arrow: "none",
                interval: t.data("interval"),
                autoplay: t.data("autoplay") === !0,
                trigger: i.ios || i.android ? "click": "hover",
                anim: t.data("anim")
            })
        }),
        t.render("progress")
    }),
    layui.use(["carousel", "echarts"], function() {
        var e = layui.$
          , a = (layui.carousel, layui.echarts);
          $.ajax({
            type: 'post',
            async : true,
            dataType: 'json',
            data: [],
            url: 'manage/index/tj_member_week',
            success: function(res){
                var l = []
                  , t = [{
                    title: {
                        text: "最近一周新增的用户量",
                        x: "center",
                        textStyle: {
                            fontSize: 14
                        }
                    },
                    tooltip: {
                        trigger: "axis",
                        formatter: "{b}<br>新增用户：{c}"
                    },
                    xAxis: [{
                        type: "category",
                        data: res.category
                    }],
                    yAxis: [{
                        type: "value"
                    }],
                    series: [{
                        type: "line",
                        data: res.line
                    }]
                }]
                , i = e("#TJ_index_member").children("div")
                , n = function(e) {
                    l[e] = a.init(i[e], layui.echartsTheme),
                    l[e].setOption(t[e]),
                    window.onresize = l[e].resize
                };
                i[0] && n(0)
            },
            error: function(e){
                layer.msg('统计一周会员数据请求异常', {shift: 6, icon: 2, fixed: true, offset: '80%', time: 1000});
            }
        })
    }),
    layui.use(["carousel", "echarts"],
    function() {
        var d;
        var e = layui.$;
        var a = (layui.carousel, layui.echarts);
        $.ajax({
            type: 'post',
            async : true,
            dataType: 'json',
            data: [],
            url: 'manage/index/tj_member',
            success: function(res){
                var l = [],
                t = [{
                    title: {
                        text: "全国用户分布",
                        subtext: "不完全统计"
                    },
                    tooltip: {
                        trigger: "item"
                    },
                    dataRange: {
                        orient: "horizontal",
                        min: 0,
                        max: 6e4,
                        text: ["高", "低"],
                        splitNumber: 0
                    },
                    series: [{
                        name: "全国用户分布",
                        type: "map",
                        mapType: "china",
                        selectedMode: "multiple",
                        itemStyle: {
                            normal: {
                                label: {
                                    show: !0
                                }
                            },
                            emphasis: {
                                label: {
                                    show: !0
                                }
                            }
                        },
                        data: res
                    }]
                }],
                i = e("#TJ_index_member_earth").children("div"),
                n = function(e) {
                    l[e] = a.init(i[e], layui.echartsTheme),
                    l[e].setOption(t[e]),
                    window.onresize = l[e].resize
                };
                i[0] && n(0)
            },
            error: function(e){
                layer.msg('统计会员地域请求异常', {shift: 6, icon: 2, fixed: true, offset: '80%', time: 1000});
            }
        });
    }),
    e("monitor", {})
});