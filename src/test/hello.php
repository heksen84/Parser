<?php

/*require '../vendor/autoload.php'; // Include the Guzzle library

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
$htmlParser->parseHTML($html, $selector, $attribute);*/

class HTMLParser {
    public function selectElement($html, $selector) {
        $matches = [];

        $type = substr($selector, 0, 1); // Get the first character of the selector

        switch ($type) {
            case '#':
                $selector = 'id="' . substr($selector, 1) . '"';
                break;
            case '.':
                $selector = 'class="' . substr($selector, 1) . '"';
                break;
            default:
                $selector = $selector; // Keep the selector as is for other types
        }

        if (preg_match_all('/<[^>]*' . $selector . '[^>]*>(.*?)<\/[^>]*>/s', $html, $matches)) {
            return new HTMLParserResult($matches[0]);
        } else {
            return new HTMLParserResult([]);
        }
    }

    public function parseURL($url, $selector, $attribute = null) {
        $html = file_get_contents($url);

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

class HTMLParserResult {
    private $elements;

    public function __construct($elements) {
        $this->elements = [];
        foreach ($elements as $element) {
            $this->elements[] = new HTMLElement($element);
        }
    }

    public function getElements() {
        return $this->elements;
    }
}

class HTMLElement {
    private $html;

    public function __construct($html) {
        $this->html = $html;
    }

    public function getHTML() {
        return $this->html;
    }

    public function getAttribute($attribute) {
        preg_match('/' . $attribute . '="(.*?)"/', $this->html, $attrMatches);
        if (isset($attrMatches[1])) {
            return $attrMatches[1];
        } else {
            return null;
        }
    }
}

$url = 'https://kolesa.kz';

$selector = 'a'; // Example selector to choose elements by class
$attribute = 'href'; // Example attribute to extract

$htmlParser = new HTMLParser();
$htmlParser->parseURL($url, $selector, $attribute);
