<?php

namespace PauloLeo\LaravelQJS;

use PauloLeo\LaravelQJS\Replace;

class Filter
{
    private $where = false;
    private $index = 0;
    public function where($table, $filters)
    {
        if (is_string($filters))
            $filters = [$filters];

        $replace = new Replace;
        foreach ($filters as $filter) {

            if (is_string($filter)) {
                $filter = explode(",", $filter);
            }

            if (
                !isset($filter[2]) &&
                !in_array($filter[1], ['null', '!null', 'between', '!between', 'in', '!in'])
            ) {
                $filter[2] = $filter[1];
                $filter[1] = '=';
            }

            $key = $filter[0];
            $op = $filter[1];
            $value = $filter[2] ?? '';
            $or = $filter[3] ?? false;
            $or = (bool) $or;

            if ($this->index == 0 && !$or) {
                $this->where = true;
            }

            $or2 = ($or && $this->where);

            switch ($op) {
                case 'in':
                    if (!is_array($value))
                        $value = explode(",", $value);
                    $table = $or2 ? $table->orWhereIn($key, $value) :
                        $table->whereIn($key, $value);
                    break;
                case '!in':
                    if (!is_array($value))
                        $value = explode(",", $value);
                    $table = $or2 ? $table->orWhereNotIn($key, $value) :
                        $table->whereNotIn($key, $value);
                    break;
                case 'between':
                    $value = str_ireplace(['&',';'], '|', $value);
                    $value = explode("|", $value);
                    $table = $or2 ? $table->orWhereBetween($key, [$replace->filter($value[0]), ($replace->filter($value[1]) ?? '')]) :
                        $table->whereBetween($key, [$replace->filter($value[0]), ($replace->filter($value[1]) ?? '')]);
                    break;
                case '!between':
                    $value = str_ireplace(['&',';'], '|', $value);
                    $value = explode("|", $value);
                    $table = $or2 ? $table->orWhereNotBetween($key, [$replace->filter($value[0]), ($replace->filter($value[1]) ?? '')]) :
                        $table->whereNotBetween($key, [$replace->filter($value[0]), ($replace->filter($value[1]) ?? '')]);
                    break;
                case 'null':
                    $table = $or2 ? $table->orWhereNull($key) :
                        $table->whereNull($key);
                    break;
                case '!null':
                    $table = $or2 ? $table->orWhereNotNull($key) :
                        $table->whereNotNull($key);
                    break;
                default:
                    $table = $or2 ? $table->orWhere($key, $op, $replace->filter($value)) :
                        $table->where($key, $op, $replace->filter($value));
            }

            $this->index += 1;
        }
        return $table;
    }

    public function having($havings, $table)
    {
        if (is_string($havings))
            $havings = [$havings];
        foreach ($havings as $having) {
            if (is_string($having))
                $having = explode(',', $having);
            if (!isset($having[2])) {
                $having[2] = $having[1];
                $having[1] = '=';
            }
            $table = $table->having($having[0], $having[1], $having[2]);
        }
        return $table;
    }

    public function orderBy($orderBy, $table)
    {
        if (is_string($orderBy))
            $orderBy = [$orderBy];
        foreach ($orderBy as $order) {
            $order = explode(',', $order);
            $key = $order[0];
            $type = $order[1] ?? 'asc';
            $type = strtolower($type);
            $type = !in_array($type, ['asc', 'desc']) ? 'asc' : $type;
            $table = $table->orderBy($key, $type);
        }
        return $table;
    }

    public function select($table, $rows)
    {
        $rows = !is_array($rows) ? explode(',', $rows) : $rows;
        $selects = [];
        foreach ($rows as $row) {
            $trimmedRow = trim($row);
            $index = substr($trimmedRow, 0, 1);
            if ($index == '$') {
                $selects[] = DB::raw(substr($trimmedRow, 1));
            } else {
                $selects[] = $trimmedRow;
            }
        }

        $table = $table->select($selects);
        return $table;
    }

    public function groupBy($groupBy, $table)
    {
        $groupBy = !is_array($groupBy) ? explode(',', $groupBy) : $groupBy;
        foreach ($groupBy as $group) {
            $table = $table->groupBy($group);
        }
        return $table;
    }
}
