<?php

namespace App\Console\Commands;

use App\Models\Link;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class LinkScraperCommand extends Command
{
    protected $signature = 'fetch:links';

    protected $description = 'Fetch and process links from Pinboard';

    public function handle()
    {
        $url = 'https://pinboard.in/u:alasdairw?per_page=120';

        // Log that we are starting the request
        $this->info('Fetching links from: ' . $url);

        // Fetch HTML via HTTP client with SSL verification disabled
        $response = Http::withOptions(['verify' => false])->get($url);

        // Log the status of the request
        if ($response->successful()) {
            $this->info('Successfully fetched the page.');
            $html = $response->body();

            // Log the start of the DOM processing
            $this->info('Processing HTML.');

            // Use DOMDocument to parse HTML
            $dom = new \DOMDocument();
            @$dom->loadHTML($html);
            $xpath = new \DOMXPath($dom);

            // Define tags to filter
            $tagsToCheck = ['laravel', 'vue', 'vue.js', 'php', 'api'];

            // Query for the bookmark links
            $nodes = $xpath->query("//a[contains(@class, 'bookmark_title')]");

            // Log the number of nodes found
            $this->info('Found ' . $nodes->length . ' bookmark titles.');

            foreach ($nodes as $node) {

                $linkData = [
                    'title' => trim($node->textContent),
                    'url' => $node->getAttribute('href'),
                    'comments' => '', // Add comments parsing if needed
                    'tags' => [],
                    'is_live' => $this->isUrlLive($node->getAttribute('href'))
                ];

                // Get the parent node text (contains tags)
                $parent = $node->parentNode;
                $possibleTagNode = $parent->textContent;

                // Replace non-breaking spaces with normal spaces and clean the text
                $cleanedText = preg_replace('/\s+/', ' ', str_replace("\u{A0}", ' ', $possibleTagNode));
                $cleanedText = preg_replace('/copy to mine|[a-zA-Z]+\s+\d{4}/', '', $cleanedText); // Remove copy-to-mine and dates
                $cleanedText = strtolower($cleanedText); // Convert all text to lowercase
                dump('Cleaned Text:', $cleanedText); // Inspect the cleaned text

                // Split the cleaned text and search for tags
                $splitText = explode(' ', trim($cleanedText));

                // Extract the matching tags strictly (only exact matches for tags we're looking for)
                $linkTags = array_filter($splitText, function ($text) use ($tagsToCheck) {
                    return in_array($text, $tagsToCheck, true); // Strict comparison for exact match
                });

                // Reset the array keys to avoid saving with numeric keys
                $linkTags = array_values($linkTags);

                // Log the found tags for debugging purposes
                dump('Filtered Tags:', $linkTags); // Inspect the extracted tags

                if (!empty($linkTags)) {
                    $linkData['tags'] = $linkTags;

                    // Log tags and saving to database
                    $this->info('Tags matched: ' . implode(', ', $linkTags));
                    $this->info('Attempting to save link: ' . $linkData['url']);

                    // Save to database
                    try {
                        Link::updateOrCreate(
                            ['url' => $linkData['url']],
                            $linkData
                        );
                        $this->info('Saved link to database: ' . $linkData['url']);
                    } catch (\Exception $e) {
                        $this->error('Failed to save link: ' . $linkData['url'] . ' - Error: ' . $e->getMessage());
                    }
                } else {
                    $this->info('No matching tags for link: ' . $linkData['url']);
                }
            }

            $this->info('Links fetched and processed successfully.');
        } else {
            $this->error('Failed to fetch links from Pinboard. Status Code: ' . $response->status());
        }
    }

    private function isUrlLive($url)
    {
        try {
            $response = @get_headers($url);
            return $response && strpos($response[0], '200') !== false;
        } catch (\Exception $e) {
            return false;
        }
    }
}
