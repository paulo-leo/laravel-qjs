<?php

namespace PauloLeo\LaravelQJS;

use PauloLeo\LaravelQJS\QJS;

class XLS
{
    private function body($data)
    {
        $qjs = new QJS;
        if (!$data->query) {
            return $this->rows($data);
        } else {
            return $this->loopRows($data);
        }
    }

    private function adapterData($data)
    {
        $result = array();
        $i = 0;
        foreach ($data as $rows) {
            foreach ($rows as $row) {
                $result[$i][] = $row;
            }
            $i++;
        }
        return $result;
    }

    private function rows($data)
    {
        $table = [];
        $query = $data->query;
        $data = $data->data;

        if (!$query) {
            $data = $this->adapterData($data);
        }

        if (!is_array($data[0])) {
            $data = [$data];
        }

        foreach ($data as $rows) {
            $xls = [];
            foreach ($rows as $row) {
                $xls[] = $row;
            }
            $table[] = implode("\t", $xls);
        }
        return implode("\n", $table);
    }

    private function loopRows($data)
    {
        $table =[];
        $items = $data->data;
        foreach ($items as $key => $value) {
            $value = (object) ['data' => $value, 'query' => true];
            $table[] = $this->rows($value);

        }
        return implode("\n", $table);
    }

    public function render($data, $name = 'report-system')
    {
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $name . '.xls"');
        header('Cache-Control: max-age=0');

        try {
            echo $this->body($data);
        } catch (\Exception $e) {
            echo "\tCode\tMessage\n";
            echo "\t{$e->getCode()}\t{$e->getMessage()}";
        }
        exit();
    }
}
