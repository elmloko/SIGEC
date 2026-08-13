/*
 * by Ivan Marcelo Chacolla
 * date: 10/10/2014 
 */

function getInternetExplorerVersion() {
    var e = -1;
    if (navigator.appName == "Microsoft Internet Explorer") {
        var t = navigator.userAgent;
        var n = new RegExp("MSIE ([0-9]{1,}[.0-9]{0,})");
        if (n.exec(t) != null)
            e = parseFloat(RegExp.$1)
    }
    return e
}
(function (e) {
    var t = {};
    var n = {};
    var r = {};
    var i = function () {
        if (typeof i.instance === "object") {
            return i.instance
        }
        i.instance = this
    };
    i.prototype.init = function (r, i) {
        n[r] = {};
        t[r] = e.extend(true, {}, i);
        var s = e("<div></div>");
        s.attr({id: "x-table-" + r}).addClass("x-table");
        if (typeof i.theme == "string") {
            s.addClass(i.theme)
        }
        e("#" + r).append(s);
        n[r] = {};
        if (i.order != undefined && i.order.column != undefined && i.order.type != undefined) {
            n[r].order_column = i.order.column;
            n[r].order_type = i.order.type;
            if (i.pagination != undefined) {
                n[r].record_per_page = i.pagination.record_per_page != undefined ? i.pagination.record_per_page : 20
            }
        }
        n[r].page_index = 1;
        return s
    };
    i.prototype.createTitle = function (t, n, r) {
        if (r.title != undefined) {
            n.append(e("<div></div>").attr({id: "x-table-header-title-" + t}).addClass("title").append(r.title))
        } else {
            n.css({border: "0px", padding: "0px"})
        }
        return n
    };
    i.prototype.createToolbar = function (n, r, i) {
        var i = t[n];
        if (i.toolbar != undefined) {
            var s = e("<div></div>").addClass("toolbar").attr({id: "#x-table-toolbar-" + n});
            if (typeof i.toolbar.align == "string") {
                s.css({textAlign: i.toolbar.align})
            }
            if (i.toolbar.buttons != undefined) {
                for (var o in i.toolbar.buttons) {
                    var u = e("<button></button>");
                    if (typeof i.toolbar.buttons[o].icon == "string") {
                        u.append('<i class="' + i.toolbar.buttons[o].icon + '">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</i>')
                    }
                    u.append(i.toolbar.buttons[o].text);
                    if (typeof i.toolbar.buttons[o].script == "function") {
                        u.each(function () {
                            i.toolbar.buttons[o].script(this, n)
                        })
                    }
                    s.append(u)
                }
            }
            r.append(s)
        }
        return r
    };
    i.prototype.createHeader = function (t, r, i) {
        var s = this;
        r.append(e("<div></div>").attr({id: "x-table-header-" + t}).addClass("header")
                .append(e("<div></div>").attr({id: "x-table-header-box-" + t})
                .addClass("box").css({paddingRight: "17px"})
                .append(e("<table></table>").attr({id: "x-table-header-table-" + t, cellpadding: 0, cellspacing: 0, border: 0})
                //.width(50).
                        .append(e("<thead></thead>").append(e("<tr></tr>"))))));
        for (var o in i.columns) {
            var u = i.columns[o].header != undefined ? i.columns[o].header : "";
            var a = e("<th></th>");
            if (i.columns[o].width != undefined) {
                a.width(i.columns[o].width)
            }
            if (i.columns[o].resize == false) {
                a.addClass("fixed-width")
            }
            var f = e("<div></div>");
            if (i.order != undefined && i.order.column != undefined && i.order.type != undefined) {
                if (i.columns[o].name == i.order.column) {
                    switch (i.order.type.toLowerCase()) {
                        case"asc":
                            f.addClass("up");
                            break;
                        case"desc":
                            f.addClass("down");
                            break
                        }
                }
            }
            if (i.columns[o].name != undefined && i.columns[o].order == true) {
                a.css({cursor: "pointer"}).click(function () {
                    var o = "DESC";
                    var u = e(this).attr("name");
                    if (e(this).children().hasClass("up")) {
                        e(this).children().removeClass("up");
                        e(this).children().addClass("down")
                    } else {
                        e('div[name="x-table-header-title-' + t + '"]').removeClass("up").removeClass("down");
                        e(this).children().addClass("up");
                        o = "ASC"
                    }
                    n[t].order_column = u;
                    n[t].order_column = u;
                    n[t].order_type = o;
                    n[t].page_index = parseInt(e("#x-table-pagination-page-index-" + t).val());
                    s.loadData(t, r, i)
                })
            }
            a.attr({name: i.columns[o].name}).append(f.attr({name: "x-table-header-title-" + t}).append(e("<div></div>").css({overflow: "hidden"}).append(u)));
            e("#x-table-header-table-" + t + " > thead > tr").append(a)
        }
        return r
    };
    i.prototype.createSlider = function (t, n, r) {
        var i = this;
        e("#x-table-header-table-" + t + " > thead > tr >th").each(function (n) {
            var r = n;
            var s = e(this).offset().left + e(this).outerWidth();
            var o = e('<div class="draghandle"><span /></div>').attr({id: "x-table-drag-" + t}).css({left: s - 3, height: 22}).insertBefore("#x-table-header-table-" + t);
            if (e(this).hasClass("fixed-width") == false) {
                o.mousedown(function () {
                    i.startDrag(e(this), r, t)
                });
                e(document).mousemove(function (e) {
                    i.dragMove(e.clientX)
                });
                e(document).mouseup(function (e) {
                    i.stopDrag()
                })
            } else {
                o.hide()
            }
        });
        return n
    };
    i.prototype.createBody = function (t, n, r) {
        var i = this;
        n.append(e("<div></div>").attr({id: "x-table-body-" + t})
                .addClass("body").append(e("<div></div>")
                .attr({id: "x-table-body-box-" + t})
                .addClass("box").append(e("<table></table>").attr({id: "x-table-body-table-" + t, cellpadding: 0, cellspacing: 0, border: 0})
                //.width(50)
                 .append(e("<tbody></tbody>")))));
        e("#x-table-body-" + t).scroll(function () {
            e("#x-table-header-" + t).scrollLeft(e(this).scrollLeft());
            i.setSliderPosition(t)
        });
        return n
    };
    i.prototype.createPagination = function (r, i, s) {
        if (s.pagination == undefined) {
            return i
        }
        var s = t[r];
        var o = this;
        var u = e("<div/>").attr({id: "x-table-pagination-button-first-" + r}).addClass("first");
        var a = e("<div/>").attr({id: "x-table-pagination-button-prev-" + r}).addClass("prev");
        var f = e("<div/>").attr({id: "x-table-pagination-button-next-" + r}).addClass("next");
        var l = e("<div/>").attr({id: "x-table-pagination-button-last-" + r}).addClass("last");
        u.click(function () {
            if (parseInt(e("#x-table-pagination-page-index-" + r).val()) > 1) {
                n[r].page_index = 1;
                o.loadData(r, i, s)
            }
        });
        a.click(function () {
            if (parseInt(e("#x-table-pagination-page-index-" + r).val()) > 1) {
                var t = parseInt(e("#x-table-pagination-page-index-" + r).val());
                n[r].page_index = t > 1 ? t - 1 : 1;
                o.loadData(r, i, s)
            }
        });
        f.click(function () {
            if (parseInt(e("#x-table-pagination-total-page-" + r).text()) > parseInt(e("#x-table-pagination-page-index-" + r).val())) {
                var t = parseInt(e("#x-table-pagination-page-index-" + r).val());
                var u = parseInt(e("#x-table-pagination-total-page-" + r).html());
                n[r].page_index = t < u ? t + 1 : u;
                o.loadData(r, i, s)
            }
        });
        l.click(function () {
            if (parseInt(e("#x-table-pagination-total-page-" + r).text()) > parseInt(e("#x-table-pagination-page-index-" + r).val())) {
                n[r].page_index = e("#x-table-pagination-total-page-" + r).html();
                o.loadData(r, i, s)
            }
        });
        i.append(e("<div></div>").addClass("pagination").attr({id: "#x-table-pagination-" + r}).append(e("<div></div>").addClass("control").append(e('<table  border="0" cellpadding="0" cellspacing="0" ></table>').append(e("<tbody></tbody>").append(e('<tr valign="middle"></tr>').append('<td><div><div class="separate"><span/></div></div></td>').append(e("<td></td>").append(e("<div></div>").append(u))).append(e("<td></td>").append(e("<div></div>").append(a))).append('<td><div><div class="separate"><span/></div></div></td>').append("<td><div><span> P&aacute;gina: </span></div></td>").append('<td><input type="text" value="1" id="x-table-pagination-page-index-' + r + '"/></td>').append('<td><div> de <span id="x-table-pagination-total-page-' + r + '"></span> </div></td>').append('<td><div><div class="separate"><span/></div></div></td>').append(e("<td></td>").append(e("<div></div>").append(f))).append(e("<td></td>").append(e("<div></div>").append(l))))))).append('<span class="info" id="x-table-pagination-info-' + r + '"></span>'));
        e("#x-table-pagination-page-index-" + r).keypress(function (t) {
            if (t.keyCode == 13) {
                var u = parseInt(e("#x-table-pagination-total-page-" + r).text());
                var a = parseInt(e("#x-table-pagination-page-index-" + r).val());
                if (a > u) {
                    n[r].page_index = u
                } else {
                    n[r].page_index = a
                }
                o.loadData(r, i, s)
            }
        });
        return i
    };
    i.prototype.filter = function (t, r) {
        var i = n[r].conditions;
        return jQuery.grep(t, function (n, r) {
            var s = t[r];
            var o = true;
            if (i != undefined) {
                for (var r in i) {
                    if (s[r] != undefined && s[r] != "" && i[r] != undefined && i[r] != "") {
                        switch (typeof s[r]) {
                            case"string":
                                i[r] = i[r] + "";
                                if (s[r].toLowerCase().indexOf(e.trim(i[r].toLowerCase())) == -1) {
                                    o = false;
                                    break
                                }
                                break;
                            case"number":
                                if (s[r] != i[r]) {
                                    o = false;
                                    break
                                }
                                break
                            }
                    }
                }
            }
            return o
        })
    };
    i.prototype.paginate = function (n, r, i, s, o) {
        var u = t[n];
        if (u.pagination != undefined && u.pagination.message != undefined) {
            var a = (o - 1) * s + 1;
            var f = o * s;
            if (f > i) {
                f = i
            }
            e("#x-table-pagination-info-" + n).html(this.printf(u.pagination.message, a, f, i))
        }
        e("#x-table-pagination-total-record-" + n).html(i);
        e("#x-table-pagination-total-page-" + n).html(r);
        e("#x-table-pagination-page-index-" + n).val(o);
        if (o == 1) {
            e("#x-table-pagination-button-first-" + n).css({cursor: "default", opacity: "0.4", filter: "alpha(opacity=40)"});
            e("#x-table-pagination-button-prev-" + n).css({cursor: "default", opacity: "0.4", filter: "alpha(opacity=40)"})
        } else {
            e("#x-table-pagination-button-first-" + n).css({cursor: "pointer", opacity: "1", filter: "alpha(opacity=100)"});
            e("#x-table-pagination-button-prev-" + n).css({cursor: "pointer", opacity: "1", filter: "alpha(opacity=100)"})
        }
        if (o >= r) {
            e("#x-table-pagination-button-next-" + n).css({cursor: "default", opacity: "0.4", filter: "alpha(opacity=40)"});
            e("#x-table-pagination-button-last-" + n).css({cursor: "default", opacity: "0.4", filter: "alpha(opacity=40)"})
        } else {
            e("#x-table-pagination-button-next-" + n).css({cursor: "pointer", opacity: "1", filter: "alpha(opacity=100)"});
            e("#x-table-pagination-button-last-" + n).css({cursor: "pointer", opacity: "1", filter: "alpha(opacity=100)"})
        }
    };
    i.prototype.loadData = function (n, i, s) {
        var s = t[n];
        var o = s.loading_message != undefined ? s.loading_message : "Loading...";
        this.mask(n, o);
        if (typeof r[n] == "object") {
            e("#x-table-body-table-" + n + " > tbody > tr").remove();
            this.buildData(n, i, s, r[n]);
            this.unmask(n)
        } else {
            switch (s.type) {
                case"xml":
                    this._loadXmlData(n, i, s);
                    break;
                default:
                    this._loadJsonData(n, i, s);
                    break
                }
        }
        return i
    };
    i.prototype.buildData = function (r, i, s, o) {
        var s = t[r];
        o.rows = this.filter(o.rows, r);
        if (n[r].order_type != undefined) {
            if (n[r].order_type.toLowerCase() == "asc") {
                o.rows.sort(this.asc(n[r].order_column))
            } else {
                o.rows.sort(this.desc(n[r].order_column))
            }
        }
        if (s.pagination != undefined && s.pagination.record_per_page != undefined) {
            var u = o.rows.slice((n[r].page_index - 1) * s.pagination.record_per_page, n[r].page_index * s.pagination.record_per_page)
        } else {
            var u = o.rows
        }
        for (var a in u) {
            var f = e("<tr></tr>");
            var l = u[a];
            if (l["id"] != undefined) {
                f.attr({id: l["id"]})
            }
            f = this.createRow(r, i, s, l, f);
            e("#x-table-body-table-" + r + " > tbody").append(f)
        }
        if (s.pagination != undefined && s.pagination.record_per_page != undefined) {
            var c = Math.ceil(o.rows.length / s.pagination.record_per_page);
            this.paginate(r, c, o.rows.length, s.pagination.record_per_page, n[r].page_index)
        }
        e("#x-table-header-" + r).scrollLeft(e("#x-table-body-" + r).scrollLeft());
        this.resetWidth(r, i, s);
        this.setSliderPosition(r)
    };
    i.prototype.createRow = function (t, n, r, i, s) {
        for (var o in r.columns) {
            var u = e("<td></td>");
            var a = e("<div></div>");
            if (r.columns[o].align != undefined) {
                a.css({textAlign: r.columns[o].align})
            }
            if (r.columns[o].name != undefined) {
                a.attr({id: r.columns[o].name})
            }
            if (r.columns[o].data != undefined) {
                switch (typeof r.columns[o].data) {
                    case"function":
                        var f = r.columns[o].data(i, t, s);
                        a.append(f);
                        break;
                    default:
                        a.append(r.columns[o].data);
                        break
                    }
            } else {
                a.html(i[r.columns[o].name])
            }
            s.append(u.append(a))
        }
        return s
    };
    i.prototype._loadXmlData = function (t, i, s) {
        if (s.url != undefined) {
            var o = this;
            e.post(s.url, {"x-table": n[t]}, function (n) {
                var u = o.xml2Json(n);
                r[t] = {rows: u};
                e("#x-table-body-table-" + t + " > tbody > tr").remove();
                o.buildData(t, i, s, r[t]);
                o.unmask(t)
            }, "xml")
        } else if (s.rows != undefined) {
            if (typeof s.rows == "object") {
                r[t] = {rows: s.rows}
            } else {
                var u = this.xml2Json(s.rows);
                r[t] = {rows: u}
            }
            e("#x-table-body-table-" + t + " > tbody > tr").remove();
            this.buildData(t, i, s, r[t]);
            this.unmask(t)
        }
    };
    i.prototype._loadJsonData = function (t, i, s) {
        var o = this;
        if (s.url != undefined) {
            e.post(s.url, {"x-table": n[t]}, function (n) {
                if (n.rows == undefined) {
                    r[t] = {rows: n}
                } else {
                    r[t] = n
                }
                e("#x-table-body-table-" + t + " > tbody > tr").remove();
                o.buildData(t, i, s, r[t]);
                o.unmask(t)
            }, "json")
        } else if (s.rows != undefined) {
            if (typeof s.rows == "object") {
                r[t] = {rows: s.rows}
            } else {
                var u = this.xml2Json(s.rows);
                r[t] = {rows: u}
            }
            e("#x-table-body-table-" + t + " > tbody > tr").remove();
            this.buildData(t, i, s, r[t]);
            this.unmask(t)
        }
    };
    i.prototype.resetWidth = function (t, n, r) {
        e("#x-table-header-table-" + t + " > thead > tr >th").each(function (n) {
            var r = 1;
            var i = getInternetExplorerVersion();
            var s = !!window.opera || navigator.userAgent.indexOf(" OPR/") >= 0;
            var o = Object.prototype.toString.call(window.HTMLElement).indexOf("Constructor") > 0;
            var u = !!window.chrome && !s;
            if (i < 8 && i != -1) {
                r = n == e("#x-table-header-table-" + t + " > thead > tr >th").length - 1 ? 2 : r;
                e(e("#x-table-body-table-" + t + " > tbody > tr >td")[n])
                        .width(e(this).outerWidth() - r)
            } else if (o == true || o == true) {
                if (/chrome/.test(navigator.userAgent.toLowerCase())) {
                    if (n == 0)
                        r--;
                    e(e("#x-table-body-table-" + t + " > tbody > tr >td")[n])
                            .width(e(this).outerWidth() - r)
                } else {
                    if (n == 0)
                        r--;
                    e(e("#x-table-body-table-" + t + " > tbody > tr >td")[n])
                            .width(e(this).outerWidth() - r)
                }
            } else if (s) {
                if (n == 0)
                    r--;
                e(e("#x-table-body-table-" + t + " > tbody > tr >td")[n])
                        . width(e(this).outerWidth() - r)
            } else {
                e(e("#x-table-body-table-" + t + " > tbody > tr >td")[n])
                      .width(e(this).outerWidth() - r)
            }
        })
    };
    i.prototype.mask = function (t, n) {
        var r = e("#x-table-body-" + t);
        if (r.hasClass("masked")) {
            this.unmask(t)
        }
        if (r.css("position") == "static") {
            r.addClass("masked-relative")
        }
        r.addClass("masked");
        var i = e('<div class="loadmask"></div>');
        i.height(r.height() + parseInt(r.css("padding-top")) + parseInt(r.css("padding-bottom")) + r.scrollTop());
        i.width(r.width() + parseInt(r.css("padding-left")) + parseInt(r.css("padding-right")) + r.scrollLeft());
        r.append(i);
        if (n !== undefined) {
            var s = e('<div class="loadmask-msg" style="display:none;"></div>');
            s.append("<div>" + n + "</div>");
            r.append(s);
            var o = Math.round(r.height() / 2 + r.scrollTop() - (s.height() - parseInt(s.css("padding-top")) - parseInt(s.css("padding-bottom"))) / 2);
            var u = Math.round(r.width() / 2 + r.scrollLeft() - (s.width() - parseInt(s.css("padding-left")) - parseInt(s.css("padding-right"))) / 2);
            s.css({top: o, left: u}).show()
        }
    };
    i.prototype.unmask = function (t) {
        var n = e("#x-table-body-" + t);
        n.find(".loadmask-msg,.loadmask").remove();
        n.removeClass("masked");
        n.removeClass("masked-relative");
        n.find("select").removeClass("masked-hidden")
    };
    i.prototype.dragOffset = {};
    i.prototype.startDrag = function (t, n, r) {
        var i = t.offset().left;
        this.dragOffset = {target: t, left: i, index: n, targetId: r};
        e(t).toggleClass("dragged");
        e(t).prev(".draghandle").addClass("dragged");
        e(this.dragOffset.target).prev(".draghandle").addClass("dragged");
        var s = 0;
        e(t).css({height: s + 22});
        e(t).prev(".draghandle").css({height: s + 22})
    };
    i.prototype.dragMove = function (e) {
        if (this.dragOffset.target != undefined) {
            if (window.getSelection)
                window.getSelection().removeAllRanges();
            else if (document.selection)
                document.selection.empty();
            this.dragOffset.target.css({left: e - 4})
        }
    };
    i.prototype.stopDrag = function () {
        if (this.dragOffset.target != undefined) {
            var t = this.dragOffset.left;
            var n = this.dragOffset.target.offset().left;
            e(this.dragOffset.target).removeClass("dragged");
            e(this.dragOffset.target).prev(".draghandle").removeClass("dragged");
            this.resetHeaderColumn(n - t - 2, this.dragOffset.index, this.dragOffset.targetId);
            e(this.dragOffset.target).css({height: 22});
            e(this.dragOffset.target).prev(".draghandle").css({height: 22});
            this.dragOffset = {}
        }
    };
    i.prototype.resetHeaderColumn = function (t, n, r) {
        var i = e("#x-table-header-table-" + r + " > thead > tr >th")[n].offsetWidth;
        var s = e("#x-table-header-table-" + r)[0].offsetWidth;
        if (i + t > 30) {
            var o = i + t
        } else {
            o = 30;
            t = o - i
        }
        e("#x-table-header-box-" + r).width(s + t);
        e("#x-table-body-box-" + r).width(s + t);
        e(e("#x-table-header-table-" + r + " > thead > tr >th")[n]).width(Math.ceil(o) - 1);
        this.resetWidth(r);
        e("#x-table-header-" + r).scrollLeft(e("#x-table-body-" + r).scrollLeft());
        this.setSliderPosition(r)
    };
    i.prototype.setSliderPosition = function (t) {
        e("#x-table-header-table-" + t + " > thead > tr >th").each(function (n) {
            var r = e(this).offset().left + e(this).outerWidth();
            e(e('div[id="x-table-drag-' + t + '"]')[n]).css({left: r - 3, height: 25})
        })
    };
    i.prototype.printf = function (e) {
        if (arguments.length < 2) {
            return e
        }
        for (var t = 1; t < arguments.length; t++) {
            e = e.replace(/%s/, arguments[t])
        }
        return e
    };
    i.prototype.asc = function (e) {
        return function (t, n) {
            return t[e] < n[e] ? -1 : t[e] > n[e] ? 1 : 0
        }
    };
    i.prototype.desc = function (e) {
        return function (t, n) {
            return n[e] < t[e] ? -1 : n[e] > t[e] ? 1 : 0
        }
    };
    i.prototype.xml2Json = function (t) {
        var n = [];
        e(t).find("row").each(function () {
            var t = {};
            e(this).children().each(function () {
                switch (this.nodeName.toLowerCase()) {
                    case"id":
                        t[this.nodeName] = parseInt(e(this).text());
                        break;
                    default:
                        t[this.nodeName] = e(this).text();
                        break
                    }
            });
            n[n.length] = t
        });
        return n
    };
    i.decorators = {};
    i.prototype.decorate = function (e) {
        var t = function () {
        }, n = this.constructor.decorators[e], r, i;
        t.prototype = this;
        i = new t;
        i.uber = t.prototype;
        for (r in n) {
            if (n.hasOwnProperty(r)) {
                i[r] = n[r]
            }
        }
        return i
    };
    i.decorators.remote = {buildData: function (r, i, s, o) {
            var s = t[r];
            var u = o.rows;
            for (var a in u) {
                var f = e("<tr></tr>");
                var l = u[a];
                if (l["id"] != undefined) {
                    f.attr({id: l["id"]})
                }
                f = this.createRow(r, i, s, l, f);
                e("#x-table-body-table-" + r + " > tbody").append(f)
            }
            if (s.pagination != undefined && s.pagination.record_per_page != undefined) {
                var c = o.total_page;
                this.paginate(r, c, o.total_row, s.pagination.record_per_page, n[r].page_index)
            }
            e("#x-table-header-" + r).scrollLeft(e("#x-table-body-" + r).scrollLeft());
            this.resetWidth(r, i, s);
            this.setSliderPosition(r)
        }, loadData: function (e, n, r) {
            var r = t[e];
            var i = r.loading_message != undefined ? r.loading_message : "Cargando...";
            this.mask(e, i);
            switch (r.type) {
                case"xml":
                    this._loadXmlData(e, n, r);
                    break;
                default:
                    this._loadJsonData(e, n, r);
                    break
            }
            return n
        }};
    e.fn.xTable = function (s, o) {
      /*  if (document.domain != "localhost")
            return;*/
        var u = e(this).attr("id");
        var a = s;
        if (t[u] != undefined) {
            s = t[u]
        }
        var f = new i;
        if (typeof s.pakages == "object" && s.pakages instanceof Array) {
            for (var l in s.pakages) {
                f = f.decorate(s.pakages[l])
            }
        }
        if (o == undefined)
            o = undefined;
        switch (typeof a) {
            case"string":
                var c = e("#x-table-" + u);
                switch (a) {
                    case"reload":
                        r[u] = undefined;
                        n[u].page_index = 1;
                        n[u].order_column = t[u].order.column;
                        n[u].order_type = t[u].order.type;
                        e("#x-table-header-table-" + u + " > thead > tr > th > div").removeClass("up");
                        e("#x-table-header-table-" + u + " > thead > tr > th > div").removeClass("down");
                        switch (n[u].order_type.toLowerCase()) {
                            case"asc":
                                e('th[name="' + n[u].order_column + '"]').children().addClass("up");
                                break;
                            case"desc":
                                e('th[name="' + n[u].order_column + '"]').children().addClass("down");
                                break
                        }
                        f.loadData(u, c, s);
                        break;
                    case"conditions":
                        switch (typeof o) {
                            case"object":
                                n[u].conditions = e.extend(true, {}, o);
                                break;
                            case"undefined":
                                return n[u].conditions != undefined ? n[u].conditions : {};
                                break
                        }
                        break
                }
                break;
            case"object":
                return e(this).each(function () {
                    var e = f.init(u, s);
                    e = f.createTitle(u, e, s);
                    e = f.createToolbar(u, e, s);
                    e = f.createHeader(u, e, s);
                    e = f.createSlider(u, e, s);
                    e = f.createBody(u, e, s);
                    e = f.createPagination(u, e, s);
                    f.loadData(u, e, s)
                });
                break
            }
    }
})(jQuery)