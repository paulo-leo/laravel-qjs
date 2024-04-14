<?php

namespace PauloLeo\LaravelQJS;

class HTML
{
    private function header($data)
    {
        $row = "<tr>";
        if (count($data) > 0) {
            $data = $data[0];
            $data = (array) $data;
            $data = array_keys($data);
            foreach ($data as $col) {
                $row .= "<th>{$col}</th>";
            }
            $row .= "</tr>";
        }
        return $row;
    }

    private function body($data)
    {
        if (count($data) > 0) {
            $row = '';
            $keys = $data[0];
            $keys = (array) $keys;
            $keys = array_keys($keys);
            foreach ($data as $col) {
                $row .= "<tr>";
                $item = (array) $col;
                foreach ($keys as $key) {
                    $row .= "<td>{$item[$key]}</td>";
                }
                $row .= "</tr>";
            }
            return $row;
        } else {
            return '';
        }
    }

    public function render($data, $name = 'report-system')
    {
        header('Content-Type: text/html');
        header('Cache-Control: no-cache');
        
        echo "<table><thead>{$this->header($data)}</thead><tbody>{$this->body($data)}</tbody></table>";

        exit();
    }
}
