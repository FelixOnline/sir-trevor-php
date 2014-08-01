<?php

namespace Sioen\Types;

use \Michelf\Markdown;

class ImageConverter extends BaseConverter implements ConverterInterface
{
    public function toJson(\DOMElement $node)
    {
        return array(
            'type' => 'image',
            'data' => array(
                'file' => array(
                    'url' => $node->getAttribute('src')
                )
            )
        );
    }

    public function toHtml(array $data)
    {
        return '<img src="' . $data['file']['url'] . '" />' . "\n";
    }
}
