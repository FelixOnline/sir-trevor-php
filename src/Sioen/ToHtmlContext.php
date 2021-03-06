<?php

namespace Sioen;

use Sioen\Types\TwitterConverter;
use Sioen\Types\FactoidConverter;
use Sioen\Types\FelixImageConverter;
use Sioen\Types\BlockquoteConverter;
use Sioen\Types\HeadingConverter;
use Sioen\Types\IframeConverter;
use Sioen\Types\ImageConverter;
use Sioen\Types\ListConverter;
use Sioen\Types\BaseConverter;

class ToHtmlContext
{
    protected $converter = null;

    public function __construct($type)
    {
        switch ($type) {
            case 'heading':
                $this->converter = new HeadingConverter();
                break;
            case 'factoid':
                $this->converter = new FactoidConverter();
                break;
            case 'list':
            case 'ordered_list':
                $this->converter = new ListConverter();
                break;
            case 'quote':
                $this->converter = new BlockquoteConverter();
                break;
            case 'video':
                $this->converter = new IframeConverter();
                break;
            case 'image':
                $this->converter = new ImageConverter();
                break;
            case 'feliximage':
                $this->converter = new FelixImageConverter();
                break;
            case 'tweet':
                $this->converter = new TwitterConverter();
                break;
            default:
                $this->converter = new BaseConverter();
                break;
        }
    }

    public function getHtml(array $data)
    {
        return $this->converter->toHtml($data);
    }
}
