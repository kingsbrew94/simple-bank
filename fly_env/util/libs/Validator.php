<?php namespace FLY\Libs;

abstract class Validator extends FLYFormValidator {

    protected ?Request $request;

    protected FLYFormValidator $validator;

    protected $model;

    protected $response;
    
    public function __construct(?Request $request)
    {
        if($request <> null) {
            $error_report = $this->error_report();
            $this->validator = self::check($request, $error_report);
            $this->request   = $this->validator->get_request();
        }
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function validate()
	{
		return $this->validator->has_error();
	}

	public function getMessage() 
	{
		return $this->validator->get_error_message();
	}

    abstract protected function error_report(): array;
}