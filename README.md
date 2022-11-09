# Prices Importer
## Prerequisites
The Importer requires an external PSR7-compliant HTTP Client such as Guzzle.

## Example

```php

$csv = new \S25\PricePusher\CsvFile();

$csv->add('bransslug1', 'RAWPARTNUMBER1', 12.3 'CUR');
$csv->add('bransslug2', 'RAWPARTNUMBER2', 45.6 'REN');
$csv->add('bransslug3', 'RAWPARTNUMBER3', 78.9 'CYC');    
    
$importer = new \S25\PricesImporter\Importer(
    'http://service.url/import',
    new \GuzzleHttp\Client(),
    new \GuzzleHttpHttpFactory(),
);

$importer->send($csv);

```
