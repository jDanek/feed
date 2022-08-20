<?php declare(strict_types=1);

namespace Danek\FeedIo\Rule;

use Danek\FeedIo\Feed\NodeInterface;
use Danek\FeedIo\RuleAbstract;

class PublicId extends RuleAbstract
{
    const NODE_NAME = 'guid';

    /**
     * @param NodeInterface $node
     * @param \DOMElement $element
     */
    public function setProperty(NodeInterface $node, \DOMElement $element): void
    {
        $node->setPublicId($element->nodeValue);
    }

    /**
     * @inheritDoc
     */
    protected function hasValue(NodeInterface $node): bool
    {
        return !!$node->getPublicId();
    }

    /**
     * @inheritDoc
     */
    protected function addElement(\DOMDocument $document, \DOMElement $rootElement, NodeInterface $node): void
    {
        $rootElement->appendChild($document->createElement($this->getNodeName(), $node->getPublicId()));
    }
}
