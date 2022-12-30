<?php 
/**
 * @author  K.B. Brew <flyartisan@gmail.com>
 * @package FLY\Model\Algorithm
 * @version 3.0.0
 */

namespace FLY\Model\Algorithm;

use FLY\Libs\Request;
use FLY_ENV\Util\Model\QueryBuilder;

/**
 * @trait  Model_Controllers
 * @todo   Implements model methods
 */

 trait Model_Controllers {
    
	/**
	 * @return QueryBuilder
	 */
	static function instance(): QueryBuilder
	{
		return new Self;
	}



	/**
	 * @param string $alias_name
	 * @return string
	 * @Todo Creates an alias of reference table or model.
	 */
	static function alias(string $alias_name): string
	{
    	return self::searchModelName(__CLASS__).' | '.$alias_name;
	}
    
    
 
 
	/**
	 * @return array
	 * @Todo fetches all records from the referenced table.
	 */   
	static function all(): array
	{
    	return self::_all(new Self);
	}



	/**
	 * @return array
	 * @Todo fetches all records in a reversed manner from the referenced table.
	 */
	static function all_reverse(): array
	{
    	return self::_reverse(new Self);
	}



	/**
	 * @return object
	 * @Todo  It fetches the first record from the referenced table.
	 */
	static function first(): object
	{
    	return self::_first(new Self);
	}



	/**
	 * @return object
	 * @Todo  It fetches the second record from the referenced table.
	 */
	static function second(): object
	{
    	return self::_second(new Self);
	}



	/**
	 * @return object
	 * @Todo  It fetches the third record from the referenced table.
	 */
	static function third(): object
	{
    	return self::_third(new Self);
	}



	/**
	 * @return object
	 * @Todo It fetches the middle record from the referenced table.
	 */
	static function middle(): object
	{
    	return self::_middle(new Self);
	}



	/**
	 * @return object
	 * @Todo It fetches the last record from the table.
	 */
	static function last(): object
	{
    	return self::_last(new Self);
	}



	/**
	 * @return integer
	 * @Todo It counts the total number of records in the referenced table.
	 */
	static function length(): int
	{
    	return self::_count(new Self);
	}



	/**
	 * @return array
	 * @Todo It returns the field name of the referenced table.
	 */
	static function fields(): array
	{
    	return self::_fields(new Self);
	}



	/**
	 * @return array
	 * @Todo It returns the blueprint or the description of the referenced table.
	 */
	static function describe(): array
	{
    	return self::_describe(new Self);
	}



	/**
	 * @return bool
	 * @Todo It returns true or false where the referenced table is empty or not.
	 */
	static function is_empty(): bool
	{
    	return self::_is_empty(new Self);
	}



	/**
	 * @return QueryBuilder
	 * @Todo It's automatically set request payload and then returns a QueryBuilder.
	 */
	static function auto_set(): QueryBuilder
	{
		return self::make_auto_set(Request::all(),new Self);
	}



	/**
	 * @return __anonymous@4523
	 * @Todo It automatically saves data to the referenced through a request.
	 */
	static function auto_save()
	{
    	return self::make_auto_save(Request::all(),new Self);
	}



	/**
	 * @return object|bool
	 * @Todo It automatically update in the referenced table through a request.
	 */
	static function auto_update()
	{
    	return self::make_auto_update(Request::all(),new Self);
	}



	/**
	 * @return array
	 * @Todo fetches all records in a reversed manner from the referenced table.
	 */
	static function reverse(): array
	{
   	return self::_reverse(new Self);
	}



	/**
	 * @return bool
	 * @Todo It removes all records from the referenced table.
	 */
	static function clear(): bool
	{
    	return self::_clear(new Self);
	}



	/**
	 * @param mixed ...$expressions
	 * @return object|bool
	 * @Todo It removes a specific record from the referenced table.
	 */
	static function delete_where(...$expressions)
	{
    	return self::_delete_where($expressions,new Self);
	}



	/**
	 * @param array $data
	 * @return object|bool
	 * @Todo It removes a specific record from the referenced table.
	 */
	static function delete_when(array $data)
	{
    	return self::_delete_when((object) $data,new Self);
	}



	/**
	 * @param string $field_name
	 * @return bool
	 * @Todo Checks whether the field name of the referenced table has null as default.
	 */
	static function is_default_null(string $field_name): bool
	{
    	return self::_field_null(new Self,$field_name);
	}



	/**
	 * @param string $field_name
	 * @return string
	 * @Todo It returns a specific field capacity of the reference table.
	 */
	static function capacity(string $field_name): string
	{
    return self::_field_capacity(new Self,$field_name);
	}



	/**
	 * @param string $field_name
	 * @return string
	 * @Todo It returns a specific field type of the reference table.
	 */
	static function type(string $field_name): string
	{
    	return self::_field_type(new Self,$field_name);
	}



	/**
	 * @param string $field_name
	 * @return string
	 * @Todo It returns a specific field's default value of the reference table.
	 */
	static function get_default(string $field_name): string
	{
    	return self::_field_default(new Self,$field_name);
	}



	/**
	 * @param string $field_name
	 * @return string
	 * @Todo It returns a specific field extra value of the reference table.
	 */
	static function extra(string $field_name): string
	{
    	return self::_field_extra(new Self,$field_name);
	}



	/**
	 * @param string $field_name
	 * @return bool
	 * @Todo It checks whether a specific field exist in the referenced table.
	 */
	static function field_exists(string $field_name): bool
	{
    	return self::_field_exists(new Self,$field_name);
	}



	/**
	 * @param string $field_value
	 * @param string|null $field_name
	 * @return bool
	 * @Todo It checks whether a specific value exists in the referenced table.
	 */
	static function value_exists(string $field_value,string $field_name=null): bool
	{
    	return self::_value_exists(new Self,$field_value,$field_name??'');
	}



	/**
	 * @param array $data
	 * @return __anonymous@4523
	 * @Todo It pushes a record to the referenced table.
	 */
	static function push(array $data)
	{
    	return self::make_auto_save($data,new Self);
	}



	/**
	 * @param string $reference_model_name
	 * @return mixed
	 * @Todo It appends records from a reference or a secondary table to the referenced table.            
	 */
	static function auto_append(string $reference_model_name)
	{
    	return self::make_auto_append($reference_model_name,new Self);
	}



	/**
	 * @param array $data
	 * @Todo It pushes an update to the referenced table.
	 */
	static function push_update(array $data)
	{
    	return self::make_auto_update($data, new Self);
	}



	/**
	 * @param $payload
	 * @return QueryBuilder
	 * @Todo It helps search record of the referenced table.
	 */
	static function get($payload): QueryBuilder
	{
    	return self::getData($payload,new Self);
	}



	/**
	 * @param int $position
	 * @return object
	 * @Todo It searches and returns a record by index
	 */
	static function index(int $position): object
	{
    	return self::_index(new Self,$position);
	}



	/**
	 * @param string $field_name
	 * @return array
	 * @Todo It returns specific field(s) by field name of the referenced table.
	 */
	static function field(string $field_name): array
	{
    	return self::_field(new Self,$field_name);
	}



	/**
	 * @param string $field_value
	 * @return array
	 * @Todo It returns specific value(s) by field value of the referenced table.
	 */
	static function value(string $field_value): array
	{
    	return self::_value(new Self,$field_value);
	}



	/**
	 * @param string $field_name
	 * @param string $field_value
	 * @return array
	 * @Todo It returns records by searching with field name and field value of the referenced table.
	 */
	static function field_value(string $field_name, string $field_value): array
	{
    	return self::_field_value(new Self,$field_name,$field_value);
	}



	/**
	 * @param string $pattern
	 * @return array
	 * @Todo It returns the field names from the referenced table by matching the field names to a provisional pattern. 
	 */
	static function match_field(string $pattern): array
	{
    	return self::_match_field(new Self,$pattern);
	}



	/**
	 * @param string $pattern
	 * @return array
	 * @Todo It returns the field values from the referenced table by matching the field values to a provisional pattern.
	 */
	static function match_value(string $pattern): array
	{
    	return self::_match_value(new Self,$pattern);
	}



	/**
	 * @param string $field_pattern
	 * @param string $value_pattern
	 * @return array
	 * @Todo It returns the field names and values from the referenced table by matching 
	 *       the field names and values to a respective provisional pattern.
	 */
	static function match_field_value(string $field_pattern,string $value_pattern): array
	{
    	return self::_match_field_value(new Self,$field_pattern,$value_pattern);
	}



	/**
	 * @param string $query
	 * @return array|bool
	 * @Todo It executes custom sql queries
	 */
	static function query(string $query)
	{
    	return self::_query($query,new Self);
	}

	/**
	 * @return string
	 * @Todo It returns the referenced class name
	 */
	public function get_name(): string
	{
    	return __CLASS__;
	}

 }