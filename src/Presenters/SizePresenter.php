<?php
declare(strict_types=1);

namespace App\Presenters;

use App\Types\Size;

class SizePresenter
{
    public function present(Size $size): array{
        return [
          'id' => $size->getValue(),
          'measurement' => $size->getMeasurement()
        ];
    }

    public function presentList(array $sizes): array{
        return array_map(fn (Size $size) => $this->present($size), $sizes);
    }
}
