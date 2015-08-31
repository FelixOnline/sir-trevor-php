<?php

namespace Sioen\Types;

use \Michelf\Markdown;

class FactoidConverter extends BaseConverter implements ConverterInterface
{
    public function toJson(\DOMElement $node)
    {
        // check if the quote contains a cite
        $cite = '';

        foreach ($node->childNodes as $child) {
            // if it contains a 'cite' node, we should add it in the cite property
            if ($child->nodeName == 'cite') {
                $html = $child->ownerDocument->saveXML($child);
                $html = preg_replace('/<(\/|)cite>/i', '', $html);
                $child->parentNode->removeChild($child);
                $cite = ' ' . $this->htmlToMarkdown($html);
            }
        }

        // we use the remaining html to create the remaining text
        $html = $node->ownerDocument->saveXML($node);
        $html = preg_replace('/<(\/|)blockquote( class="factoid")?>/i', '', $html);

        return array(
            'type' => 'factoid',
            'data' => array(
                'text' => ' ' . $this->htmlToMarkdown($html),
                'description' => $cite
            )
        );
    }

    public function toHtml(array $data)
    {
        $text = $data['text'];
        $html = '<blockquote class="factoid">';
        $html .= Markdown::defaultTransform($text);

        // Add the description as a citation if necessary
        if (isset($data['description']) && !empty($data['description'])) {
            // remove the indent thats added by Sir Trevor
            $html .= '<cite>' . $data['description'] . '</cite>';
        }

        $html .= '</blockquote>';

        return $html;
    }
}
