<?php declare(strict_types=1);

namespace Danek\FeedIo\Rule\Atom;

use Danek\FeedIo\Feed\ItemInterface;
use Danek\FeedIo\Feed\NodeInterface;
use Danek\FeedIo\RuleAbstract;
use Danek\FeedIo\RuleSet;

class LinkNode extends RuleAbstract
{
    const NODE_NAME = 'link';

    /**
     * @var RuleSet
     */
    protected $ruleSet;

    /**
     * @param string|null $nodeName
     */
    public function __construct(string $nodeName = null)
    {
        parent::__construct($nodeName);
        $mediaRule = new Media();
        $mediaRule->setUrlAttributeName('href');
        $this->ruleSet = new RuleSet(new Link('related'));
        $this->ruleSet->add($mediaRule, ['media', 'enclosure']);
    }

    /**
     * @param NodeInterface $node
     * @param \DOMElement $element
     * @return mixed
     */
    public function setProperty(NodeInterface $node, \DOMElement $element): void
    {
        if ($element->hasAttribute('rel')) {
            $this->ruleSet->get($element->getAttribute('rel'))->setProperty($node, $element);
        } else {
            $this->ruleSet->getDefault()->setProperty($node, $element);
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
        if ($node instanceof ItemInterface && $node->hasMedia()) {
            $this->ruleSet->get('media')->apply($document, $rootElement, $node);
        }

        $this->ruleSet->getDefault()->apply($document, $rootElement, $node);
    }
}
