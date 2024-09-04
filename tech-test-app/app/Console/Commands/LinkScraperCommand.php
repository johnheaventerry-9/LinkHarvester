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
        $this->info('Fetching links from: ' . $url);

        $response = Http::withOptions(['verify' => false])->get($url);

        if ($response->successful()) {
            $this->info('Successfully fetched the page.');
            $html = $response->body();
            $content = $this->parseHtmlForBookmarks($html);

            $this->info('Found ' . $content->length . ' bookmark titles.');

            foreach ($content as $node) {
                $linkData = $this->extractLinkData($node);
                $linkTags = $this->extractTagsFromNode($node);

                if (!empty($linkTags)) {
                    $linkData['tags'] = $linkTags;
                    $this->saveLinkToDatabase($linkData);
                } else {
                    $this->info('No matching tags for link: ' . $linkData['url']);
                }
            }

            $this->info('Links fetched successfully.');
        } else {
            $this->error('Failed to fetch links');
        }
    }

    /**
     * Parses the HTML content and retrieves bookmark nodes (links).
     *
     * @param string $html The raw HTML content.
     * @return \DOMNodeList The list of nodes matching the bookmark links.
     */
    private function parseHtmlForBookmarks($html)
    {
        $dom = new \DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new \DOMXPath($dom);
        return $xpath->query("//a[contains(@class, 'bookmark_title')]");
    }

    /**
     * Extracts link data (title, URL, etc.) from the node.
     *
     * @param \DOMNode $node The DOM node representing a bookmark.
     * @return array The link data including title, URL, and live status.
     */
    private function extractLinkData($node)
    {
        return [
            'title' => trim($node->textContent),
            'url' => $node->getAttribute('href'),
            'comments' => '',
            'tags' => [],
            'is_live' => $this->isUrlLive($node->getAttribute('href'))
        ];
    }

    /**
     * Extracts tags from the parent node of the bookmark.
     * 
     * Cleans and filters text to find specific tags.
     *
     * @param \DOMNode $node The DOM node representing a bookmark.
     * @return array The array of matched tags.
     */
    private function extractTagsFromNode($node)
    {
        $tagsToCheck = ['laravel', 'vue', 'vue.js', 'php', 'api'];
        $parent = $node->parentNode;
        $possibleTagNode = $parent->textContent;

        // Clean and process the text for tag extraction
        $cleanedText = preg_replace('/\s+/', ' ', str_replace("\u{A0}", ' ', $possibleTagNode));
        $cleanedText = preg_replace('/copy to mine|[a-zA-Z]+\s+\d{4}/', '', $cleanedText);
        $cleanedText = strtolower($cleanedText);

        $splitText = explode(' ', trim($cleanedText));

        // Filter and return the tags that match the predefined list
        return array_values(array_filter($splitText, function ($text) use ($tagsToCheck) {
            return in_array($text, $tagsToCheck, true);
        }));
    }

    /**
     * Saves the link data into the database.
     *
     * @param array $linkData The link data including title, URL, tags, etc.
     */
    private function saveLinkToDatabase($linkData)
    {
        try {
            Link::updateOrCreate(['url' => $linkData['url']], $linkData);
            $this->info('Saved link to database: ' . $linkData['url']);
        } catch (\Exception $e) {
            $this->error('Failed to save link: ' . $linkData['url'] . ' - Error: ' . $e->getMessage());
        }
    }

    /**
     * Checks if the URL is live (HTTP 200 status).
     *
     * @param string $url The URL to check.
     * @return bool Whether the URL is live or not.
     */
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
