<?php

namespace App\Http\Controllers;

use App\Publishing\Entities\Publication;
use Illuminate\Http\Request;
use Knp\Snappy\Pdf;

class ReportsController extends Controller
{
    public function report($reportId, $page = null)
    {
        $publication = $this->makePublication($reportId);
        $page = $page ?? 1;

        return $publication->getPage($page - 1)->output();
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

    public function makePublication($reportId): Publication
    {
        $publication = new Publication($reportId);

        // add three pages
        for ($i = 0; $i < 3; $i++) {
            $publication->addPage();
        }

        return $publication;
    }
}
