<?php

use PHPUnit\Framework\TestCase;

class CsvFileTest extends TestCase
{
    public function test(): void
    {
        $csvStream = new \S25\PricesImporter\CsvFile();
        $csvStream->add('suzuki', '123456789', 67.8, 'USD');
        $csvStream->add('toyota', '987654321012', 0.18, 'JPY');

        $expected =
            "brand,oem,price,valuta\n" .
            "suzuki,123456789,67.8,USD\n" .
            "toyota,987654321012,0.18,JPY\n";

        $csvContent = fread($csvStream->openStream(), 1024);

        $this->assertEquals($csvContent, $expected);
    }
}
