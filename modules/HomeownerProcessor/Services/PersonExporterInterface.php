<?php

namespace Modules\HomeownerProcessor\Services;

interface PersonExporterInterface
{
    /**
     * Exports properties with associated homeowners to a JSON format.
     *
     * @param array $propertiesWithOwners
     * @return void
     */
    public function exportToJson(array $propertiesWithOwners): void;
}
