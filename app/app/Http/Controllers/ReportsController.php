<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Knp\Snappy\Pdf;

class ReportsController extends Controller
{
    public function report($reportId, $page = null)
    {
        return view('report.page', [
            'report_id' => $reportId,
            'page' => $page ?? 1
        ]);
    }

    public function pdf($reportId)
    {
        $pdf = new Pdf('/bin/wkhtmltopdf');

        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="file.pdf"');

        echo $pdf->getOutput(array_map(function($page) use ($reportId) {
            return 'http://localhost/reports/' . $reportId . '/' . $page;
        }, [1, 2, 3]));
    }
}
