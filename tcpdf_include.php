
<?php
// Include the TCPDF library
require_once('C:\xampp\htdocs\ecommerce website (1)\ecommerce website (1)\ecommerce website (1)\ecommerce website\tcpdf\tcpdf_include.php

');

// Your PHP code for generating the PDF receipt continues...
// Define TCPDF constants if necessary
if (!defined('PDF_PAGE_ORIENTATION')) {
    define('PDF_PAGE_ORIENTATION', 'P');
}
if (!defined('PDF_UNIT')) {
    define('PDF_UNIT', 'mm');
}

// Create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true, 'UTF-8', false);

// Set document information, header, footer, fonts, etc.
// Add your PDF generation code here...

// Output the PDF as a download
$pdf->Output('receipt.pdf', 'D');