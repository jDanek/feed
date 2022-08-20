<?php declare(strict_types=1);

namespace Danek\FeedIo\Rule;

use Danek\FeedIo\Feed\ItemInterface;
use Danek\FeedIo\Feed\NodeInterface;

class Description extends TextAbstract
{
    const NODE_NAME = 'description';

    /**
     * @param NodeInterface $node
     * @param \DOMElement $element
     */
    public function setProperty(NodeInterface $node, \DOMElement $element): void
    {
        $node->setDescription($this->getProcessedContent($element, $node));
    }

    /**
     * @inheritDoc
     */
    protected function hasValue(NodeInterface $node): bool
    {
        if ($node instanceof ItemInterface) {
            return !!$node->getContent();
        }
        return !!$node->getDescription();
    }

    protected function addElement(\DOMDocument $document, \DOMElement $rootElement, NodeInterface $node): void
    {
        $description = '';
        if ($node instanceof ItemInterface) {
            $description = $node->getContent();
        } else {
            $description = $node->getDescription();
        }
        $element = $this->generateElement($document, $description);

        $rootElement->appendChild($element);
    }
}
