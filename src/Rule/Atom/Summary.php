<?php declare(strict_types=1);

namespace Danek\FeedIo\Rule\Atom;

use Danek\FeedIo\Feed\ItemInterface;
use Danek\FeedIo\Feed\NodeInterface;
use Danek\FeedIo\Rule\TextAbstract;

class Summary extends TextAbstract
{
    const NODE_NAME = 'summary';

    public function setProperty(NodeInterface $node, \DOMElement $element): void
    {
        if ($node instanceof ItemInterface) {
            $node->setSummary(
                $this->getProcessedContent($element, $node)
            );
        }
    }

    protected function addElement(\DOMDocument $document, \DOMElement $rootElement, NodeInterface $node): void
    {
        if ($node instanceof ItemInterface) {
            $rootElement->appendChild(
                $this->generateElement($document, $node->getSummary())
            );
        }
    }

    protected function hasValue(NodeInterface $node): bool
    {
        if ($node instanceof ItemInterface) {
            return !!$node->getSummary();
        }

        return false;
    }
}
