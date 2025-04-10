<?php
namespace App\Services;

use Symfony\Component\Panther\Client;

class Scraper
{
    public static function scrape(string $url, string $look_for, string $wait_for): array
    {
        $client = Client::createChromeClient(__DIR__.'/../../drivers/chromedriver');

        try {
            $client->request('GET', $url);

            // Wait for the element to appear
            $client->waitFor($wait_for);

            $crawler = $client->getCrawler();

            if ($crawler->filter($look_for)->count() === 0) {
                throw new \Exception('No elements found for query: ' . $look_for);
            }
            $data = [];
            $crawler->filter($look_for)->each(function ($node) use (&$data) {
                $data[] = trim($node->text());
            });

        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage();
            $data = null;
        } finally {
            $client->quit();
        }

        return $data;
    }
}
