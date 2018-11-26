<?php

namespace App\Models;

class Field
{
    public const FIELD_WIDTH  = 10;
    public const FIELD_HEIGHT = 10;

    public const CELL_EMPTY       = 0;
    public const CELL_UNAVAILABLE = 1;
    public const CELL_SHIP        = 2;
    public const CELL_HIT         = 3;
    public const CELL_MISS        = 4;

    private const ALLOWED_CELL_VALUES = [
        self::CELL_EMPTY,
        self::CELL_UNAVAILABLE,
        self::CELL_SHIP,
        self::CELL_HIT,
        self::CELL_MISS,
    ];

    private const SHIP_L = 'L';
    private const SHIP_I = 'I';
    private const SHIP_1 = '.';

    private const SHIP_L_CELLS = 4;
    private const SHIP_I_CELLS = 4;
    private const SHIP_1_CELLS = 1;

    private $field = [];
    private $shipCellsLeft;

    /**
     * Reset game field, set all cells to empty value
     */
    public function reset() : void
    {
        $this->field = [];
        for ($i = 0; $i < self::FIELD_HEIGHT; $i++) {
            $this->field[$i] = array_fill(0, self::FIELD_WIDTH, self::CELL_EMPTY);
        }
    }

    /**
     * Fill game field with ships
     */
    public function generateShips() : void
    {
        $this->shipCellsLeft = 0;

        $pack = [
            self::SHIP_L => 1,
            self::SHIP_I => 1,
            self::SHIP_1 => 2
        ];

        foreach ($pack as $type => $count) {
            for ($i = 0; $i < $count; $i++) {
                switch ($type) {
                    case self::SHIP_L:
                        $this->generateLShip($this->field);
                        $this->shipCellsLeft += self::SHIP_L_CELLS;
                        break;
                    case self::SHIP_I:
                        $this->generateIShip($this->field);
                        $this->shipCellsLeft += self::SHIP_I_CELLS;
                        break;
                    case self::SHIP_1:
                        $this->generate1Ship($this->field);
                        $this->shipCellsLeft += self::SHIP_1_CELLS;
                        break;
                }
            }
        }
    }

    private function generateLShip(&$field) : void
    {
        // x   xxx  Xx    X
        // x   X     x  xxx
        // xX        x

        $success = false;

        while (!$success) {
            $f = [];
            $x = random_int(0, 9);
            $y = random_int(0, 9);

            $o = random_int(0, 3); // rotate: 0, 90, 180, 270

            switch ($o) {
                case 0: // 0
                    if ($x >= 1 && $y >= 2) {
                        $f[$y][$x] = $f[$y][$x - 1] = $f[$y - 1][$x - 1] = $f[$y - 2][$x - 1] = self::CELL_SHIP;
                    }
                    break;
                case 1: // 90
                    if ($x <= 7 && $y >= 1) {
                        $f[$y][$x] = $f[$y - 1][$x] = $f[$y - 1][$x + 1] = $f[$y - 1][$x + 2] = self::CELL_SHIP;
                    }
                    break;
                case 2: // 180
                    if ($x <=8 && $y <= 7) {
                        $f[$y][$x] = $f[$y][$x + 1] = $f[$y + 1][$x + 1] = $f[$y + 2][$x + 1] = self::CELL_SHIP;
                    }
                    break;
                case 3: // 270
                    if ($x >=2 && $y <= 8) {
                        $f[$y][$x] = $f[$y + 1][$x] = $f[$y + 1][$x - 1] = $f[$y + 1][$x - 2] = self::CELL_SHIP;
                    }
                    break;
            }

            // check field cells: if ship fit to it, then $success = true
            if ($this->checkCellsAroundForAvailability($field, $f)) {
                $this->setShipCellsToField($field, $f);
                $success = true;
            }
        }
    }

    private function generateIShip(&$field) : void
    {
        // x   Xxxx  X  xxxX
        // x         x
        // x         x
        // X         x
        $success = false;

        while (!$success) {
            $f = [];
            $x = random_int(0, 9);
            $y = random_int(0, 9);

            $o = random_int(0, 0); // rotate: 0, 90, 180, 270

            switch ($o) {
                case 0: // 0
                    if ($y >= 3) {
                        $f[$y][$x] = $f[$y - 1][$x] = $f[$y - 2][$x] = $f[$y - 3][$x] = self::CELL_SHIP;
                    }
                    break;
                case 1: // 90
                    if ($x <= 6) {
                        $f[$y][$x] = $f[$y][$x + 1] = $f[$y][$x + 2] = $f[$y][$x + 3] = self::CELL_SHIP;
                    }
                    break;
                case 2: // 180
                    if ($y <= 6) {
                        $f[$y][$x] = $f[$y + 1][$x] = $f[$y + 2][$x] = $f[$y + 3][$x] = self::CELL_SHIP;
                    }
                    break;
                case 3: // 270
                    if ($x >= 3) {
                        $f[$y][$x] = $f[$y][$x - 1] = $f[$y][$x - 2] = $f[$y][$x - 3] = self::CELL_SHIP;
                    }
                    break;
            }

            // check field cells: if ship fit to it, then $success = true
            if ($this->checkCellsAroundForAvailability($field, $f)) {
                $this->setShipCellsToField($field, $f);
                $success = true;
            }
        }
    }

    private function generate1Ship(array &$field) : void
    {
        $success = false;

        while (!$success) {
            $f = [];
            $x = random_int(0, 9);
            $y = random_int(0, 9);

            if ($field[$x][$y] === self::CELL_EMPTY) {
                $f[$y][$x] = self::CELL_SHIP;
            }

            // check field cells: if ship fit to it, then $success = true
            if ($this->checkCellsAroundForAvailability($field, $f)) {
                $this->setShipCellsToField($field, $f);
                $success = true;
            }
        }
    }

    /**
     * Check cells is nearest to ship as unavailable
     * @param array $field
     * @param array $ship
     * @return bool
     */
    private function checkCellsAroundForAvailability(array &$field, array $ship) : bool
    {
        if (empty($field) || empty($ship)) {
            return false;
        }

        $isAvailable = true;
        foreach ($ship as $y => $row) {
            foreach ($row as $x => $cell) {
                if (isset($field[$y][$x]) && $field[$y][$x] !== self::CELL_EMPTY) {
                    return false;
                }
            }
        }

        return $isAvailable;
    }

    /**
     * Place ship to field and mark nearest cells as unavailable
     * @param array $field
     * @param array $ship
     */
    private function setShipCellsToField(array &$field, array $ship) : void
    {
        foreach ($ship as $y => $shipRow) {
            foreach ($shipRow as $x => $cell) {
                $cells = [
                    [$y - 1, $x - 1],
                    [$y - 1, $x],
                    [$y - 1, $x + 1],
                    [$y, $x - 1],
                    [$y, $x + 1],
                    [$y + 1, $x - 1],
                    [$y + 1, $x],
                    [$y + 1, $x + 1],
                ];
                if (isset($field[$y][$x]) && $field[$y][$x] === self::CELL_EMPTY) {
                    $field[$y][$x] = self::CELL_SHIP;
                    foreach ($cells as [$cY, $cX]) {
                        if (isset($field[$cY][$cX]) && !isset($ship[$cY][$cX])) {
                            $field[$cY][$cX] = self::CELL_UNAVAILABLE;
                        }
                    }

                }
            }
        }
    }

    /**
     * Возвращает текущее состояние поля в виде двумерного массива
     * @return array
     */
    public function getFieldArray() : array
    {
        return $this->field;
    }

    /**
     * @param int $rowNum
     * @param int $colNum
     * @return int
     */
    public function getCell(int $rowNum, int $colNum) : int
    {
        return $this->field[$rowNum][$colNum];
    }

    /**
     * @param int $rowNum
     * @param int $colNum
     * @param int $value
     */
    public function setCellValue(int $rowNum, int $colNum, int $value) : void
    {
        if (!in_array($value, self::ALLOWED_CELL_VALUES)) {
            throw new \InvalidArgumentException('Wrong cell value');
        }

        $oldValue = $this->field[$rowNum][$colNum];
        if ($value !== self::CELL_SHIP && $oldValue === self::CELL_SHIP) {
            $this->shipCellsLeft--;
        } elseif ($value === self::CELL_SHIP && $oldValue !== self::CELL_SHIP) {
            $this->shipCellsLeft++;
        }

        $this->field[$rowNum][$colNum] = $value;
    }

    /**
     * Returns count of left ship cells
     * @return int
     */
    public function getLeftShipCellsCount() : int
    {
        return $this->shipCellsLeft;
    }


    /**
     * Debug field dump
     * @param $field
     */
    public function debugDumpField($field) : void
    {
        echo "<pre>";
        foreach ($field as $row) {
            $str = implode(' ', $row);
            echo $str . "\n";
        }
        echo "</pre>";
    }

}