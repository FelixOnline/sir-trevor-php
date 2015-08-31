<?php

namespace Sioen\Types;

class TwitterConverter extends BaseConverter implements ConverterInterface
{
    public function toJson(\DOMElement $node)
    {
        $data = array();

        foreach ($node->childNodes as $child) {
            if ($child->nodeName == 'p') {
                $html = $child->ownerDocument->saveXML($child);
                $data['text'] = $html;

                continue;
            }

            if ($child->nodeName == 'a') {
                $link = $child->getAttribute('href');
                $datetime = $child->getAttribute('data-datetime');
                $data['status_url'] = $link;
                $data['created_at'] = $datetime;

                continue;
            }
        }

        // we use the remaining html to create the remaining text
        $user = $node->ownerDocument->saveXML($node);
        $user = strip_tags(preg_replace('/<(\/|)blockquote( class="twitter-tweet" align="center")?>/i', '', $html));

        // We now just have the bit in the middle
        $user = explode("(@", $user);
        $name = $user[0];
        $handle = $user[1];

        $handle = trim(str_replace(")", $handle));

        $name = trim(str_replace("—", "", $name));

        $data['user'] = array();
        $data['name'] = $name;
        $data['screen_name'] = $handle;

        return array(
            'type' => 'tweet',
            'data' => $data
        );

    }

    public function toHtml(array $data)
    {
        return '<blockquote class="twitter-tweet" align="center"><p>' . $data['text'] . '</p>
        &mdash; '.$data['user']['name'].' (@'.$data['user']['screen_name'].')
        <a href="'.$data['status_url'].'" data-datetime="'.$data['created_at'].'">'.$data['created_at'].'</a></blockquote>' . "\n";
    }
}