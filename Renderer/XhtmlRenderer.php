<?php

namespace Thekwasti\WikiBundle\Renderer;

use Thekwasti\WikiBundle\Tree\TableCellHead;
use Thekwasti\WikiBundle\Tree\TableCell;
use Thekwasti\WikiBundle\Tree\TableRow;
use Thekwasti\WikiBundle\Tree\Table;
use Thekwasti\WikiBundle\Tree\NoWikiInline;
use Thekwasti\WikiBundle\Tree\Paragraph;
use Thekwasti\WikiBundle\Tree\ListItem;
use Thekwasti\WikiBundle\Tree\OrderedList;
use Thekwasti\WikiBundle\Tree\UnorderedList;
use Thekwasti\WikiBundle\Tree\NoWiki;
use Thekwasti\WikiBundle\Tree\ListSharpItem;
use Thekwasti\WikiBundle\Tree\ListBulletItem;
use Thekwasti\WikiBundle\Tree\Document;
use Thekwasti\WikiBundle\Tree\HorizontalRule;
use Thekwasti\WikiBundle\Tree\Link;
use Thekwasti\WikiBundle\Tree\Italic;
use Thekwasti\WikiBundle\Tree\Bold;
use Thekwasti\WikiBundle\Tree\EmptyLine;
use Thekwasti\WikiBundle\Tree\NodeInterface;
use Thekwasti\WikiBundle\Tree\Headline;
use Thekwasti\WikiBundle\Tree\Chain;
use Thekwasti\WikiBundle\Tree\Text;

class XhtmlRenderer implements RendererInterface
{
    public function render($element)
    {
        if (is_array($element)) {
            $result = '';
            
            foreach ($element as $subElement) {
                $result .= $this->render($subElement);
            }
            
            return $result;
        } else if ($element instanceof Document) {
            return $this->render($element->getChildren());
        } else if ($element instanceof Paragraph) {
            return sprintf("<p>\n%s\n</p>\n", trim($this->render($element->getChildren())));
        } else if ($element instanceof UnorderedList) {
            return sprintf("<ul>\n%s\n</ul>\n", trim($this->render($element->getChildren())));
        } else if ($element instanceof OrderedList) {
            return sprintf("<ol>\n%s\n</ol>\n", trim($this->render($element->getChildren()))); 
        } else if ($element instanceof ListItem) {
            return sprintf("<li>%s</li>\n", trim($this->render($element->getChildren())));
        } else if ($element instanceof NoWiki) {
            return sprintf("<pre>%s</pre>\n", $this->render($element->getChildren()));
        } else if ($element instanceof NoWikiInline) {
            return sprintf('<tt>%s</tt>', $this->render($element->getChildren()));
        } else if ($element instanceof Text) {
            return $this->escape($element->getText());
        } else if ($element instanceof EmptyLine) {
            return "\n<br/><br/>\n";
        } else if ($element instanceof Headline) {
            return sprintf("<h%d>%s</h%d>\n",
                $element->getLevel(),
                trim($this->render($element->getChildren())),
                $element->getLevel()
            );
        } else if ($element instanceof HorizontalRule) {
            return "\n<hr/>\n";
        } else if ($element instanceof Bold) {
            return sprintf('<strong>%s</strong>', $this->render($element->getChildren()));
        } else if ($element instanceof Italic) {
            return sprintf('<em>%s</em>', $this->render($element->getChildren()));
        } else if ($element instanceof Link) {
            if ($element->getHasSpecialPresentation()) {
                return sprintf('<a href="%s">%s</a>', trim($element->getDestination()), trim($this->render($element->getChildren())));
            } else {
                return sprintf('<a href="%s">%s</a>', trim($element->getDestination()), trim($element->getDestination()));
            }
        } else if ($element instanceof Table) {
            return sprintf("<table>\n%s\n</table>\n", trim($this->render($element->getChildren())));
        } else if ($element instanceof TableRow) {
            return sprintf("<tr>\n%s\n</tr>\n", trim($this->render($element->getChildren())));
        } else if ($element instanceof TableCell) {
            return sprintf("<td>%s</td>\n", trim($this->render($element->getChildren())));
        } else if ($element instanceof TableCellHead) {
            return sprintf("<th>%s</th>\n", trim($this->render($element->getChildren())));
        } else {
            throw new \Exception(sprintf('Unsupported element of type %s', gettype($element) == 'object' ? get_class($element) : gettype($element)));
        }
    }
    
    public function escape($string)
    {
        return htmlspecialchars($string);
    }
}
