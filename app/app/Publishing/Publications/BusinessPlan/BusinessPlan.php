<?php


namespace App\Publishing\Publications\BusinessPlan;

use App\Engineroom\Client;
use App\Publishing\Entities\Page;
use App\Publishing\Entities\Publication;
use App\Publishing\Publications\BusinessPlan\Pages\Cover;
use App\Publishing\Publications\BusinessPlan\Pages\Section;
use App\Publishing\Publications\BusinessPlan\Pages\Stage;
use App\Publishing\Publications\BusinessPlan\Pages\Standard;

class BusinessPlan extends Publication
{
    protected $businessPlanId;
    protected $model;

    protected $pageTypes = [
        'cover' => Cover::class,
        'section' => Section::class,
        'stage' => Stage::class,
        'standard' => Standard::class,
    ];

    public function __construct($key, string $businessPlanId, array $model = [])
    {
        $this->businessPlanId = $businessPlanId;
        $this->model = $model;

        parent::__construct($key);
    }

    public function addPage(array $model = [], ?string $pageType = null): Page
    {
        $class = $this->pageTypes[$pageType] ?? $this->pageTypes['standard'];

        $page = new $class($this, $model);
        $this->pages[] = $page;

        return $page;
    }

    public static function publish(string $businessPlanId, string $businessId)
    {
        $endpoint = 'plan/' . $businessPlanId . '?business=' . $businessId;
        $engineroomClient = app(Client::class);

        $response = $engineroomClient->request('GET', $endpoint);
        $businessPlanModel = json_decode($response->getBody()->getContents(), true)['data'];
    }

    public static function makeWithPlanAndStageModels(array $businessPlanModel, array $stageModels)
    {
        $plan = new static(
            $businessPlanModel['id'],
            $businessPlanModel['id'],
            array_merge(
                $businessPlanModel,
                [
                    'stages' => $stageModels
                ]
            )
        );

        foreach ($stageModels as $stageModel) {
            $plan->addPage($stageModel);
        }

        return $plan;
    }
}
