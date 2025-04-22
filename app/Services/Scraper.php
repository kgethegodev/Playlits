<?php
namespace App\Services;

use Illuminate\Support\Facades\Log;
use Symfony\Component\Panther\Client;

class Scraper
{
    public static function scrape(string $url, string $look_for, string $wait_for): array
    {
        $client = Client::createChromeClient(null, [
            '--headless',
            '--disable-gpu',
            '--no-sandbox',
            '--disable-dev-shm-usage',
            '--remote-debugging-port=9222',  // Add this
            '--disable-extensions',          // Add this
            '--disable-setuid-sandbox',      // Add this
            '--no-first-run',                // Add this
            '--no-zygote',                   // Add this
            '--single-process'               // Add this - important for some environments
        ]);
        $data = [];
        try {
            $client->request('GET', $url);

            // Wait for the element to appear
            $client->waitFor($wait_for);

            $crawler = $client->getCrawler();

            if ($crawler->filter($look_for)->count() === 0) {
                throw new \Exception('No elements found for query: ' . $look_for);
            }

            $crawler->filter($look_for)->each(function ($node) use (&$data) {
                info($node);
                $data[] = trim($node->text());
            });

        } catch (\Exception $e) {
            Log::error("Scraper error: " . $e->getMessage());
            throw new \RuntimeException("Failed to scrape $url", 0, $e);
        } finally {
            $client->quit();
        }

        return $data;
    }
}
