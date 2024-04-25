<?php

namespace PauloLeo\LaravelQJS;

use PauloLeo\LaravelQJS\QJS;

class HTML
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
        $table = "";
        $query = $data->query;
        $data = $data->data;

        if (!$query) {
            $data = $this->adapterData($data);
        }

        if (!is_array($data[0])) {
            $data = [$data];
        }

        foreach ($data as $rows) {
            $table .= "<tr>";
            foreach ($rows as $row) {
                $table .= "<td>{$row}</td>";
            }
            $table .= "<tr>";
        }
        return $table;
    }

    private function loopRows($data)
    {
        $table = "";
        $items = $data->data;
        foreach ($items as $key=>$value) {
            $value = (object) ['data'=>$value,'query'=>true];
            $table .= $this->rows($value);

        }
        return $table;
    }

    public function render($data)
    {
        header('Content-Type: text/html');
        header('Cache-Control: no-cache');

        try {
          echo "<table border='1'>{$this->body($data)}</table>";
        } catch (\Exception $e) {
            echo "<table border='1'>
                  <tr>
                      <td>Code</td><td>Message</td>
                  </tr>
                  <tr>
                      <td>{$e->getCode()}</td><td>{$e->getMessage()}</td>
                  </tr>
                   </table>";
        }
        exit();
    }
}
