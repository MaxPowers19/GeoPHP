<?php

namespace JWare\GeoPHP\Exceptions;

public $message;

class FirstAndLastPointNotEqualException extends \Throwable {
    public function __construct($message, $code = 0, Exception $previous = null) {
        $this->message = $message;
        $this->__construct($message, $code, $previous);
    }
}

?>
