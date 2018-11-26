<?php


namespace App\Http\Controllers;


use App\Models\Field;
use Illuminate\Contracts\Container\Container;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends Controller
{
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function index(Request $request)
    {
        $field = session('field') ?? $this->container->make(Field::class);

        $field->reset();
        $field->generateShips();

        session(['field' => $field]);

        return view('home.index', [
            'field' => $field,
            'debug' => (bool) $request->get('debug', false),
        ]);
    }

    /**
     * @param int $rowNum
     * @param int $colNum
     * @return JsonResponse
     * @throws \RuntimeException
     */
    public function hit(int $rowNum, int $colNum) : JsonResponse
    {
        /** @var Field $field */
        $field = session('field');
        if (! ($field instanceof Field)) {
            throw new \RuntimeException('Wrong state of game field');
        }

        $isHit = false;
        $value = $field->getCell($rowNum, $colNum);
        switch ($value) {
            case Field::CELL_SHIP:
                $isHit = true;
                $field->setCellValue($rowNum, $colNum, Field::CELL_HIT);
                break;
            case Field::CELL_EMPTY:
            case Field::CELL_UNAVAILABLE:
                $field->setCellValue($rowNum, $colNum, Field::CELL_MISS);
                break;
        }

        return JsonResponse::create([
            'hit' => $isHit,
            'cellsLeft' => $field->getLeftShipCellsCount(),
        ]);
    }
}