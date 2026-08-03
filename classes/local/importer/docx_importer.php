<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

/**
 * DOCX checklist importer.
 *
 * @package    gradingform_checklist
 * @copyright  2026 John Braz
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace gradingform_checklist\local\importer;

defined('MOODLE_INTERNAL') || die();

/**
 * Parses the checklist Word template.
 */
class docx_importer {
    /** DOCX stop heading. */
    protected const STOP_HEADING = 'Reference Only - Do Not Import';

    /** @var \ZipArchive */
    protected \ZipArchive $zip;

    /** @var array relationship id to target path */
    protected array $relationships = [];

    /** @var array extracted benchmark files */
    protected array $files = [];

    /** @var array warnings */
    protected array $warnings = [];

    /**
     * Parses a DOCX file.
     *
     * @param string $path file path
     * @return canonical_import_data
     */
    public function parse(string $path): canonical_import_data {
        $this->warnings = [];
        $this->files = [];
        $this->relationships = [];
        $this->zip = new \ZipArchive();
        if ($this->zip->open($path) !== true) {
            return new canonical_import_data([], [], [get_string('importerrordocxopen', 'gradingform_checklist')]);
        }

        $documentxml = $this->zip->getFromName('word/document.xml');
        if ($documentxml === false) {
            $this->zip->close();
            return new canonical_import_data([], [], [get_string('importerrordocxdocument', 'gradingform_checklist')]);
        }
        $this->load_relationships();

        $dom = new \DOMDocument();
        $dom->preserveWhiteSpace = false;
        if (!$dom->loadXML($documentxml)) {
            $this->zip->close();
            return new canonical_import_data([], [], [get_string('importerrordocxdocument', 'gradingform_checklist')]);
        }
        $xpath = $this->xpath($dom);
        $body = $xpath->query('//w:body')->item(0);
        if (!$body) {
            $this->zip->close();
            return new canonical_import_data([], [], [get_string('importerrordocxdocument', 'gradingform_checklist')]);
        }

        $elements = [];
        foreach ($body->childNodes as $node) {
            if (!$node instanceof \DOMElement) {
                continue;
            }
            if ($node->localName === 'p') {
                $text = $this->paragraph_text($xpath, $node);
                if (trim($text) === self::STOP_HEADING) {
                    break;
                }
                $elements[] = ['type' => 'p', 'text' => trim($text)];
            } else if ($node->localName === 'tbl') {
                $elements[] = ['type' => 'tbl', 'table' => $node];
            }
        }

        $raw = [
            'format' => canonical_import_data::FORMAT,
            'version' => canonical_import_data::VERSION,
            'name' => '',
            'description' => '',
            'settings' => [],
            'benchmark' => [
                'enabled' => false,
                'buttonlabel' => get_string('benchmarkbuttondefault', 'gradingform_checklist'),
                'buttonicon' => 'fa-solid fa-file-circle-check',
                'html' => '',
                'files' => [],
            ],
            'groups' => [],
        ];

        $pendinggroup = null;
        foreach ($elements as $element) {
            if ($element['type'] === 'p') {
                continue;
            }
            $table = $element['table'];
            $rows = $this->table_rows($xpath, $table);
            if (empty($rows)) {
                continue;
            }
            $headers = array_map('trim', $rows[0]);
            if ($headers === ['Field', 'Value']) {
                $fields = $this->key_value_rows($rows);
                if (array_key_exists('Checklist name', $fields) || array_key_exists('Description', $fields)) {
                    $raw['name'] = $fields['Checklist name'] ?? $raw['name'];
                    $raw['description'] = $fields['Description'] ?? $raw['description'];
                } else if (array_key_exists('Use benchmark', $fields)) {
                    $raw['benchmark']['enabled'] = $fields['Use benchmark'] ?? false;
                    $raw['benchmark']['buttonlabel'] = $fields['Benchmark button label']
                        ?? $raw['benchmark']['buttonlabel'];
                    $raw['benchmark']['buttonicon'] = $fields['Benchmark button icon']
                        ?? $raw['benchmark']['buttonicon'];
                } else if (array_key_exists('Group description', $fields)) {
                    if ($pendinggroup !== null) {
                        $raw['groups'][] = $pendinggroup;
                    }
                    if ($this->cell_has_non_text_content($xpath, $table)) {
                        $this->warnings[] = get_string('importwarningrichgroup', 'gradingform_checklist');
                    }
                    $pendinggroup = [
                        'description' => $fields['Group description'] ?? '',
                        'items' => [],
                    ];
                } else {
                    $raw['settings'] = $fields + $raw['settings'];
                }
            } else if ($headers === ['Item', 'Points']) {
                if ($pendinggroup === null) {
                    $this->warnings[] = get_string('importwarningorphanitems', 'gradingform_checklist');
                    continue;
                }
                if ($this->cell_has_non_text_content($xpath, $table)) {
                    $this->warnings[] = get_string('importwarningrichitems', 'gradingform_checklist');
                }
                for ($i = 1; $i < count($rows); $i++) {
                    $pendinggroup['items'][] = [
                        'definition' => $rows[$i][0] ?? '',
                        'score' => $rows[$i][1] ?? '',
                    ];
                }
            } else if (count($headers) === 1 && $headers[0] === 'Benchmark guidance') {
                $cells = $xpath->query('./w:tr[position()=2]/w:tc', $table);
                if ($cells->length) {
                    $raw['benchmark']['html'] = $this->cell_to_html($xpath, $cells->item(0));
                    $raw['benchmark']['files'] = $this->files;
                }
            }
        }
        if ($pendinggroup !== null) {
            $raw['groups'][] = $pendinggroup;
        }

        $this->zip->close();
        $result = import_validator::validate($raw, true);
        return new canonical_import_data($result->get_data(),
            array_values(array_unique(array_merge($this->warnings, $result->get_warnings()))),
            $result->get_errors());
    }

    /**
     * Creates a namespace-aware xpath.
     *
     * @param \DOMDocument $dom document
     * @return \DOMXPath
     */
    protected function xpath(\DOMDocument $dom): \DOMXPath {
        $xpath = new \DOMXPath($dom);
        $xpath->registerNamespace('w', 'http://schemas.openxmlformats.org/wordprocessingml/2006/main');
        $xpath->registerNamespace('a', 'http://schemas.openxmlformats.org/drawingml/2006/main');
        $xpath->registerNamespace('r', 'http://schemas.openxmlformats.org/officeDocument/2006/relationships');
        return $xpath;
    }

    /**
     * Loads document relationships.
     */
    protected function load_relationships(): void {
        $rels = $this->zip->getFromName('word/_rels/document.xml.rels');
        if ($rels === false) {
            return;
        }
        $dom = new \DOMDocument();
        if (!$dom->loadXML($rels)) {
            return;
        }
        foreach ($dom->getElementsByTagName('Relationship') as $rel) {
            $id = $rel->getAttribute('Id');
            $target = $rel->getAttribute('Target');
            if ($id !== '' && $target !== '') {
                $this->relationships[$id] = strpos($target, 'word/') === 0 ? $target : 'word/' . ltrim($target, '/');
            }
        }
    }

    /**
     * Extracts paragraph text.
     *
     * @param \DOMXPath $xpath xpath
     * @param \DOMElement $paragraph paragraph element
     * @return string
     */
    protected function paragraph_text(\DOMXPath $xpath, \DOMElement $paragraph): string {
        $parts = [];
        foreach ($xpath->query('.//w:t|.//w:tab|.//w:br', $paragraph) as $node) {
            if ($node->localName === 'tab') {
                $parts[] = "\t";
            } else if ($node->localName === 'br') {
                $parts[] = "\n";
            } else {
                $parts[] = $node->textContent;
            }
        }
        return implode('', $parts);
    }

    /**
     * Returns table rows as text values.
     *
     * @param \DOMXPath $xpath xpath
     * @param \DOMElement $table table
     * @return array
     */
    protected function table_rows(\DOMXPath $xpath, \DOMElement $table): array {
        $rows = [];
        foreach ($xpath->query('./w:tr', $table) as $row) {
            $values = [];
            foreach ($xpath->query('./w:tc', $row) as $cell) {
                $paragraphs = [];
                foreach ($xpath->query('./w:p', $cell) as $paragraph) {
                    $text = trim($this->paragraph_text($xpath, $paragraph));
                    if ($text !== '') {
                        $paragraphs[] = $text;
                    }
                }
                $values[] = implode("\n", $paragraphs);
            }
            $rows[] = $values;
        }
        return $rows;
    }

    /**
     * Converts key/value rows into an associative array.
     *
     * @param array $rows table rows
     * @return array
     */
    protected function key_value_rows(array $rows): array {
        $values = [];
        for ($i = 1; $i < count($rows); $i++) {
            $key = trim($rows[$i][0] ?? '');
            if ($key === '') {
                continue;
            }
            $values[$key] = trim($rows[$i][1] ?? '');
        }
        return $values;
    }

    /**
     * Detects rich content where plain text is expected.
     *
     * @param \DOMXPath $xpath xpath
     * @param \DOMElement $table table
     * @return bool
     */
    protected function cell_has_non_text_content(\DOMXPath $xpath, \DOMElement $table): bool {
        return $xpath->query('.//w:drawing|.//w:pict|.//w:tbl//w:tbl', $table)->length > 0;
    }

    /**
     * Converts a benchmark cell to simple HTML.
     *
     * @param \DOMXPath $xpath xpath
     * @param \DOMElement $cell table cell
     * @return string
     */
    protected function cell_to_html(\DOMXPath $xpath, \DOMElement $cell): string {
        $html = [];
        foreach ($cell->childNodes as $node) {
            if (!$node instanceof \DOMElement) {
                continue;
            }
            if ($node->localName === 'p') {
                $content = $this->paragraph_to_html($xpath, $node);
                if (trim(strip_tags($content)) !== '' || str_contains($content, '<img')) {
                    $html[] = '<p>' . $content . '</p>';
                }
            } else if ($node->localName === 'tbl') {
                $html[] = $this->table_to_html($xpath, $node);
            }
        }
        return implode("\n", $html);
    }

    /**
     * Converts a paragraph to simple HTML.
     *
     * @param \DOMXPath $xpath xpath
     * @param \DOMElement $paragraph paragraph
     * @return string
     */
    protected function paragraph_to_html(\DOMXPath $xpath, \DOMElement $paragraph): string {
        $parts = [];
        foreach ($xpath->query('./w:r', $paragraph) as $run) {
            $text = '';
            foreach ($xpath->query('.//w:t|.//w:tab|.//w:br', $run) as $node) {
                if ($node->localName === 'tab') {
                    $text .= ' ';
                } else if ($node->localName === 'br') {
                    $text .= '<br />';
                } else {
                    $text .= s($node->textContent);
                }
            }
            foreach ($xpath->query('.//a:blip', $run) as $blip) {
                $rid = $blip->getAttributeNS(
                    'http://schemas.openxmlformats.org/officeDocument/2006/relationships',
                    'embed'
                );
                $src = $this->extract_image($rid);
                if ($src !== '') {
                    $text .= '<img src="' . s($src) . '" alt="" />';
                }
            }
            if ($text === '') {
                continue;
            }
            if ($xpath->query('./w:rPr/w:b', $run)->length) {
                $text = '<strong>' . $text . '</strong>';
            }
            if ($xpath->query('./w:rPr/w:i', $run)->length) {
                $text = '<em>' . $text . '</em>';
            }
            $parts[] = $text;
        }
        return implode('', $parts);
    }

    /**
     * Converts a nested benchmark table to HTML.
     *
     * @param \DOMXPath $xpath xpath
     * @param \DOMElement $table table
     * @return string
     */
    protected function table_to_html(\DOMXPath $xpath, \DOMElement $table): string {
        $rows = $this->table_rows($xpath, $table);
        $html = '<table>';
        foreach ($rows as $row) {
            $html .= '<tr>';
            foreach ($row as $cell) {
                $html .= '<td>' . s($cell) . '</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</table>';
        return $html;
    }

    /**
     * Extracts an embedded image relationship.
     *
     * @param string $rid relationship id
     * @return string pluginfile placeholder URL
     */
    protected function extract_image(string $rid): string {
        if (empty($this->relationships[$rid])) {
            return '';
        }
        $path = $this->relationships[$rid];
        $content = $this->zip->getFromName($path);
        if ($content === false) {
            return '';
        }
        $filename = clean_param(basename($path), PARAM_FILE);
        if ($filename === '') {
            $filename = 'benchmark-image-' . (count($this->files) + 1) . '.png';
        }
        $this->files[] = [
            'filename' => $filename,
            'content' => base64_encode($content),
            'encoding' => 'base64',
        ];
        return '@@PLUGINFILE@@/' . $filename;
    }
}
