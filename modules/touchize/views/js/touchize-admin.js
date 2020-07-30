/**
 * 2018 Touchize Sweden AB.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to prestashop@touchize.com so we can send you a copy immediately.
 *
 *  @author    Touchize Sweden AB <prestashop@touchize.com>
 *  @copyright 2018 Touchize Sweden AB
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of Touchize Sweden AB
 *  
 *  Touchize TouchMap Interface v0.1.26
 *  All rights reserved. This notice may NOT be removed.
 *  http://Touchize.com/ 
 */

(function(exports) {
    var Extensions = {};
    function inherit(child, base) {
        var baseP = base.prototype, childP;
        childP = child.prototype = Object.create(baseP);
        childP.constructor = child;
    }
    var Extension = function Extension() {};
    function log(obj) {
        if (window.console && window.console.log) {
            if (obj.notification) {
                console.log(obj.notification, obj);
            } else {
                console.log(obj);
            }
        }
    }
    function error(obj) {
        if (window.console && window.console.error) {
            if (obj.notification) {
                console.error(obj.notification, obj);
            } else {
                console.error(obj);
            }
        }
    }
    function debug(obj) {
        if (window.console && window.console.debug) {
            if (obj.notification) {
                console.debug(obj.notification, obj);
            } else {
                console.debug(obj);
            }
        }
    }
    function getConfig() {
        return false;
    }
    Extensions.EventRouter = function EventRouter() {
        "use strict";
        var that = this, events = [];
        function Notify(eventName, eventData, eventCallback) {
            function trigger(triggerName, triggerData, triggerCallback) {
                try {
                    if (triggerName && typeof triggerName.fn === "function") {
                        triggerName.fn.call(this, triggerData, triggerCallback);
                        log({
                            notification: triggerName.name,
                            data: triggerData,
                            callback: triggerCallback,
                            module: triggerName.desc ? triggerName.desc : triggerName.mod
                        });
                    }
                } catch (err) {
                    error({
                        notification: triggerName.name,
                        data: triggerData,
                        callback: triggerCallback,
                        module: triggerName.desc ? triggerName.desc : triggerName.mod,
                        error: err
                    });
                }
            }
            function notifier(notifyQue, notifyData, notifyCallback) {
                for (var notification in notifyQue) {
                    trigger(notifyQue[notification], notifyData, notifyCallback);
                }
            }
            if (events.hasOwnProperty(eventName)) {
                notifier(events[eventName], eventData, eventCallback);
            }
        }
        function Subscribe(moduleName, moduleUID, eventName, eventFunction, eventCallback) {
            var event = {
                name: eventName,
                mod: moduleUID,
                fn: eventFunction,
                cb: eventCallback,
                desc: moduleName
            };
            if (!events.hasOwnProperty(eventName)) {
                events[eventName] = {};
            }
            events[eventName][moduleUID] = event;
        }
        function UnSubscribe(moduleUID, eventName) {
            if (events.hasOwnProperty(eventName)) {
                if (events[eventName].hasOwnProperty(moduleUID)) {
                    delete events[eventName][moduleUID];
                }
            }
        }
        function UnSubscribeAll(moduleUID) {
            for (var event in events) {
                if (events[event].hasOwnProperty(moduleUID)) {
                    delete events[event][moduleUID];
                }
            }
        }
        Extension.apply(this, [ this.constructor.name ]);
        return {
            notify: Notify,
            subscribe: Subscribe,
            unsubscribe: UnSubscribe,
            unsubscribeall: UnSubscribeAll
        };
    };
    inherit(Extensions.EventRouter, Extension, {});
    Extensions.TemplateEngine = function TemplateEngine() {
        "use strict";
        var that = this, BOOL_ATTRIBUTES = /^(autoplay|loop|disabled|checked|readonly|required|selected)$/, SVG_ELEMENTS = /^(svg|circle|path|rect)$/, HTML_ELEMENTS = /^(canvas|footer|fieldset|h1|h2|h3|h4|h5|h6|header|main|aside|iframe|section|div|ul|a|li|i|p|img|form|table|tbody|thead|input|label|button|tr|td|th|source|span|select|option|video)$/;
        function evil(statement, model) {
            return eval(statement);
        }
        function getProperty(statement, model) {
            var i = 0, properties = statement.substring(7).split(".");
            while (properties[i]) {
                try {
                    model = model[properties[i]];
                } catch (err) {
                    error({
                        notification: "TemplateEngine",
                        error: err,
                        object: object,
                        model: model
                    });
                }
                i++;
            }
            return model;
        }
        function condition(object, model) {
            object = object.replace(/@item/g, "@model");
            try {
                return evil(object.replace(/@/g, ""), model);
            } catch (e) {
                return false;
            }
        }
        function findReplacement(object, model) {
            object = object.replace(/@item/g, "@model");
            var statements = object.match(/(@model).*?(?=;|<|,|"|'|\)|\r|\n|\s| |$)/gi);
            if (statements) {
                var i = 0;
                while (statements[i]) {
                    try {
                        var replacement = getProperty(statements[i], model);
                        if (typeof replacement === "function") {
                            var fn = replacement.apply(model);
                            if (typeof fn === "boolean") {
                                return fn;
                            } else {
                                object = object.replace(statements[i], replacement.apply(model));
                            }
                        } else {
                            if (getConfig("RemoveInlineStyles") && typeof replacement === "string" && replacement.match(/style="([^"]*)"/gi)) {
                                replacement = replacement.replace(/style="([^"]*)"/gi, "");
                            }
                            if (typeof replacement === "boolean") {
                                replacement = replacement ? replacement : "";
                            }
                            object = replacement && replacement !== null ? object.replace(statements[i], replacement) : "";
                        }
                    } catch (err) {
                        object = object.replace(statements[i], "");
                        error({
                            notification: "TemplateEngine",
                            error: err,
                            object: object,
                            model: model
                        });
                    }
                    i++;
                }
            }
            return object;
        }
        function createEl(structure, model, element, parent) {
            for (var key in structure) {
                var object = structure[key];
                if (typeof object === "string") {
                    if (key === "html") {
                        element.innerHTML = findReplacement(object, model);
                    } else if (key === "text") {
                        element.textContent = findReplacement(object, model);
                    } else if (key === "css") {
                        element.appendChild(document.createTextNode(findReplacement(object, model)));
                    } else {
                        var attribute = findReplacement(object, model);
                        if (attribute && attribute !== "undefined" && attribute !== null && attribute !== "") {
                            element.setAttribute(key, attribute);
                        }
                    }
                } else if (typeof object === "boolean") {
                    if (BOOL_ATTRIBUTES.test(key)) {
                        var singleAttribute;
                        if (typeof object === "boolean") {
                            singleAttribute = object;
                        } else {
                            singleAttribute = findReplacement(object, model);
                        }
                        if (singleAttribute) {
                            element.setAttribute(key, "");
                        }
                    }
                } else if (typeof object === "object") {
                    if (HTML_ELEMENTS.test(key)) {
                        element = document.createElement(key);
                        createEl(object, model, element, parent);
                    } else if (key === "empty") {
                        element = document.createDocumentFragment();
                        createEl(object, model, element, parent);
                    } else if (key === "children") {
                        i = 0;
                        while (object[i]) {
                            try {
                                createEl(object[i], model, null, element);
                            } catch (e) {
                                error({
                                    notification: "TemplateEngine",
                                    error: e,
                                    object: object[i],
                                    key: key,
                                    model: model
                                });
                            }
                            i++;
                        }
                    } else if (key === "if") {
                        try {
                            if (condition(object.condition, model)) {
                                createEl(object.template, model, element, parent);
                            } else if (object.elseif && condition(object.elseif.condition, model)) {
                                createEl(object.elseif.template, model, element, parent);
                            } else if (object.else) {
                                createEl(object.else, model, element, parent);
                            }
                        } catch (e) {
                            error({
                                notification: "TemplateEngine",
                                error: e,
                                object: object,
                                key: key,
                                model: model
                            });
                        }
                    } else if (key === "each") {
                        var items = getProperty(object.items, model);
                        var i = 0;
                        while (items[i]) {
                            if (JSON.stringify(object.template).match(/@recursive/)) {
                                items[i].Level = (model.Level || 0) + 1;
                            }
                            if (typeof items[i] === "object") {
                                items[i].Index = i.toString();
                            }
                            try {
                                createEl(object.template, items[i], null, element);
                            } catch (err) {
                                error({
                                    notification: "TemplateEngine",
                                    error: err,
                                    object: object,
                                    key: key,
                                    model: items[i]
                                });
                            }
                            i++;
                        }
                    } else if (SVG_ELEMENTS.test(key)) {
                        element = document.createElementNS("http://www.w3.org/2000/svg", key);
                        createEl(object, model, element, parent);
                    } else if (key === "style") {
                        element = document.createElement(key);
                        element.type = "text/css";
                        createEl(object, model, element, parent);
                    } else if (key === "comment") {
                        element = document.createComment(null);
                        createEl(object, model, element, parent);
                    }
                }
                if (element) {
                    try {
                        parent.appendChild(element);
                        if (object.recursive && model && model.Children && model.Children.length) {
                            var recursiveStructure = JSON.stringify(object.recursive).replace(/"@recursive"/, JSON.stringify(object)), recursiveStructure = JSON.parse(recursiveStructure);
                            createEl(recursiveStructure, model, null, parent);
                        }
                    } catch (err) {
                        error({
                            notification: "TemplateEngine",
                            error: err,
                            object: object,
                            model: model
                        });
                    }
                }
            }
            return;
        }
        function View(structure, model, callback) {
            var documentFragment;
            documentFragment = document.createDocumentFragment();
            createEl(structure, model, null, documentFragment);
            callback.call(this, documentFragment);
        }
        Extension.apply(this, [ this.constructor.name ]);
        return {
            view: View
        };
    };
    inherit(Extensions.TemplateEngine, Extension, {});
    Extensions.Utils = function Utils() {
        "use strict";
        var that = this;
        var userAgent = navigator.userAgent.toLowerCase();
        var hasLocalStorage = function() {
            try {
                window.localStorage.setItem("_", "");
                window.localStorage.removeItem("_");
            } catch (e) {
                if (/QUOTA_?EXCEEDED/i.test(e.name)) {
                    return false;
                }
            }
            return true;
        }();
        var hasCookiesEnabled = navigator.cookieEnabled;
        var vendorPrefix = function() {
            var styles = window.getComputedStyle(document.documentElement, ""), pre = (Array.prototype.slice.call(styles).join("").match(/-(moz|webkit|ms)-/) || styles.OLink === "" && [ "", "o" ])[1], dom = "WebKit|Moz|MS|O".match(new RegExp("(" + pre + ")", "i"))[1];
            return {
                dom: dom,
                lowercase: pre,
                css: "-" + pre + "-",
                js: pre[0].toUpperCase() + pre.substr(1)
            };
        }();
        var pixelRatio = function() {
            return (window.devicePixelRatio || 1) > 1 ? "hdpi" : "mdpi";
        }();
        var screenSize = function() {
            return (window.innerHeight || window.clientHeight) > 700 ? "big" : "small";
        }();
        var log = function(obj) {
            if (getConfig("Debug") && window.console && window.console.log) {
                if (obj.notification) {
                    console.log(obj.notification, obj);
                } else {
                    console.log(obj);
                }
            }
        };
        var error = function(obj) {
            if (getConfig("Debug") && window.console && window.console.error) {
                if (obj.notification) {
                    console.error(obj.notification, obj);
                } else {
                    console.error(obj);
                }
            }
        };
        var getDevice = {
            Android: function() {
                return userAgent.match(/android/i);
            },
            BlackBerry: function() {
                return userAgent.match(/blackberry/i);
            },
            iOS: function() {
                return userAgent.match(/iphone|ipad|ipod/i);
            },
            iPad: function() {
                return userAgent.match(/ipad/i);
            },
            iPhone: function() {
                return userAgent.match(/iphone/i);
            },
            Opera: function() {
                return userAgent.match(/opera mini/i);
            },
            Windows: function() {
                return userAgent.match(/iemobile/i);
            }
        };
        var genericRequest = function(url, data, done, fail) {
            var toClass = {}.toString, xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4) {
                    var response = 0;
                    try {
                        response = JSON.parse(xhr.responseText);
                    } catch (error) {
                        response = xhr.responseText;
                    }
                    if (xhr.status === 200) {
                        if (done !== undefined) {
                            done.call(this, response ? response : "");
                        }
                    } else {
                        if (fail !== undefined) {
                            fail.call(this, response);
                        } else {
                            console.error("Error");
                        }
                    }
                }
            };
            if (data.method && data.method === "POST") {
                xhr.open("POST", url, true);
                if (data && toClass.call(data.form) === "[object Object]") {
                    xhr.setRequestHeader("Content-Type", "application/json");
                    data.form = JSON.stringify(data.form);
                } else if (data.type) {
                    xhr.setRequestHeader("Content-Type", data.type);
                }
            } else {
                xhr.open("GET", url, true);
            }
            xhr.send(data && data.form ? data.form : "");
        };
        Extension.apply(this, [ this.constructor.name ]);
        return {
            genericRequest: genericRequest,
            log: log,
            error: error,
            vendorPrefix: vendorPrefix,
            pixelRatio: pixelRatio,
            screenSize: screenSize,
            hasLocalStorage: hasLocalStorage,
            hasCookiesEnabled: hasCookiesEnabled,
            userAgent: userAgent,
            getDevice: getDevice
        };
    };
    inherit(Extensions.Utils, Extension, {});
    var actionAreaView = {
        "product-list": {
            empty: {
                each: {
                    items: "@model",
                    template: {
                        span: {
                            class: "slq-product",
                            "data-pid": "@item.Id",
                            "data-sku": "@item.SKU",
                            children: [ {
                                div: {
                                    "data-pid": "@item.Id",
                                    "data-sku": "@item.SKU",
                                    text: "@item.Id - @item.Title"
                                }
                            }, {
                                div: {
                                    "data-pid": "@item.Id",
                                    html: "@item.ShortDescription"
                                }
                            } ]
                        }
                    }
                }
            }
        },
        "category-list": {
            empty: {
                each: {
                    items: "@model",
                    template: {
                        span: {
                            class: "slq-category",
                            "data-tid": "@item.Id",
                            text: "@item.Name"
                        }
                    }
                }
            }
        },
        "more-chars": {
            span: {
                text: "Type @model more char(s)"
            }
        },
        "no-result": {
            span: {
                text: "No result for '@model'"
            }
        },
        searching: {
            span: {
                text: "Searching for '@model'"
            }
        },
        table: {
            empty: {
                each: {
                    items: "@model",
                    template: {
                        tr: {
                            class: "slq-action-area-table-row",
                            "data-id": "@item.Id",
                            children: [ {
                                td: {
                                    style: "background-color: @item.color;",
                                    text: "@item.Id"
                                }
                            }, {
                                td: {
                                    class: "slq-action-area-product-id",
                                    "data-pid": "@item.ProductId",
                                    "data-id": "@item.Id",
                                    children: [ {
                                        if: {
                                            condition: "@item.ProductId && @item.ProductId !== '0'",
                                            template: {
                                                input: {
                                                    type: "text",
                                                    class: "slq-input text slq-product-input",
                                                    title: "(Id: @item.ProductId) @item.ProductName",
                                                    placeholder: "(Id: @item.ProductId) @item.ProductName"
                                                }
                                            },
                                            else: {
                                                input: {
                                                    type: "text",
                                                    class: "slq-input text slq-product-input",
                                                    title: "Type name to add product",
                                                    placeholder: "Type name to add product"
                                                }
                                            }
                                        }
                                    }, {
                                        div: {
                                            class: "autocomplete-list",
                                            "data-id": "@item.Id"
                                        }
                                    } ]
                                }
                            }, {
                                td: {
                                    class: "slq-action-area-search-term",
                                    "data-id": "@item.Id",
                                    "data-pid": "@item.SearchTerm",
                                    children: [ {
                                        if: {
                                            condition: "@item.SearchTerm",
                                            template: {
                                                input: {
                                                    type: "text",
                                                    class: "slq-input text slq-search-input",
                                                    title: "@item.SearchTerm",
                                                    placeholder: "@item.SearchTerm"
                                                }
                                            },
                                            else: {
                                                input: {
                                                    type: "text",
                                                    class: "slq-input text slq-search-input",
                                                    title: "Type to add searchterm",
                                                    placeholder: "Type to add searchterm"
                                                }
                                            }
                                        }
                                    } ]
                                }
                            }, {
                                td: {
                                    class: "slq-action-area-category-id",
                                    "data-id": "@item.Id",
                                    "data-pid": "@item.TaxonId",
                                    children: [ {
                                        if: {
                                            condition: "@item.TaxonId && @item.TaxonId !== '0'",
                                            template: {
                                                input: {
                                                    type: "text",
                                                    class: "slq-input text slq-category-input",
                                                    title: "(ID: @item.TaxonId) @item.CategoryName",
                                                    placeholder: "(ID: @item.TaxonId) @item.CategoryName"
                                                }
                                            },
                                            else: {
                                                input: {
                                                    type: "text",
                                                    class: "slq-input text slq-category-input",
                                                    title: "Type name to add category",
                                                    placeholder: "Type name to add category"
                                                }
                                            }
                                        }
                                    }, {
                                        div: {
                                            class: "autocomplete-list",
                                            "data-id": "@item.Id"
                                        }
                                    } ]
                                }
                            }, {
                                td: {
                                    class: "slq-action-area-delete",
                                    "data-id": "@item.Id",
                                    children: [ {
                                        button: {
                                            type: "button",
                                            class: "delete btn btn-default",
                                            children: [ {
                                                span: {
                                                    text: "Delete"
                                                }
                                            } ]
                                        }
                                    } ]
                                }
                            } ]
                        }
                    }
                }
            }
        }
    };
    var colors = [ "#FF6633", "#FFFF66", "#66FF66", "#66CCCC", "#00FFFF", "#3399FF", "#9966FF", "#FF66FF" ];
    var CanvasTool = function(core, options, element) {
        "use strict";
        var drawingObject = {}, canvasContext = element.getContext("2d"), canvas = element, self = this, cachedAreas = {};
        function addActionArea() {
            var form = new FormData();
            form.append("id_touchize_touchmap", options.id);
            form.append("tx", drawingObject.x / canvas.width);
            form.append("ty", drawingObject.y / canvas.height);
            form.append("width", drawingObject.w / canvas.width);
            form.append("height", drawingObject.h / canvas.height);
            core.utils.genericRequest(options.add, {
                method: "POST",
                form: form
            }, function(data) {
                core.router.notify("Render.ActionAreas", data);
                self.render(data);
            });
        }
        function startDraw(e) {
            drawingObject.x = e.offsetX || e.layerX;
            drawingObject.y = e.offsetY || e.layerY;
            drawingObject.w = 0;
            drawingObject.h = 0;
            canvas.addEventListener("mousemove", draw);
            canvas.addEventListener("mouseup", endDraw);
        }
        function draw(e) {
            function dynDrawRect(draw) {
                canvasContext.clearRect(0, 0, canvas.width, canvas.height);
                self.render(cachedAreas);
                canvasContext.beginPath();
                canvasContext.setLineDash([ 5, 2 ]);
                canvasContext.fillStyle = "rgba(255,255,255,0.3)";
                canvasContext.lineWidth = "1";
                canvasContext.strokeStyle = "rgba(0,0,0,0.8)";
                canvasContext.rect(draw.x, draw.y, draw.w, draw.h);
                canvasContext.stroke();
                canvasContext.fill();
            }
            drawingObject.w = (e.offsetX || e.layerX) - drawingObject.x;
            drawingObject.h = (e.offsetY || e.layerY) - drawingObject.y;
            dynDrawRect(drawingObject);
        }
        function endDraw(e) {
            if (Math.abs(drawingObject.w) > 10 && Math.abs(drawingObject.h) > 10) {
                if (drawingObject.w < 0) {
                    drawingObject.w = Math.abs(drawingObject.w);
                    drawingObject.x = Math.abs(drawingObject.x - drawingObject.w);
                }
                if (drawingObject.h < 0) {
                    drawingObject.h = Math.abs(drawingObject.h);
                    drawingObject.y = Math.abs(drawingObject.y - drawingObject.h);
                }
                addActionArea();
            }
            canvas.removeEventListener("mousemove", draw);
        }
        element.addEventListener("mousedown", startDraw);
        this.redraw = function(data) {
            canvas.setAttribute("height", data.height);
            canvas.setAttribute("width", data.width);
            canvas.style.setProperty("background-image", "url('" + data.src + "')");
        };
        this.render = function(data, element) {
            cachedAreas = data;
            canvasContext.clearRect(0, 0, canvas.width, canvas.height);
            canvasContext.font = "bold 14px Arial";
            canvasContext.setLineDash([ 5, 2 ]);
            canvasContext.lineWidth = "1";
            canvasContext.textBaseline = "top";
            for (var i = 0; i < data.length; i++) {
                data[i].color = colors[i];
                var x = parseFloat(data[i].Tx) / 100 * canvas.width;
                var y = parseFloat(data[i].Ty) / 100 * canvas.height;
                var height = parseFloat(data[i].Width) / 100 * canvas.width;
                var width = parseFloat(data[i].Height) / 100 * canvas.height;
                canvasContext.beginPath();
                canvasContext.fillStyle = "rgba(255,255,255,0.3)";
                canvasContext.strokeStyle = colors[i];
                canvasContext.rect(x, y, height, width);
                canvasContext.stroke();
                canvasContext.fill();
                canvasContext.fillStyle = "#000";
                canvasContext.fillText(data[i].Id, x + 5, y + 5);
            }
        };
    };
    var ActionAreasModule = function(core, options) {
        "use strict";
        var frame, addImage, deleteImage, imageContainer, CanvasDrawer = new CanvasTool(core, options, document.getElementById("slq-touchmap-canvas"));
        core.router.subscribe("ActionAreas", "actionareas", "Render.ActionAreas", function(data) {
            renderTableActionAreas(data);
        });
        function renderTableActionAreas(data) {
            for (var i = 0; i < data.length; i++) {
                data[i].color = colors[i];
            }
            var actionAreaList = document.getElementById("slq-action-area-list-table");
            actionAreaList.innerHTML = "";
            core.tmpl.view(actionAreaView["table"], data, function(element) {
                actionAreaList.appendChild(element);
                var inputs = document.querySelectorAll(".slq-input");
                [].forEach.call(inputs, function(element) {
                    element.onkeyup = getResult;
                    element.onfocus = getResult;
                    element.onblur = hideResult;
                });
                var deleteButtons = document.querySelectorAll(".slq-action-area-delete button");
                [].forEach.call(deleteButtons, function(element) {
                    element.addEventListener("click", deleteActionArea);
                });
            });
        }
        var searchTimeout;
        function hideResult(e) {
            setTimeout(function() {
                var list = e.target.parentNode.querySelector(".autocomplete-list");
                if (list) {
                    list.innerHTML = "";
                }
            }, 300);
        }
        function getResult(e) {
            function showResult(data, element, term) {
                if (!term) {
                    element.target.classList.remove("working");
                }
                var list = element.target.parentNode.querySelector(".autocomplete-list");
                list.innerHTML = "";
                var view = actionAreaView["category-list"];
                var attachTo = ".slq-category";
                if (element.target.classList.contains("slq-product-input")) {
                    view = actionAreaView["product-list"];
                    attachTo = ".slq-product";
                }
                if (!data && !term) {
                    view = actionAreaView["more-chars"];
                    var str = element.target.value;
                    data = Math.abs(str.length - 3);
                    attachTo = null;
                } else if (data && !data.length && !term) {
                    view = actionAreaView["no-result"];
                    data = element.target.value;
                    attachTo = null;
                } else if (term) {
                    view = actionAreaView["searching"];
                    data = term;
                    attachTo = null;
                }
                core.tmpl.view(view, data, function(element) {
                    list.appendChild(element);
                    var items = list.querySelectorAll(attachTo);
                    [].forEach.call(items, function(element) {
                        element.onclick = selectItem;
                    });
                });
            }
            var str = e.target.value;
            if (str.length <= 2) {
                if (!e.target.classList.contains("slq-search-input")) {
                    showResult(null, e);
                }
                return;
            }
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(function() {
                var endpoint = options.categories;
                if (e.target.classList.contains("slq-product-input")) {
                    endpoint = options.products;
                } else if (e.target.classList.contains("slq-search-input")) {
                    editActionArea(e.target.parentNode.getAttribute("data-id"), null, null, e.target.value);
                    return;
                }
                e.target.classList.add("working");
                showResult(null, e, str);
                core.utils.genericRequest(endpoint + "&q=" + str, {
                    method: "GET"
                }, function(data) {
                    showResult(data, e);
                });
            }, 1e3);
        }
        function selectItem(e) {
            var id = e.target.parentNode.parentNode.getAttribute("data-id");
            if (id === null) {
                id = e.target.parentNode.parentNode.parentNode.getAttribute("data-id");
            }
            var pid = e.target.getAttribute("data-pid");
            if (pid === null) {
                pid = e.target.parentNode.getAttribute("data-pid");
            }
            var tid = e.target.getAttribute("data-tid");
            editActionArea(id, pid, tid);
        }
        var redrawTimeout;
        function redrawImage() {
            var campaignImage = document.getElementById("slq-touchmap-image");
            var container = document.querySelector("#slq-touchmap-width");
            var containerWidth = container.offsetWidth;
            var scale = containerWidth < campaignImage.naturalWidth ? containerWidth * .94 / campaignImage.naturalWidth : 1;
            var imageHeight = Math.floor(campaignImage.naturalHeight * scale.toFixed(2));
            var imageWidth = Math.floor(campaignImage.naturalWidth * scale.toFixed(2));
            CanvasDrawer.redraw({
                height: imageHeight,
                width: imageWidth,
                src: campaignImage.getAttribute("src")
            });
            clearTimeout(redrawTimeout);
            redrawTimeout = setTimeout(function() {
                listActionAreas();
            }, 250);
        }
        function fixDrawingArea(data) {
            var campaignImage = document.getElementById("slq-touchmap-image");
            if (data.imageUrl) {
                campaignImage.src = data.imageUrl;
                campaignImage.onload = redrawImage;
            } else {
                redrawImage();
            }
        }
        function editActionArea(id, pid, tid, term) {
            var form = new FormData();
            form.append("id", id);
            form.append("id_touchize_touchmap", options.id);
            if (pid) {
                form.append("product_id", pid);
            }
            if (tid) {
                form.append("taxon_id", tid);
            }
            if (term) {
                form.append("search_term", term);
            }
            core.utils.genericRequest(options.edit, {
                method: "POST",
                form: form
            }, function(data) {
                renderTableActionAreas(data);
            });
        }
        function listActionAreas() {
            var form = new FormData();
            form.append("id_touchize_touchmap", options.id);
            core.utils.genericRequest(options.list, {
                method: "POST",
                form: form
            }, function(data) {
                CanvasDrawer.render(data);
                renderTableActionAreas(data);
            });
        }
        function deleteActionArea(e) {
            e.target.setAttribute("disabled", "disabled");
            var form = new FormData();
            form.append("id", e.currentTarget.parentNode.getAttribute("data-id")), form.append("id_touchize_touchmap", options.id);
            core.utils.genericRequest(options.delete, {
                method: "POST",
                form: form
            }, function(data) {
                renderTableActionAreas(data);
                CanvasDrawer.render(data);
            });
        }
        var campaignImage = document.getElementById("slq-touchmap-image");
        campaignImage.onload = function(e) {
            redrawImage();
        };
        addImage = document.querySelector(".upload-custom-img");
        deleteImage = document.querySelector(".delete-custom-img");
        var imageIdInput = document.querySelector(".custom-img-id");
        window.onresize = fixDrawingArea;
        redrawImage();
    };
    exports.Edit = function(options) {
        "use strict";
        var Core = function(core, name) {
            this.log = function(obj) {
                core.utils.log(obj);
            };
            this.error = function(obj) {
                core.utils.error(obj);
            };
            this.getConfig = function(configObject) {
                if (configObject in core.Cfg) {
                    return core.Cfg[configObject];
                }
                return false;
            };
        };
        var that = this;
        this.Cfg = {};
        this.Cfg.Debug = true;
        Extensions.EventRouter.prototype = new Core(that, "EventRouter");
        this.router = new Extensions.EventRouter(this);
        Extensions.TemplateEngine.prototype = new Core(that, "TemplateEngine");
        this.tmpl = new Extensions.TemplateEngine(this);
        Extensions.Utils.prototype = new Core(that, "Utils");
        this.utils = new Extensions.Utils(this);
        new ActionAreasModule(that, options);
    };
})(typeof window.TouchMap == "undefined" ? window.TouchMap = {} : window.TouchMap);