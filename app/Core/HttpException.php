<?php
namespace App\Core;
class HttpException extends \RuntimeException {
    public function __construct(public int $status, string $message) { parent::__construct($message); }
}
