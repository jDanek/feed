<?php declare(strict_types=1);

namespace Danek\FeedIo;

use Danek\FeedIo\Feed\NodeInterface;

abstract class RuleAbstract
{
    const NODE_NAME = 'node';

    /**
     * @var string
     */
    protected $nodeName;

    /**
     * @param string|null $nodeName
     */
    public function __construct(string $nodeName = null)
    {
        $this->nodeName = is_null($nodeName) ? static::NODE_NAME : $nodeName;
    }

    /**
     * @return string
     */
    public function getNodeName(): string
    {
        return $this->nodeName;
    }

    /**
     * @param \DOMElement $element
     * @param string $name
     * @return string|null
     */
    public function getAttributeValue(\DOMElement $element, $name): ?string
    {
        if ($element->hasAttribute($name)) {
            return $element->getAttribute($name);
        }

        return null;
    }

    /**
     * @param \DOMElement $element
     * @param string $name
     * @param string|null $ns
     * @return string|null
     */
    public function getChildValue(\DOMElement $element, string $name, ?string $ns = null): ?string
    {
        if ($ns === null) {
            $list = $element->getElementsByTagName($name);
        } else {
            $list = $element->getElementsByTagNameNS($ns, $name);
        }
        if ($list->length > 0) {
            return $list->item(0)->nodeValue;
        }

        return null;
    }

    /**
     * @param \DOMElement $element
     * @param string $child_name
     * @param string $attribute_name
     * @param string|null $ns
     * @return string|null
     */
    public function getChildAttributeValue(\DOMElement $element, string $child_name, string $attribute_name, ?string $ns = null): ?string
    {
        if ($ns === null) {
            $list = $element->getElementsByTagName($child_name);
        } else {
            $list = $element->getElementsByTagNameNS($ns, $child_name);
        }
        if ($list->length > 0) {
            return $list->item(0)->getAttribute($attribute_name);
        }

        return null;
    }


    /**
     * adds the accurateDOMElement content according to the node's property
     *
     * @param \DOMDocument $document
     * @param \DOMElement $rootElement
     * @param NodeInterface $node
     */
    public function apply(\DOMDocument $document, \DOMElement $rootElement, NodeInterface $node): void
    {
        if ($this->hasValue($node)) {
            $this->addElement($document, $rootElement, $node);
        }
    }

    /**
     * Sets the attribute only if the value is not empty
     * @param \DOMElement $element
     * @param string $name
     * @param string|null $value
     */
    protected function setNonEmptyAttribute(\DOMElement $element, string $name, string $value = null): void
    {
        if (!is_null($value)) {
            $element->setAttribute($name, $value);
        }
    }

    /**
     * Appends a child node only if the value is not null
     * @param \DOMDocument $document
     * @param \DOMElement $element
     * @param string $name
     * @param string|null $value
     */
    protected function appendNonEmptyChild(\DOMDocument $document, \DOMElement $element, string $name, string $value = null): void
    {
        if (!is_null($value)) {
            $element->appendChild($document->createElement($name, $value));
        }
    }

    /**
     * Sets the accurate $item property according to theDOMElement content
     *
     * @param NodeInterface $node
     * @param \DOMElement $element
     */
    abstract public function setProperty(NodeInterface $node, \DOMElement $element): void;

    /**
     * Tells if the node contains the expected value
     *
     * @param NodeInterface $node
     * @return bool
     */
    abstract protected function hasValue(NodeInterface $node): bool;

    /**
     * Creates and adds the element(s) to the document's rootElement
     *
     * @param \DOMDocument $document
     * @param \DOMElement $rootElement
     * @param NodeInterface $node
     */
    abstract protected function addElement(\DOMDocument $document, \DOMElement $rootElement, NodeInterface $node): void;
}
