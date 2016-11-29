<?php


namespace Benwilkins\Analyst\Clients\Internal\Metrics;


use App\User;
use Benwilkins\Analyst\AnalystDataCollection;
use Benwilkins\Analyst\AnalystDataGroup;
use Benwilkins\Analyst\Period;
use Carbon\Carbon;

class NewUsersMetric extends Metric
{
    /**
     * @inheritdoc
     */
    public function run(Period $period, $params = [])
    {
        $data = new AnalystDataCollection();
        $dataGroup = new AnalystDataGroup();

        $data->setPeriod($period);
        $data->setTotal($this->getTotalNewUsers($period));
        $dataGroup->addDataPoint(['Date', 'Users']);

        /** @var \Carbon\Carbon $interval */
        foreach ($period->interval() as $interval) {
            $dataGroup->addDataPoint([
                $interval->format('M j'),
                $this->getNewUsersCountByDate($interval)
            ]);
        }

        $data->addGroup($dataGroup);
        $data->setGeneratedAt(Carbon::now());
        $data->setGolden($this->isGolden($period));

        return $data;
    }

    /**
     * @param Period $period
     * @return mixed
     */
    protected function getTotalNewUsers(Period $period)
    {
        return User::whereDate('created_at', '>=', $period->start)->whereDate('created_at', '<=', $period->end)->count();
    }

    /**
     * @param $interval
     * @return mixed
     */
    protected function getNewUsersCountByDate($interval)
    {
        return User::whereDate('created_at', $interval->format('Y-m-d'))->count();
    }
}