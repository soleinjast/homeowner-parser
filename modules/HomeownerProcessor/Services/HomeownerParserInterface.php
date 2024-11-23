<?php

namespace Modules\HomeownerProcessor\Services;

use Modules\HomeownerProcessor\Exceptions\InvalidRowFormatException;

interface HomeownerParserInterface
{
    /**
     * Parse a raw name string into structured parts.
     *
     * @param string $rawName
     * @return array
     * @throws InvalidRowFormatException
     */
    public function parseRow(string $rawName): array;
}
