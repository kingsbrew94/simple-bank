"use strict";
var __extends = (this && this.__extends) || (function () {
    var extendStatics = Object.setPrototypeOf ||
        ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
        function (d, b) { for (var p in b) if (b.hasOwnProperty(p)) d[p] = b[p]; };
    return function (d, b) {
        extendStatics(d, b);
        function __() { this.constructor = d; }
        d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
    };
})();
var UI;
(function (UI) {
    var Redirect = /** @class */ (function () {
        function Redirect() {
        }
        Redirect.to = function (url) {
            window.location.assign(url);
        };
        Redirect.toOpen = function (url) {
            window.open(url);
        };
        return Redirect;
    }());
    UI.Redirect = Redirect;
    var Spark = /** @class */ (function () {
        function Spark() {
        }
        Spark.onrequest = function (globalObject) {
            return function (callback) {
                var objectString = globalObject.toString();
                switch (objectString) {
                    case "[object Window]":
                        globalObject.onload = callback;
                        break;
                    case "[object HTMLDocument]":
                        if (globalObject.readyState === 'loading') {
                            callback();
                        }
                        break;
                    default:
                        try {
                            throw new Error("Arguement Type Error: Excepted a global object like 'window' or 'document'");
                        }
                        catch (error) {
                            console.log(error);
                        }
                        break;
                }
            };
        };
        return Spark;
    }());
    UI.Spark = Spark;
    var View = /** @class */ (function () {
        function View() {
            this.ON = true;
            this.OFF = false;
        }
        View.prototype.setStyle = function (docElement, css) {
            var self = docElement;
            if (css === undefined)
                return;
            var getKeys = Object.getOwnPropertyNames(css);
            if (self.length > 1) {
                self.forEach(function (element) {
                    getKeys.forEach(function (csskey) {
                        element.style[csskey] = css[csskey];
                    });
                });
            }
            else {
                getKeys.forEach(function (csskey) {
                    self.style[csskey] = css[csskey];
                });
            }
        };
        View.prototype.setProps = function (docElement, attributes) {
            if (attributes === undefined)
                throw new Error('Object property is null');
            var attributesKeys = Object.getOwnPropertyNames(attributes);
            if (docElement.length > 1 && !(typeof docElement === 'string')) {
                docElement.forEach(function (element) {
                    for (var _i = 0, attributesKeys_1 = attributesKeys; _i < attributesKeys_1.length; _i++) {
                        var property = attributesKeys_1[_i];
                        var key = property;
                        switch (property) {
                            case 'style':
                                if (attributes[property] === true) {
                                    continue;
                                }
                                break;
                            case 'class':
                                docElement.className = attributes[property];
                                continue;
                            case 'text':
                                docElement.innerHTML = attributes[property];
                                continue;
                            case 'addtext':
                                docElement.appendChild(document.createTextNode(attributes[property]));
                                continue;
                            case 'children':
                                for (var _a = 0, _b = attributes[property]; _a < _b.length; _a++) {
                                    element = _b[_a];
                                    docElement.appendChild(attributes[property]);
                                }
                                continue;
                            case 'child':
                                docElement.appendChild(attributes[property]);
                                continue;
                        }
                        docElement.setAttribute(property.toString(), attributes[property]);
                    }
                });
            }
            else {
                if (typeof docElement === 'object') {
                    for (var _i = 0, attributesKeys_2 = attributesKeys; _i < attributesKeys_2.length; _i++) {
                        var attr = attributesKeys_2[_i];
                        var key = attr;
                        switch (attr) {
                            case 'style':
                                if (attributes[attr] === true) {
                                    continue;
                                }
                                break;
                            case 'class':
                                docElement.className = attributes[attr];
                                continue;
                            case 'text':
                                docElement.innerHTML = attributes[attr];
                                continue;
                            case 'addtext':
                                docElement.appendChild(document.createTextNode(attributes[attr]));
                                continue;
                            case 'children':
                                for (var _a = 0, _b = attributes[attr]; _a < _b.length; _a++) {
                                    var element = _b[_a];
                                    docElement.appendChild(element);
                                }
                                continue;
                            case 'child':
                                docElement.appendChild(attributes[key]);
                                continue;
                        }
                        docElement.setAttribute(attr.toString(), attributes[attr]);
                    }
                }
            }
        };
        View.prototype.selectNode = function (selector) {
            var element = (document.querySelector(selector) !== undefined) || (document.querySelector(selector) !== null)
                ? document.querySelector(selector) : null;
            return element;
        };
        View.prototype.selectNodes = function (selector) {
            var element = (document.querySelectorAll(selector) !== undefined) || (document.querySelector(selector) !== null)
                ? document.querySelectorAll(selector) : null;
            return element;
        };
        View.prototype.selectNodesByNames = function (selector) {
            var element = (document.getElementsByName(selector) !== undefined) || (document.getElementsByName(selector) !== null)
                ? document.getElementsByName(selector) : null;
            return element;
        };
        View.prototype.getNodeById = function (idName) {
            return this.selectNode("#" + idName);
        };
        View.prototype.getNodesByName = function (name_selector) {
            return this.selectNodesByNames(name_selector);
        };
        View.prototype.getNodeValueById = function (idName) {
            var node = this.selectNode("#" + idName);
            var value = node !== null ? node.value : null;
            return value;
        };
        View.prototype.getNodesByClass = function (className) {
            return this.selectNodes("." + className);
        };
        View.prototype.getNodesByTag = function (tagName) {
            return this.selectNodes(tagName);
        };
        View.prototype.int = function (data) {
            return parseInt(data);
        };
        View.prototype.str = function (data) {
            return data.toString();
        };
        View.prototype.float = function (data) {
            return parseFloat(data);
        };
        View.prototype.json = function (data) {
            return JSON.parse(data);
        };
        View.prototype.len = function (data) {
            return data.length;
        };
        View.prototype.getHTMNode = function (element) {
            element = element.replace(/\s/g, "");
            var lt = element.indexOf("{");
            var gt = element.indexOf("}");
            var value = element.split("{");
            if (!(lt === -1) && !(gt === -1)) {
                var numberstr = value[1].split("}")[0];
                if (element.indexOf("#") == 0) {
                    throw Error("id is unique to an element, class name or tag name expected.");
                }
                if (numberstr === "")
                    numberstr = "1";
                if (parseInt(numberstr) >= 1) {
                    return this.selectNodes(value[0])[parseInt(numberstr) - 1];
                }
                if (isNaN(numberstr))
                    throw new Error("element's index must be a number");
                throw new Error("element index must not be less than 1");
            }
            else if (!(lt === -1) || !(gt === -1)) {
                throw new Error("to style individual element '{ and '} is expected.");
            }
            return this.selectNode(element);
        };
        View.prototype.getNodeText = function (element) {
            return element.innerHTML;
        };
        View.prototype.setNodeText = function (element_selector, text) {
            var _node = this.selectNode(element_selector);
            if (_node !== null && Utility.ObjectTypes.isset(_node)) {
                _node.innerHTML = text;
            }
        };
        View.prototype.appendTextToNode = function (element_selector, text) {
            var nodeText = document.createTextNode(text);
            this.selectNode(element_selector).appendChild(nodeText);
        };
        View.prototype.on = function (element) {
            return function (event, func) {
                if (element.addEventListener) {
                    element.addEventListener(event, func);
                }
                else if (this.attachEvent) {
                    element.attachEvent("on".concat(event), func);
                }
            };
        };
        View.prototype.nodeHasStyle = function (style) {
            var flag = false;
            if (style !== undefined && style !== false && typeof style === 'boolean') {
                flag = true;
            }
            return flag;
        };
        View.prototype.newTag = function (name) {
            var doc = document.createElement(name);
            if (name === 'fragment') {
                doc = document.createDocumentFragment();
            }
            var self = this;
            return function (props) {
                var style = props.style;
                var hasStyle = self.nodeHasStyle(style);
                self.setProps(doc, props);
                return hasStyle ? ((function (css) { self.setStyle(doc, css); return doc; })) : doc;
            };
        };
        View.prototype.addAttributes = function (docElement) {
            return function (props) {
                var style = props.style;
                var self = this;
                var hasStyle = self.nodeHasStyle(style);
                this.setProps(docElement, props);
                return hasStyle ? ((function (css) { self.setStyle(docElement, css); })) : '';
            };
        };
        return View;
    }());
    UI.View = View;
})(UI || (UI = {}));
var Utility;
(function (Utility) {
    var ObjectTypes = /** @class */ (function () {
        function ObjectTypes() {
        }
        ObjectTypes.compare = function (inputOne, inputTwo) {
            var self = ObjectTypes;
            self.first = inputOne;
            self.second = inputTwo;
            if (self.isFunction(self)) {
                throw new Error("cannot compare functions");
            }
            return (self.isNull(self) || self.equalObjects(self) || self.checkStrictly(self));
        };
        ObjectTypes.input = function (msg) {
            return window.prompt(msg);
        };
        ObjectTypes.echo = function (message) {
            document.write(message);
            document.close();
        };
        ObjectTypes.cout = function (message) {
            console.log(message);
        };
        ObjectTypes.show = function (message) {
            window.alert(message);
        };
        ObjectTypes.isset = function ($var) {
            return ($var !== undefined);
        };
        ObjectTypes.issetNull = function ($var) {
            return ($var === null);
        };
        ObjectTypes.empty = function (inputOne) {
            var self = ObjectTypes;
            return (self.compare(inputOne, null) || self.compare(inputOne, '') ||
                self.compare(inputOne, {}) || self.compare(inputOne, []));
        };
        ObjectTypes.isNull = function (self) {
            if (self.first === null && self.second === null) {
                return (self.first === null && self.second === null);
            }
            return false;
        };
        ObjectTypes.isFunction = function (self) {
            if (typeof self.first === "function" && typeof self.second === "function") {
                return true;
            }
            return false;
        };
        ObjectTypes.equalObjects = function (self) {
            var flag = false;
            if (self.first === null || self.second === null)
                return self.first === self.second;
            if (typeof self.first === "object" && typeof self.second === "object") {
                var firstKeys = Object.keys(self.first);
                var secondKeys = Object.keys(self.second);
                /******************************************************
                * Checks the length of each objects if they are      **
                * the same set mainlength variable to the length of  **
                * one object, else if the length are not the same    **
                * return false.                                      **
                * ****************************************************/
                if (firstKeys.length !== secondKeys.length)
                    return false;
                else {
                    for (var index in firstKeys) {
                        var keyOfFirst = firstKeys[index];
                        var keyOfSecond = secondKeys[index];
                        if (keyOfFirst !== keyOfSecond)
                            return false;
                        if (self.first[keyOfFirst] !== self.second[keyOfSecond])
                            return false;
                    }
                }
                flag = true;
            }
            return flag;
        };
        ObjectTypes.checkStrictly = function (self) {
            return (self.first === self.second);
        };
        return ObjectTypes;
    }());
    Utility.ObjectTypes = ObjectTypes;
    var GroupEvent = /** @class */ (function (_super) {
        __extends(GroupEvent, _super);
        function GroupEvent(event_name) {
            var _this = _super.call(this) || this;
            _this.class_selector = "";
            _this.tag_selector = "";
            _this.name_selector = "";
            _this.event_name = event_name;
            return _this;
        }
        GroupEvent.prototype.setEventName = function (event_name) {
            this.event_name = event_name;
        };
        GroupEvent.prototype.setClassName = function (class_selector) {
            this.class_selector = class_selector;
        };
        GroupEvent.prototype.setTagName = function (tag_selector) {
            this.tag_selector = tag_selector;
        };
        GroupEvent.prototype.setTagByName = function (name_selector) {
            this.name_selector = name_selector;
        };
        GroupEvent.prototype.handle = function (callback) {
            var self = this;
            if (self.tag_selector.indexOf('#') === 0 || self.class_selector.indexOf('#') === 0)
                throw new Error('Selector Error: Expected a class name or a tag name');
            self.applyEvent(self, callback);
        };
        GroupEvent.prototype.applyEvent = function (self, callback) {
            var elements = self.tag_selector === "" ?
                self.getNodesByClass(self.class_selector) : self.getNodesByTag(self.tag_selector) || self.getNodesByName(self.tag_selector);
            self.setEvent(self, elements, callback);
        };
        GroupEvent.prototype.setEvent = function (self, elements, callback) {
            var isset = ObjectTypes.isset, empty = ObjectTypes.empty;
            var count = 0;
            if (isset(elements[0]) && !empty(elements)) {
                elements.forEach(function (element) {
                    (function (element, count) {
                        self.on(element)(self.event_name, function () { return callback({ element: element, position: count }); });
                    })(element, count);
                    ++count;
                });
            }
        };
        return GroupEvent;
    }(UI.View));
    Utility.GroupEvent = GroupEvent;
    var Convert = /** @class */ (function () {
        function Convert() {
        }
        Convert.toBinary = function (intValue) {
            var self = Convert;
            if (typeof intValue === 'number') {
                self.inputValue = parseInt(intValue);
            }
            else
                throw Error('Value Error: Expected parameter to be a type of number');
            self.setBinaries(self);
            return self.binaries.reverse().join('');
        };
        Convert.setBinaries = function (self) {
            self.flag = true;
            var index = self.inputValue;
            self.binaries.push(index % 2);
            while (self.flag) {
                index /= 2;
                var indexValue = parseInt(index);
                if (indexValue === 0) {
                    self.flag = false;
                    continue;
                }
                self.binaries.push(indexValue % 2);
            }
        };
        Convert.binaries = [];
        return Convert;
    }());
    Utility.Convert = Convert;
})(Utility || (Utility = {}));
var ObjectTypes = Utility.ObjectTypes;
var Validators;
(function (Validators) {
    var check = /** @class */ (function () {
        function check() {
        }
        check.email = function (email) {
            var atIndex = email.indexOf('@');
            var dotIndex = email.indexOf('.');
            var partOne = email.split('@');
            var standard_pattern = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            var partTwo = [];
            if (partOne.length === 2)
                partTwo = partOne[1].split('.');
            return check.checkEmail(partOne, partTwo, atIndex, dotIndex, standard_pattern, email);
        };
        check.checkEmail = function (partOne, partTwo, atIndex, dotIndex, standard_pattern, email) {
            return this.validateEmailParts(partOne, partTwo, atIndex, dotIndex) && standard_pattern.test(email);
        };
        check.validateEmailParts = function (partOne, partTwo, atIndex, dotIndex) {
            var flag = false;
            if (partTwo.length === 2) {
                flag = check.matchParts(partOne, partTwo, atIndex, dotIndex);
            }
            return flag;
        };
        check.matchParts = function (partOne, partTwo, atIndex, dotIndex) {
            var flag = false;
            if (atIndex >= 1 && atIndex < (dotIndex - 1) && dotIndex > (atIndex + 1)) {
                var pattern = /^[A-Za-z0-9\.\-]+$/;
                var firstPart = partOne[0];
                var secondPart = partTwo[0];
                var thirdPart = partTwo[1];
                flag = check.matchPatterns(pattern, firstPart, secondPart, thirdPart);
            }
            return flag;
        };
        check.matchPatterns = function (pattern, firstPart, secondPart, thirdPart) {
            return pattern.test(firstPart) && pattern.test(secondPart) && pattern.test(thirdPart);
        };
        check.alpha = function (alpha) {
            var flag = false;
            if (!check.empty(alpha)) {
                var pattern = /^[A-Za-z\s]+$/;
                if (pattern.test(alpha))
                    flag = true;
            }
            return flag;
        };
        check.alphaNumeric = function (alpha_numeric) {
            var flag = false;
            if (!check.empty(alpha_numeric)) {
                var pattern = /^[A-Za-z0-9\s]+$/;
                if (pattern.test(alpha_numeric))
                    flag = true;
            }
            return flag;
        };
        check.telNumber = function (phone_number) {
            var number_len = phone_number.length;
            var validValue = phone_number.replace('+', '0');
            var flag = false;
            if (/^[0-9]+$/.test(validValue)) {
                flag = check.validateTelNumber(parseInt(phone_number), number_len);
            }
            return flag;
        };
        check.validateTelNumber = function (phone_number, number_len) {
            return (check.numeric(phone_number) && (number_len >= 10 && number_len <= 15));
        };
        check.backData = function (method, request_object, callback) {
            method = method.toLowerCase();
            if (method === 'post')
                check.getDataByPost(request_object, callback);
            else if (method === 'get')
                check.getDataByGet(request_object, callback);
        };
        check.getDataByPost = function (request, callback) {
            if (typeof servo !== 'undefined')
                check.setPost(request, callback);
            else
                callback({ state: false, payload: null });
        };
        check.setPost = function (request, callback) {
            servo.post(request).catch(function (_a) {
                var error = _a.error;
                return error;
            })
                .then(function (_a) {
                var response = _a.response;
                check.getPostResponse(response, callback);
            });
        };
        check.getPostResponse = function (response, callback) {
            if (!check.empty(response))
                callback(response);
            else
                callback({ state: false, data: null });
        };
        check.getDataByGet = function (request, callback) {
            if (typeof servo !== 'undefined')
                check.setGet(request, callback);
            else
                callback({ state: false, data: null });
        };
        check.setGet = function (request, callback) {
            servo.get(request).catch(function (_a) {
                var error = _a.error;
                return error;
            })
                .then(function (response) {
                check.getGetResponse(response, callback);
            });
        };
        check.getGetResponse = function (response, callback) {
            if (check.empty(response))
                callback({ state: true, data: response });
            else
                callback({ state: false, data: null });
        };
        check.numeric = function (number) {
            return !isNaN(number);
        };
        check.empty = function (data) {
            return (check.checkStrings(data) ||
                check.checkObjectAndArray(data) ||
                check.checkAny(data));
        };
        check.checkObjectAndArray = function (data) {
            return check.checkObjects(data) || check.checkArrays(data);
        };
        check.checkStrings = function (data) {
            return ObjectTypes.compare(data, "");
        };
        check.checkObjects = function (data) {
            return ObjectTypes.compare(data, {});
        };
        check.checkArrays = function (data) {
            return ObjectTypes.compare(data, []);
        };
        check.checkAny = function (data) {
            return ObjectTypes.compare(data, undefined);
        };
        return check;
    }());
    Validators.check = check;
})(Validators || (Validators = {}));

const setUrl = (path) => {
    let url = path;
    const meta_url = document.querySelector('script[data-main]');
    if(meta_url !== undefined || meta_url !== null) {
        let url_array = meta_url.src.split('/');
        let new_url_array = [];
        for(let element of url_array) {
            if(element === 'statics' || element === 'app' || element === 'admin') {
                url = new_url_array.join('/');
                break;
            }
            new_url_array.push(element);
        }
        url = `${url}/${path}`;
    } else url = `${window.location.origin}/${url}`;
    return url;
};
//# sourceMappingURL=lib.js.map