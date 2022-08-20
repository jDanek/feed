<?php declare(strict_types=1);

namespace Danek\FeedIo\Rule;

use Danek\FeedIo\Feed\NodeInterface;
use Danek\FeedIo\RuleAbstract;
use Danek\FeedIo\RuleSet;

class Structure extends RuleAbstract
{
    const NODE_NAME = 'structure';

    /**
     * @var RuleSet
     */
    protected $ruleSet;

    /**
     * @param string|null $nodeName
     * @param RuleSet|null $ruleSet
     */
    public function __construct(string $nodeName = null, RuleSet $ruleSet = null)
    {
        parent::__construct($nodeName);

        $this->ruleSet = is_null($ruleSet) ? new RuleSet() : $ruleSet;
    }

    /**
     * @param NodeInterface $node
     * @param \DOMElement $element
     * @return mixed
     */
    public function setProperty(NodeInterface $node, \DOMElement $element): void
    {
        foreach ($element->childNodes as $domNode) {
            if ($domNode instanceof \DOMElement) {
                $rule = $this->ruleSet->get($domNode->tagName);
                $rule->setProperty($node, $domNode);
            }
        }
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
        $element = $document->createElement($this->getNodeName());

        /** @var RuleAbstract $rule */
        foreach ($this->ruleSet->getRules() as $rule) {
            $rule->apply($document, $element, $node);
        }

        $rootElement->appendChild($element);
    }
}
