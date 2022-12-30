var servo = /** @class */ (function () {
    function servo() {
        servo.$self.holdsdata = false;
        servo.xhttp.onreadystatechange = function () {
            if (servo.xhttp.readyState === XMLHttpRequest.DONE && servo.xhttp.status === 200 && !servo.$self.raised_error) {
                servo.$self.holdsdata = true;
                servo.$self.requestcompleted = true;
                servo.response = !this.isxml ? servo.xhttp.responseText : servo.xhttp.responseXML;
            }
            else if (servo.xhttp.readyState === 4 && (servo.xhttp.status === 0 || servo.xhttp.status === 404 || servo.$self.raised_error)) {
                servo.$self.requestcompleted = false;
            }
        };
    }
    servo.initVars = function () {
        servo.$self.holdsdata;
        servo.$self.interval;
        servo.$self.requestcompleted = null;
        servo.$self.filename = "";
        servo.$self.errortext = "";
        servo.$self.issync = false;
        servo.$self.isjson = false;
        servo.$self.isxml = false;
        servo.$self.fileupload = false;
        servo.$self.isstringified = false;
        servo.$self.raised_error = false;
        servo.$self.untilgetData = null;
        servo.$self.progressCount = 1;
        servo.$self.jsonParse = false;
    };
    servo.runservo = function (_a) {
        var method = _a.method, url = _a.url, async = _a.async, json = _a.json, fileUpload = _a.fileUpload;
        async = !async ? false : async;
        json = !json ? false : (function () { return servo.$self.isjson = true; })();
        this.$self.filename = (url.split('/')[url.split('/').length - 1]).split('?')[0];
        fileUpload = !fileUpload ? false : (function () { return servo.$self.fileupload = fileUpload; })();
        if (method === "POST" && !fileUpload && !json) {
            servo.xhttp.open(method, url, async);
            servo.xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        }
        else if ((method === "POST" || method === "GET") && json && !fileUpload) {
            servo.xhttp.open(method, url, async);
            servo.xhttp.setRequestHeader("Content-type", "application/json", async);
        }
        else if ((method === "POST" || method === "GET") && fileUpload) {
            servo.xhttp.open(method, url, async);
            servo.xhttp.setRequestHeader("Content-type", "multipart/form-data");
        }
        else {
            servo.xhttp.open(method, url, async);
        }
    };
    Object.defineProperty(servo, "setRequestDetail", {
        set: function (req) {
            servo.initVars();
            this.$self.isxml = !req.xml ? false : req.xml;
            servo.$self.jsonParse = !req.jsonParse ? false : req.jsonParse;
            servo.xhttp = new XMLHttpRequest();
            switch (req.async) {
                case true:
                    new servo();
                    this.runservo(req);
                    break;
                default:
                    this.$self.issync = true;
                    this.runservo(req);
                    break;
            }
        },
        enumerable: true,
        configurable: true
    });
    servo.sendRequest = function (data) {
        if (data === void 0) { data = ""; }
        data != "" ? servo.xhttp.send(data) : servo.xhttp.send();
    };
    Object.defineProperty(servo, "getResponse", {
        get: function () {
            if (servo.$self.isjson || servo.$self.jsonParse) {
                return JSON.parse(servo.response);
            }
            return servo.response;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(servo, "getErrorMsg", {
        get: function () {
            return servo.$self.errortext;
        },
        enumerable: true,
        configurable: true
    });
    Object.defineProperty(servo, "hasData", {
        get: function () {
            return servo.$self.holdsdata;
        },
        enumerable: true,
        configurable: true
    });
    servo.requestCompleted = function (onprogress) {
        if (onprogress === void 0) { onprogress = undefined; }
        var self = this;
        servo.$self.errortext = servo.$self.errortext !== "" ? (function () {
            servo.$self.raised_error = true;
            return servo.$self.errortext;
        })() : servo.$self.errortext;
        return new /** @class */ (function () {
            function class_1() {
            }
            class_1.prototype.then = function (func) {
                if (onprogress !== undefined &&
                    servo.$self.progressCount === 1 &&
                    !servo.$self.issync) {
                    onprogress();
                    ++servo.$self.progressCount;
                }
                var untilprogressdstops = setInterval(function () {
                    if (servo.$self.requestcompleted === false) {
                        clearInterval(untilprogressdstops);
                    }
                    if (servo.$self.requestcompleted) {
                        servo.synchronize(func);
                        clearInterval(untilprogressdstops);
                    }
                }, servo.$self.interval);
                return servo.requestCompleted();
            };
            class_1.prototype.catch = function (func) {
                var waitUntilRequestFail = setInterval(function () {
                    if (servo.$self.requestcompleted) {
                        clearInterval(waitUntilRequestFail);
                    }
                    else if (servo.$self.requestcompleted === false) {
                        clearInterval(servo.$self.untilgetData);
                        servo.$self.progressCount = 0;
                        var returnvalue = servo.$self.errortext !== "" && servo.$self.errortext !== "NetworkError: A network error occurred."
                            ? func({ error: servo.$self.errortext })
                            : (function () {
                                servo.$self.errortext = "NetworkError: A network error occured:";
                                servo.$self.errortext = servo.$self.errortext.toString().concat(" unable to locate file: '" + servo.$self.filename + "'");
                                return func({ error: servo.$self.errortext });
                            })();
                        if (returnvalue !== undefined) {
                            clearInterval(waitUntilRequestFail);
                            throw new Error(returnvalue.toString());
                        }
                        clearInterval(waitUntilRequestFail);
                        if (servo.$self.errortext === "") {
                            throw new Error(servo.$self.errortext);
                        }
                        else {
                            throw new Error(servo.$self.errortext);
                        }
                    }
                }, self.$self.interval);
                return servo.requestCompleted();
            };
            return class_1;
        }());
    };
    servo.runscript = function (ctrl) {
        var _this = this;
        if (!this.$self.issync && ctrl.module !== undefined) {
            this.$self.interval = !ctrl.interval ? 12 : ctrl.interval;
            servo.$self.untilgetData = setInterval(function () {
                if (servo.$self.holdsdata) {
                    ctrl.module({ response: servo.getResponse });
                    clearInterval(servo.$self.untilgetData);
                }
                else if (ctrl.moduleprogress !== undefined) {
                    ctrl.moduleprogress();
                }
            }, this.$self.interval);
        }
        else {
            if (servo.$self.fileupload) {
                this.getsyncModeData(servo.$self.fileupload);
                if (servo.$self.raised_error) {
                    servo.$self.requestcompleted = false;
                    return this.requestCompleted();
                }
                this.xhttp.upload.addEventListener("progress", function () {
                    if (ctrl.moduleprogress !== undefined)
                        ctrl.moduleprogress();
                }, this.$self.issync);
                this.xhttp.addEventListener("load", function (event) {
                    this.xhttp.responseText = event.target.responseText;
                    ctrl.module({ response: servo.getResponse });
                }, this.$self.issync);
                this.xhttp.addEventListener("error", function () {
                    var error = "Upload Failed";
                    try {
                        if (ctrl.moduleprogress !== undefined)
                            ctrl.moduleprogress();
                        throw new Error(error);
                    }
                    catch (error) {
                        servo.$self.raised_error = true;
                        servo.$self.errortext = error;
                    }
                    finally {
                        if (servo.$self.raised_error) {
                            return this.requestCompleted();
                        }
                    }
                }, this.$self.issync);
                this.xhttp.addEventListener("abort", function () {
                    var error = "Upload Aborted";
                    try {
                        if (ctrl.moduleprogress !== undefined)
                            ctrl.moduleprogress();
                        throw new Error(error);
                    }
                    catch (error) {
                        servo.$self.raised_error = true;
                        servo.$self.errortext = error;
                    }
                    finally {
                        if (servo.$self.raised_error) {
                            return this.requestCompleted();
                        }
                    }
                }, this.$self.issync);
            }
            else {
                if (servo.$self.issync && ctrl.moduleprogress !== undefined) {
                    ctrl.moduleprogress();
                    var delays_1 = setTimeout(function () {
                        _this.getsyncModeData();
                        if (servo.$self.raised_error) {
                            servo.$self.requestcompleted = false;
                            return _this.requestCompleted();
                        }
                        if (ctrl.module !== undefined) {
                            ctrl.module({ response: servo.getResponse });
                        }
                        clearTimeout(delays_1);
                    }, this.$self.interval);
                }
                else {
                    this.getsyncModeData();
                    if (servo.$self.raised_error) {
                        servo.$self.requestcompleted = false;
                        return this.requestCompleted();
                    }
                    if (ctrl.module !== undefined)
                        ctrl.module({ response: servo.getResponse });
                }
            }
        }
    };
    servo.post = function (_a) {
        var url = _a.url, async = _a.async, response = _a.response, onprogress = _a.onprogress, json = _a.json, jsonstringify = _a.jsonstringify, xml = _a.xml, fileUpload = _a.fileUpload, send = _a.send, interval = _a.interval, jsonParse = _a.jsonParse;
        jsonstringify = !jsonstringify ? false : servo.$self.isstringified = jsonstringify;
        servo.$self.progressload = onprogress;
        var queryData = (send !== undefined && send !== null) ? "" + servo.submit(send) : "";
        this.setRequestDetail = { method: 'POST', url: url, async: async, json: json, xml: xml, fileUpload: fileUpload, jsonParse: jsonParse };
        try {
            this.sendRequest(queryData);
            if (servo.xhttp.status === 404) {
                servo.$self.requestcompleted = false;
                return servo.requestCompleted();
            }
        }
        catch (error) {
            servo.$self.requestcompleted = false;
            servo.$self.errortext = error;
            return this.requestCompleted();
        }
        finally {
            if (servo.$self.errortext !== "") {
                console.error(servo.$self.errortext);
            }
        }
        this.runscript({
            module: response,
            interval: interval,
            moduleprogress: onprogress
        });
        return this.requestCompleted(servo.$self.progressload);
    };
    servo.get = function (_a) {
        var url = _a.url, async = _a.async, response = _a.response, onprogress = _a.onprogress, json = _a.json, jsonstringify = _a.jsonstringify, xml = _a.xml, fileUpload = _a.fileUpload, send = _a.send, interval = _a.interval, jsonParse = _a.jsonParse;
        jsonstringify = !jsonstringify ? false : servo.$self.isstringified = jsonstringify;
        servo.$self.progressload = onprogress;
        if (onprogress !== undefined && async === true) {
            servo.$self.onprogress = onprogress;
        }
        var queryData = (send !== undefined && send !== null) ? "?" + servo.submit(send) : "";
        this.setRequestDetail = { method: 'GET', url: "" + url + queryData, async: async, json: json, xml: xml, fileUpload: fileUpload, jsonParse: jsonParse };
        try {
            this.sendRequest();
            if (servo.xhttp.status === 404) {
                servo.$self.requestcompleted = false;
                return servo.requestCompleted();
            }
        }
        catch (error) {
            servo.$self.requestcompleted = false;
            servo.$self.holdsdata = false;
            servo.$self.errortext = error;
            return servo.requestCompleted();
        }
        finally {
            if (servo.$self.errortext !== "") {
                console.error(servo.$self.errortext);
            }
        }
        this.runscript({
            module: response,
            interval: interval,
            moduleprogress: onprogress
        });
        return this.requestCompleted(servo.$self.progressload);
    };
    Object.defineProperty(servo, "awaits", {
        set: function (func) {
            servo.requestCompleted(servo.$self.progressload)
                .then(function (_a) {
                    var response = _a.response;
                    return func({ response: response }, null);
                })
                .catch(function (error) { return func(null, { error: servo.getErrorMsg }); });
        },
        enumerable: true,
        configurable: true
    });
    servo.submit = function (data) {
        var result = data.reduce(function (result, objdata) {
            var keys = Object.getOwnPropertyNames(objdata);
            keys.forEach(function (key) {
                result += (key.toString()).concat("=" + (function () {
                    if (servo.$self.isstringified)
                        return JSON.stringify(objdata[key]);
                    return objdata[key];
                })() + "&");
            });
            return result;
        }, "");
        //if(servo.errortext != "")   return this.requestCompleted();
        return result = result.substring(0, result.length - 1);
    };
    servo.synchronize = function (module) {
        if (!this.$self.issync) {
            var waitUtilResponse_1 = setInterval(function () {
                try {
                    if (servo.$self.requestcompleted === false) {
                        clearInterval(waitUtilResponse_1);
                        servo.$self.progressCount = 0;
                        throw new Error(servo.$self.errortext);
                    }
                    if (servo.$self.holdsdata && servo.$self.requestcompleted) {
                        servo.$self.progressCount = 0;
                        module({ response: servo.getResponse });
                        clearInterval(waitUtilResponse_1);
                    }
                }
                catch (error) {
                    module({ response: servo.getErrorMsg });
                }
            }, this.$self.interval);
        }
        else {
            module({ response: servo.getResponse });
        }
    };
    servo.getsyncModeData = function (filemode) {
        if (filemode === void 0) { filemode = false; }
        servo.$self.errortext = servo.$self.errortext !== "" ? (function () {
            servo.$self.raised_error = true;
            return servo.$self.errortext;
        })() : servo.$self.errortext;
        if (this.$self.issync && !filemode) {
            servo.$self.holdsdata = true;
            servo.$self.requestcompleted = true;
            servo.response = !this.$self.isxml ? servo.xhttp.responseText : servo.xhttp.responseXML;
        }
        else if (filemode && this.$self.issync) {
            servo.$self.holdsdata = true;
            servo.$self.requestcompleted = true;
        }
    };
    servo.$self = Object.create({});
    return servo;
}());