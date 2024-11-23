<?php

namespace Modules\HomeownerProcessor\Services;

use Modules\HomeownerProcessor\Exceptions\FileNotFoundException;

interface CSVReaderInterface
{
    /**
     * @throws FileNotFoundException
     */
    public function readFile(string $filePath): array;
}
