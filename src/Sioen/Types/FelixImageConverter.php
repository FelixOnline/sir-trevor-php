<?php

namespace Sioen\Types;

class FelixImageConverter extends BaseConverter implements ConverterInterface
{
    public function toJson(\DOMElement $node)
    {
        // Assume there is only one image
        foreach($node->getElementsByTagName("img") as $image) {
            $imageName = $image->getAttribute('src');

            // Thankfully image formats are quite repetative so we can use basic functions to extract the filename
            $imageName = str_replace("/inc/timthumb.php?src=/", "", $imageName);
            $imageName = explode('&', $imageName);
            $imageName = $imageName[0];

            // Now we need to find the image
            $manager = new \FelixOnline\Core\BaseManager();

            $images = \FelixOnline\Core\BaseManager::build('FelixOnline\Core\Image', 'image');
            $images->filter('uri = "%s"', array($imageName));

            $images = $images->values();

            if(!is_array($images) || count($images) == 0) {
                return false; // Image doesnt exist so no point in including it
            }

            foreach($images as $image) {
                $imageId = $image->getId();
            }
        }

        $caption = '';
        $attrib = '';
        $attribLink = '';

        // Caption
        foreach($node->getElementsByTagName("div") as $div) {
            if($div->getAttribute('id') != 'imageCaption') {
                continue;
            }

            // Extract the caption up until the next div
            foreach($div->childNodes as $childNode) {
                if ($childNode->nodeType != XML_TEXT_NODE) {
                    continue;
                }
                $caption .= preg_replace('/( -$)/', '', trim($childNode->wholeText)); // Trim some fluff off the end
            }

            // Now get the attribution
            foreach($div->getElementsByTagName("div") as $div2) {
                if($div2->getAttribute('id') != 'imageAttr') {
                    continue;
                }

                // This is the attribution
                $html = $div2->ownerDocument->saveHTML($div2);

                if(count($div2->getElementsByTagName("a")) > 0) {
                    // If there are any A tags there is a link
                    foreach($div2->getElementsByTagName("a") as $a) {
                        $attribLink = $a->getAttribute('href');
                        $attrib = trim(strip_tags($a->ownerDocument->saveHTML($a)));
                    }
                } else {
                    $attrib = trim($html);
                }
            }
        }

        // NB: While we can extract the attribution and stuff from the DB, it is best to do it from the DOM as the user may have changed it

        return array(
            'type' => 'feliximage',
            'data' => array(
                'image' => $imageId,
                'caption' => $caption,
                'attribution' => $attrib,
                'attributionLink' => $attribLink
            )
        );
    }

    public function toHtml(array $data)
    {
        try {
            $image = new \FelixOnline\Core\Image($data['image']);
        } catch(\Exception $e) {
            return '';
        }

        if($data['attributionLink'] != '') {
            $attr = '<a href="'.$data['attributionLink'].'">'.$data['attribution'].'</a>';
        } else {
            $attr = $data['attribution'];
        }

        return '<div id="imgCont">
    <img alt="'.$caption.'" class="horizontal" src="'.$image->getURL().'" />
    <div id="imageCaption">
        '.$data['caption'].'
        <div id="imageAttr">'.$attr.'</div>
    </div>
</div>'."\n";
    }
}
