<?php

namespace S25\PricesImporter;

use GuzzleHttp\Psr7\FnStream;
use GuzzleHttp\Psr7\MultipartStream;
use GuzzleHttp\Psr7\Utils;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;

class Importer
{
    public function __construct(
        protected string $endpoint,
        protected ClientInterface $client,
        protected RequestFactoryInterface $requestFactory,
    ) {
    }

    /**
     * @param string $destination
     * @param CsvFile $csv
     * @param string $source
     *
     * @return void
     *
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function send(string $destination, CsvFile $csv, string $source): void
    {
        $url = rtrim($this->endpoint, "/") . "/price-list";

        $multipartData = [
            [
                'name'     => 'api_key',
                'contents' => $destination,
            ],
            [
                'name'     => 'separator',
                'contents' => $csv->getSeparator(),
            ],
            [
                'name'     => 'file',
                'contents' => FnStream::decorate(
                    Utils::streamFor($csv->openStream()),
                    ['getMetadata' => fn() => "{$source}.csv"]
                ),
            ],
        ];

        $request = $this->requestFactory
            ->createRequest('POST', $url)
            ->withHeader('Accept', 'application/json')
            ->withBody(new MultipartStream($multipartData));

        $response = $this->client->sendRequest($request);

        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException($response->getReasonPhrase());
        }
    }
}
