<?php

namespace PauloLeo\LaravelQJS;

class XLS
{
    private function header($data)
    {
        $row = [];
        if (count($data) > 0) {
            $data = $data[0];
            $data = (array) $data;
            $data = array_keys($data);
            foreach ($data as $col) {
                $row[] = $col;
            }
            $row = implode("\t", $row) . "\n";
        }
        return $row;
    }

    private function body($data)
    {
        if (count($data) > 0) {
            $body = [];
            $keys = $data[0];
            $keys = (array) $keys;
            $keys = array_keys($keys);
            foreach ($data as $col) {
                $row = [];
                $item = (array) $col;
                foreach ($keys as $key) {
                    $row[] = $item[$key];
                }
                $body[] = implode("\t", $row);
            }
            return implode("\n", $body);
        } else {
            return '';
        }
    }

    public function render($data,$name='report-system')
    {
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="'.$name.'.xls"');
        header('Cache-Control: max-age=0');

        echo $this->header($data);
        echo $this->body($data);

        exit();
    }
}
