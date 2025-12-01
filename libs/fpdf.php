<?php
// Minimal FPDF-compatible stub to generate simple text-only PDFs without external dependencies.
class FPDF
{
    private array $lines = [];
    private float $fontSize = 12.0;
    private float $y = 750.0;
    private float $marginLeft = 40.0;

    public function AddPage(): void
    {
        $this->lines = [];
        $this->y = 750.0;
    }

    public function SetFont($family, $style = '', $size = 12): void
    {
        $this->fontSize = (float) $size;
    }

    public function Cell($w, $h, $txt, $border = 0, $ln = 0, $align = '', $fill = false, $link = ''): void
    {
        $this->lines[] = [
            'x' => $this->marginLeft,
            'y' => $this->y,
            'size' => $this->fontSize,
            'text' => (string) $txt,
        ];
        $this->y -= ($h ?: $this->fontSize + 2);
    }

    public function Ln($h = null): void
    {
        $this->y -= $h ?: ($this->fontSize + 2);
    }

    public function MultiCell($w, $h, $txt): void
    {
        $wrapped = wordwrap((string) $txt, 80, "\n", true);
        foreach (explode("\n", $wrapped) as $line) {
            $this->Cell($w, $h, $line);
        }
    }

    private function escape(string $text): string
    {
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
    }

    public function Output($dest = '', $name = ''): void
    {
        $stream = '';
        foreach ($this->lines as $line) {
            $pdfY = 792 - $line['y'];
            $stream .= sprintf("BT /F1 %.2f Tf %.2f %.2f Td (%s) Tj ET\n", $line['size'], $line['x'], $pdfY, $this->escape($line['text']));
        }

        $objects = [];
        $objects[] = "1 0 obj << /Type /Catalog /Pages 2 0 R >> endobj";
        $objects[] = "2 0 obj << /Type /Pages /Kids [3 0 R] /Count 1 >> endobj";
        $objects[] = "3 0 obj << /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >> endobj";
        $objects[] = "4 0 obj << /Length " . strlen($stream) . " >> stream\n" . $stream . "endstream endobj";
        $objects[] = "5 0 obj << /Type /Font /Subtype /Type1 /BaseFont /Helvetica >> endobj";

        $pdf = "%PDF-1.4\n";
        $offsets = [];
        foreach ($objects as $index => $object) {
            $offsets[$index + 1] = strlen($pdf);
            $pdf .= $object . "\n";
        }
        $xrefPos = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";
        foreach ($offsets as $offset) {
            $pdf .= sprintf("%010d 00000 n \n", $offset);
        }
        $pdf .= "trailer << /Size " . (count($objects) + 1) . " /Root 1 0 R >>\nstartxref\n" . $xrefPos . "\n%%EOF";

        if ($dest === 'F' && $name) {
            file_put_contents($name, $pdf);
        } else {
            header('Content-Type: application/pdf');
            echo $pdf;
        }
    }
}
