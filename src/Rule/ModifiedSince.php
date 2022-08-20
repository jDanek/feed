<?php declare(strict_types=1);

namespace Danek\FeedIo\Rule;

use Danek\FeedIo\Feed\NodeInterface;
use Danek\FeedIo\DateRuleAbstract;

class ModifiedSince extends DateRuleAbstract
{
    const NODE_NAME = 'pubDate';

    /**
     * @param NodeInterface $node
     * @param \DOMElement $element
     */
    public function setProperty(NodeInterface $node, \DOMElement $element): void
    {
        $node->setLastModified($this->getDateTimeBuilder()->convertToDateTime($element->nodeValue));
    }

    /**
     * @inheritDoc
     */
    protected function hasValue(NodeInterface $node): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    protected function addElement(\DOMDocument $document, \DOMElement $rootElement, NodeInterface $node): void
    {
        $date = is_null($node->getLastModified()) ? new \DateTime() : $node->getLastModified();

        $rootElement->appendChild($document->createElement(
            $this->getNodeName(),
            $date->format($this->getDefaultFormat())
        ));
    }
}
