<?php

namespace S25\PricesImporter;

use GuzzleHttp\Psr7\FnStream;
use GuzzleHttp\Psr7\MultipartStream;
use GuzzleHttp\Psr7\Utils;
use Psr\Http\Client\ClientExceptionInterface;
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

    public function check(string $destination): bool
    {
        $url = rtrim($this->endpoint, "/") .
            "/price-list?" .
            http_build_query([
                'api_key' => $destination
            ]);

        $request = $this->requestFactory
            ->createRequest('GET', $url)
            ->withHeader('Accept', 'application/json');

        $response = $this->client->sendRequest($request);

        return match ($response->getStatusCode()) {
            200 => true,
            404 => false,
            default => throw new \RuntimeException('HTTP: ' . $response->getReasonPhrase()),
        };
    }

    /**
     * @param string $destination
     * @param CsvFile $csv
     * @param string $source
     *
     * @return void
     *
     * @throws ClientExceptionInterface
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
            throw new \RuntimeException('HTTP: ' . $response->getReasonPhrase());
        }
    }
}
