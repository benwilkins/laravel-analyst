<?php


namespace Benwilkins\Analyst\Clients\Internal\Metrics;


use App\User;
use Benwilkins\Analyst\Period;

class NewUsersMetric extends Metric
{
    /**
     * @inheritdoc
     */
    public function run(Period $period, $params = [])
    {
        $data = [
            'total' => User::whereDate('created_at', '>=', $period->start)->whereDate('created_at', '<=', $period->end)->count(),
            'points' => [['Date', 'Users']]
        ];

        /** @var \Carbon\Carbon $interval */
        foreach ($period->interval() as $interval) {
            array_push($data['points'], [
                $interval->format('M j'),
                User::whereDate('created_at', $interval->format('Y-m-d'))->count()
            ]);
        }

        return collect($data);
    }
}