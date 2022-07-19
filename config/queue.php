<?php
return [
    /*
    |--------------------------------------------------------------------------
    | TypeRocket Job Queue - Action Scheduler
    |--------------------------------------------------------------------------
    |
    | The TypeRocket jobs queue system uses the WooCommerce Action Scheduler.
    | If you have a root installation of TypeRocket run the following:
    |
    | > composer require woocommerce/action-scheduler
    |
    | @link https://actionscheduler.org/
    |
    */
    'action_scheduler' => [
        'retention_period' => DAY_IN_SECONDS * 30,
        'failure_period' => MINUTE_IN_SECONDS * 60,
        'timeout_period' => MINUTE_IN_SECONDS * 10,
    ],

    /*
    |--------------------------------------------------------------------------
    | Jobs - Action Scheduler
    |--------------------------------------------------------------------------
    |
    | Jobs must be registered for the Action Scheduler to detect and run
    | your jobs.
    |
    */
    'jobs' => [
    ]
];