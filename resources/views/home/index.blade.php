<?php
    /** @var \App\Models\Field $field */
?>

@extends('layout')

@section('content')

    <div class="game-field" id="game-field">

        <?php $field->debugDumpField($field->getFieldArray()); ?>
    @foreach($field->getFieldArray() as $y => $row)

        <div class="game-field-row">
            @foreach($row as $x => $cell)
                @php
                    $typeClass = $field::CELL_EMPTY;
                    switch ($cell) {
                        case $field::CELL_HIT:
                            $typeClass = 'hit';
                            break;
                        default:
                            $typeClass = 'empty';
                            break;
                    }
                @endphp
                <div id="cell-{{$y}}-{{$x}}" class="game-field-cell cell-{{ $typeClass }}" data-row="{{$y}}" data-col="{{$x}}"></div>
            @endforeach
        </div>
    @endforeach

    </div>


    <script>
//        var $ = function(idOrClass) {
//          var type = String(idOrClass).substr(0, 1);
//          var value = String(idOrClass).substr(1);
//          var elem;
//          switch (type) {
//            case '#':
//              elem = window.document.getElementById(value);
//              break;
//            case '.':
//              elem = window.document.getElementsByClassName(value);
//              break;
//          }
//
//          return elem;
//        };


        function sendHit(rowNum, colNum, cb) {
          const xhr = new XMLHttpRequest();
          xhr.onreadystatechange = function() {
            if (parseInt(xhr.readyState) !== 4) {
              return;
            }

            cb(xhr.responseText);
          };

          xhr.open('GET', '/hit/' + rowNum + '/' + colNum, true);
          xhr.send();
        }

        addEventListener('click', function(e) {
          var node = e.target;
          if (node.className.indexOf('cell-empty') !== -1) {
            var rowNum = node.attributes['data-row'].value;
            var colNum = node.attributes['data-col'].value;

            sendHit(rowNum, colNum, function (responseText) {
              var json = JSON.parse(responseText);
              console.log(responseText, json);
              var isHit = json.hit;
              var left = json.cellsLeft;

              var cell = $('#cell-' + rowNum + '-' + colNum);
              console.log(cell, cell.className);
              cell.className = cell.className.replace('cell-empty', isHit ? 'cell-hit' : 'cell-miss');

              // TODO: обработка события утопления всех кораблей: isCompleted = true

            });
          }

        });
    </script>

@endsection

