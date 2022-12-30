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
var __generator = (this && this.__generator) || function (thisArg, body) {
    var _ = { label: 0, sent: function() { if (t[0] & 1) throw t[1]; return t[1]; }, trys: [], ops: [] }, f, y, t, g;
    return g = { next: verb(0), "throw": verb(1), "return": verb(2) }, typeof Symbol === "function" && (g[Symbol.iterator] = function() { return this; }), g;
    function verb(n) { return function (v) { return step([n, v]); }; }
    function step(op) {
        if (f) throw new TypeError("Generator is already executing.");
        while (_) try {
            if (f = 1, y && (t = y[op[0] & 2 ? "return" : op[0] ? "throw" : "next"]) && !(t = t.call(y, op[1])).done) return t;
            if (y = 0, t) op = [0, t.value];
            switch (op[0]) {
                case 0: case 1: t = op; break;
                case 4: _.label++; return { value: op[1], done: false };
                case 5: _.label++; y = op[1]; op = [0]; continue;
                case 7: op = _.ops.pop(); _.trys.pop(); continue;
                default:
                    if (!(t = _.trys, t = t.length > 0 && t[t.length - 1]) && (op[0] === 6 || op[0] === 2)) { _ = 0; continue; }
                    if (op[0] === 3 && (!t || (op[1] > t[0] && op[1] < t[3]))) { _.label = op[1]; break; }
                    if (op[0] === 6 && _.label < t[1]) { _.label = t[1]; t = op; break; }
                    if (t && _.label < t[2]) { _.label = t[2]; _.ops.push(op); break; }
                    if (t[2]) _.ops.pop();
                    _.trys.pop(); continue;
            }
            op = body.call(thisArg, _);
        } catch (e) { op = [6, e]; y = 0; } finally { f = t = 0; }
        if (op[0] & 5) throw op[1]; return { value: op[0] ? op[1] : void 0, done: true };
    }
};
function ObjectIterators() {
    var CLoops = /** @class */ (function () {
        function CLoops() {
            this.funcIsgenerator = true;
            this.iterableObject = {};
            this.skip = 'atnu3##427$5ABSZiejjqlweDUISBAVJELityuokhg;@cbhihibd';
            this.end = '@105979QYWT1OS##$SPAjWOD4yeugludvu9JELhdirhd9ourbucv';
            this.track = 0;
        }
        return CLoops;
    }());
    var MapEach = /** @class */ (function (_super) {
        __extends(MapEach, _super);
        function MapEach() {
            return _super.call(this) || this;
        }
        MapEach.prototype.__removeBreaks = function (objectReference) {
            objectReference['skip'] = null;
            objectReference['end'] = null;
            delete objectReference['skip'];
            delete objectReference['end'];
            return objectReference;
        };
        MapEach.prototype.__isGenerator = function (func) {
            var funcString = func.toString().concat("");
            var funcStringArray = funcString.split('__generator');
            if ((funcString.indexOf("*") !== -1) &&
                funcString.indexOf("*") < funcString.indexOf("(") ||
                funcStringArray.length === 2) {
                return true;
            }
            return false;
        };
        MapEach.prototype.__mapCallbackValue = function (arr, value, flag) {
            if (flag) {
                arr.push(value);
            }
            else {
                arr = null;
            }
            return arr;
        };
        MapEach.prototype.__hasNext = function (value) {
            try {
                value.next().value;
                value.next().done;
                return true;
            }
            catch (err) {
                return false;
            }
        };
        MapEach.prototype.__returnsIsValid = function (value, done, skip, end) {
            if (done === void 0) { done = true; }
            try {
                var invalidyield = false;
                if (value !== undefined && done === false) {
                    return this.__checkGeneratorOne(value, skip, end, invalidyield);
                }
                else if (value === undefined) {
                    throw new Error("mapEach() function callback must have an ending return value");
                }
                else {
                    return this.__checkGeneratorTwo(value, done, skip, end, invalidyield);
                }
            }
            catch (err) {
                console.error(err);
            }
        };
        MapEach.prototype.__checkGeneratorOne = function (value, skip, end, invalidyield) {
            try {
                if ((this.__hasNext(value)
                    || value.toString().replace(/\s/g, "") === ""
                    || (value.toString()).indexOf("function") !== -1)) {
                    invalidyield = true;
                    throw new Error("mapEach() callback generator expects to yield 'skip' or 'end' but not return either");
                }
                else if (!(value !== skip ^ value !== end)) {
                    invalidyield = true;
                    throw new Error("mapEach() callback generator expects to yield 'skip' or 'end'");
                }
            }
            catch (err) {
                if ((err.toString()).indexOf("value.next") === -1) {
                    console.error(err);
                    invalidyield = true;
                }
            }
            finally {
                if (!invalidyield) {
                    return true;
                }
                else {
                    return false;
                }
            }
        };
        MapEach.prototype.__checkGeneratorTwo = function (value, done, skip, end, invalidyield) {
            try {
                if (done === true && (value === skip || value === end)) {
                    invalidyield = true;
                    throw new Error("mapEach() callback generator expects to yield 'skip' or 'end' but not return either");
                }
                value.next().value;
                invalidyield = true;
                this.track = 1;
                throw new Error(null);
            }
            catch (err) {
                if ((err.toString()).indexOf("value.next") === -1
                    && (err.toString()).indexOf("null") === -1) {
                    console.error(err);
                    invalidyield = true;
                }
            }
            finally {
                if (!invalidyield) {
                    return true;
                }
                else {
                    this.track = 1;
                    return false;
                }
            }
        };
        MapEach.prototype.__callbackhasReturn = function (boolarr) {
            var hasReturn = boolarr[0];
            try {
                if (!hasReturn) {
                    throw new Error("expected a return value from mapEach() callback");
                }
                else {
                    return true;
                }
            }
            catch (err) {
                console.error(err);
                return false;
            }
        };
        MapEach.prototype.iterate = function (_object, callback) {
            this.iterableObject = _object;
            try {
                if (typeof this.iterableObject.valueOf() === 'number') {
                    throw new Error("iteration error");
                }
                this.__setHashes();
                return this.__getValues(callback);
            }
            catch (error) {
                this.funcIsgenerator = false;
            }
            finally {
                if (!this.funcIsgenerator) {
                    try {
                        var mapvalues = [];
                        var isvalidReturns = true;
                        var newvalue = null;
                        for (var _i = 0, _a = this.iterableObject; _i < _a.length; _i++) {
                            var objectsObject = _a[_i];
                            objectsObject = this.__removeBreaks(objectsObject);
                            isvalidReturns = this.__returnsIsValid(callback(objectsObject));
                            newvalue = callback(objectsObject);
                            this.__mapCallbackValue(mapvalues, newvalue, isvalidReturns);
                        }
                        return mapvalues;
                    }
                    catch (error) {
                        console.error(error);
                    }
                }
            }
        };
        MapEach.prototype.__setHashes = function () {
            var objectProps = Object.keys(this.iterableObject);
            var self = this;
            this.iterableObject[Symbol.iterator] = function () {
                var _i, objectProps_1, objectkey, skipnumhash, selectedSkipValue, endnumhash, selectedEndValue;
                return __generator(this, function (_a) {
                    switch (_a.label) {
                        case 0:
                            _i = 0, objectProps_1 = objectProps;
                            _a.label = 1;
                        case 1:
                            if (!(_i < objectProps_1.length)) return [3 /*break*/, 4];
                            objectkey = objectProps_1[_i];
                            skipnumhash = (Math.random() + (Math.random() % Infinity)).toString();
                            selectedSkipValue = self.skip[parseInt(((Math.random() * 100)) % 52)];
                            self.skip = self.skip.replace(selectedSkipValue, selectedSkipValue.concat("" + skipnumhash.replace('.', selectedSkipValue)));
                            endnumhash = (Math.random() + (Math.random() % Infinity)).toString();
                            selectedEndValue = self.end[parseInt(((Math.random() * 100)) % 52)];
                            self.end = self.end.replace(selectedEndValue, selectedEndValue.concat("" + endnumhash.replace('.', selectedEndValue)));
                            return [4 /*yield*/, {
                                    key: objectkey,
                                    value: self.iterableObject[objectkey],
                                    length: objectProps.length,
                                    skip: self.skip,
                                    end: self.end
                                }];
                        case 2:
                            _a.sent();
                            _a.label = 3;
                        case 3:
                            _i++;
                            return [3 /*break*/, 1];
                        case 4: return [2 /*return*/];
                    }
                });
            };
        };
        MapEach.prototype.__getValues = function (callback) {
            if (this.__isGenerator(callback)) {
                var arr = [];
                var newValue = null;
                var isValidReturns = true;
                var getReturnFlags = [];
                for (var _i = 0, _a = this.iterableObject; _i < _a.length; _i++) {
                    var objectsObject = _a[_i];
                    var _b = callback(objectsObject).next(), value = _b.value, done = _b.done;
                    isValidReturns = this.__returnsIsValid(value, done, this.skip, this.end);
                    if (value === this.end) {
                        objectsObject = this.__removeBreaks(objectsObject);
                        break;
                    }
                    else if (value === this.skip) {
                        objectsObject = this.__removeBreaks(objectsObject);
                        continue;
                    }
                    objectsObject = this.__removeBreaks(objectsObject);
                    newValue = callback(objectsObject).next().value;
                    arr = this.__mapCallbackValue(arr, newValue, (isValidReturns));
                    getReturnFlags.push(done);
                }
                if (isValidReturns === false || this.track === 1) {
                    arr = null;
                }
                return this.__callbackhasReturn(getReturnFlags)
                    ? arr : [];
            }
            else {
                throw new Error(null);
            }
        };
        return MapEach;
    }(CLoops));
    /**
     *
     * @class ForOf
     */
    var ForOf = /** @class */ (function (_super) {
        __extends(ForOf, _super);
        function ForOf() {
            return _super.call(this) || this;
        }
        ForOf.prototype.__removeBreaks = function (objectReference) {
            objectReference['skip'] = null;
            objectReference['end'] = null;
            delete objectReference['skip'];
            delete objectReference['end'];
            return objectReference;
        };
        ForOf.prototype.__isGenerator = function (func) {
            var funcString = func.toString().concat("");
            var funcStringArray = funcString.split('__generator');
            if ((funcString.indexOf("*") !== -1) &&
                funcString.indexOf("*") < funcString.indexOf("(") ||
                funcStringArray.length === 2) {
                return true;
            }
            return false;
        };
        ForOf.prototype.iterate = function (_object, callback) {
            this.iterableObject = _object;
            try {
                if (typeof this.iterableObject.valueOf() === 'number') {
                    throw new Error("iteration error");
                }
                this.__setHashes();
                this.__getValues(callback);
            }
            catch (error) {
                this.funcIsgenerator = false;
            }
            finally {
                try {
                    if (!this.funcIsgenerator) {
                        for (var _i = 0, _a = this.iterableObject; _i < _a.length; _i++) {
                            var objectsObject = _a[_i];
                            objectsObject = this.__removeBreaks(objectsObject);
                            callback(objectsObject);
                        }
                    }
                }
                catch (error) {
                    console.error(error);
                }
            }
        };
        ForOf.prototype.__setHashes = function () {
            var objectProps = Object.keys(this.iterableObject);
            var self = this;
            this.iterableObject[Symbol.iterator] = function () {
                var _i, objectProps_2, objectkey, skipnumhash, selectedSkipValue, endnumhash, selectedEndValue;
                return __generator(this, function (_a) {
                    switch (_a.label) {
                        case 0:
                            _i = 0, objectProps_2 = objectProps;
                            _a.label = 1;
                        case 1:
                            if (!(_i < objectProps_2.length)) return [3 /*break*/, 4];
                            objectkey = objectProps_2[_i];
                            skipnumhash = (Math.random() + (Math.random() % Infinity)).toString();
                            selectedSkipValue = self.skip[parseInt(((Math.random() * 100)) % 52)];
                            self.skip = self.skip.replace(selectedSkipValue, selectedSkipValue.concat("" + skipnumhash.replace('.', selectedSkipValue)));
                            endnumhash = (Math.random() + (Math.random() % Infinity)).toString();
                            selectedEndValue = self.end[parseInt(((Math.random() * 100)) % 52)];
                            self.end = self.end.replace(selectedEndValue, selectedEndValue.concat("" + endnumhash.replace('.', selectedEndValue)));
                            return [4 /*yield*/, {
                                    key: objectkey,
                                    value: self.iterableObject[objectkey],
                                    length: objectProps.length,
                                    skip: self.skip,
                                    end: self.end
                                }];
                        case 2:
                            _a.sent();
                            _a.label = 3;
                        case 3:
                            _i++;
                            return [3 /*break*/, 1];
                        case 4: return [2 /*return*/];
                    }
                });
            };
        };
        ForOf.prototype.__getValues = function (callback) {
            if (this.__isGenerator(callback)) {
                for (var _i = 0, _a = this.iterableObject; _i < _a.length; _i++) {
                    var objectsObject = _a[_i];
                    var value = callback(objectsObject).next().value;
                    if (value === this.end) {
                        objectsObject = this.__removeBreaks(objectsObject);
                        break;
                    }
                    else if (value === this.skip) {
                        objectsObject = this.__removeBreaks(objectsObject);
                        continue;
                    }
                    objectsObject = this.__removeBreaks(objectsObject);
                }
            }
            else {
                throw new Error(null);
            }
        };
        return ForOf;
    }(CLoops));
    return {
        forIterator: new ForOf(),
        mapIterator: new MapEach()
    };
}