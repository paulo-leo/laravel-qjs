<?php

namespace PauloLeo\LaravelQJS;

class Join
{
    public function join($joins, $table)
    {
        if (is_string($joins))
            $joins = [$joins];
        foreach ($joins as $join) {
            if (is_string($join))
                $join = explode(',', $join);
            $table = isset($join[3]) ?
                $table->join($join[0], $join[1], $join[2], $join[3]) :
                $table->join($join[0], $join[1], '=', $join[2]);
        }
        return $table;
    }

    public function left($leftJoins, $table)
    {
        if (is_string($leftJoins))
            $leftJoins = [$leftJoins];
        foreach ($leftJoins as $join) {
            if (is_string($join))
                $join = explode(',', $join);
            $table = isset($join[3]) ?
                $table->leftJoin($join[0], $join[1], $join[2], $join[3]) :
                $table->leftJoin($join[0], $join[1], '=', $join[2]);
        }
        return $table;
    }

    public function right($rightJoins, $table)
    {
        if (is_string($rightJoins))
            $rightJoins = [$rightJoins];
        foreach ($rightJoins as $join) {
            if (is_string($join))
                $join = explode(',', $join);
            $table = isset($join[3]) ?
                $table->rightJoin($join[0], $join[1], $join[2], $join[3]) :
                $table->rightJoin($join[0], $join[1], '=', $join[2]);
        }
        return $table;
    }
}
