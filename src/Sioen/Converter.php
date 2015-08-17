<?php

namespace Sioen;

/**
 * Class Converter
 *
 * A Sir Trevor to HTML conversion helper for PHP
 *
 * @version 1.1.0
 * @author Wouter Sioen <wouter@woutersioen.be>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 */
class Converter
{
    /**
     * Converts the outputted json from Sir Trevor to html
     *
     * @param  string $json
     * @return string
     */
    public function toHtml($json)
    {
        // convert the json to an associative array
        $input = json_decode($json, true);
        $html = '';

        // loop trough the data blocks
        foreach ($input['data'] as $block) {
            $toHtmlContext = new ToHtmlContext($block['type']);
            $html .= $toHtmlContext->getHtml($block['data']);
        }

        return $html;
    }

    /**
     * Converts html to the json Sir Trevor requires
     *
     * @param  string $html
     * @return string The json string
     */
    public function toJson($html)
    {
        // Strip white space between tags to prevent creation of empty #text nodes
        $html = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $html);
        $html = preg_replace('~>\s+<~', '><', $html);
        $document = new \DOMDocument();

        // Load UTF-8 HTML hack (from http://bit.ly/pVDyCt)
        $document->loadHTML('<?xml encoding="UTF-8">' . $html);
        $document->encoding = 'UTF-8';

        // fetch the body of the document. All html is stored in there
        $body = $document->getElementsByTagName("body")->item(0);

        $data = array();

        // loop trough the child nodes and convert them
        if ($body) {
            foreach ($body->childNodes as $node) {
                if (get_class($node) == "DOMComment") {
                    // Comments cannot be converted to JSON
                    continue;
                }

                if (get_class($node) == "DOMText") {
                    // Comments cannot be converted to JSON
                    continue;
                }

                $toJsonContext = new ToJsonContext($node->nodeName);
                $converted = $toJsonContext->getData($node);

                if(key($converted) != 'type') { // Assume we have an array of converted items
                    foreach($converted as $item) {
                        $data[] = $item;
                    }
                } else {
                    $data[] = $converted;
                }
            }
        }

        return json_encode(array('data' => $data));
    }
}
