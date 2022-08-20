<?php declare(strict_types=1);

namespace Danek\FeedIo\Rule;

use Danek\FeedIo\Feed\ItemInterface;
use Danek\FeedIo\Feed\NodeInterface;
use Danek\FeedIo\RuleAbstract;

class Image extends RuleAbstract
{
    const NODE_NAME = 'image';

    public function setProperty(NodeInterface $node, \DOMElement $element): void
    {
        if ($node instanceof ItemInterface) {
            $media = new \Danek\FeedIo\Feed\Item\Media();
            $media->setUrl($element->textContent);
            $node->addMedia($media);
        }
    }

    protected function hasValue(NodeInterface $node): bool
    {
        return false;
    }

    protected function addElement(\DOMDocument $document, \DOMElement $rootElement, NodeInterface $node): void
    {
        throw new \RuntimeException("you should not try to write a <image> tag");
    }
}
