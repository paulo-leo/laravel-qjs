# Laravel QJS (Query JSON for SQL)

O Laravel QJS é um pacote PHP desenvolvido especificamente para aplicações Laravel, mas também é compatível com o Lumen. Ele oferece uma maneira eficiente e intuitiva de gerar relatórios usando uma linguagem baseada na estrutura JSON.
## Introdução ao QJS: Uma Linguagem Declarativa para Geração Dinâmica de Relatórios
O QJS é uma linguagem declarativa que pode ser escrita tanto em JSON quanto em arrays associativos do PHP. Desenvolvida por Paulo Leonardo Da S. Cassimiro, sua principal finalidade é resolver o desafio recorrente de geração de relatórios. A criação de relatórios é uma tarefa altamente dinâmica, cuja complexidade varia de acordo com o cenário específico. A proposta por trás dessa linguagem é permitir o armazenamento de instruções para a renderização de relatórios em arquivos de texto ou campos de banco de dados, podendo ser chamados em tempo real.

A ideia de construir o QJS usando o formato JSON foi inspirada no uso de estados de componentes em aplicações reativas, como React, Vue, Angular, entre outras. Isso possibilita a construção de relatórios em tempo real, dependendo da implementação. As instruções da linguagem seguem as mesmas regras do SQL comum, com a diferença de que as declarações não precisam seguir uma ordem específica, sendo o único campo obrigatório o from, enquanto os demais refinam o seu relatório.

Em resumo, o QJS pode ser considerado um protocolo para a criação de relatórios baseados em estados JSON.

## Uma consulta QJS pode ser interpretada da seguinte maneira:

```json
      {
	"from":"categories",
	"where":"created_at,between,$nowsub:30;$now"
      }
```
Nesta consulta, especificamos que um relatório será gerado para todas as categorias (from) do sistema, onde (where) as categorias foram criadas (created_at) dentro do intervalo (between) dos últimos 30 dias até a data atual ($nowsub:30;$now). Os placeholders $nowsub e $now são métodos avançados de data que podem ser utilizados em qualquer filtro, exceto nos operadores in e !in. O prefixo $ indica o uso de um comando interno.

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
## Guia QJS

Listar todos os usuários do sistema:
```json
{
  "from": "users"
}
```

Listar todos os usuários e trazer somente os campos id e name:
```json
{
  "from": "users",
  "rows": "id, name"
}
```

Listar todos os usuários e trazer somente os campos id e name  e renomeia o campo name para cliente:
```json
{
  "from": "users",
  "rows": "id, name as cliente"
}
```

Listar todos os usuários, trazendo apenas os campos id e name, e contar quantos existem:
```json
{
  "from": "users",
  "rows": "id, name, $count(*) as total",
  "group":"id,name"
}
```

Observação: Sempre que executar uma subconsulta, adicione o caractere $ antes do comando SQL ou da função de agregação desejada. O $ indica que você está realizando uma subconsulta ou uma função de agregação. Além disso, ao utilizar uma subconsulta com campos não agregados, é importante incluir o comando group e passar os campos não agregados nele para garantir que o resultado seja agrupado corretamente. No entanto, no exemplo abaixo, não é necessário usar o comando group, pois as regras do SQL se aplicam aqui:
```json
{
  "from": "users",
  "rows": "$count(*) as total"
}
```

Você pode usar a declaração where para aplicar filtros ao seu relatórios:

```json
{
  "from": "users",
  "rows": "id, name",
  "where":"id,10"
}
```

O comando acima busca o usuário ao qual o id é igual a "10", quando é omitido o operador, query considera como igual "=". Se você quiser passar um operador, você deverá escrever da seguinte maneira:

```json
{
  "from": "users",
  "rows": "id, name",
  "where":"id,!=,10"
}
```

Para passar mais de um filtro, você pode escrever da seguinte forma:

```json
{
  "from": "users",
  "rows": "id, name",
  "where":[
  ["id",">","1"],
  ["name","like","a%"]
  ]
}
```

Para passar mais de um filtro, você pode escrever da seguinte forma:

```json
{
  "from": "users",
  "rows": "id, name",
  "where":[
    ["id", ">", "1"],
    ["name", "like", "a%"]
  ]
}
```

O mesmo código pode ser escrito assim:

```json
{
  "from": "users",
  "rows": "id, name",
  "where":["id,>,1","name,like,a%"]
}
```

Se desejar passar um operador OR após o primeiro filtro, basta adicionar um quarto valor no filtro, um booleano true indicando que será aplicado o operador OR:

```json
{
  "from": "users",
  "rows": "id, name",
  "where":[
  ["id",">","1"],
  ["name","like","a%",true]]
}
```

Além disso, você pode usar os seguintes operadores para lidar com intervalos, valores nulos e listagem:
Para intervalos entre dois valores, utilize o operador between:

```json
{
  "from": "users",
  "rows": "id, name",
  "where":[
    ["id","between","1|10"]
  ]
}
```

Para valores nulos:

```json
{
  "from": "users",
  "rows": "id, name",
  "where":[
    ["id","null"]
  ]
}
```

Para uma lista de valores:

```json
{
  "from": "users",
  "rows": "id, name",
  "where":[
    ["id","in",[1,2,3]]
  ]
}
```

Se desejar negar a busca utilizando esses operadores, basta adicionar o caractere ! na frente do operador between, null e in:

```json
{
  "from": "users",
  "rows": "id, name",
  "where":[
    ["id","!null"],
	 ["id","!in",[1,2,3]],
	  ["id","!between","1|10"] 
  ]
}
```

Agora, se você declarou um método de agregação na row e deseja filtrar a agregação, pode usar a declaração having da seguinte forma:

```json
{
  "from": "users",
  "rows": "id, name, $count(*) as total",
  "group": "id, name",
  "having": [
    ["total", ">", "10"]
  ]
}
```

Você também pode ordenar o seu relatório utilizando a declaração order. Aqui estão dois exemplos de como fazer isso:
Ordenar por uma coluna em ordem descendente:

```json
{
  "from": "users",
  "order": "id,desc"
}
```

Ordenar por múltiplas colunas, onde a primeira é ordenada em ordem descendente e a segunda em ordem ascendente:

```json
{
  "from": "users",
  "order": ["id,desc","name"]
}
```

A funcionalidade de junção de entidades no QJS assemelha-se aos JOINS do SQL, utilizando três métodos fundamentais. Com base na teoria dos conjuntos, é possível empregar os métodos "join" para uma junção total, quando há referência entre as duas entidades; "left", quando a prioridade é dada à tabela à esquerda; e "right", quando a prioridade é atribuída à tabela à direita.
```json
{
  "from": "users",
  "rows":"users.id,users.name,categories.name as category",
  "join":"categories,categories.id,users.category_id"
}
```

Para realizar múltiplas junções, segue-se a seguinte sintaxe:

```json
{
  "from": "users",
  "rows": "users.id, users.name, categories.name as category",
  "left": [
    "categories, categories.id, users.category_id",
    "drivers, drivers.id, users.driver_id"
  ]
}
```

Alternativamente, pode-se utilizar a seguinte estrutura:

```json
{
  "from": "users",
  "rows": "users.id, users.name, categories.name as category",
  "left": [
    ["categories", "categories.id", "users.category_id"],
    ["drivers", "drivers.id", "users.driver_id"]
  ]
}
```
