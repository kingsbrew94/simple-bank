<?php
/**
 * @author  K.B. Brew <flyartisan@gmail.com>
 * @version 2.0.0
 */
class ClassModelGen {

   static public function createModels($dirName,$className,$fields,$pk,$fk,$config,$path)
   {
       self::saveClass($dirName,$className,$fields,$pk,$fk,$config,$path);
   }

   static private function saveClass(string $dirName,string $className,array $fields,array $pk,array $fk,array $config,string $path)
   {

        self::add_to_app_ds(
            $path,
            $className,
            self::setClassContent(
                self::setClassNamespace($dirName),
                self::setClass(
                    $className,
                    self::setClassMethods(
                        self::setClassFields($fields),
                        self::setClassParams($fields),
                        self::initClassFields($fields),
                        self::setPrimaryKeys($pk),
                        self::setForeignKeys($fk),
                        $config
                    )
                )
            )
        );
        
   }

    static private function add_to_app_ds($path,$className,string $full_class)
    {
        $path = $path.'/ds';
        $destdir = $path;
        $path .= "/{$className}.php";

        if(!file_exists($destdir)) {
            mkdir($destdir);
        }
        $class_file = fopen($path,'w');
        fwrite($class_file, $full_class);
        fclose($class_file);
    }

   static private function setClassContent($namespace,$class) 
   {
       return <<<CT
$namespace
$class
CT;
   }

   static private function setClassNamespace($dirName)
   {
        $namespace  = '<?php namespace App\Models'.'\\'.$dirName.'\DS;';
        $namespace .= PHP_EOL.'use FLY\Model\Algorithm\Model_Controllers;';
        $namespace .= PHP_EOL."use FLY_ENV\Util\Model\QueryBuilder;".PHP_EOL;
        return $namespace;
   }

   static private function setClassFields(array $fields) 
   {
       $field_vars = "";
        foreach($fields as $field) {
            $field_vars .="\t".'protected $'.$field.';'.PHP_EOL.PHP_EOL;
        }
       return $field_vars;
   }
   
   static private function setClassParams(array $fields)
   {
        $params = "";
        $fieldLen = count($fields);
        $counter  = 1;
        foreach($fields as $field) {
            $params .='$'."{$field}=".'""';
            if($counter++ <> $fieldLen) $params .= ",";
        }
        return $params;
   }

   static private function initClassFields(array $fields)
   {
       $inits = "";
        foreach($fields as $field) {
            $inits .= "\t\t".'$this->'.$field.' = $'."{$field};".PHP_EOL;
        }
        return $inits;
   }
   
   static private function setPrimaryKeys($pks)
   {
       $keyStr = "";
       $fieldLen = count($pks);
       $counter  = 1;
       foreach($pks as $key => $refKey) {
           $keyStr .= "'{$refKey}'";
           if($counter++ <> $fieldLen) $keyStr .= ",";
       }
       return '$this->pk_names=[ '.$keyStr.' ];';
   }

   static private function setForeignKeys($fks)
   {
       $keyStr = "";
       $fieldLen = count($fks);
       $counter  = 1;
       foreach($fks as $key => $refKey) {
           $keyStr .= "'{$key}'=>'{$refKey}'";
           if($counter++ <> $fieldLen) $keyStr .= ",";
       }
       return $keyStr <> "" ?'$this->fk_names=[ '.$keyStr.' ];' : "";
   }

   static private function assign_protocols($config)
   {
       $set  = 'return array('.PHP_EOL;
       $set .= "\t\t\t'host'\n\n\t\t\t\t=> '{$config['host']}',".PHP_EOL;
       $set .= "\n\t\t\t'user'\n\n\t\t\t\t=> '{$config['username']}',".PHP_EOL;
       $set .= "\n\t\t\t'password'\n\n\t\t\t\t=> '{$config['password']}',".PHP_EOL;
       $set .= "\n\t\t\t'model'\n\n\t\t\t\t=> '{$config['database']}'".PHP_EOL;
       $set .= "\t\t".');'; 

       return <<<PTC
\t{$set}
PTC;

   }

   static private function setClass($className, $methods)
   {
       return "class {$className} extends QueryBuilder {".PHP_EOL.$methods.PHP_EOL."}";
   }

    static private function setClassMethods($fields,$params,$setters,$pks,$fks,$config)
    {
        $protocols = self::assign_protocols($config);
        
        return <<<MTH

/*        
\t*******************************************************************************
\t* can use transaction here                                                    *
\t* example: use TRANSACTION;                                                   *
\t* To use a transaction specify the namespace above this model class.          *
\t* That is, copy and paste the namespace: use FLY\Model\Algorithm\TRANSACTION; * 
\t* right above this model class.                                               *
\t*******************************************************************************
*/

{$fields}
\tuse Model_Controllers;

\tpublic function __construct($params) 
\t{
    \tparent::__construct(\$this);
$setters
    \t{$pks}
    \t{$fks}
    \t\$this->apply();
\t}


\t/**
\t * @return string[]
\t * @Todo It returns the model connection credentials
\t */
\tprotected function connection(): array
\t{
    {$protocols}
\t}
MTH;
    }
}