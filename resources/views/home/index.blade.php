<?php
    /** @var \App\Models\Field $field */
?>

@extends('layout')

@section('content')

    <div class="game-field" id="game-field">

        <?php if (isset($debug) && ($debug === true)) {
            $field->debugDumpField($field->getFieldArray());
        } ?>

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

@endsection

