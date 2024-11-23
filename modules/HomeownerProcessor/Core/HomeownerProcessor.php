<?php

namespace Modules\HomeownerProcessor\Core;

use Exception;
use Modules\HomeownerProcessor\Enumerations\ExceptionMessage;
use Modules\HomeownerProcessor\Exceptions\FileNotFoundException;
use Modules\HomeownerProcessor\Exceptions\InvalidRowFormatException;
use Modules\HomeownerProcessor\Services\CSVReader;
use Modules\HomeownerProcessor\Services\HomeownerParser;
use Modules\HomeownerProcessor\Services\PersonExporter;
use RuntimeException;

class HomeownerProcessor
{
    public function __construct(protected CSVReader $csvReader,
                                protected HomeownerParser $homeownerParser,
                                protected PersonExporter $personExporter)
    {

    }
    public function process(string $filePath): void
    {
        try {
            $rows = $this->csvReader->readFile($filePath);
            $propertiesWithOwners = [];

            foreach ($rows as $index => $row) {
                // Use the row index as the property identifier
                $propertyRow = "Row(Property) " . ($index + 1);

                // Parse the owners from the first column of the row
                $owners = $this->homeownerParser->parseRow($row[0]);

                // Assign the owners to the corresponding row
                $propertiesWithOwners[$propertyRow] = $owners;
            }

            $this->personExporter->exportToJson($propertiesWithOwners);
        } catch (FileNotFoundException $e) {
            throw new RuntimeException(ExceptionMessage::FILE_NOT_FOUND->value, 0, $e);
        } catch (InvalidRowFormatException $e) {
            throw new \RuntimeException(ExceptionMessage::INVALID_ROW_FORMAT->value, 0, $e);
        } catch (\Exception $e) {
            throw new \RuntimeException(ExceptionMessage::UNEXPECTED_ERROR->value, 0, $e);
        }
    }
}
