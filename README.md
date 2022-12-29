# Prices Importer
## Prerequisites
The Importer requires an external PSR7-compliant HTTP Client such as Guzzle.

## Example

```php

$csv = new \S25\PricesImporter\CsvFile();

$csv->add(['bransslug1', 'RAWPARTNUMBER1'], 12.3 'CUR');
$csv->add(['bransslug2', 'RAWPARTNUMBER2'], 45.6 'REN', 10);
$csv->add('guid3', 78.9 'CYC');
    
$importer = new \S25\PricesImporter\Importer(
    'http://service.url/import',
    new \GuzzleHttp\Client(),
    new \GuzzleHttpHttpFactory(),
);

try {
    $importer->send(
        'price-list-key',
        $csv,
        'Optional source filename'
    );
} catch (\RuntimeException $e) {
    // Something went wrong
}

```
