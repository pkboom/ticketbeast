<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class NotEnoughTicketsException extends Exception
{
    public function render($request)
    {
        return response([], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
