<?php 
/**
 * @author  K.B. Brew <flyartisan@gmail.com>
 * @version 2.0.0
 */
trait ActivityTemplate {
    
    private function Service(string $object_name) 
    {
        $obj_name = strtolower($object_name);
   
        return <<<TM
<?php namespace App\Actors\Services;

use App\Actors\\DAO\\{$object_name}DAO;
use FLY\Libs\CRUD\AppService;

class {$object_name}Service extends AppService 
{
\tpublic function __construct(Request \$req) 
\t{
\t\tparent::__construct(new {$object_name}DAO());
\t}

}
TM;
    }

    private function DirectAccessObject(string $object_name, string $model, string $db) 
    {
        return <<<TM
<?php namespace App\Actors\DAO;

use App\Actors\Repositories\\{$object_name}Repository;
use App\Models\\{$db}\DS\\{$model};
use FLY\Libs\CRUD\AppDAO;

class {$object_name}DAO extends AppDAO implements {$object_name}Repository 
{
\tprotected {$model} \$user;

\tprotected \$modelName = {$model}::class;
     
}
TM;
    }

    private function Repository(string $object_name)
    {
        return <<<TM
<?php namespace App\Actors\Repositories;

use FLY\Libs\CRUD\CRUDRepository;

interface {$object_name}Repository extends CRUDRepository {}
TM;
    }

    private function CRUDEvent(string $object_name)
    {
        $obj_name = strtolower($object_name);
        return <<<TM
<?php namespace App\Actors\Events;

use App\Actors\Services\\{$object_name}Service;
use FLY\Libs\CRUD\AppService;
use FLY\Libs\Event;
use FLY\Libs\Request;

class {$object_name}CRUDEvent {

\tprivate static ?AppService \$service = null;

\tpublic static function initialize(Request \$request)
\t{

\t\t\$service = self::\$service <> null ? self::\$service: new {$object_name}Service();

\t\tEvent::on('Create{$object_name}',function(\$validatorName) use (\$request,\$service) {
\t\t\t\${$obj_name}Validator = new \$validatorName(\$request);
\t\t\tif(\${$obj_name}Validator->validate()) {
\t\t\t\treturn \${$obj_name}Validator->getMessage();
\t\t\t} 

\t\t\treturn \$service->save();
\t\t}); 

\t\tEvent::on('{$object_name}Validate',function(\$validatorName) use (\$request) {
\t\t\t\${$obj_name}Validator = new \$validatorName(\$request);
    return \${$obj_name}Validator->validate() ? \${$obj_name}Validator->getMessage() : new Dto(TRUE,'',\${$obj_name}Validator);
}); 

\t\tEvent::on('Read{$object_name}',function(\$validatorName) use (\$request,\$service) {
\t\t\t\${$obj_name}Validator = new \$validatorName(\$request);
\t\t\tif(\${$obj_name}Validator->validate()) {
\t\t\t\treturn \${$obj_name}Validator->getMessage();
\t\t\t} 

\t\t\treturn \$service->findAll();
\t\t}); 

\t\tEvent::on('Update{$object_name}',function(\$validatorName) use (\$request,\$service) {
\t\t\t\${$obj_name}Validator = new \$validatorName(\$request);
\t\t\tif(\${$obj_name}Validator->validate()) {
\t\t\t\treturn \${$obj_name}Validator->getMessage();
\t\t\t} 

\t\t\treturn \$service->update(\$request);
\t\t}); 

\t\tEvent::on('Delete{$object_name}',function(\$validatorName) use (\$request,\$service) {
\t\t\t\${$obj_name}Validator = new \$validatorName(\$request);
\t\t\tif(\${$obj_name}Validator->validate()) {
\t\t\t\treturn \${$obj_name}Validator->getMessage();
\t\t\t} 

\t\t\treturn \$service->delete(\$request);
\t\t}); 
\t}
}
TM;
    }
}