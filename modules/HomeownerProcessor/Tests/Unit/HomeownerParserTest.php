<?php

namespace Modules\HomeownerProcessor\Tests\Unit;

use Modules\HomeownerProcessor\Exceptions\InvalidRowFormatException;
use Modules\HomeownerProcessor\Services\HomeownerParser;
use Tests\TestCase;

class HomeownerParserTest extends TestCase
{
    protected HomeownerParser $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new HomeownerParser();
    }

    /**
     * @throws InvalidRowFormatException
     */
    public function testParseValidRowSingleOwner(): void
    {
        $row = "Mr. John Doe";
        $parsed = $this->parser->parseRow($row);

        $this->assertCount(1, $parsed);
        $this->assertSame('Mr', $parsed[0]['title']);
        $this->assertSame('John', $parsed[0]['first_name']);
        $this->assertSame('Doe', $parsed[0]['last_name']);
    }

    /**
     * @throws InvalidRowFormatException
     */
    public function testParseValidRowMultipleOwners(): void
    {
        $row = "Mr. John Doe & Mrs. Jane Doe";
        $parsed = $this->parser->parseRow($row);

        $this->assertCount(2, $parsed);
        $this->assertSame('Mr', $parsed[0]['title']);
        $this->assertSame('John', $parsed[0]['first_name']);
        $this->assertSame('Doe', $parsed[0]['last_name']);

        $this->assertSame('Mrs', $parsed[1]['title']);
        $this->assertSame('Jane', $parsed[1]['first_name']);
    }

    public function testParseEmptyRowThrowsException(): void
    {
        $this->expectException(InvalidRowFormatException::class);
        $this->parser->parseRow("");
    }

    /**
     * @throws InvalidRowFormatException
     */
    public function testParseRowWithMissingTitle(): void
    {
        $row = "John Doe";
        $parsed = $this->parser->parseRow($row);

        $this->assertEmpty($parsed);
    }

    /**
     * @throws InvalidRowFormatException
     */
    public function testParseRowWithInitials(): void
    {
        $row = "Dr. A. B. Smith";
        $parsed = $this->parser->parseRow($row);

        $this->assertCount(1, $parsed);
        $this->assertSame('Dr', $parsed[0]['title']);
        $this->assertSame('A B', $parsed[0]['initial']);
        $this->assertSame('Smith', $parsed[0]['last_name']);
    }

    /**
     * @throws InvalidRowFormatException
     */
    public function testParseRowWithTrailingSpaces(): void
    {
        $row = "   Mr. John Doe   &   Mrs. Jane Doe   ";
        $parsed = $this->parser->parseRow($row);

        $this->assertCount(2, $parsed);
        $this->assertSame('John', $parsed[0]['first_name']);
        $this->assertSame('Jane', $parsed[1]['first_name']);
    }

    /**
     * @throws InvalidRowFormatException
     */
    public function testParseRowWithSpecialCharacters(): void
    {
        $row = "Dr. John O'Connor & Mrs. Jane-Anne Doe";
        $parsed = $this->parser->parseRow($row);

        $this->assertCount(2, $parsed);
        $this->assertSame("O'Connor", $parsed[0]['last_name']);
        $this->assertSame('Jane-Anne', $parsed[1]['first_name']);
    }

    /**
     * @throws InvalidRowFormatException
     */
    public function testParseRowWithMissingFirstName(): void
    {
        $row = "Mr. Smith";
        $parsed = $this->parser->parseRow($row);

        $this->assertCount(1, $parsed);
        $this->assertSame('Mr', $parsed[0]['title']);
        $this->assertNull($parsed[0]['first_name']);
        $this->assertSame('Smith', $parsed[0]['last_name']);
    }

    /**
     * @throws InvalidRowFormatException
     */
    public function testParseRowWithMiddleName(): void
    {
        $row = "Mr. John Michael Doe";
        $parsed = $this->parser->parseRow($row);

        $this->assertCount(1, $parsed);
        $this->assertSame('John', $parsed[0]['first_name']);
        $this->assertSame('Doe', $parsed[0]['last_name']);
    }

    /**
     * @throws InvalidRowFormatException
     */
    public function testParseRowWithInvalidTitle(): void
    {
        $row = "Engineer John Doe";
        $parsed = $this->parser->parseRow($row);

        $this->assertEmpty($parsed);
    }

    /**
     * @throws InvalidRowFormatException
     */
    public function testParseRowWithMoreThanTwoOwners(): void
    {
        $row = "Mr. Smith & Mrs. Johnson and Dr. Brown";
        $parsed = $this->parser->parseRow($row);

        $this->assertCount(3, $parsed);

        // First owner
        $this->assertSame('Mr', $parsed[0]['title']);
        $this->assertNull($parsed[0]['first_name']);
        $this->assertSame('Smith', $parsed[0]['last_name']);

        // Second owner
        $this->assertSame('Mrs', $parsed[1]['title']);
        $this->assertNull($parsed[1]['first_name']);
        $this->assertSame('Johnson', $parsed[1]['last_name']);

        // Third owner
        $this->assertSame('Dr', $parsed[2]['title']);
        $this->assertNull($parsed[2]['first_name']);
        $this->assertSame('Brown', $parsed[2]['last_name']);
    }

    /**
     * @throws InvalidRowFormatException
     */
    public function testParseRowWithMultipleOwnersFullNames(): void
    {
        $row = "Mr. John Doe & Mrs. Jane Smith and Dr. Emily Brown";
        $parsed = $this->parser->parseRow($row);

        $this->assertCount(3, $parsed);

        // Owner 1
        $this->assertSame('Mr', $parsed[0]['title']);
        $this->assertSame('John', $parsed[0]['first_name']);
        $this->assertSame('Doe', $parsed[0]['last_name']);

        // Owner 2
        $this->assertSame('Mrs', $parsed[1]['title']);
        $this->assertSame('Jane', $parsed[1]['first_name']);
        $this->assertSame('Smith', $parsed[1]['last_name']);

        // Owner 3
        $this->assertSame('Dr', $parsed[2]['title']);
        $this->assertSame('Emily', $parsed[2]['first_name']);
        $this->assertSame('Brown', $parsed[2]['last_name']);
    }

    public function testParseRowWithMultipleOwnersMissingLastNames(): void
    {
        $row = "Mr. John & Mrs. Jane and Dr. Emily";
        $parsed = $this->parser->parseRow($row);

        $this->assertCount(3, $parsed);

        // Owner 1
        $this->assertSame('Mr', $parsed[0]['title']);
        $this->assertSame('John', $parsed[0]['last_name']);
        $this->assertNull($parsed[0]['first_name']);

        // Owner 2
        $this->assertSame('Mrs', $parsed[1]['title']);
        $this->assertSame('Jane', $parsed[1]['last_name']);
        $this->assertNull($parsed[1]['first_name']);

        // Owner 3
        $this->assertSame('Dr', $parsed[2]['title']);
        $this->assertSame('Emily', $parsed[2]['last_name']);
        $this->assertNull($parsed[2]['first_name']);
    }

    /**
     * @throws InvalidRowFormatException
     */
    public function testParseRowWithMultipleOwnersInitials(): void
    {
        $row = "Dr. J. Doe & Mrs. A. B. Smith and Prof. C. Brown";
        $parsed = $this->parser->parseRow($row);

        $this->assertCount(3, $parsed);

        // Owner 1
        $this->assertSame('Dr', $parsed[0]['title']);
        $this->assertSame('J', $parsed[0]['initial']);
        $this->assertSame('Doe', $parsed[0]['last_name']);

        // Owner 2
        $this->assertSame('Mrs', $parsed[1]['title']);
        $this->assertSame('A B', $parsed[1]['initial']);
        $this->assertSame('Smith', $parsed[1]['last_name']);

        // Owner 3
        $this->assertSame('Prof', $parsed[2]['title']);
        $this->assertSame('C', $parsed[2]['initial']);
        $this->assertSame('Brown', $parsed[2]['last_name']);
    }

    /**
     * @throws InvalidRowFormatException
     */
    public function testParseRowWithMixedDelimitersAndSpecialCharacters(): void
    {
        $row = "Mr. O'Connor & Dr. Jane-Anne Smith and Mrs. Emily-Brown";
        $parsed = $this->parser->parseRow($row);

        $this->assertCount(3, $parsed);

        // Owner 1
        $this->assertSame('Mr', $parsed[0]['title']);
        $this->assertNull($parsed[0]['first_name']);
        $this->assertSame("O'Connor", $parsed[0]['last_name']);

        // Owner 2
        $this->assertSame('Dr', $parsed[1]['title']);
        $this->assertSame('Jane-Anne', $parsed[1]['first_name']);
        $this->assertSame('Smith', $parsed[1]['last_name']);

        // Owner 3
        $this->assertSame('Mrs', $parsed[2]['title']);
        $this->assertSame('Emily-Brown', $parsed[2]['last_name']);
        $this->assertNull($parsed[2]['first_name']);
    }


    /**
     * @throws InvalidRowFormatException
     */

    public function testParseRowWithTrailingDelimitersAndSpaces(): void
    {
        $row = "  Mr. John Doe   &   Mrs. Jane Smith and  Dr. Emily Brown   ";
        $parsed = $this->parser->parseRow($row);

        $this->assertCount(3, $parsed);

        // Owner 1
        $this->assertSame('Mr', $parsed[0]['title']);
        $this->assertSame('John', $parsed[0]['first_name']);
        $this->assertSame('Doe', $parsed[0]['last_name']);

        // Owner 2
        $this->assertSame('Mrs', $parsed[1]['title']);
        $this->assertSame('Jane', $parsed[1]['first_name']);
        $this->assertSame('Smith', $parsed[1]['last_name']);

        // Owner 3
        $this->assertSame('Dr', $parsed[2]['title']);
        $this->assertSame('Emily', $parsed[2]['first_name']);
        $this->assertSame('Brown', $parsed[2]['last_name']);
    }

    /**
     * @throws InvalidRowFormatException
     */
    public function testParseRowWithSingleInitialForFirstName(): void
    {
        $row = "Dr. J. Doe";
        $parsed = $this->parser->parseRow($row);
        $this->assertCount(1, $parsed);
        $this->assertSame('Dr', $parsed[0]['title']);
        $this->assertSame('J', $parsed[0]['initial']);
        $this->assertSame('Doe', $parsed[0]['last_name']);
    }

    /**
     * @throws InvalidRowFormatException
     */
    public function testParseRowWithMultipleInitialsForFirstName(): void
    {
        $row = "Dr. A. B. Smith";
        $parsed = $this->parser->parseRow($row);

        $this->assertCount(1, $parsed);
        $this->assertSame('Dr', $parsed[0]['title']);
        $this->assertSame('A B', $parsed[0]['initial']);
        $this->assertSame('Smith', $parsed[0]['last_name']);
    }

    /**
     * @throws InvalidRowFormatException
     */
    public function testParseRowWithInitialsForMiddleNames(): void
    {
        $row = "Mr. John A. B. Doe";
        $parsed = $this->parser->parseRow($row);

        $this->assertCount(1, $parsed);
        $this->assertSame('Mr', $parsed[0]['title']);
        $this->assertSame('John', $parsed[0]['first_name']);
        $this->assertSame('A B', $parsed[0]['initial']);
        $this->assertSame('Doe', $parsed[0]['last_name']);
    }

    /**
     * @throws InvalidRowFormatException
     */
    public function testParseRowWithInitialsAndExtraSpaces(): void
    {
        $row = "Prof. A.   B.  Smith";
        $parsed = $this->parser->parseRow($row);
        $this->assertCount(1, $parsed);
        $this->assertSame('Prof', $parsed[0]['title']);
        $this->assertSame('A B', $parsed[0]['initial']);
        $this->assertSame('Smith', $parsed[0]['last_name']);
    }

    public function testParseRowWithManyOwners(): void
    {
        $row = "Mr. John Doe & Mrs. Jane Smith and Dr. Emily Brown & Miss Sarah Connor and Prof. Michael Green";
        $parsed = $this->parser->parseRow($row);

        $this->assertCount(5, $parsed);

        // Owner 1
        $this->assertSame('Mr', $parsed[0]['title']);
        $this->assertSame('John', $parsed[0]['first_name']);
        $this->assertSame('Doe', $parsed[0]['last_name']);

        // Owner 2
        $this->assertSame('Mrs', $parsed[1]['title']);
        $this->assertSame('Jane', $parsed[1]['first_name']);
        $this->assertSame('Smith', $parsed[1]['last_name']);

        // Owner 3
        $this->assertSame('Dr', $parsed[2]['title']);
        $this->assertSame('Emily', $parsed[2]['first_name']);
        $this->assertSame('Brown', $parsed[2]['last_name']);

        // Owner 4
        $this->assertSame('Miss', $parsed[3]['title']);
        $this->assertSame('Sarah', $parsed[3]['first_name']);
        $this->assertSame('Connor', $parsed[3]['last_name']);

        // Owner 5
        $this->assertSame('Prof', $parsed[4]['title']);
        $this->assertSame('Michael', $parsed[4]['first_name']);
        $this->assertSame('Green', $parsed[4]['last_name']);
    }

    /**
     * @throws InvalidRowFormatException
     */
    public function testParseRowWithManyOwnersAndInitials(): void
    {
        $row = "Dr. J. Doe & Mrs. A. B. Smith and Prof. C. D. Brown & Mr. E. Connor and Miss F. Green";
        $parsed = $this->parser->parseRow($row);

        $this->assertCount(5, $parsed);

        // Owner 1
        $this->assertSame('Dr', $parsed[0]['title']);
        $this->assertSame('J', $parsed[0]['initial']);
        $this->assertSame('Doe', $parsed[0]['last_name']);

        // Owner 2
        $this->assertSame('Mrs', $parsed[1]['title']);
        $this->assertSame('A B', $parsed[1]['initial']);
        $this->assertSame('Smith', $parsed[1]['last_name']);

        // Owner 3
        $this->assertSame('Prof', $parsed[2]['title']);
        $this->assertSame('C D', $parsed[2]['initial']);
        $this->assertSame('Brown', $parsed[2]['last_name']);

        // Owner 4
        $this->assertSame('Mr', $parsed[3]['title']);
        $this->assertSame('E', $parsed[3]['initial']);
        $this->assertSame('Connor', $parsed[3]['last_name']);

        // Owner 5
        $this->assertSame('Miss', $parsed[4]['title']);
        $this->assertSame('F', $parsed[4]['initial']);
        $this->assertSame('Green', $parsed[4]['last_name']);
    }

    /**
     * @throws InvalidRowFormatException
     */
    public function testParseRowWithMultipleOwnersSharedLastName(): void
    {
        $row = "Mr and Mrs Smith and Dr. Emily Smith";
        $parsed = $this->parser->parseRow($row);

        $this->assertCount(3, $parsed);

        // Owner 1
        $this->assertSame('Mr', $parsed[0]['title']);
        $this->assertNull($parsed[0]['first_name']);
        $this->assertSame('Smith', $parsed[0]['last_name']);

        // Owner 2
        $this->assertSame('Mrs', $parsed[1]['title']);
        $this->assertNull($parsed[1]['first_name']);
        $this->assertSame('Smith', $parsed[1]['last_name']);

        // Owner 3
        $this->assertSame('Dr', $parsed[2]['title']);
        $this->assertSame('Emily', $parsed[2]['first_name']);
        $this->assertSame('Smith', $parsed[2]['last_name']);
    }


    /**
     * @throws InvalidRowFormatException
     */
    public function testParseRowWithManyOwnersSharingLastName(): void
    {
        $row = "Mr and Mrs Smith and Dr. Emily Smith & Prof. John Smith and Miss Sarah Smith";
        $parsed = $this->parser->parseRow($row);

        $this->assertCount(5, $parsed);

        // Owner 1
        $this->assertSame('Mr', $parsed[0]['title']);
        $this->assertNull($parsed[0]['first_name']);
        $this->assertSame('Smith', $parsed[0]['last_name']);

        // Owner 2
        $this->assertSame('Mrs', $parsed[1]['title']);
        $this->assertNull($parsed[1]['first_name']);
        $this->assertSame('Smith', $parsed[1]['last_name']);

        // Owner 3
        $this->assertSame('Dr', $parsed[2]['title']);
        $this->assertSame('Emily', $parsed[2]['first_name']);
        $this->assertSame('Smith', $parsed[2]['last_name']);

        // Owner 4
        $this->assertSame('Prof', $parsed[3]['title']);
        $this->assertSame('John', $parsed[3]['first_name']);
        $this->assertSame('Smith', $parsed[3]['last_name']);

        // Owner 5
        $this->assertSame('Miss', $parsed[4]['title']);
        $this->assertSame('Sarah', $parsed[4]['first_name']);
        $this->assertSame('Smith', $parsed[4]['last_name']);
    }


    /**
     * @throws InvalidRowFormatException
     */
    public function testParseRowWithMixedOwners(): void
    {
        $row = "Dr. Heckel Fessa & Mr and Mrs Smith";
        $parsed = $this->parser->parseRow($row);

        $this->assertCount(3, $parsed);

        // Owner 1
        $this->assertSame('Dr', $parsed[0]['title']);
        $this->assertSame('Heckel', $parsed[0]['first_name']);
        $this->assertSame('Fessa', $parsed[0]['last_name']);

        // Owner 2
        $this->assertSame('Mr', $parsed[1]['title']);
        $this->assertNull($parsed[1]['first_name']);
        $this->assertSame('Smith', $parsed[1]['last_name']);

        // Owner 3
        $this->assertSame('Mrs', $parsed[2]['title']);
        $this->assertNull($parsed[2]['first_name']);
        $this->assertSame('Smith', $parsed[2]['last_name']);
    }
}
