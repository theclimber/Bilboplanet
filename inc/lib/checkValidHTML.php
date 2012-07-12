<?php

/* SafeHtmlChecker - checks HTML against a subset of 
   elements to ensure safety and XHTML validation.
   
   Simon Willison, 23rd Feb 2003
   
   Note: HTML sent to the checker must be wrapped in an '<all>' tag.
   HTML can be sent to the checker in chunks, with multiple calls to 
   the check() method.
   
   Usage:
   
   $checker = new SafeHtmlChecker;
   $checker->check('<all>'.$html.'</all>');
   if ($checker->isOK()) {
       echo 'Everything is fine';
   } else {
       echo '<ul>';
       foreach ($checker->getErrors() as $error) {
           echo '<li>'.$error.'</li>';
       }
       echo '</ul>';
   }

   Updated 15th September 2003: Added extra <? and <script filters.

*/

// Entity classes, adapted from XHTML 1.0 strict DTD
define('E_INLINE_CONTENTS', 'em strong dfn code q samp kbd var cite abbr acronym sub sup a #PCDATA');
define('E_BLOCK_CONTENTS', 'dl ul ol blockquote p');
define('E_FLOW_CONTENTS', E_BLOCK_CONTENTS.' '.E_INLINE_CONTENTS);

class SafeHtmlChecker {
    // Array showing what tags each tag can contain
    var $tags = array(
        'all' => E_FLOW_CONTENTS,
        'p' => E_INLINE_CONTENTS,
        'blockquote' => E_BLOCK_CONTENTS,
        // Lists
        'ul' => 'li',
        'ol' => 'li',
        'li' => E_FLOW_CONTENTS,
        'dl' => 'dt dd',
        'dt' => E_INLINE_CONTENTS,
        'dd' => E_FLOW_CONTENTS,
        // Inline elements
        'em' => E_INLINE_CONTENTS,
        'strong' => E_INLINE_CONTENTS,
        'dfn' => E_INLINE_CONTENTS,
        'code' => E_INLINE_CONTENTS,
        'q' => E_INLINE_CONTENTS,
        'samp' => E_INLINE_CONTENTS,
        'kbd' => E_INLINE_CONTENTS,
        'var' => E_INLINE_CONTENTS,
        'cite' => E_INLINE_CONTENTS,
        'abbr' => E_INLINE_CONTENTS,
        'acronym' => E_INLINE_CONTENTS,
        'sub' => E_INLINE_CONTENTS,
        'sup' => E_INLINE_CONTENTS,
        'a' => E_INLINE_CONTENTS
    );
    // Array showing allowed attributes for tags
    var $tagattrs = array(
        'blockquote' => 'cite',
        'q' => 'cite',
        'a' => 'href title',
        'dfn' => 'title',
        'acronym' => 'title',
        'abbr' => 'title'
    );
    // Internal variables
    var $errors = array();
    var $parser;
    var $stack = array();
    function SafeHtmlChecker() {
        $this->parser = xml_parser_create();
        xml_set_object($this->parser, $this);
        xml_set_element_handler($this->parser, 'tag_open', 'tag_close');
        xml_set_character_data_handler($this->parser, 'cdata');
        xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, false);
    }
    function check($xhtml) {
        // Open comments are dangerous
        $xhtml = str_replace('<!--', '', $xhtml);
        // So is CDATA
        $xhtml = str_replace('<![CDATA[', '', $xhtml);
        // And processing directives
        $comment = str_replace('<?', '', $xhtml);
        // And script elements require double checking just to be sure
        $comment = preg_replace('/<script/i', '', $xhtml);
        if (!xml_parse($this->parser, $xhtml)) {
            $this->errors[] = 'XHTML is not well-formed';
        }
    }
    function tag_open($parser, $tag, $attrs) {
        if ($tag == 'all') {
            $this->stack[] = 'all';
            return;
        }
        $previous = $this->stack[count($this->stack)-1];
        // If previous tag is illegal, no point in running tests
        if (!in_array($previous, array_keys($this->tags))) {
            $this->stack[] = $tag;
            return;
        }
        // Is tag a legal tag?
        if (!in_array($tag, array_keys($this->tags))) {
            $this->errors[] = "Illegal tag: <code>$tag</code>";
            $this->stack[] = $tag;
            return;
        }
        // Is tag allowed in the current context?
        if (!in_array($tag, explode(' ', $this->tags[$previous]))) {
            if ($previous == 'all') {
                $this->errors[] = "Tag <code>$tag</code> must occur inside another tag";
            } else {
                $this->errors[] = "Tag <code>$tag</code> is not allowed within tag <code>$previous</code>";
            }
        }
        // Are tag attributes valid?
        foreach ($attrs as $attr => $value) {
            if (!isset($this->tagattrs[$tag]) || !in_array($attr, explode(' ', $this->tagattrs[$tag]))) {
                $this->errors[] = "Tag <code>$tag</code> may not have attribute <code>$attr</code>";
            }
            // Special case for javascript: in href attribute
            if ($attr == 'href' && preg_match('/^javascript/i', trim($value))) {
                $this->errors[] = "<code>href</code> attributes may not contain the <code>javascript:</code> protocol";
            }
            // Special case for data: in href attribute
            if ($attr == 'href' && preg_match('/^data/i', trim($value))) {
                $this->errors[] = "<code>href</code> attributes may not contain the <code>data:</code> protocol";
            }
            // Special case for javascript: in blockquote cites (for use with blockquotes.js)
            if ($attr == 'cite' && preg_match('/^javascript/i', trim($value))) {
                $this->errors[] = "<code>cite</code> attributes may not contain the <code>javascript:</code> protocol";
            }
            // Special case for data: in blockquote cites (for use with blockquotes.js)
            if ($attr == 'cite' && preg_match('/^data/i', trim($value))) {
                $this->errors[] = "<code>cite</code> attributes may not contain the <code>data:</code> protocol";
            }
        }
        // Set previous, used for checking nesting context rules
        $this->stack[] = $tag;
    }
    function cdata($parser, $cdata) {
        // Simply check that the 'previous' tag allows CDATA
        $previous = $this->stack[count($this->stack)-1];
        // If previous tag is illegal, no point in running test
        if (!in_array($previous, array_keys($this->tags))) {
            return;
        }
        if (trim($cdata) != '') {
            if (!in_array('#PCDATA', explode(' ', $this->tags[$previous]))) {
                $this->errors[] = "Tag <code>$previous</code> may not contain raw character data";
            }
        }
    }
    function tag_close($parser, $tag) {
        // Move back one up the stack
        array_pop($this->stack);
    }
    function isOK() {
        return count($this->errors) < 1;
    }
    function getErrors() {
        return $this->errors;
    }
}

?>
