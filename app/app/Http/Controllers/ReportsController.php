<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportsController extends Controller
{
    public function report($reportId, $page = null)
    {
        return view('report.page', [
            'report_id' => $reportId
        ]);
    }
}
