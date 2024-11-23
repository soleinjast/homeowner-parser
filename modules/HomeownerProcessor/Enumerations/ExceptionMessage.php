<?php

namespace Modules\HomeownerProcessor\Enumerations;

enum ExceptionMessage: string
{
    case FILE_NOT_FOUND = 'The specified file does not exist.';
    case INVALID_ROW_FORMAT = 'The row format is invalid.';
    case UNEXPECTED_ERROR = 'An unexpected error occurred during processing.';
}
