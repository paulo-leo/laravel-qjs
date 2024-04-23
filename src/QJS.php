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
        return $this->build($data, $filters);
    }

    private function buildQuery($data, $filters)
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

    private function dataIn($key, $data)
    {
        if (!is_array($data)) {
            $data = json_encode($data);
        }
        return $data[$key] ?? false;
    }

    public function build($data, $filters = false)
    {
        if (is_string($data)) {
            $data = json_encode($data);
        }

        if (!$this->dataIn('query', $data)) {
            $query = $this->buildQuery($data, $filters);
            return $query;
        }

        $dataQuery = $this->querys($this->dataIn('query', $data), $filters);
        $dataView = $this->view($this->dataIn('view', $data), $dataQuery);

        return (object) [
            'render'=>true,
            'data'=>$dataView
        ];
    }

    private function adpterQuery($querys)
    {
        foreach ($querys as $key => $val) {
            if (is_string($val))
                return ['query' => $querys];
        }
        return $querys;
    }

    private function querys($querys, $filters)
    {
        $querys = $this->adpterQuery($querys);
        $data = array();
        foreach ($querys as $query => $build) {
            $data[$query] = $this->buildQuery($querys[$query], $filters);
        }
        return $data;

    }

    private function toArray($datas)
    {

        $data = array();
        foreach ($datas as $key => $value) {
            $data[$key] = $value->render ? $value->data : [];
        }
        return $data;

    }

    private function countMax($arrays)
    {
        $max = 0;
        foreach ($arrays as $key => $val) {
            $size = count($val);
            if ($size > $max)
                $max = $size;
        }
        return $max;
    }

    private function view($views, $data)
    {
        $data = $this->toArray($data);
        $max = $this->countMax($data);
        $view = array();
        foreach ($views as $key => $value) {
            if (in_array($key, ['header', 'footer'])) {
                $view[$key] = $this->row($value, $data);
            } else {

                $call = $this->getKeyValue($key);

                if ($call->key == 'row') {
                    $view[$call->name] = $this->row($value, $data);
                }

                if ($call->key == 'for' && $call->value) {
                    $view[$call->name] = $this->rows($value, $data[$call->value]);
                }
            }
        }
        return $view;
    }

    private function row($values, $datas)
    {
        if (is_string($values))
            $values = [$values];
        $view = array();
        $i = 0;
        foreach ($values as $data) {

            $point = explode('.', $data);
            if (isset($point[1])) {
                $v = (array) $datas[$point[0]][0] ?? null;
                $view[] = $v ? ($v[$point[1]] ?? null) : null;
            } else {
                $view[] = $point[0] ?? null;
            }
        }
        return $view;
    }

    private function rows($values, $datas)
    {
        if (is_string($values))
            $values = [$values];
        $view = array();
        $i = 0;
        foreach ($datas as $data) {
            $data = (array) $data;
            foreach ($values as $value) {
                $view[$i][$value] = $data[$value] ?? null;
            }
            $i++;
        }
        return $view;
    }

    private function getKeyValue($key)
    {
        $key = explode('@', $key);
        $func = $key[0];
        $param = $key[1] ?? null;
        $param = explode(' as ', $param);
        $name = $param[1] ?? $param[0];
        $param = trim($param[0]);
        return (object) [
            'key' => $func,
            'value' => $param,
            'name' => $name
        ];
    }


}
