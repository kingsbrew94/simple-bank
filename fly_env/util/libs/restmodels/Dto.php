<?php namespace FLY\Libs\Restmodels;

/**
 * @author K.B Brew <flyartisan@gmail.com>
 * @package libs
 */
 
class Dto {

    public bool $state;
   
    public string $responseCode;
   
    public $payload;

    public string $message;

    public function __construct(bool $state=false,string $message='',$payload=null, string $responseCode='UNKNOWN')
    {
        $this->state   = $state;
        $this->message = $message;
        $this->payload = $payload;
        $this->responseCode = $responseCode;
    }

    public static function set(array $data): Dto
    {
        $self = new Self;
        foreach($data as $key => $value)
            $self->{strtolower($key)} = $value;
        return $self;
    }

    public function getState(): bool 
    {
        return $this->state;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getPayload()
    {
        return $this->payload;
    }

    public function getResponseCode()
    {
        return $this->responseCode;
    }

    public function setState(bool $state): void 
    {
        $this->state = $state;
    }

    public function setPayload($payload): void 
    {
        $this->payload = $payload;
    }

    public function setMessage(string $message): void 
    {
        $this->message = $message;
    }

    public function setResponseCode(string $responseCode): void
    {
        $this->responseCode = $responseCode;
    }
}