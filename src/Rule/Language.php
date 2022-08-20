<?php


namespace Danek\FeedIo\Rule;

use Danek\FeedIo\Feed\NodeInterface;
use Danek\FeedIo\FeedInterface;
use Danek\FeedIo\RuleAbstract;

class Language extends RuleAbstract
{
    const NODE_NAME = 'language';

    /**
     * @param NodeInterface $node
     * @param \DOMElement $element
     */
    public function setProperty(NodeInterface $node, \DOMElement $element): void
    {
        if ($node instanceof FeedInterface) {
            $node->setLanguage($element->nodeValue);
        }
    }

    /**
     * @inheritDoc
     */
    protected function hasValue(NodeInterface $node): bool
    {
        return $node instanceof FeedInterface && !!$node->getLanguage();
    }

    /**
     * creates the accurateDOMElement content according to the $item's property
     *
     * @param \DOMDocument $document
     * @param \DOMElement $rootElement
     * @param NodeInterface $node
     */
    public function addElement(\DOMDocument $document, \DOMElement $rootElement, NodeInterface $node): void
    {
        if (!($node instanceof FeedInterface) || is_null($node->getLanguage())) {
            return;
        }
        $rootElement->appendChild($document->createElement($this->getNodeName(), $node->getLanguage()));
    }
}
