<?php

namespace Sioen\Types;
use Sioen\Types\FelixImageConverter;

class DivConverter extends BaseConverter implements ConverterInterface
{
    public function toJson(\DOMElement $node)
    {
        if($node->getAttribute('id') == "imgCont") {
            // This only extracts images
            foreach($node->getElementsByTagName("img") as $image) {
                $converter = new FelixImageConverter();
                return $converter->toJson($node); // We pass node as we need to extract multiple components
            }
        } else {
            // Not handled here
            $converter = new BaseConverter();
            return $converter->toJson($node);
        }
    }

    public function toHtml(array $data)
    {
        return false; // This should never happen
    }
}
