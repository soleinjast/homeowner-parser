<?php

namespace Modules\HomeownerProcessor\Services;

use Modules\HomeownerProcessor\Exceptions\InvalidRowFormatException;

class HomeownerParser implements HomeownerParserInterface
{
    /**
     * @param string $rawName
     * @return array
     * @throws InvalidRowFormatException
     */
    public function parseRow(string $rawName): array
    {
        if (empty(trim($rawName))) {
            throw new InvalidRowFormatException('Row is empty or contains invalid data.');
        }

        $parsedNames = [];


        if (preg_match('/\s*(and|&)\s*/i', $rawName)) {
            // Split names by "and" or "&" to process them
            $splitNames = preg_split('/\s*(and|&)\s*/i', $rawName);

            // Extract the family name from the last segment
            $lastSegment = trim(end($splitNames));
            $familyName = $this->extractLastName($lastSegment);

            // Process each split name
            foreach ($splitNames as $name) {
                $parsedName = $this->extractNameParts(trim($name));
                if ($parsedName) {
                    $parsedName['last_name'] = $parsedName['last_name'] ?? $familyName;
                    $parsedNames[] = $parsedName;
                }
            }
        } else {
            // Process as a single name
            $parsedName = $this->extractNameParts(trim($rawName));
            if ($parsedName) {
                $parsedNames[] = $parsedName;
            }
        }

        return $parsedNames;
    }

    /**
     * @param string $name
     * @return array|null
     */
    private function extractNameParts(string $name): ?array
    {
        // Match the title and the optional rest of the name
        preg_match('/^(?<title>Mrs|Mr|Ms|Miss|Dr|Prof|Mister)\.?(?:\s+(?<rest>.+))?$/i', $name, $matches);
        // Ensure there is at least a title
        if (empty($matches['title'])) {
            return null;
        }

        $title = $matches['title'];
        $rest = $matches['rest'] ?? null;

        $firstName = null;
        $initials = [];
        $lastName = null;

        if ($rest) {
            // Remove periods from initials
            $rest = str_replace('.', '', $rest);

            $nameParts = preg_split('/\s+/', $rest);

            if (count($nameParts) > 0) {
                // Extract the last name (last word)
                $lastName = array_pop($nameParts);

                // Check if the first name is an initial
                $firstNamePart = array_shift($nameParts);
                if ($firstNamePart) {
                    if (strlen($firstNamePart) === 1) {
                        // The first name is an initial
                        $initials[] = $firstNamePart;
                    } else {
                        $firstName = $firstNamePart;
                    }
                }

                foreach ($nameParts as $part) {
                    if (strlen($part) === 1) {
                        $initials[] = $part;
                    } else {
                        // Ignore middle names longer than one character
                        continue;
                    }
                }
            }
        }


        $initial = !empty($initials) ? implode(' ', $initials) : null;

        return [
            'title'      => $title,
            'first_name' => $firstName,
            'initial'    => $initial,
            'last_name'  => $lastName,
        ];
    }

    /**
     * Extract the last name from the final segment.
     * @param string $lastSegment
     * @return string|null
     */
    private function extractLastName(string $lastSegment): ?string
    {
        preg_match('/\b([\w\-]+)$/', $lastSegment, $matches);
        return $matches[1] ?? null;
    }
}
