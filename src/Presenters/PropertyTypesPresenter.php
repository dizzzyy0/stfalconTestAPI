<?php
declare(strict_types=1);

namespace App\Presenters;

use App\Enum\PropertyTypes;

class PropertyTypesPresenter
{
    public function present(PropertyTypes $types): array{
        return [
            'id' => $types->value,
            'name' => $types->getName(),
        ];
    }

    public function presentList(array $types): array{
        return array_map(fn (PropertyTypes $type) => $this->present($type), $types);
    }
}
