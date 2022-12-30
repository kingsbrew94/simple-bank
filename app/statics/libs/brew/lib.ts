"use strict";
namespace UI {
    export class Redirect {
        static to(url: string) {
            window.location.assign(url);
        }
        static toOpen(url: string) {
            window.open(url);
        }
    }
    export class Spark {
        static onrequest(globalObject: any) {
            return (callback: Function) => {
                const objectString = globalObject.toString();
                switch(objectString) {
                    case "[object Window]":
                        globalObject.onload = callback;
                    break;
                    case "[object HTMLDocument]":
                        if( globalObject.readyState === 'loading') {
                            callback();
                        }
                    break;
                    default:
                       try {
                           throw new Error("Arguement Type Error: Excepted a global object like 'window' or 'document'");
                       } catch(error) {
                           console.log(error);
                       }
                    break;
                }
            };
        }
    }
    export class View {
        public ON: boolean;
        public OFF: boolean;

        constructor() {
            this.ON  = true;
            this.OFF = false;
        }

        private setStyle(docElement: any, css: any) {
            const self = docElement;

            if(css === undefined) return;
            const getKeys = Object.getOwnPropertyNames(css);
            if(self.length > 1) {
                self.forEach(element => {
                    getKeys.forEach(csskey => {
                        element.style[csskey] = css[csskey];
                    });
                });
            } else {
                getKeys.forEach(csskey => {
                    self.style[csskey] = css[csskey];
                });
            }
        }

        private setProps(docElement: any, attributes: any) {
            if (attributes === undefined) throw new Error('Object property is null');
            let attributesKeys = Object.getOwnPropertyNames(attributes);

            if(docElement.length > 1 && !(typeof docElement === 'string')) {
                docElement.forEach(element => {
                    for (let property of attributesKeys) {
                        let key = property;
                        switch(property) {
                            case 'style':
                                    if(attributes[property] === true) {
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
                                   for (element of attributes[property]) {
                                   docElement.appendChild(attributes[property]);
                                   }
                                   continue;
                            case 'child':
                                 docElement.appendChild(attributes[property]);
                            continue;
                        }
                        docElement.setAttribute(property.toString(),attributes[property]);
                    }
                });
            } else {
                if(typeof docElement === 'object') {
                    for (let attr of attributesKeys) {
                        let key = attr;
                        switch(attr) {
                            case 'style':
                                    if(attributes[attr] === true) {
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
                                  for (let element of attributes[attr]) {
                                   docElement.appendChild(element);
                                  }
                                  continue;
                            case 'child':
                                  docElement.appendChild(attributes[key]);
                                  continue;
                        }
                        docElement.setAttribute(attr.toString(),attributes[attr]);
                    }
                }
            }
        }

        private selectNode(selector: string): any {
            
            const element = (document.querySelector(selector) !== undefined) || (document.querySelector(selector) !== null)
                            ? document.querySelector(selector) : null;
            return element;
        }

        private selectNodes(selector: string) {
            const element = (document.querySelectorAll(selector) !== undefined) || (document.querySelector(selector) !== null)
                            ? document.querySelectorAll(selector) : null;
            return element;
        }

        private selectNodesByNames(selector:string): any {
            const element = (document.getElementsByName(selector) !== undefined) || (document.getElementsByName(selector) !== null)
            ? document.getElementsByName(selector) : null;
            return element;
        }

        public getNodeById(idName: string): HTMLElement {
            return this.selectNode(`#${idName}`);
        }

        public getNodesByName(name_selector: string): HTMLElement {
            return this.selectNodesByNames(name_selector);
        }

        public getNodeValueById(idName: string): any {
            const node  = this.selectNode(`#${idName}`);
            const value = node !== null ? node.value : null;
            return value;
        }

        public getNodesByClass(className: string): any {
            return this.selectNodes(`.${className}`);
        }

        public getNodesByTag(tagName: string): any {
            return this.selectNodes(tagName);
        }

        public int(data: any) : number {
            return parseInt(data);
        }

        public str(data: any) : string {
            return data.toString();
        }

        public float(data: any) : number {
            return parseFloat(data);
        }

        public json(data: any): any {
            return JSON.parse(data);
        }

        public len(data: any): number {
            return data.length;
        }

        public getHTMNode(element: string) {
            element = element.replace(/\s/g,"");
            const lt = element.indexOf("{");
            const gt = element.indexOf("}");
            let value = element.split("{");
            if(!(lt === -1) && !(gt === -1)) {
                let numberstr = value[1].split("}")[0];
                if(element.indexOf("#") == 0) {
                    throw Error("id is unique to an element, class name or tag name expected.");
                }
                if (numberstr === "") numberstr = "1";
                if(parseInt(numberstr) >= 1) {
                return this.selectNodes(value[0])[parseInt(numberstr)-1];
                }
                if(isNaN(numberstr)) throw new Error("element's index must be a number");
                throw new Error("element index must not be less than 1");
            } else if(!(lt === -1) || !(gt === -1)) {
                throw new Error("to style individual element '{ and '} is expected.");
            }
            return this.selectNode(element);
        }
        public getNodeText(element: HTMLElement): any {
            return element.innerHTML;
        }

        public setNodeText(element_selector: string, text: string) {
           const _node = this.selectNode(element_selector);
           if(_node !== null && Utility.ObjectTypes.isset(_node)) {
               _node.innerHTML = text;
           } 
        }

        public appendTextToNode(element_selector: string, text: string) {
            const nodeText = document.createTextNode(text);
            this.selectNode(element_selector).appendChild(nodeText);
        }
        public on(element: any) {
            return function(event,func) {
                if(element.addEventListener) {
                    element.addEventListener(event,func);
                } else if(this.attachEvent) {
                    element.attachEvent("on".concat(event),func);
                }
            }
        }

        private nodeHasStyle(style: any): boolean {
            let flag = false;

            if(style !== undefined && style !== false && typeof style === 'boolean') {
                flag = true;
            }
            return flag;
        }
        public newTag(name: string) {
            let doc:any = document.createElement(name);

            if(name === 'fragment') {
                doc = document.createDocumentFragment();
            }
            const self = this;

            return (props: any) => {
                const { style } = props;
                let hasStyle = self.nodeHasStyle(style);
                self.setProps(doc,props);

            return hasStyle ? (((css:any) => { self.setStyle(doc,css); return doc; })): doc;
            }
        }

        public addAttributes(docElement: HTMLElement) {
            return function(props: any){
                const { style } = props;
                const self = this;
                let hasStyle = self.nodeHasStyle(style);
                this.setProps(docElement,props);
            return hasStyle ? (((css: any) => { self.setStyle(docElement,css)})): '';
            }
        }
    }
}

namespace Utility {
    export class ObjectTypes {

        protected static first: any;

        protected static second: any;

        public static compare(inputOne: any, inputTwo :any) {
            const self = ObjectTypes;
            self.first  = inputOne;
            self.second = inputTwo;
    
            if(self.isFunction(self)) {
                throw new Error("cannot compare functions");
            }

            return (
                self.isNull(self) || self.equalObjects(self) || self.checkStrictly(self)
            );
        }

        public static input(msg: string) {
            return window.prompt(msg);
        }

        public static echo(message: any) {
            document.write(message);
            document.close();
        }

        public static cout(message) {
            console.log(message);
        }

        public static show(message: any) {
            window.alert(message);
        }

        public static isset($var: any) {
            return ($var !== undefined);
        }

        public static issetNull($var: any) {
            return ($var === null);
        }

        public static empty(inputOne: any) {
            const self = ObjectTypes;

            return (
                   self.compare(inputOne,null) || self.compare(inputOne,'') ||
                   self.compare(inputOne,{})   || self.compare(inputOne,[])
                );
        }

        private static isNull(self: any): boolean {
            if(self.first === null && self.second === null) {
                return (self.first === null && self.second === null);
            } 
            return false;
        }

        private static isFunction(self: any) {
            if(typeof self.first === "function" && typeof self.second === "function") {
                return true;
            } 
            return false;
        }

        private static equalObjects(self: any): boolean {
            let flag: boolean = false;
    
            if(self.first === null || self.second === null) return self.first === self.second;
            
            if(typeof self.first === "object" && typeof self.second === "object") {
               
                const firstKeys  = Object.keys(self.first);
                const secondKeys = Object.keys(self.second);

                /******************************************************
                * Checks the length of each objects if they are      **   
                * the same set mainlength variable to the length of  ** 
                * one object, else if the length are not the same    **
                * return false.                                      **
                * ****************************************************/

                if(firstKeys.length !== secondKeys.length) return false;
                
                /**********************************************************
                * checks and compares the keys and values of the objects ** 
                * if they match return true else if they are not the     **
                * same return false.                                     **
                **********************************************************/
                else {
                    for(let index in firstKeys) {
                        let keyOfFirst  = firstKeys[index];
                        let keyOfSecond = secondKeys[index];

                        if(keyOfFirst !== keyOfSecond) return false;

                        if(self.first[keyOfFirst] !== self.second[keyOfSecond]) return false;
                    }
                }
                flag = true;
            }
            return flag;
        }

        private static checkStrictly(self: any) {
            return (self.first === self.second);
        }
    }

    export class GroupEvent extends UI.View {
        private class_selector: string = "";

        private tag_selector: string = "";

        private name_selector: string = "";

        private event_name: string;

        public constructor(event_name: string) {
            super();
            this.event_name = event_name;
        }

        public setEventName(event_name: string) {
            this.event_name = event_name;
        }

        public setClassName(class_selector: string) {
            this.class_selector = class_selector;
        }

        public setTagName(tag_selector: string) {
            this.tag_selector = tag_selector;
        }

        public setTagByName(name_selector: string) {
            this.name_selector = name_selector;
        }

        public handle(callback: Function) {
            const self = this;
            if(self.tag_selector.indexOf('#') === 0 || self.class_selector.indexOf('#') === 0) 
                throw new Error('Selector Error: Expected a class name or a tag name');
            self.applyEvent(self, callback)
        }

        private applyEvent(self: GroupEvent, callback: Function) {
            const elements = self.tag_selector === "" ? 
                             self.getNodesByClass(self.class_selector) : self.getNodesByTag(self.tag_selector) || self.getNodesByName(self.tag_selector);
            self.setEvent(self,elements, callback);
        }

        private setEvent(self: GroupEvent,elements: any, callback: Function) {
            const { isset, empty } = ObjectTypes;
            let count = 0;
            if(isset(elements[0]) && !empty(elements)) {
                elements.forEach((element: any) => {
                    (function(element, count){
                        self.on(element)(self.event_name,() => callback({element, position: count}))
                    })(element, count);
                    ++count;
                });
            }
        }
    }
    export class Convert {

        private static inputValue: number;

        private static binaries: Array<number> = [];

        private static flag: boolean;
        public static toBinary(intValue: number) {
            const self = Convert;
            if(typeof intValue === 'number') {
                self.inputValue = parseInt(intValue);
            } else throw Error('Value Error: Expected parameter to be a type of number');
            self.setBinaries(self);
            return self.binaries.reverse().join('');
        }

        private static setBinaries(self: any) {
            self.flag = true;
            let index = self.inputValue;

            self.binaries.push(index % 2);

            while(self.flag) {
                index /= 2;
                let indexValue = parseInt(index);
                if(indexValue === 0) {
                    self.flag = false;
                    continue;
                }
                self.binaries.push(indexValue % 2);
            }
        }
    }
}
const {ObjectTypes} = Utility;

namespace Validators {
    export class check {

        static email(email: string): boolean {
            const atIndex  = email.indexOf('@');
            const dotIndex = email.indexOf('.');
            const partOne  = email.split('@');
            const standard_pattern = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
            let partTwo = [];

            if(partOne.length === 2) 
                partTwo = partOne[1].split('.');
            return check.checkEmail(partOne,partTwo,atIndex,dotIndex,standard_pattern, email);
            
        }

        private static checkEmail(partOne: Array<string>, partTwo: Array<string>, atIndex: number, dotIndex: number, standard_pattern: any, email: string) {
           return this.validateEmailParts(partOne,partTwo,atIndex,dotIndex) && standard_pattern.test(email);
        }

        private static validateEmailParts(partOne: Array<string>, partTwo: Array<string>, atIndex: number, dotIndex: number) {
            let flag = false;
            if(partTwo.length === 2) {
                flag = check.matchParts(partOne, partTwo,atIndex,dotIndex);
            }
            return flag;
        }

        private static matchParts(partOne: any, partTwo: Array<string>, atIndex: number, dotIndex: number): boolean {
            let flag = false;
            if(atIndex >= 1 && atIndex < (dotIndex - 1) && dotIndex >(atIndex + 1)) {
                const pattern =  /^[A-Za-z0-9\.\-]+$/;
                const firstPart  = partOne[0];
                const secondPart = partTwo[0];
                const thirdPart  = partTwo[1];
                flag = check.matchPatterns(pattern, firstPart,secondPart,thirdPart);
            }
            return flag;
        }

        private static matchPatterns(pattern: any, firstPart: string, secondPart: string, thirdPart: string) {
            return pattern.test(firstPart) && pattern.test(secondPart) && pattern.test(thirdPart);
        }
        static alpha(alpha: string): boolean {
            let flag = false;

            if(!check.empty(alpha)) {
                const pattern = /^[A-Za-z\s]+$/;
                if(pattern.test(alpha)) flag = true;
            }
            return flag;
        }

        static alphaNumeric(alpha_numeric: string): boolean {
            let flag = false;

            if(!check.empty(alpha_numeric)) {
                const pattern = /^[A-Za-z0-9\s]+$/;
                if(pattern.test(alpha_numeric)) flag = true;
            }
            return flag;
        }

        static telNumber(phone_number: string) {
            const number_len = phone_number.length;
            const validValue = phone_number.replace('+','0');
            let flag = false;
            if(/^[0-9]+$/.test(validValue)) {
               flag = check.validateTelNumber(parseInt(phone_number), number_len);
            }
            return flag;
         }

         private static validateTelNumber(phone_number: number, number_len) {
             return (check.numeric(phone_number) &&  (number_len >= 10 && number_len <= 15));
         }

        static backData(method: string,request_object: any,callback: Function) {
            method = method.toLowerCase();
            if(method === 'post') 
               check.getDataByPost(request_object, callback);
            else if(method === 'get')
               check.getDataByGet(request_object, callback);
        }

        private static getDataByPost(request: any, callback: Function) {
            if(typeof servo !== 'undefined') 
                check.setPost(request,callback);
            else 
                callback({state: false, payload: null});
        } 

        private static setPost(request: any, callback: Function) {
            servo.post(request).catch(({error}) => error)
            .then(({response}) =>{
                check.getPostResponse(response, callback);
            });
        }

        private static getPostResponse(response: any, callback: Function){
            if(!check.empty(response)) 
                callback(response);
            else 
                callback({state: false, data: null});
        }

        private static getDataByGet(request: any, callback: Function) {
            if(typeof servo !== 'undefined') 
                check.setGet(request, callback);
            else 
                callback({state: false, data: null});
        } 

        private static setGet(request: any, callback: Function) {
            servo.get(request).catch(({error}) => error)
            .then((response: any) =>{
                check.getGetResponse(response, callback);
            });
        }

        private static getGetResponse(response: any, callback: Function){
            if(check.empty(response)) 
                callback({state: true, data: response});
            else 
                callback({state: false, data: null});
        }
        static numeric(number: any): boolean {
            return !isNaN(number);
        }

        static empty(data: any): boolean {
            return (
                check.checkStrings(data)        ||
                check.checkObjectAndArray(data) ||
                check.checkAny(data)
            );
        }

        static checkObjectAndArray(data: any) 
        {
            return check.checkObjects(data) || check.checkArrays(data);
        }

        private static checkStrings(data: any): boolean {
            return ObjectTypes.compare(data,"");
        }

        private static checkObjects(data: any): boolean {
            return ObjectTypes.compare(data,{});
        }

        private static checkArrays(data: any): boolean {
            return ObjectTypes.compare(data,[]);
        }
        private static checkAny(data: any): boolean {
            return ObjectTypes.compare(data, undefined);
        }
    }
}

const setUrl = (path: string) => {
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


