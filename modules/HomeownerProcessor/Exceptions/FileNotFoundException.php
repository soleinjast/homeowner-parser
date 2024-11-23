<?php

namespace Modules\HomeownerProcessor\Exceptions;

use Exception;
use Modules\HomeownerProcessor\Enumerations\ExceptionMessage;

class FileNotFoundException extends Exception
{
    public function __construct()
    {
        parent::__construct(ExceptionMessage::FILE_NOT_FOUND->value);
    }
}
