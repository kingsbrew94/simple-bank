class servo {
    private static $self: any = Object.create({});
    private static xhttp:any;
    private static response:any;

    private constructor() {
        servo.$self.holdsdata = false;
        servo.xhttp.onreadystatechange = function() {
            if(servo.xhttp.readyState === XMLHttpRequest.DONE && servo.xhttp.status === 200 && !servo.$self.raised_error) {
                servo.$self.holdsdata = true;
                servo.$self.requestcompleted = true;
                servo.response = !this.isxml ? servo.xhttp.responseText : servo.xhttp.responseXML;
            } else if(servo.xhttp.readyState === 4 && (servo.xhttp.status === 0 || servo.xhttp.status === 404 || servo.$self.raised_error)){
                servo.$self.requestcompleted = false;
            }
        };
    }

    private static initVars(){
        servo.$self.holdsdata;
        servo.$self.interval;
        servo.$self.requestcompleted = null;
        servo.$self.filename  = "";
        servo.$self.errortext = "";
        servo.$self.issync = false;
        servo.$self.isjson = false;
        servo.$self.isxml  = false;
        servo.$self.fileupload = false;
        servo.$self.isstringified = false;
        servo.$self.raised_error  = false;
        servo.$self.untilgetData  = null;
        servo.$self.progressCount = 1;
        servo.$self.jsonParse = false;
    }
    private static runservo({ method, url, async, json,fileUpload }) {
        async = !async ? false : async;
        json = !json ? false : (function(){return servo.$self.isjson = true;})();
        this.$self.filename = (url.split('/')[url.split('/').length - 1]).split('?')[0];
        fileUpload = !fileUpload ? false : (function(){return servo.$self.fileupload = fileUpload;})();
        if(method === "POST" && !fileUpload && !json) {
            servo.xhttp.open(method,url,async);
            servo.xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        } else if((method === "POST" || method === "GET") && json && !fileUpload) {
            servo.xhttp.open(method,url,async);
            servo.xhttp.setRequestHeader("Content-type", "application/json",async);
        } else if((method === "POST" || method === "GET") && fileUpload){
           servo.xhttp.open(method,url,async);
           servo.xhttp.setRequestHeader("Content-type", "multipart/form-data");
        } else {
            servo.xhttp.open(method,url,async);
        }
    }
    static set setRequestDetail(req : {
                                       method:   string,
                                          url:   string,
                                        async:   boolean,
                                         json:   boolean,
                                          xml:   boolean,
                                     fileUpload: boolean,
                                     jsonParse: boolean,
                                })
    {
        servo.initVars()
        this.$self.isxml = !req.xml ? false : req.xml;
        servo.$self.jsonParse = !req.jsonParse ? false : req.jsonParse;
        servo.xhttp = new XMLHttpRequest();
        switch(req.async) {
        case true:
            new servo();
            this.runservo(req);
        break;
        default:
                this.$self.issync = true;
                this.runservo(req);
        break;
        }

    }

    static sendRequest(data="") {
       data !="" ? servo.xhttp.send(data) : servo.xhttp.send();
    }
    static get getResponse(){

        if(servo.$self.isjson || servo.$self.jsonParse) {
            return JSON.parse(servo.response);
        }
        return servo.response;
    }

    static get getErrorMsg() {
        return servo.$self.errortext;
    }
    static get hasData() {
        return servo.$self.holdsdata;
    }
   private static requestCompleted(onprogress=undefined) {
        const self = this;
        servo.$self.errortext = servo.$self.errortext !== "" ? (function(){
            servo.$self.raised_error= true;
            return servo.$self.errortext;
        })() : servo.$self.errortext;
        return new class {
            then(func) {
                if(onprogress !== undefined && 
                    servo.$self.progressCount === 1 &&
                    !servo.$self.issync
                ) {
                    onprogress();
                    ++servo.$self.progressCount;
                }
                const untilprogressdstops = setInterval(()=>{
                    if(servo.$self.requestcompleted === false) {
                        clearInterval(untilprogressdstops);
                    }
                    if(servo.$self.requestcompleted) {
                        servo.synchronize(func);
                        clearInterval(untilprogressdstops);
                    }
                },servo.$self.interval);
                return servo.requestCompleted();
            }
            catch(func) {
                const waitUntilRequestFail = setInterval(()=>{
                    if(servo.$self.requestcompleted) {
                        clearInterval(waitUntilRequestFail);
                    }
                    else if(servo.$self.requestcompleted === false) {
                        clearInterval(servo.$self.untilgetData);
                        servo.$self.progressCount = 0;
                        let returnvalue =  servo.$self.errortext !=="" && servo.$self.errortext !== "NetworkError: A network error occurred."
                        ? func({error: servo.$self.errortext })
                        :(function(){
                           servo.$self.errortext = "NetworkError: A network error occured:";
                           servo.$self.errortext = servo.$self.errortext.toString().concat(` unable to locate file: '${servo.$self.filename}'`);
                           return func({ error: servo.$self.errortext });
                        })();
                        if(returnvalue !== undefined) {
                            clearInterval(waitUntilRequestFail);
                            throw new Error(returnvalue.toString());
                        }

                        clearInterval(waitUntilRequestFail);
                        if(servo.$self.errortext === "") {
                            throw new Error(servo.$self.errortext);
                        } else {
                            throw new Error(servo.$self.errortext);
                        }
                    }
                },self.$self.interval);

                return servo.requestCompleted();
            }
        };


    }
    static runscript(ctrl: {module: any,moduleprogress?: any,interval: number}) {
        if(!this.$self.issync && ctrl.module !== undefined) {
         this.$self.interval =  !ctrl.interval ? 12 : ctrl.interval;
         servo.$self.untilgetData = setInterval(function(){
             if(servo.$self.holdsdata) {
                 ctrl.module({response: servo.getResponse});
                 clearInterval(servo.$self.untilgetData);
             } else if(ctrl.moduleprogress !== undefined) {
                 ctrl.moduleprogress();
             }
         },this.$self.interval);
        } else {
            if(servo.$self.fileupload) {
                this.getsyncModeData(servo.$self.fileupload);
                if(servo.$self.raised_error) {
                    servo.$self.requestcompleted = false;
                    return this.requestCompleted();
                }
                this.xhttp.upload.addEventListener("progress",
                function(){
                    if(ctrl.moduleprogress !== undefined)
                       ctrl.moduleprogress();
                }, this.$self.issync);
                this.xhttp.addEventListener("load",
                 function(event){
                    this.xhttp.responseText = event.target.responseText;
                    ctrl.module({response: servo.getResponse});
                 }, this.$self.issync);
                this.xhttp.addEventListener("error", function(){
                    const error:string = "Upload Failed";
                   try{
                      if(ctrl.moduleprogress !== undefined)
                      ctrl.moduleprogress();
                      throw new Error(error);
                   } catch(error) {
                       servo.$self.raised_error = true;
                       servo.$self.errortext = error;
                   } finally {
                       if(servo.$self.raised_error) {
                           return this.requestCompleted();
                       }
                   }
                }, this.$self.issync);
                this.xhttp.addEventListener("abort",function() {
                   const error:string = "Upload Aborted";
                   try {
                    if(ctrl.moduleprogress !== undefined)
                        ctrl.moduleprogress();
                    throw new Error(error);
                   } catch(error) {
                     servo.$self.raised_error = true;
                     servo.$self.errortext = error;
                   } finally {
                     if(servo.$self.raised_error) {
                         return this.requestCompleted();
                     }
                   }
                }, this.$self.issync);

            } else {
                if(servo.$self.issync &&  ctrl.moduleprogress !== undefined) {
                    ctrl.moduleprogress();
                    const delays = setTimeout(() => {
                        this.getsyncModeData();
                        if(servo.$self.raised_error) {
                            servo.$self.requestcompleted = false;
                            return this.requestCompleted();
                        }
                        if(ctrl.module !== undefined) {
                            ctrl.module({response: servo.getResponse});
                        }
                        clearTimeout(delays);
                    },this.$self.interval);
                } else {
                    this.getsyncModeData();

                    if(servo.$self.raised_error) {
                        servo.$self.requestcompleted = false;
                        return this.requestCompleted();
                    }
                    if(ctrl.module !== undefined)
                       ctrl.module({response: servo.getResponse});
                }
            }
        }
    }
     static post({url,async,response,onprogress,json,jsonstringify,xml,fileUpload,send,interval, jsonParse}) {
         jsonstringify = !jsonstringify ? false : servo.$self.isstringified = jsonstringify;
         servo.$self.progressload = onprogress;
         const queryData = (send !== undefined && send !== null) ? `${servo.submit(send)}`: "";
         this.setRequestDetail = {method: 'POST', url, async, json,xml,fileUpload, jsonParse};
         try{
             this.sendRequest(queryData);
             if(servo.xhttp.status === 404) {
                 servo.$self.requestcompleted = false;
                 return servo.requestCompleted();
             }
         } catch(error) {

             servo.$self.requestcompleted = false;
             servo.$self.errortext = error;
             return this.requestCompleted();
         } finally {
             if(servo.$self.errortext !== "") {
                 console.error(servo.$self.errortext);
             }
         }
         this.runscript({
             module: response,
             interval: interval,
             moduleprogress: onprogress
         });
         return this.requestCompleted(servo.$self.progressload);
     }
     static get({url ,async,response,onprogress,json,jsonstringify,xml,fileUpload,send,interval, jsonParse}) {
         jsonstringify = !jsonstringify ? false : servo.$self.isstringified = jsonstringify;
         servo.$self.progressload = onprogress;
         if(onprogress !== undefined && async === true) {
               servo.$self.onprogress = onprogress;
         }
         const queryData = (send !== undefined && send !== null) ? `?${servo.submit(send)}`: "";
         this.setRequestDetail = {method: 'GET', url: `${url}${queryData}`,async,json,xml,fileUpload, jsonParse};
         try{
             this.sendRequest();
             if(servo.xhttp.status === 404) {
                 servo.$self.requestcompleted = false;
                 return servo.requestCompleted();
             }
         } catch(error) {
             servo.$self.requestcompleted = false;
             servo.$self.holdsdata = false;
             servo.$self.errortext = error;
             return servo.requestCompleted();
         } finally {
            if(servo.$self.errortext !== "") {
                console.error(servo.$self.errortext);
            }
        }
         this.runscript({
           module: response,
           interval: interval,
           moduleprogress: onprogress
         });
         return this.requestCompleted(servo.$self.progressload);
     }

     public static set awaits(func:any) {
        servo.requestCompleted(servo.$self.progressload)
        .then(({response})=> func({response},null))
        .catch((error) => func(null,{error: servo.getErrorMsg}));
     }

     private static submit(data) {
         let result = data.reduce((result,objdata) => {
            const keys = Object.getOwnPropertyNames(objdata);
            keys.forEach(key =>{
                result += (key.toString()).concat(`=${(function(){
                    if(servo.$self.isstringified) return JSON.stringify(objdata[key]);
                    return objdata[key];
                })()}&`);
            });
            return result;
         },"");

         //if(servo.errortext != "")   return this.requestCompleted();
         return result = result.substring(0,result.length-1);
    }
     private static synchronize(module?:any) {
         if(!this.$self.issync) {
             const waitUtilResponse = setInterval(() => {
                try{ 
                if(servo.$self.requestcompleted === false) {
                    clearInterval(waitUtilResponse);
                    servo.$self.progressCount = 0;
                    throw new Error(servo.$self.errortext);
                 }
                 if(servo.$self.holdsdata && servo.$self.requestcompleted) {
                     servo.$self.progressCount = 0;
                     module({response: servo.getResponse});
                     clearInterval(waitUtilResponse);
                 }
                } catch(error) {
                    module({response: servo.getErrorMsg});
                }
             },this.$self.interval);
         } else {
             module({response: servo.getResponse});
         }
     }
     private static getsyncModeData(filemode=false) {
        servo.$self.errortext = servo.$self.errortext !== "" ? (function(){
            servo.$self.raised_error= true;
            return servo.$self.errortext;
        })() : servo.$self.errortext;

         if(this.$self.issync && !filemode) {
             servo.$self.holdsdata = true;
             servo.$self.requestcompleted = true;
             servo.response = !this.$self.isxml ? servo.xhttp.responseText : servo.xhttp.responseXML;
         } else if(filemode && this.$self.issync) {
            servo.$self.holdsdata = true;
            servo.$self.requestcompleted = true;
         }
    }
 }
 