<?php

class InsertHelper
{
    private const INVALID_ITEMS_AFTER = ['table', 'ul', 'ol'];
    private const INVALID_ITEMS_BEFORE = ['h1', 'h2', 'h3'];
    private const MIN_PARAGRAPH_COUNT = 7;
    private const MIN_PARAGRAPH_LENGTH = 30;

    public $content;
    public $bannerHtml;
    public $paragraphId = 0;
    public $adaptiveIndex = null;

    private $xpath;

    public $query = '//*[not(self::td)][not(self::th)][not(self::li)][not(self::blockquote)]/p[string-length( text()) > ' . self::MIN_PARAGRAPH_LENGTH . ']';

    public function run()
    {
        if(!empty($this->adaptiveIndex)) {
            $this->paragraphId = $this->getParagraphId();
        }

        $doc = new \DOMDocument();
        libxml_use_internal_errors(true);

        $doc->loadHTML('<?xml encoding="UTF-8">' . $this->content, LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();
        $this->xpath = new \DomXpath($doc);

        $paragraphs = $this->xpath->query($this->query);

        $par1 = $this->paragraphId;
        $par2 = $paragraphs->length;

        $paragraph = $paragraphs->item($this->paragraphId);
        $newDiv = $doc->createElement('div');

        if($paragraph && (($par2 - $par1) > 2)) {
            self::insertNewElement($paragraph, $newDiv);
        }
        
        $result = $doc->saveHTML();
        $result = str_replace('<?xml encoding="UTF-8">', '', $result);
    
        $result_encode = html_entity_decode($result);
        $result_encode = preg_replace('~<(?:!DOCTYPE|/?(?:html|head|body))[^>]*>\s*~i', '', $result_encode);    

        return $result_encode;
    }

    /**
     * @param  \DOMElement $paragraph
     * @param  \DOMElement $newDiv
     * @return object
     */
    private function insertNewElement(\DOMElement $paragraph, \DOMElement $newDiv)
    {
        $insertPlace = $paragraph;
        $previousElement = $this->xpath->query("preceding-sibling::*[1]", $paragraph)->item(0);

        if(!empty($previousElement)) {
            $containsInvalidItemsBefore = in_array($previousElement->tagName, self::INVALID_ITEMS_BEFORE);

            if($containsInvalidItemsBefore) {
                $insertPlace = $previousElement;
            }
            else {
                $nextElement = $this->xpath->query("following-sibling::*[1]", $paragraph)->item(0);
                if(!empty($nextElement) && !in_array($nextElement->tagName, self::INVALID_ITEMS_AFTER)) {
                    $insertPlace = $paragraph->nextSibling;
                }
            }
        }

        if($this->paragraphId === 0) {
            $insertPlace = $paragraph->nextSibling;
        }
        
        $paragraph->parentNode->insertBefore($newDiv, $insertPlace);
        self::appendHTML($newDiv, $newDiv->firstChild);

        return $this;
    }

    /**
     * @param \DOMElement $parent
     * @param \DOMElement|null $place
     * @return object
     */
    private function appendHTML(\DOMElement $parent, \DOMElement $place = null) {
        $tmpDoc = new \DOMDocument();
        libxml_use_internal_errors(true);
        $tmpDoc->loadHTML('<?xml encoding="UTF-8">' . $this->bannerHtml);
        libxml_clear_errors();

        foreach ($tmpDoc->getElementsByTagName('body')->item(0)->childNodes as $node) {
            $node = $parent->ownerDocument->importNode($node, true);
            $parent->insertBefore($node, $place);
        }

        return $this;
    }

     /**
     * @return int
     */
    private function getParagraphId() {
        $doc = new \DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="UTF-8">' . $this->content);
        libxml_clear_errors();
        $xpath = new \DomXpath($doc);

        $paragraphs = $xpath->query($this->query);

        $lengthText = 0;
        $counter = 0;

        foreach ($paragraphs as $paragraph) {
            $value = $paragraph->nodeValue;
            $paragraph->lenValue = mb_strlen($value);

            if(!empty($value) && $paragraph->lenValue > self::MIN_PARAGRAPH_LENGTH)
            {
                $lengthText += $paragraph->lenValue;
                $counter++;
            }
        }

        $middle = round($lengthText*$this->adaptiveIndex);
        $paragraphIndex = 0;
        
        if($counter < self::MIN_PARAGRAPH_COUNT) {
            $paragraphIndex = ($this->adaptiveIndex > 0.5) ? $counter-1 : 2;
        }
        else {
            $lengthToMiddle = 0;
            foreach ($paragraphs as $key => $paragraph) {
                $value = $paragraph->nodeValue;

                if(!empty($value) && $paragraph->lenValue > self::MIN_PARAGRAPH_LENGTH)
                {
                    if($lengthToMiddle + $paragraph->lenValue <= $middle)
                    {
                        $lengthToMiddle += $paragraph->lenValue;
                    }
                    else {
                        $paragraphIndex = $key;
                        break;
                    }
                }
            }
        }

        return $paragraphIndex;
    }
}
