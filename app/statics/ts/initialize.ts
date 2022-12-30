/***
 * @todo initializes modules
 */

function export_module(_modules: any) 
{    
   try {
       if (define !== undefined && define.amd && typeof define == 'function') {
           define([], function () {
               return _modules
           });
       }
   } catch(Error) { /* no module export */ }
}

function _export(imports: Array<string>, callback) 
{
   define(imports, callback);
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

function staticUrl(path: string) 
{
    let url = path;
    const meta_url = document.querySelector('script[data-main]');
    if(meta_url !== undefined || meta_url !== null) {
        let url_array = meta_url.src.split('/');
        let new_url_array = [];
        for(let element of url_array) {
            if(element === 'statics') {
                new_url_array.push(element);
                url = new_url_array.join('/');
                break;
            }
            new_url_array.push(element);
        }
    }
    return `${url}/${path}`;
};

require([staticUrl('js/manifest/main_activity.js')]);

