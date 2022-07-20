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

        /*
        |--------------------------------------------------------------------------
        | Retention Period - Action Scheduler
        |--------------------------------------------------------------------------
        |
        | How long to keep actions with STATUS_COMPLETE and STATUS_CANCELED within
        | the database after specified number of seconds. However, this does not
        | clean up STATUS_FAILED actions.
        |
        */
        'retention_period' => DAY_IN_SECONDS * 30,

        /*
        |--------------------------------------------------------------------------
        | Failure Period - Action Scheduler
        |--------------------------------------------------------------------------
        |
        | The number of seconds to allow an action to run before it is considered
        | to have failed.
        |
        */
        'failure_period' => MINUTE_IN_SECONDS * 60,

        /*
        |--------------------------------------------------------------------------
        | Timeout Period - Action Scheduler
        |--------------------------------------------------------------------------
        |
        | The number of seconds to allow a queue to run before unclaiming its
        | pending actions. Actions remain pending and can be claimed again.
        |
        */
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