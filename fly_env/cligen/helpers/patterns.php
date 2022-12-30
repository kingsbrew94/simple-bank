<?php
/**
 * @author  K.B. Brew <flyartisan@gmail.com>
 * @version 2.0.0
 */
class Patterns {

    static public function modelQueryPattern()
    {
        return '%
        (?=CREATE(?:\s)+TABLE)
            (?:CREATE(?:\s)+TABLE(?:\s+))(?:IF(?:\s+)NOT(?:\s+)EXISTS(?:\s+))? 
            (?P<tableName>[a-zA-Z_][a-zA-Z_0-9]*)(?:\s*)[(]
            (?P<fieldQuery>
              (?:  (?:\s*)(?P<fieldName>(?:[a-zA-Z_][a-zA-Z_0-9]*))(?:\s+)(?P<dataType>[a-zA-Z]+)(?:\s*)
                (?:[(](?P<size>[\w\d\s\W\S\D]*?)[)])?(?:\s*)
                (?:
                    (?:NOT(\s+?)NULL)?(?:\s+)?
                    (?:AUTO_INCREMENT|DEFAULT(?:\s+)
                    (?:
                        (?:
                            [\']?[\w\d\s\W\S\D]*?[\']?|["]?[\w\d\s\W\S\D]*?["]?|[^\s]+
                        )?
                    ))?
                    (?:\s*)(?P<keyType>PRIMARY(?:\s+)KEY|UNIQUE)?(?:\s*)
                    (?:[^,]+)?
                )?
            )[,]?[)]?)
        %ixm';
    }
    
    static public function modelFile()
    {
        return '%
            [a-zA-Z_][a-zA-Z_0-9]*[.]php
        %ixm';
    }

    static public function createDatabasePattern()
    {
        return '%
        (?=CREATE(?:\s)+DATABASE)
            (?:CREATE(?:\s)+DATABASE(?:\s+))(?:IF(?:\s+)NOT(?:\s+)EXISTS(?:\s+))? 
            (?P<dbName>[a-zA-Z_][a-zA-Z_0-9]*)(?:\s*)
        %ixm';
    }

    static public function createTable()
    {
        return '%
            (?=^CREATE(?:\s)+TABLE)
            (?:CREATE(?:\s)+TABLE(?:\s+))(?:IF(?:\s+)NOT(?:\s+)EXISTS(?:\s+))? 
            (?P<tableName>[a-zA-Z_][a-zA-Z_0-9]*)
        %ixm';
    }

    static public function fieldSingleStrings()
    {
        return '%
         [\'](?P<fieldName>[^\']+)[\']
        %ixm';
    }

    static public function fieldDoubleStrings()
    {
        return '%
         ["](?P<fieldName>[^"]+)["]
        %ixm';
    }

    static public function insertInto()
    {
        return '%
            (?=^INSERT(?:\s)+INTO)
        %ixm';
    }

    static public function alterTable()
    {
        return '%
            (?=^ALTER(?:\s)+TABLE)
        %ixm';
    }

    static public function dropTable()
    {
        return '%
            (?=^DROP(?:\s)+TABLE)
        %ixm';
    }

    static public function primaryKeys()
    {
        return '%
            PRIMARY\s+KEY\s*[(]
                (?P<keys>[^\)]+)
            [)]\s*[,]?
        %ixm';
    }

    static public function foreignKey()
    {
        return '%
            FOREIGN\s+KEY\s*[(]
                (?P<fkey>[^\)]+)
            [)]\s*
            REFERENCES\s+[a-zA-Z_][a-zA-Z_0-9]*\s*[(] 
                (?P<ref>[^\)]+)
            [)]\s*(?:[^,]+|[^\)]+)
        %ixm';
    }

    static public function insertQueryPattern()
    {
        return '%
            (?=INSERT\s+INTO)
            (?:
                INSERT\s+INTO\s+(?P<tableName>[a-zA-Z_][a-zA-Z_0-9]*)\s*[(]
                (?P<insertFields>[^\)]+)
                [)]\s*
            )VALUES\s* 
            (?P<insertData>[(](?P<insertValues>[^\)]*)[)](?:[,]|[;])?) 
        %ixm';
    }

    static public function alterAddPattern()
    {
        return '%
           (?=ALTER\s+TABLE)
           (?:ALTER\s+TABLE\s+)(?:IF(?:\s+)EXISTS(?:\s+))?(?P<tableName>[a-zA-Z_][a-zA-Z_0-9]*)\s+
           ADD\s+(?P<columnName>(?:[a-zA-Z_][a-zA-Z_0-9]*))
        %ixm';
    }

    static public function alterDropPattern()
    {
        return '%
           (?=ALTER\s+TABLE)
           (?:ALTER\s+TABLE\s+)(?:IF(?:\s+)EXISTS(?:\s+))?(?P<tableName>[a-zA-Z_][a-zA-Z_0-9]*)\s+
           DROP\s+COLUMN\s+(?P<columnName>(?:[a-zA-Z_][a-zA-Z_0-9]*))
        %ixm';
    }

    static public function alterModifyPattern()
    {
        return '%
           (?=ALTER\s+TABLE)
           (?:ALTER\s+TABLE\s+)(?:IF(?:\s+)EXISTS(?:\s+))?(?P<tableName>[a-zA-Z_][a-zA-Z_0-9]*)\s+
           MODIFY\s+COLUMN\s+(?P<columnName>(?:[a-zA-Z_][a-zA-Z_0-9]*))
        %ixm';
    }

    static public function dropTablePattern()
    {
        return '%
           (?=DROP\s+TABLE)
           (?:DROP(?:\s)+TABLE(?:\s+))(?:IF(?:\s+)EXISTS(?:\s+))? 
           (?P<tableName>[a-zA-Z_][a-zA-Z_0-9]*)\s*[;]?
        %ixm';
    }

    static public function dropDBPattern()
    {
        return '%
           (?=DROP\s+DATABASE)
           (?:DROP(?:\s)+DATABASE(?:\s+))(?:IF(?:\s+)EXISTS(?:\s+))? 
           (?P<tableName>[a-zA-Z_][a-zA-Z_0-9]*)\s*[;]?
        %ixm';
    }

    static public function ifExistsPattern()
    {
        return '%
            (?:\s+)(?:IF(?:\s+)EXISTS(?:\s+))
        %ixm';
    }

    static public function modelNamePattern()
    {
        return '%
            [a-zA-Z_][a-zA-Z_0-9]*
        %ixm';
    }
    
}
