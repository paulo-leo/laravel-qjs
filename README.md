# Laravel QJS (Query JSON for SQL)

O Laravel QJS é um pacote PHP desenvolvido especificamente para aplicações Laravel, mas também é compatível com o Lumen. Ele oferece uma maneira eficiente e intuitiva de gerar relatórios usando uma linguagem baseada na estrutura JSON.

## Recursos

- **Sintaxe Simples:** A estrutura de uma consulta QJS é simples e não requer uma ordem específica para os comandos.
  
- **Flexibilidade:** O comando "from" é o único obrigatório para gerar um relatório básico, mas é possível realizar relacionamentos entre entidades do esquema do banco de dados, executar agregações e subconsultas nas linhas e utilizar comandos avançados de datas nos filtros dos relatórios.
  
- **Facilidade de Uso:** A estrutura do QJS pode ser facilmente salva em uma string e executada diretamente a partir de uma tabela de relatórios.
  
- **Simplificação de Relatórios Complexos:** Uma das maiores motivações para o desenvolvimento deste recurso é a simplificação de relatórios complexos, tornando o processo mais ágil e eficiente.
  
- **Conversão Nativa:** O QJS oferece suporte nativo para a conversão dos relatórios gerados em tabelas HTML e XLS.

## Instalação via composer:

```bash
    composer require paulo-leo/laravel-qjs:dev-main
```

## Exemplo de uso

```php
<?php

namespace App\Http\Controllers;

use PauloLeo\LaravelQJS\QJS;

 class ReportController extends Controller{

    public function render(Request $request){

               $qjs = new QJS;
               $query = $request->all();
               $report = $qjs->render($query);
              
               if($report->render){
                    response()->json($report->data,200);
               }else{
                    response()->json($report,422);
               }      
         }  
  }
    
```

## Convertendo em XLS(Excel) ou HTML

```php

   <?php

    namespace App\Http\Controllers;

    use PauloLeo\LaravelQJS\QJS;

    public function render(Request $request){

               $qjs = new QJS;
               $query = $request->all();
               $report = $qjs->render($query);
              
               if($request->type == 'xls' && $report->render) 
                      return $qjs->toXLS($report->data);

              if($request->type == 'html' && $report->render) 
                      return $qjs->toHTML($report->data);

            
              return response()->json($report);    
       }  
   }
    
```

O método render é o núcleo da classe, responsável por processar a consulta que pode ser especificada em formato JSON ou como um array associativo. Seu primeiro parâmetro é a própria consulta, enquanto o segundo refere-se aos filtros aplicados. Esses filtros podem ser passados como um array associativo ou como uma string em formato JSON.

O método render sempre retorna um objeto contendo a chave render, a qual pode assumir os valores true ou false. Quando true, indica que a renderização do relatório foi bem-sucedida, devolvendo, então, a chave data contendo os dados do relatório. Esses dados são organizados como um array indexado, onde cada índice contém um objeto com as informações a serem apresentadas no relatório.

Aqui está um exemplo de uso:
```php
$query = array(
    "from" => "users"
);

$filter = array(
    array("name", "!=", "Carlos")
);

$qjs = new QJS;

return response()->json($qjs->render($query, $filter));
```
