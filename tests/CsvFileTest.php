<?php

use PHPUnit\Framework\TestCase;

class CsvFileTest extends TestCase
{
    public function test(): void
    {
        $csvStream = new \S25\PricesImporter\CsvFile();
        $csvStream->add(['suzuki', '123456789'], 67.8, 'USD');
        $csvStream->add(['toyota', '987654321012'], 0.18, 'JPY');
        $csvStream->add('used-parts-guid-987654321012', 0.18, 'JPY');

        $expected =
            "brand,number,price,currency,pieces\n" .
            "suzuki,123456789,67.8,USD,1\n" .
            "toyota,987654321012,0.18,JPY,1\n" .
            ",used-parts-guid-987654321012,0.18,JPY,1\n";

        $csvContent = fread($csvStream->openStream(), 1024);

        $this->assertEquals($csvContent, $expected);
    }
}
