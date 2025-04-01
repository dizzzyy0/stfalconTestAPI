<?php
declare(strict_types=1);

namespace App\Presenters;

use App\Enum\PropertyStatus;

class PropertyStatusPresenter
{
    public function present(PropertyStatus $status): array{
        return [
            'id' => $status->value,
            'name' => $status->getName(),
        ];
    }

    public function presentList(array $statuses): array{
        return array_map(fn (PropertyStatus $status) => $this->present($status), $statuses);
    }
}
