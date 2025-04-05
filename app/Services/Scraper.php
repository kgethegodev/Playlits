<?php
namespace App\Services;

use Symfony\Component\Panther\Client;

class Scraper
{
    public static function scrape(string $url, string $look_for, string $wait_for): array
    {
        putenv('PATH=' . getenv('PATH') . ':' . $_SERVER['HOME'] . '/.cache/bdi/chromedriver');
        $client = Client::createChromeClient();

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
