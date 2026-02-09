<?php

namespace App\Services;

class SubtitleParserService
{
    /**
     * Parse subtitle content and return clean text.
     *
     * @param string $content
     * @param string $extension
     * @return string
     */
    public function parse(string $content, string $extension): string
    {
        // Normalize line endings
        $content = str_replace(["\r\n", "\r"], "\n", $content);

        // Remove BOM if present
        $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);

        if (strtolower($extension) === 'vtt') {
            return $this->parseVtt($content);
        }

        return $this->parseSrt($content);
    }

    private function parseSrt(string $content): string
    {
        $lines = explode("\n", $content);
        $textLines = [];

        foreach ($lines as $line) {
            $line = trim($line);

            // Skip empty lines
            if ($line === '') {
                continue;
            }

            // Skip numeric counters (lines that differ only by being a number)
            if (is_numeric($line)) {
                continue;
            }

            // Skip timestamp lines (e.g. "00:00:20,000 --> 00:00:24,400")
            if (preg_match('/^\d{2}:\d{2}:\d{2},\d{3} --> \d{2}:\d{2}:\d{2},\d{3}/', $line)) {
                continue;
            }

            // What remains is text
            $textLines[] = $line;
        }

        return implode("\n", $textLines);
    }

    private function parseVtt(string $content): string
    {
        $lines = explode("\n", $content);
        $textLines = [];

        $isHeader = true;

        foreach ($lines as $line) {
            $line = trim($line);

            // Skip 'WEBVTT' header or empty lines
            if ($isHeader) {
                if ($line === 'WEBVTT' || $line === '') {
                    continue;
                }
                // Once we hit something else, we assume header is done (simplified)
                $isHeader = false;
            }

            if ($line === '') {
                continue;
            }

            // Skip numeric counters (often not in VTT, but sometimes present)
            if (is_numeric($line)) {
                continue;
            }

            // Skip timestamp lines (e.g. "00:00.000 --> 00:04.140" or with hours)
            // VTT allows "MM:SS.mmm" or "HH:MM:SS.mmm"
            if (strpos($line, '-->') !== false) {
                continue;
            }

            // Skip "NOTE" lines (comments) if any
            if (strpos($line, 'NOTE') === 0) {
                continue;
            }

            // What remains is likely text
            $textLines[] = $line;
        }

        return implode("\n", $textLines);
    }
}
