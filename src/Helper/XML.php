<?php
/**
 * HHVM
 *
 * Copyright (C) Tony Yip 2015.
 *
 * @author   Tony Yip <tony@opensource.hk>
 * @license  http://opensource.org/licenses/GPL-3.0 GNU General Public License
 */

namespace Elearn\Foundation\Helper;

use DOMDocument;
use DOMXPath;
use Symfony\Component\CssSelector\CssSelector;

class XML
{

    /**
     * @var DOMDocument
     */
    private $dom;

    /**
     * @var \DOMElement
     */
    private $root;

    /**
     * @var array
     */
    private $namespaces = [];

    /**
     * @param string $version
     * @param string $encoding
     */
    public function __construct($version = '1.0', $encoding = 'UTF-8')
    {
        $this->dom = new DOMDocument($version, $encoding);
    }

    /**
     * Parse XML from string.
     *
     * @param string $source
     */
    public function parseString($source)
    {
        $this->dom->loadXML($source);
        $this->root = $this->dom->documentElement;
    }

    /**
     * Parse XML from file.
     *
     * @param string $source
     */
    public function parseFile($source)
    {
        $this->dom->load($source);
        $this->root = $this->dom->documentElement;
    }

    /**
     * Registers the namespace
     *
     * @param string $prefix
     * @param string $uri URI of namespace.
     */
    public function registerNamespace($prefix, $uri)
    {
        $this->namespaces[$prefix] = $uri;
    }

    /**
     * Evaluates the given XPath expression
     *
     * @param string $query XPath query
     * @param \DOMNode $context specified node for doing relative queries.
     *
     * @return \DOMNodeList matching node.
     */
    public function query($query, \DOMNode $context = null)
    {
        $xpath = new DOMXPath($this->dom);
        foreach ($this->namespaces as $prefix => $uri) {
            $xpath->registerNamespace($prefix, $uri);
        }

        $result = $xpath->query($query, $context);

        return $result;
    }

    /**
     * Evaluates the given CSS expression
     *
     * @param string $query css query
     * @param \DOMNode $context specified node for doing relative queries.
     *
     * @return \DOMNodeList matching node.
     */
    public function cssQuery($query, \DOMNode $context = null)
    {
        return $this->query(CssSelector::toXPath($query), $context);
    }

    /**
     * @return \DOMElement
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * @return DOMDocument
     */
    public function getDocument()
    {
        return $this->dom;
    }
}