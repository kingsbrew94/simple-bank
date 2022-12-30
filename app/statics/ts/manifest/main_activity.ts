/**
 * Scripts loads here
 * @var _appImportModules 
 * @todo sets exported namespace from the src file
 */

 const _appImportModules = [
     /** namespaces here */
     'jQuery'
 ];

 /**
  *  @function define
  *  @callback main
  */
 _export(_appImportModules, Main);

/**
 *  @function Main 
 *  @param modules
 *  @todo access all loaded script modules
 */

function Main($: any): void {
    $(document).ready(function(): void {
        // code here
    });
}

