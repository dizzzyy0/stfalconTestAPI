<?php
declare(strict_types=1);
namespace App\Enum;

enum Currencies: string
{
    case UAH = "uah";
    case USD = "usd";
    case EUR = "eur";

    public function getName(): string
    {
        return match($this) {
            self::UAH => 'UAH',
            self::USD => 'USD',
            self::EUR => 'EUR',
        };
    }

    public static function fromId(string $id): ?self{
        return match($id){
            "uah" => self::UAH,
            "usd" => self::USD,
            "eur" => self::EUR,
            default => null,
        };
    }
}
