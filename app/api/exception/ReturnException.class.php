<?php
namespace api\exception;
use think\Exception;

class ReturnException extends Exception {
    private $result;

    public function __construct($return) {
        $this->result = $return;
    }

    public function getResult() {
        return $this->result;
    }
}