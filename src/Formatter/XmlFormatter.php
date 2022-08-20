<?php declare(strict_types=1);

namespace Danek\FeedIo\Formatter;

use Danek\FeedIo\Feed\NodeInterface;
use Danek\FeedIo\Feed\StyleSheet;
use Danek\FeedIo\FeedInterface;
use Danek\FeedIo\Rule\OptionalField;
use Danek\FeedIo\RuleSet;
use Danek\FeedIo\Standard\XmlAbstract;
use Danek\FeedIo\FormatterInterface;

/**
 * Turns a FeedInterface instance into a XML document.
 *
 * Depends on :
 *  - Danek\FeedIo\StandardAbstract
 *
 */
class XmlFormatter implements FormatterInterface
{

    /**
     * @var XmlAbstract
     */
    protected $standard;

    /**
     * @param XmlAbstract $standard
     */
    public function __construct(XmlAbstract $standard)
    {
        $this->standard = $standard;
    }

    /**
     * @param \DOMDocument $document
     * @param FeedInterface $feed
     * @return XmlFormatter
     */
    public function setHeaders(\DOMDocument $document, FeedInterface $feed): XmlFormatter
    {
        $rules = $this->standard->getFeedRuleSet();
        $mainElement = $this->standard->getMainElement($document);
        $this->buildElements($rules, $document, $mainElement, $feed);

        return $this;
    }

    /**
     * @param \DOMDocument $document
     * @param NodeInterface $node
     * @return XmlFormatter
     */
    public function addItem(\DOMDocument $document, NodeInterface $node): XmlFormatter
    {
        $domItem = $document->createElement($this->standard->getItemNodeName());
        $rules = $this->standard->getItemRuleSet();
        $this->buildElements($rules, $document, $domItem, $node);

        $this->standard->getMainElement($document)->appendChild($domItem);

        return $this;
    }

    /**
     * @param RuleSet $ruleSet
     * @param \DOMDocument $document
     * @param \DOMElement $rootElement
     * @param NodeInterface $node
     */
    public function buildElements(RuleSet $ruleSet, \DOMDocument $document, \DOMElement $rootElement, NodeInterface $node): void
    {
        $rules = $this->getAllRules($ruleSet, $node);

        foreach ($rules as $rule) {
            $rule->apply($document, $rootElement, $node);
        }
    }

    /**
     * @param RuleSet $ruleSet
     * @param NodeInterface $node
     * @return iterable
     */
    public function getAllRules(RuleSet $ruleSet, NodeInterface $node): iterable
    {
        $rules = $ruleSet->getRules();
        $optionalFields = $node->listElements();
        foreach ($optionalFields as $optionalField) {
            $rules[$optionalField] = new OptionalField($optionalField);
        }

        return $rules;
    }

    /**
     * @return \DOMDocument
     */
    public function getEmptyDocument(): \DOMDocument
    {
        return new \DOMDocument('1.0', 'utf-8');
    }

    /**
     * @return \DOMDocument
     */
    public function getDocument(): \DOMDocument
    {
        $document = $this->getEmptyDocument();

        return $this->standard->format($document);
    }

    /**
     * @param FeedInterface $feed
     * @return string
     */
    public function toString(FeedInterface $feed): string
    {
        $document = $this->toDom($feed);

        return $document->saveXML();
    }

    /**
     * @param FeedInterface $feed
     * @return \DOMDocument
     */
    public function toDom(FeedInterface $feed): \DOMDocument
    {
        $document = $this->getDocument();

        $this->setHeaders($document, $feed);
        $this->setItems($document, $feed);
        $this->setNS($document, $feed);
        $this->setStyleSheet($document, $feed);

        return $document;
    }

    public function setNS(\DOMDocument $document, FeedInterface $feed)
    {
        $firstChild = $document->firstChild;
        foreach ($feed->getNS() as $namespace => $dtd) {
            $firstChild->setAttributeNS(
                'http://www.w3.org/2000/xmlns/', // xmlns namespace URI
                'xmlns:' . $namespace,
                $dtd
            );
        }
    }

    /**
     * @param \DOMDocument $document
     * @param FeedInterface $feed
     * @return XmlFormatter
     */
    public function setItems(\DOMDocument $document, FeedInterface $feed): XmlFormatter
    {
        foreach ($feed as $item) {
            $this->addItem($document, $item);
        }

        return $this;
    }

    public function setStyleSheet(\DOMDocument $document, FeedInterface $feed): XmlFormatter
    {
        $styleSheet = $feed->getStyleSheet();
        if ($styleSheet instanceof StyleSheet) {
            $attributes = sprintf('type="%s" href="%s"', $styleSheet->getType(), $styleSheet->getHref());
            $xsl = $document->createProcessingInstruction('xml-stylesheet', $attributes);
            $document->insertBefore($xsl, $document->firstChild);
        }

        return $this;
    }
}
