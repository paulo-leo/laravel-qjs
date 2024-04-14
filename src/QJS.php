<?php

namespace PauloLeo\LaravelQJS;

use PauloLeo\LaravelQJS\XLS;
use PauloLeo\LaravelQJS\HTML;
use PauloLeo\LaravelQJS\Join;
use PauloLeo\LaravelQJS\Filter;
use Illuminate\Support\Facades\DB;

class QJS
{
    public function render($data, $filters = false)
    {
        if (is_array($data)) {
            $data = json_encode($data);
        }

        $json = $data;
        $data = json_decode($json);

        $from = $data->from ?? $data->table ?? false;

        if (!$from) {
            return (object) array(
                'render' => false,
                'message' => "Você deve informar o nome da tabela em 'from' ou 'table' para gerar o relatório."
            );
        }

        $wheres = $data->where ?? $data->wheres ?? false;
        $havings = $data->having ?? false;
        $rows = $data->rows ?? $data->row ?? $data->select ?? false;
        $joins = $data->joins ?? $data->join ?? false;
        $groupBy = $data->groupBy ?? $data->group ?? false;
        $orderBy = $data->orderBy ?? $data->order ?? false;
        $leftJoins = $data->leftJoins ?? $data->left ?? false;
        $rightJoins = $data->rightJoins ?? $data->right ?? false;
        $limit = $data->limit ?? false;
        $paginate = $data->paginate ?? false;
        $type = $data->type ?? 'array';
        $table = DB::table($from);

        $classFilter = new Filter;
        if ($rows) {
            $table = $classFilter->select($table, $rows);
        }

        $classJoin = new Join;

        if ($joins) {
            $table = $classJoin->join($joins, $table);
        }

        if ($leftJoins) {
            $table = $classJoin->left($leftJoins, $table);
        }

        if ($rightJoins) {
            $table = $classJoin->right($rightJoins, $table);
        }

        if ($wheres) {
            $table = $classFilter->where($table, $wheres);
        }

        if ($filters) {
            $table = $classFilter->where($table, $filters);
        }

        if ($groupBy) {
            $table = $classFilter->groupBy($groupBy, $table);
        }

        if ($havings) {
            $table = $classFilter->having($havings, $table);
        }

        if ($orderBy) {
            $table = $classFilter->orderBy($orderBy, $table);
        }

        try {

            if ($limit && !$paginate) {
                $table = $table->limit((is_numeric($limit) ? $limit : 10));
            }

            $data = ($paginate) ? $table->paginate((is_numeric($paginate) ? $paginate : 10)) :
                $table->get();

            return (object) array(
                'render' => true,
                'data' => $data
            );
        } catch (\Exception $e) {
            return (object) array(
                'render' => false,
                'error' => 'Houve um problema na criação do SQL. 
                Por favor, verifique a conexão com o banco de dados ou revise o código da sua consulta de relatório.',
                'report_code' => $json,
                'sql_code' => $e->getMessage()
            );
        }
    }

    public function toXLS($data, $name = 'report-system')
    {
        return (new XLS)->render($data, $name);
    }

    public function toHTML($data, $name = 'report-system')
    {
        return (new HTML)->render($data, $name);
    }
}
