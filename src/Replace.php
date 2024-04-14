<?php

namespace PauloLeo\LaravelQJS;

use Illuminate\Support\Carbon;

class Replace
{
    public function filter($value)
    {
        $value = trim($value);
        $value = str_ireplace('!$', '', $value);
        $index = substr($value, 0, 1);

        if ($index == '$') {
            $value = substr($value, 1);
            $call = explode(':', $value);
            $method = $call[0];
            $argument = $call[1] ?? null;

            if (method_exists($this, $method)) {
                if ($argument) {
                    $value = $this->$method($argument);
                } else {
                    $value = $this->$method();
                }
            }
        }

        return $value;
    }

    private function nowadd($value = 30)
    {
        return Carbon::now()->addDays($value)->toDateTimeString();
    }

    private function nowsub($value = 30)
    {
        return Carbon::now()->subDays($value)->toDateTimeString();
    }

    private function yearadd($value = 1)
    {
        return Carbon::now()->addYears($value)->toDateTimeString();
    }

    private function yearsub($value = 1)
    {
        return Carbon::now()->subYears($value)->toDateTimeString();
    }

    private function monthadd($value = 1)
    {
        return Carbon::now()->addMonths($value)->toDateTimeString();
    }

    private function monthsub($value = 1)
    {
        return Carbon::now()->subMonths($value)->toDateTimeString();
    }

    private function weekadd($value = 2)
    {
        return Carbon::now()->addWeeks($value)->toDateTimeString();
    }

    private function weeksub($value = 2)
    {
        return Carbon::now()->subWeeks($value)->toDateTimeString();
    }

    private function now()
    {
        return Carbon::now()->toDateString();
    }
}