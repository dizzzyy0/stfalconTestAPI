<?php
declare(strict_types=1);
namespace App\Enum;

enum PropertyTypes: string
{
    case RESIDETIAL = "residential";
    case COMMERCIAL = "commercial";
    case LAND = "land";

    public function getName(): string{
        return match($this){
            self::RESIDETIAL => "Residential Properties",
            self::COMMERCIAL => "Commercial Properties",
            self::LAND => "Land Properties",
        };
    }

    public static function fromId(string $id): ?self{
        return match($id){
            "residential" => self::RESIDETIAL,
            "commercial" => self::COMMERCIAL,
            "land" => self::LAND,
            default => null,
        };
    }
}
