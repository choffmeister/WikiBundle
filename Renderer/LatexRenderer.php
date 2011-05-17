<?php

namespace Thekwasti\WikiBundle\Renderer;

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
    public function render(NodeInterface $element)
    {
        if ($element instanceof Chain) {
            $result = '';
            
            foreach ($element->getElements() as $subElement) {
                $result .= $this->render($subElement);
            }
            
            return $result;
        } else if ($element instanceof Text) {
            return $element->getText();
        } else if ($element instanceof EmptyLine) {
            return "\n\\\\\n";
        } else if ($element instanceof HorizontalRule) {
            return "\n\\begin{center}\\rule{0.5\\textwidth}{0.5pt}\\end{center}\n";
        } else if ($element instanceof Headline) {
            $level = $element->getLevel() - 1;
            if ($level > 2) $level = 2;
            
            return sprintf("\\%ssection{%s}\n",
                str_repeat('sub', $level),
                $this->render($element->getContent())
            );
        } else if ($element instanceof Bold) {
            return sprintf('\textbf{%s}', $this->render($element->getContent()));
        } else if ($element instanceof Italic) {
            return sprintf('\textit{%s}', $this->render($element->getContent()));
        } else if ($element instanceof Link) {
            return sprintf('%s\footnote{%s}', $this->render($element->getContent()), $element->getDestination());
        } else {
            return '';
        }
    }
    
    public function renderPre()
    {
        return <<<EOF
\documentclass{article}
\begin{document}
EOF;
    }
    
    public function renderPost()
    {
        return <<<EOF
\end{document}
EOF;
    }
}
