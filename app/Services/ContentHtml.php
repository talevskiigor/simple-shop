<?php
namespace App\Services;

use Symfony\Component\HtmlSanitizer\HtmlSanitizer;
use Symfony\Component\HtmlSanitizer\HtmlSanitizerConfig;

class ContentHtml
{
    public function clean(?string $html): string
    {
        $config = (new HtmlSanitizerConfig)->allowSafeElements()->allowRelativeLinks()->allowRelativeMedias()
            ->allowMediaSchemes(['https', 'http'])->allowLinkSchemes(['https', 'http', 'mailto', 'tel'])
            ->allowElement('video', ['src', 'controls', 'preload', 'poster'])
            ->allowElement('source', ['src', 'type'])->forceAttribute('video', 'controls', '')
            ->forceAttribute('a', 'rel', 'noopener noreferrer')->withMaxInputLength(200000);
        return (new HtmlSanitizer($config))->sanitize($html ?? '');
    }
}
