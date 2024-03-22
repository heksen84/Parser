<?php

require '../vendor/autoload.php'; // Include the Guzzle library

use GuzzleHttp\Client;

class HTMLParserResult {
    private $elements;

    public function __construct($elements) {
        $this->elements = $elements;
    }

    public function getElements() {
        return $this->elements;
    }
}

class HTMLElement {
    private $element;

    public function __construct($element) {
        $this->element = $element;
    }

    public function getHTML() {
        return $this->element->ownerDocument->saveHTML($this->element);
    }

    public function getAttribute($attribute) {
        return $this->element->getAttribute($attribute);
    }
}

class AsyncHTMLParser {
    public function selectElement($html, $selector) {
        $doc = new DOMDocument();
        @$doc->loadHTML($html);

        $xpath = new DOMXPath($doc);
        $elements = $xpath->query($selector);

        $selectedElements = [];
        foreach ($elements as $element) {
            $selectedElements[] = new HTMLElement($element);
        }

        return new HTMLParserResult($selectedElements);
    }

    public function parseHTML($html, $selector, $attribute = null) {
        $selectedElements = $this->selectElement($html, $selector);

        foreach ($selectedElements->getElements() as $element) {
            if ($attribute) {
                echo $element->getAttribute($attribute) . "\n";
            } else {
                echo $element->getHTML() . "\n";
            }
        }
    }
}

$url = 'http://example.com';
$selector = '.article'; // Example selector for selecting elements by class
$attribute = 'href'; // Example attribute to extract

$client = new Client();
$response = $client->get($url);
$html = $response->getBody()->getContents();

$htmlParser = new AsyncHTMLParser();
$htmlParser->parseHTML($html, $selector, $attribute);
