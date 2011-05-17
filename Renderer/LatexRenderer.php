<?php

namespace Thekwasti\WikiBundle\Renderer;

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
    
    public function render($element)
    {
        if (is_array($element)) {
            $result = '';
            
            foreach ($element as $subElement) {
                $result .= $this->render($subElement);
            }

            return $result;
        } else if ($element instanceof Document) {
            return $this->documentPre . $this->render($element->getChildren()) . $this->documentPost;
        } else if ($element instanceof Text) {
            return $element->getText();
        } else if ($element instanceof EmptyLine) {
            return "";//\n\\\\\n";
        } else if ($element instanceof HorizontalRule) {
            return "\n\\begin{center}\\rule{0.5\\textwidth}{0.5pt}\\end{center}\n";
        } else if ($element instanceof Headline) {
            $level = $element->getLevel() - 1;
            if ($level > 2) $level = 2;
            
            return sprintf("\\%ssection{%s}\n",
                str_repeat('sub', $level),
                $this->render($element->getChildren())
            );
        } else if ($element instanceof Bold) {
            return sprintf('\textbf{%s}', $this->render($element->getChildren()));
        } else if ($element instanceof Italic) {
            return sprintf('\textit{%s}', $this->render($element->getChildren()));
        } else if ($element instanceof Link) {
            return sprintf('%s\footnote{%s}', $this->render($element->getChildren()), $element->getDestination());
        } else {
            throw new Exception();
        }
    }
}
