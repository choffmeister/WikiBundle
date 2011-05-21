<?php

/*
 * This file is part of WikiBundle
 *
 * (c) Christian Hoffmeister <choffmeister.github@googlemail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Thekwasti\WikiBundle\Renderer;

use Thekwasti\WikiBundle\UrlGenerator;
use Thekwasti\WikiBundle\Tree\TableCellHead;
use Thekwasti\WikiBundle\Tree\TableCell;
use Thekwasti\WikiBundle\Tree\TableRow;
use Thekwasti\WikiBundle\Tree\Table;
use Thekwasti\WikiBundle\Tree\NoWikiInline;
use Thekwasti\WikiBundle\Tree\ListItem;
use Thekwasti\WikiBundle\Tree\OrderedList;
use Thekwasti\WikiBundle\Tree\UnorderedList;
use Thekwasti\WikiBundle\Tree\Paragraph;
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

/**
 * LatexRenderer
 * 
 * @author Christian Hoffmeister <choffmeister.github@googlemail.com>
 */
class LatexRenderer implements RendererInterface
{
    protected $documentPre = <<<EOF
\documentclass[a4paper, 10pt]{article}
\usepackage[utf8]{inputenc}
\usepackage{geometry}
\geometry{a4paper,left=25mm,right=25mm,top=25mm,bottom=25mm}
\pagestyle{myheadings}
\markright{Document}
\begin{document}
EOF;

    protected $documentPost = <<<EOF
\end{document}
EOF;

    private $urlGenerator;
    
    public function __construct(UrlGenerator $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }
    
    public function render($element, $currentWiki = null)
    {
        if ($currentWiki !== null) {
            $this->urlGenerator->setCurrentWiki($currentWiki);
        }
        
        if (is_array($element)) {
            $result = '';
            
            foreach ($element as $subElement) {
                $result .= $this->render($subElement);
            }

            return $result;
        } else if ($element instanceof Document) {
            return $this->documentPre . $this->render($element->getChildren()) . $this->documentPost;
        } else if ($element instanceof Paragraph) {
            return sprintf("%s\n\n", trim($this->render($element->getChildren())));
        } else if ($element instanceof UnorderedList) {
            return sprintf("\\begin{itemize}\n%s\n\\end{itemize}\n", trim($this->render($element->getChildren())));
        } else if ($element instanceof OrderedList) {
            return sprintf("\\begin{enumerate}\n%s\\end{enumerate}\n", trim($this->render($element->getChildren())));
        } else if ($element instanceof ListItem) {
            return sprintf("\\item %s\n", trim($this->render($element->getChildren())));
        } else if ($element instanceof NoWiki) {
            return sprintf("\texttt{%s}\n", $this->render($element->getChildren()));
        } else if ($element instanceof NoWikiInline) {
            return sprintf('\texttt{%s}', $this->render($element->getChildren()));
        } else if ($element instanceof Text) {
            return $this->escape($element->getText());
        } else if ($element instanceof EmptyLine) {
            return "\n\n";
        } else if ($element instanceof Headline) {
            $level = $element->getLevel() - 1;
            if ($level > 2) $level = 2;
            
            return sprintf("\\%ssection{%s}\n",
                str_repeat('sub', $level),
                trim($this->escape($element->getText()))
            );
        } else if ($element instanceof HorizontalRule) {
            return "\n\\begin{center}\\rule{0.5\\textwidth}{0.5pt}\\end{center}\n";
        } else if ($element instanceof Bold) {
            return sprintf('\textbf{%s}', $this->render($element->getChildren()));
        } else if ($element instanceof Italic) {
            return sprintf('\textit{%s}', $this->render($element->getChildren()));
        } else if ($element instanceof Link) {
            $url = $this->urlGenerator->generateUrl($element);
            
            if ($element->getHasSpecialPresentation()) {
                return sprintf('%s\footnote{%s}', $this->render($element->getChildren()), $this->escape($url));
            } else {
                return sprintf('%s\footnote{%s}', $this->escape(trim($element->getDestination())), $this->escape($url));
            }
        } else if ($element instanceof Table) {
            return '[Table not supported yet]';
        } else {
            throw new \Exception(sprintf('Unsupported element of type %s', gettype($element) == 'object' ? get_class($element) : gettype($element)));
        }
    }
    
    public function escape($string)
    {
        $specialchars = '\$%^&_{}#~';
        $escaper = "\\";
        
        for ($i = 0; $i < 10; $i++)
            $string = str_replace($specialchars[$i], $escaper . $specialchars[$i], $string);
            
        return $string;
    }
}
