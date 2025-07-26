<?php

namespace App\Service;

class KeywordExtractor
{
    private array $stopWords = [
        'the', 'and', 'a', 'an', 'of', 'in', 'on', 'for', 'to', 'with',
        'is', 'are', 'was', 'were', 'this', 'that', 'by', 'at', 'as', 'it', 'be', 'or'
    ];

    public function extract(string $text): array
    {
        $words = preg_split('/\W+/', strtolower($text));
        $keywords = array_filter($words, function ($word) {
            return $word && !in_array($word, $this->stopWords);
        });

        return array_values(array_unique($keywords));
    }
}
