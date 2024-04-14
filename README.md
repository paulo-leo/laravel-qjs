# Laravel QJS (Query JSON for SQL)

O Laravel QJS é um pacote PHP desenvolvido especificamente para aplicações Laravel, mas também é compatível com o Lumen. Ele oferece uma maneira eficiente e intuitiva de gerar relatórios usando uma linguagem baseada na estrutura JSON.

## Recursos

- **Sintaxe Simples:** A estrutura de uma consulta QJS é simples e não requer uma ordem específica para os comandos.
  
- **Flexibilidade:** O comando "from" é o único obrigatório para gerar um relatório básico, mas é possível realizar relacionamentos entre entidades do esquema do banco de dados, executar agregações e subconsultas nas linhas e utilizar comandos avançados de datas nos filtros dos relatórios.
  
- **Facilidade de Uso:** A estrutura do QJS pode ser facilmente salva em uma string e executada diretamente a partir de uma tabela de relatórios.
  
- **Simplificação de Relatórios Complexos:** Uma das maiores motivações para o desenvolvimento deste recurso é a simplificação de relatórios complexos, tornando o processo mais ágil e eficiente.
  
- **Conversão Nativa:** O QJS oferece suporte nativo para a conversão dos relatórios gerados em tabelas HTML e XLS.

##Instalação via composer:

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
              
               if($report->render) 
                      response()->json($report->data,200);
               else 
                 response()->json($report,200);
    }  
 }
    
```
