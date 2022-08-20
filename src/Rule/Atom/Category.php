<?php declare(strict_types=1);

namespace Danek\FeedIo\Rule\Atom;

use Danek\FeedIo\Feed\Node\CategoryInterface;
use Danek\FeedIo\Feed\NodeInterface;

class Category extends \Danek\FeedIo\Rule\Category
{

    /**
     * @param NodeInterface $node
     * @param \DOMElement $element
     */
    public function setProperty(NodeInterface $node, \DOMElement $element): void
    {
        $category = $node->newCategory();
        $category->setScheme($this->getAttributeValue($element, 'scheme'))
            ->setLabel($this->getAttributeValue($element, 'label'))
            ->setTerm($this->getAttributeValue($element, 'term'));

        $node->addCategory($category);
    }

    /**
     * @param \DOMDocument $document
     * @param CategoryInterface $category
     * @return \DOMElement
     */
    public function createCategoryElement(\DOMDocument $document, CategoryInterface $category): \DOMElement
    {
        $element = $document->createElement($this->getNodeName());
        $this->setNonEmptyAttribute($element, 'scheme', $category->getScheme());
        $this->setNonEmptyAttribute($element, 'term', $category->getTerm());
        $this->setNonEmptyAttribute($element, 'label', $category->getLabel());

        return $element;
    }
}
