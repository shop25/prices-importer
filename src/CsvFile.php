<?php

namespace S25\PricesImporter;

class CsvFile
{
    protected string $filename;

    /** @var resource|false */
    protected $file = false;

    public function __construct(
        protected string $separator = ',',
    ) {
        // Create a temporary csv file

        $this->filename = tempnam(sys_get_temp_dir(), "prices-buffer-");

        if (!$this->filename) {
            throw new \RuntimeException("Temporary file creation failed");
        }

        // Open the file for writing only
        $this->file = fopen($this->filename, 'wb');

        if (!$this->file) {
            throw new \RuntimeException('Temporary file creation failed');
        }

        // Write the header right away
        $this->put(['brand', 'number', 'price', 'currency', 'pieces']);
    }

    /**
     * @param array{string, string}|string $product - [$brandSlug, $rawNumber] | $guid
     * @param float $price
     * @param string $currencyCode
     * @param int $piecesPerPack
     * @return void
     */
    public function add(string|array $product, float $price, string $currencyCode, int $piecesPerPack = 1): void {
        [$brandSlug, $rawNumber] = is_array($product) ? $product : ['', $product];
        $this->put([$brandSlug, $rawNumber, $price, $currencyCode, $piecesPerPack]);
    }

    /**
     * @return string
     */
    public function getSeparator(): string
    {
        return $this->separator;
    }

    /**
     * It's possible to open multiple streams
     * @return resource
     */
    public function openStream()
    {
        $stream = fopen($this->filename, 'rb');

        if ($stream === false) {
            throw new \RuntimeException("A stream couldn't be opened");
        }

        return $stream;
    }

    protected function put(array $data): void
    {
        if (fputcsv($this->file, $data, $this->separator) === false) {
            throw new \RuntimeException('Writing to the file failed');
        }
    }
}
