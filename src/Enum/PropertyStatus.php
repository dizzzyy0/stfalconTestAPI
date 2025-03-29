<?php
declare(strict_types=1);
namespace App\Enum;

enum PropertyStatus: string
{
    case DRAFT = 'draft';
    case AVAILABLE = 'available';
    case UNDER_CONTRACT = 'under_contract';
    case SOLD = 'sold';
    case OFF_MARKET = 'off_market';

    public function getName():string{
        return match($this){
          self::DRAFT => 'Draft',
          self::AVAILABLE => 'Available',
          self::UNDER_CONTRACT => 'Under Contract',
          self::SOLD => 'Sold',
          self::OFF_MARKET => 'Off Market',
        };
    }

    public static function fromId(string $id): ?self{
        return match($id){
            "draft" => self::DRAFT,
            "available" => self::AVAILABLE,
            "under_contract" => self::UNDER_CONTRACT,
            "sold" => self::SOLD,
            "off_market" => self::OFF_MARKET,
            default => null,
        };
    }
}
