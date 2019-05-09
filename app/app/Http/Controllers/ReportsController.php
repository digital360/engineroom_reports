<?php

namespace App\Http\Controllers;

use App\Publishing\Entities\Publication;
use App\Engineroom\Client;
use App\Publishing\Publications\BusinessPlan\BusinessPlan;
use App\Publishing\Publications\BusinessPlan\Jobs\RetrieveStageModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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

    public function test()
    {
        $client = app(Client::class);
        $response = $client->request('GET', 'plan/5ae94a2f794a4c10cc2d5fa3');

        return response()->json($response->getBody()->getContents());
    }

    public function businessPlan($key, $page = null)
    {
        $businessPlanModel = Cache::get($key);

        $remaining = array_filter($businessPlanModel['stages'], function($stageKey) use ($key) {
            $stageCacheKey = $key . '_' . $stageKey;
            return !Cache::has($stageCacheKey);
        });

        if (count($remaining)) {

            // still collection data, display wait message

            die('Making Business Plan');

        } else {

            // has all data, make report

            $stageModels = array_combine(
                $businessPlanModel['stages'],
                array_map(function($stageKey) use ($key) {
                    $stageCacheKey = $key . '_' . $stageKey;
                    return Cache::get($stageCacheKey);
                }, $businessPlanModel['stages'])
            );

            $businessPlan = BusinessPlan::makeWithPlanAndStageModels($businessPlanModel, $stageModels);

            $page = $page ?? 1;
            return $businessPlan->getPage($page - 1)->output();
        }
    }

    public function makeBusinessPlan(Request $request)
    {
        $businessPlanId = $request->plan;
        $businessId = $request->business;

        $endpoint = 'plan/' . $businessPlanId . '?business=' . $businessId;
        $engineroomClient = app(Client::class);

        $response = $engineroomClient->request('GET', $endpoint);
        $businessPlanModel = json_decode($response->getBody()->getContents(), true)['data'];

        $businessPlanCacheKey = $businessPlanModel['id'];
        Cache::put($businessPlanCacheKey, $businessPlanModel, now()->addHours(12));

        foreach ($businessPlanModel['stages'] as $stageKey) {
            RetrieveStageModel::dispatch($stageKey, $businessPlanModel);
        }

        return response()->json($request->getSchemeAndHttpHost() . '/reports/business-plan/' . $businessPlanCacheKey);
    }
}
