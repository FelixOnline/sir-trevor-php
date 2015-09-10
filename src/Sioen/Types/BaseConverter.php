<?php

namespace Sioen\Types;

use \Michelf\Markdown;
use League\HTMLToMarkdown\HtmlConverter;

class BaseConverter implements ConverterInterface
{
    /**
     * The options we use for html to markdown
     *
     * @var array
     */
    protected $options = array(
        'header_style' => 'atx',
        'bold_style' => '__',
        'italic_style' => '_',
        'strip_tags' => true
    );

    public function toJson(\DOMElement $node)
    {
        $html = $node->ownerDocument->saveXML($node);
        $html = $this->htmlToMarkdown($html);
        $html = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x80-\x9F]/u', '', $html);

        $html = trim($html);

        if($html == '') {
            return null;
        }

        return array(
            'type' => 'text',
            'data' => array(
                'text' => ' ' . $html
            )
        );
    }

    public function toHtml(array $data)
    {
        return Markdown::defaultTransform($data['text']);
    }

    protected function htmlToMarkdown($html)
    {
        $converter = new HtmlConverter($this->options);
        $markdown = $converter->convert($html);
        return $markdown;
    }
}
