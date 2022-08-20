<?php declare(strict_types=1);

namespace Danek\FeedIo\Rule;

use Danek\FeedIo\Feed\Node\CategoryInterface;
use Danek\FeedIo\Feed\NodeInterface;
use Danek\FeedIo\RuleAbstract;

class Category extends RuleAbstract
{
    const NODE_NAME = 'category';

    /**
     * @param NodeInterface $node
     * @param \DOMElement $element
     * @return mixed
     */
    public function setProperty(NodeInterface $node, \DOMElement $element): void
    {
        $category = $node->newCategory();
        $category->setScheme($this->getAttributeValue($element, 'domain'))
            ->setLabel($element->nodeValue)
            ->setTerm($element->nodeValue);
        $node->addCategory($category);
    }

    /**
     * @inheritDoc
     */
    protected function hasValue(NodeInterface $node): bool
    {
        return !!$node->getCategories();
    }

    /**
     * @inheritDoc
     */
    protected function addElement(\DOMDocument $document, \DOMElement $rootElement, NodeInterface $node): void
    {
        foreach ($node->getCategories() as $category) {
            $rootElement->appendChild($this->createCategoryElement($document, $category));
        }
    }

    /**
     * @param \DOMDocument $document
     * @param CategoryInterface $category
     * @return \DOMElement
     */
    public function createCategoryElement(\DOMDocument $document, CategoryInterface $category): \DOMElement
    {
        $element = $document->createElement(
            $this->getNodeName(),
            is_null($category->getTerm()) ? $category->getLabel() : $category->getTerm()
        );
        if (!!$category->getScheme()) {
            $element->setAttribute('domain', $category->getScheme());
        }

        return $element;
    }
}
