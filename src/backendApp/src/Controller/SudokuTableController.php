<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class SudokuTableController extends AbstractController
{
    #[Route('/api/sudoku/table/load')]
    public function load()
    {
        $table = [
            'cells' => [],
        ];
        for ($row = 0; $row < 9; $row++) {
            if (!isset($table['cells'][$row])) {
                $table['cells'][$row] = [];
            }

            for ($col = 0; $col < 9; $col++) {
                $squareId = (floor($col / 3) + 1) + (floor($row / 3) * 3);
                $cell = [
                    'row' => $row + 1,
                    'col' => $col + 1,
                    'groups' => [
                        ['id' => $row + 1, 'type' => 'ROW'],
                        ['id' => $col + 1, 'type' => 'COL'],
                        ['id' => $squareId, 'type' => 'SQR'],
                    ],
                    'value' => $col + 1,
                ];
                $table['cells'][$row][$col] = $cell;
            }
        }

        $response = $this->json($table);

        return $response;
    }
}
