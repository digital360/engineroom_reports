<?php


namespace App\Publishing\Publications\BusinessPlan\Jobs;


use App\Engineroom\Client;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Cache;

class RetrieveStageModel implements ShouldQueue
{
    use Dispatchable;

    protected $stageKey;
    protected $businessPlanModel;

    protected $engineroomClient;

    public function __construct(string $stageKey, array $businessPlanModel)
    {
        $this->stageKey = $stageKey;
        $this->businessPlanModel = $businessPlanModel;
    }

    public function handle(Client $engineroomClient)
    {
        $this->engineroomClient = $engineroomClient;
        $this->storeModel($this->retrieveStageModel());
    }

    protected function retrieveStageModel()
    {
        $endpoint = 'plan/' . $this->businessPlanModel['id'] .
            '/' . $this->stageKey .
            '?business=' . $this->businessPlanModel['business']['id'];

        $response = $this->engineroomClient->request('GET', $endpoint);

        return json_decode($response->getBody()->getContents(), true)['data'];
    }

    protected function storeModel(array $model): void
    {
        $key = $this->businessPlanModel['id'] . '_' . $this->stageKey;
        Cache::put($key, $model, now()->addHours(12));
    }
}
