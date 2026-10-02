<?php

namespace App\Exports;

use RuntimeException;
use ZipArchive;

class SalesReportExporter
{
    /**
     * @param  array<string, mixed>  $report
     */
    public function toExcel(array $report): string
    {
        $rows = [
            ['Sales Report', null],
            ['Period', $report['period']],
            ['Date range', $report['date_label']],
            ['Orders', $report['order_count']],
            ['Total sales (PHP)', $report['total_sales']],
            ['Payments collected (PHP)', $report['payments_collected']],
            ['Outstanding balance (PHP)', $report['outstanding_balance']],
            ['Average order (PHP)', $report['average_order']],
            [],
            ['Order status', 'Orders', 'Sales (PHP)'],
        ];

        foreach ($report['statuses'] as $status => $totals) {
            $rows[] = [$status, $totals['count'], $totals['total']];
        }

        $rows[] = [];
        $rows[] = ['Order ID', 'Order date', 'Customer', 'Employee', 'Status', 'Total (PHP)', 'Paid (PHP)', 'Balance (PHP)'];

        foreach ($report['orders'] as $order) {
            $rows[] = [
                $order['order_id'],
                $order['order_date'],
                $order['customer'],
                $order['employee'],
                $order['status'],
                $order['total'],
                $order['paid'],
                $order['balance'],
            ];
        }

        $sheetRows = '';

        foreach ($rows as $rowIndex => $row) {
            $cells = '';

            foreach ($row as $columnIndex => $value) {
                if ($value === null) {
                    continue;
                }

                $cellReference = $this->excelColumnName($columnIndex + 1).($rowIndex + 1);

                if (is_int($value) || is_float($value)) {
                    $cells .= '<c r="'.$cellReference.'"><v>'.$value.'</v></c>';
                } else {
                    $cells .= '<c r="'.$cellReference.'" t="inlineStr"><is><t>'.$this->xmlEscape((string) $value).'</t></is></c>';
                }
            }

            $sheetRows .= '<row r="'.($rowIndex + 1).'">'.$cells.'</row>';
        }

        return $this->makeZip([
            '[Content_Types].xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/></Types>',
            '_rels/.rels' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/></Relationships>',
            'xl/workbook.xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets><sheet name="Sales Report" sheetId="1" r:id="rId1"/></sheets></workbook>',
            'xl/_rels/workbook.xml.rels' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/></Relationships>',
            'xl/worksheets/sheet1.xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData>'.$sheetRows.'</sheetData></worksheet>',
        ]);
    }

    /**
     * @param  array<string, mixed>  $report
     */
    public function toDocx(array $report): string
    {
        $body = $this->wordParagraph('Laundry Sales Report', true)
            .$this->wordParagraph($report['period'].' report · '.$report['date_label'])
            .$this->wordTable([
                ['Orders', (string) $report['order_count']],
                ['Total sales (PHP)', $this->money($report['total_sales'])],
                ['Payments collected (PHP)', $this->money($report['payments_collected'])],
                ['Outstanding balance (PHP)', $this->money($report['outstanding_balance'])],
                ['Average order (PHP)', $this->money($report['average_order'])],
            ])
            .$this->wordParagraph('Orders by status', true);

        $statusRows = [['Status', 'Orders', 'Sales (PHP)']];

        foreach ($report['statuses'] as $status => $totals) {
            $statusRows[] = [$status, (string) $totals['count'], $this->money($totals['total'])];
        }

        $body .= $this->wordTable($statusRows);
        $body .= $this->wordParagraph('Order details', true);

        $orderRows = [['Order ID', 'Date', 'Customer', 'Employee', 'Status', 'Total', 'Paid', 'Balance']];

        foreach ($report['orders'] as $order) {
            $orderRows[] = [
                (string) $order['order_id'],
                $order['order_date'],
                $order['customer'],
                $order['employee'],
                $order['status'],
                $this->money($order['total']),
                $this->money($order['paid']),
                $this->money($order['balance']),
            ];
        }

        $body .= $this->wordTable($orderRows);
        $document = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"><w:body>'.$body.'<w:sectPr><w:pgSz w:w="15840" w:h="12240" w:orient="landscape"/><w:pgMar w:top="720" w:right="720" w:bottom="720" w:left="720"/></w:sectPr></w:body></w:document>';

        return $this->makeZip([
            '[Content_Types].xml' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/></Types>',
            '_rels/.rels' => '<?xml version="1.0" encoding="UTF-8" standalone="yes"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/></Relationships>',
            'word/document.xml' => $document,
        ]);
    }

    /**
     * @param  array<string, mixed>  $report
     */
    public function toPdf(array $report): string
    {
        $orderPages = array_chunk($report['orders'], 34);
        $orderPages = $orderPages === [] ? [[]] : $orderPages;
        $pageStreams = [];

        foreach ($orderPages as $pageIndex => $orders) {
            $content = $this->pdfText(40, 555, 'Laundry Sales Report', 18)
                .$this->pdfText(40, 532, $report['period'].' report | '.$report['date_label'], 10)
                .$this->pdfText(40, 507, 'Orders: '.$report['order_count'].'  Total sales (PHP): '.$this->money($report['total_sales']).'  Payments collected (PHP): '.$this->money($report['payments_collected']), 9)
                .$this->pdfText(40, 490, 'Outstanding balance (PHP): '.$this->money($report['outstanding_balance']).'  Average order (PHP): '.$this->money($report['average_order']), 9)
                .$this->pdfText(40, 463, 'ID', 8)
                .$this->pdfText(85, 463, 'Date', 8)
                .$this->pdfText(140, 463, 'Customer', 8)
                .$this->pdfText(285, 463, 'Employee', 8)
                .$this->pdfText(415, 463, 'Status', 8)
                .$this->pdfText(525, 463, 'Total', 8)
                .$this->pdfText(610, 463, 'Paid', 8)
                .$this->pdfText(690, 463, 'Balance', 8)
                .'0.5 w 40 456 m 802 456 l S'."\n";

            if ($orders === []) {
                $content .= $this->pdfText(40, 435, 'No orders were recorded during this period.', 9);
            }

            foreach ($orders as $rowIndex => $order) {
                $y = 439 - ($rowIndex * 12);
                $content .= $this->pdfText(40, $y, (string) $order['order_id'], 7)
                    .$this->pdfText(85, $y, $order['order_date'], 7)
                    .$this->pdfText(140, $y, mb_substr($order['customer'], 0, 26), 7)
                    .$this->pdfText(285, $y, mb_substr($order['employee'], 0, 23), 7)
                    .$this->pdfText(415, $y, mb_substr($order['status'], 0, 18), 7)
                    .$this->pdfText(525, $y, $this->money($order['total']), 7)
                    .$this->pdfText(610, $y, $this->money($order['paid']), 7)
                    .$this->pdfText(690, $y, $this->money($order['balance']), 7);
            }

            $content .= $this->pdfText(760, 22, 'Page '.($pageIndex + 1).' of '.count($orderPages), 8);
            $pageStreams[] = $content;
        }

        return $this->makePdf($pageStreams);
    }

    /** @param array<string, string> $files */
    private function makeZip(array $files): string
    {
        $path = tempnam(sys_get_temp_dir(), 'sales-report-');

        if ($path === false) {
            throw new RuntimeException('Unable to create a temporary report file.');
        }

        $archive = new ZipArchive;
        $openResult = $archive->open($path, ZipArchive::OVERWRITE);

        if ($openResult !== true) {
            unlink($path);
            throw new RuntimeException('Unable to create the report archive.');
        }

        foreach ($files as $name => $contents) {
            $archive->addFromString($name, $contents);
        }

        $archive->close();
        $contents = file_get_contents($path);
        unlink($path);

        if ($contents === false) {
            throw new RuntimeException('Unable to read the generated report archive.');
        }

        return $contents;
    }

    /** @param list<list<string>> $rows */
    private function wordTable(array $rows): string
    {
        $columnCount = max(array_map('count', $rows));
        $columnWidth = (int) (14400 / $columnCount);
        $table = '<w:tbl><w:tblPr><w:tblBorders><w:top w:val="single" w:sz="4"/><w:left w:val="single" w:sz="4"/><w:bottom w:val="single" w:sz="4"/><w:right w:val="single" w:sz="4"/><w:insideH w:val="single" w:sz="4"/><w:insideV w:val="single" w:sz="4"/></w:tblBorders></w:tblPr><w:tblGrid>';

        for ($column = 0; $column < $columnCount; $column++) {
            $table .= '<w:gridCol w:w="'.$columnWidth.'"/>';
        }

        $table .= '</w:tblGrid>';

        foreach ($rows as $row) {
            $table .= '<w:tr>';

            foreach ($row as $cell) {
                $table .= '<w:tc><w:tcPr><w:tcW w:w="'.$columnWidth.'" w:type="dxa"/></w:tcPr>'.$this->wordParagraph((string) $cell).'</w:tc>';
            }

            $table .= '</w:tr>';
        }

        return $table.'</w:tbl>';
    }

    private function wordParagraph(string $text, bool $bold = false): string
    {
        $boldRun = $bold ? '<w:b/>' : '';

        return '<w:p><w:r><w:rPr>'.$boldRun.'</w:rPr><w:t xml:space="preserve">'.$this->xmlEscape($text).'</w:t></w:r></w:p>';
    }

    private function excelColumnName(int $column): string
    {
        $name = '';

        while ($column > 0) {
            $remainder = ($column - 1) % 26;
            $name = chr(65 + $remainder).$name;
            $column = intdiv($column - 1, 26);
        }

        return $name;
    }

    private function xmlEscape(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }

    private function money(float $amount): string
    {
        return number_format($amount, 2, '.', ',');
    }

    private function pdfText(int $x, int $y, string $text, int $fontSize): string
    {
        $asciiText = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text);
        $asciiText = $asciiText === false ? $text : $asciiText;
        $asciiText = preg_replace('/[^\x20-\x7E]/', '', $asciiText) ?? '';
        $escapedText = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $asciiText);

        return "BT /F1 {$fontSize} Tf {$x} {$y} Td ({$escapedText}) Tj ET\n";
    }

    /** @param list<string> $pageStreams */
    private function makePdf(array $pageStreams): string
    {
        $objects = [
            1 => '<< /Type /Catalog /Pages 2 0 R >>',
            3 => '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>',
        ];
        $pageIds = [];

        foreach ($pageStreams as $index => $stream) {
            $pageId = 4 + ($index * 2);
            $contentId = $pageId + 1;
            $pageIds[] = $pageId.' 0 R';
            $objects[$pageId] = '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 842 595] /Resources << /Font << /F1 3 0 R >> >> /Contents '.$contentId.' 0 R >>';
            $objects[$contentId] = '<< /Length '.strlen($stream)." >>\nstream\n{$stream}endstream";
        }

        $objects[2] = '<< /Type /Pages /Kids ['.implode(' ', $pageIds).'] /Count '.count($pageIds).' >>';
        ksort($objects);
        $pdf = "%PDF-1.4\n";
        $offsets = [0];

        foreach ($objects as $objectId => $object) {
            $offsets[$objectId] = strlen($pdf);
            $pdf .= $objectId." 0 obj\n{$object}\nendobj\n";
        }

        $xrefOffset = strlen($pdf);
        $pdf .= 'xref'."\n0 ".(count($objects) + 1)."\n0000000000 65535 f \n";

        for ($objectId = 1; $objectId <= count($objects); $objectId++) {
            $pdf .= sprintf('%010d 00000 n ', $offsets[$objectId])."\n";
        }

        return $pdf.'trailer'."\n<< /Size ".(count($objects) + 1)." /Root 1 0 R >>\nstartxref\n{$xrefOffset}\n%%EOF";
    }
}
