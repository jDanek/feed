<?php declare(strict_types=1);

namespace Danek\FeedIo\Rule\Atom;

use Danek\FeedIo\Feed\NodeInterface;
use Danek\FeedIo\RuleAbstract;
use Danek\FeedIo\FeedInterface;

class Logo extends RuleAbstract
{
    // https://tools.ietf.org/html/rfc4287#section-4.2.8
    const NODE_NAME = 'logo';

    /**
     * @param NodeInterface $node
     * @param \DOMElement $element
     */
    public function setProperty(NodeInterface $node, \DOMElement $element): void
    {
        if ($node instanceof FeedInterface) {
            $node->setLogo($element->nodeValue);
        }
    }

    /**
     * @inheritDoc
     */
    protected function hasValue(NodeInterface $node): bool
    {
        return $node instanceof FeedInterface && !!$node->getLogo();
    }

    /**
     * @inheritDoc
     */
    protected function addElement(\DOMDocument $document, \DOMElement $rootElement, NodeInterface $node): void
    {
        if (!($node instanceof FeedInterface) || is_null($node->getLogo())) {
            return;
        }
        $rootElement->appendChild($document->createElement($this->getNodeName(), $node->getLogo()));
    }
}
