<?php

namespace Modules\HomeownerProcessor\Services;

use Exception;
use Modules\HomeownerProcessor\Exceptions\FileNotFoundException;

class CSVReader implements CSVReaderInterface
{
    /**
     * @throws FileNotFoundException
     */
    public function readFile(string $filePath): array
    {
        if (!file_exists($filePath)) {
            throw new FileNotFoundException();
        }

        $rows = array_filter(array_map('str_getcsv', file($filePath)), function ($row) {
            return !empty(array_filter($row));
        });

        // Ignore the header row
        return array_slice($rows, 1);
    }
}
