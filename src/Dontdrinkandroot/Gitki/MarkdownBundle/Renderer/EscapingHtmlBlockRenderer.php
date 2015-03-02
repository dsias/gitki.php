<?php

namespace Dontdrinkandroot\Gitki\MarkdownBundle\Renderer;

use League\CommonMark\Block\Element\AbstractBlock;
use League\CommonMark\Block\Element\HtmlBlock;
use League\CommonMark\Block\Renderer\BlockRendererInterface;
use League\CommonMark\HtmlRenderer;

class EscapingHtmlBlockRenderer implements BlockRendererInterface
{
    /**
     * @param HtmlBlock    $block
     * @param HtmlRenderer $htmlRenderer
     * @param bool         $inTightList
     *
     * @return string
     */
    public function render(AbstractBlock $block, HtmlRenderer $htmlRenderer, $inTightList = false)
    {
        if (!($block instanceof HtmlBlock)) {
            throw new \InvalidArgumentException('Incompatible block type: ' . get_class($block));
        }

        return htmlspecialchars($block->getStringContent(), ENT_HTML5);
    }
}