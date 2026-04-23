<?php

namespace App\Services;

class PdfExporter
{
    /**
     * Generate PDF from HTML content
     * This uses a simple HTML to PDF approach
     */
    public function generatePdf(string $html, string $filename = 'document.pdf'): void
    {
        // Set headers for PDF download
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');

        // Use wkhtmltopdf if available, otherwise use simple HTML rendering
        if ($this->isWkhtmltopdfAvailable()) {
            $this->generateWithWkhtmltopdf($html, $filename);
        } else {
            // Fallback: Generate a simple text-based PDF or use browser print
            $this->generateSimplePdf($html, $filename);
        }
    }

    private function isWkhtmltopdfAvailable(): bool
    {
        $output = [];
        $returnVar = 0;
        @exec('wkhtmltopdf --version 2>&1', $output, $returnVar);
        return $returnVar === 0;
    }

    private function generateWithWkhtmltopdf(string $html, string $filename): void
    {
        $tempHtml = tempnam(sys_get_temp_dir(), 'html_');
        $tempPdf = tempnam(sys_get_temp_dir(), 'pdf_');
        
        file_put_contents($tempHtml, $html);
        
        exec("wkhtmltopdf {$tempHtml} {$tempPdf}");
        
        readfile($tempPdf);
        
        unlink($tempHtml);
        unlink($tempPdf);
    }

    private function generateSimplePdf(string $html, string $filename): void
    {
        // Simple approach: Return HTML that can be printed to PDF by browser
        // This is a fallback when no PDF library is available
        echo $html;
    }
}