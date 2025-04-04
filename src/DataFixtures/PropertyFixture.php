<?php

namespace App\DataFixtures;

use App\DTO\Property\CreatePropertyDTO;
use App\Entity\Agent;
use App\Entity\User;
use App\Enum\Currencies;
use App\Enum\PropertyStatus;
use App\Enum\PropertyTypes;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Uid\Uuid;

class PropertyFixture extends Fixture implements DependentFixtureInterface
{
    private const array ADDRESSES = [
        'Київ, вул. Хрещатик, 22',
        'Львів, пл. Ринок, 1',
        'Одеса, вул. Дерибасівська, 10',
        'Харків, вул. Сумська, 25',
        'Дніпро, пр. Яворницького, 67',
        'Запоріжжя, пр. Соборний, 158',
        'Вінниця, вул. Соборна, 12',
        'Івано-Франківськ, вул. Незалежності, 5',
        'Чернівці, вул. Кобилянської, 20',
        'Ужгород, вул. Корзо, 8',
    ];

    private const array DESCRIPTIONS = [
        'Затишна квартира в центрі міста з прекрасним виглядом на парк. Включає просторий зал, дві спальні, сучасну кухню та ванну кімнату. Паркувальне місце включено.',
        'Комерційне приміщення на першому поверсі бізнес-центру. Великі вікна, високі стелі та зручний доступ. Ідеально для магазину, офісу або кафе.',
        'Просторий офіс у діловому центрі міста. Оснащений сучасними комунікаціями, кондиціонерами, та системою безпеки. Є кімнати для переговорів.',
        'Земельна ділянка на околиці міста. Доступні всі комунікації: вода, газ, електрика. Чудовий варіант для будівництва приватного будинку.',
        'Розкішний пентхаус з панорамними вікнами та терасою. Представницький вестибюль, консьєрж-сервіс, підземний паркінг. Ідеальне розташування.',
        'Промислове приміщення з великими виробничими площами, складськими зонами та офісними кімнатами. Зручна логістика.',
        'Земельна ділянка сільськогосподарського призначення з доступом до дороги. Родючі ґрунти, підходить для різноманітних культур.',
        'Сучасний котедж з басейном, гаражем та садом. Розумний будинок з системою безпеки. Розташований у престижному передмісті.',
        'Історична будівля в центрі міста. Зберігає архітектурні особливості, але повністю модернізована всередині. Підходить для готелю чи ресторану.',
        'Новозбудована квартира з дизайнерським ремонтом. Енергоефективні технології, підігрів підлоги, панорамні вікна. Розвинена інфраструктура.',
    ];

    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {}

    public function getDependencies(): array
    {
        return [
            // Add your dependencies here, e.g. AgentFixture or UserFixture
            // This ensures that agents are created before properties
            UserFixture::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $agents = $this->entityManager->getRepository(Agent::class)->findAll();
        $agentCount = count($agents);

        if ($agentCount === 0) {
            throw new \RuntimeException('No agents found in the database. Please load agent fixtures first.');
        }

        $propertyTypes = [
            PropertyTypes::RESIDETIAL->value,
            PropertyTypes::COMMERCIAL->value,
            PropertyTypes::LAND->value
        ];

        $currencies = [
            Currencies::UAH->value,
            Currencies::USD->value,
            Currencies::EUR->value
        ];

        $statuses = [
            PropertyStatus::DRAFT->value,
            PropertyStatus::AVAILABLE->value,
            PropertyStatus::UNDER_CONTRACT->value,
            PropertyStatus::SOLD->value,
            PropertyStatus::OFF_MARKET->value
        ];

        $measurements = ['м²', 'га', 'сот'];

        for ($i = 0; $i < 50; $i++) {
            $propertyType = $propertyTypes[array_rand($propertyTypes)];
            $currency = $currencies[array_rand($currencies)];
            $status = $statuses[array_rand($statuses)];
            $measurement = $measurements[array_rand($measurements)];

            $agent = $agents[array_rand($agents)];
            $agentId = $agent->getId();

            $priceAmount = match($propertyType) {
                PropertyTypes::RESIDETIAL->value => mt_rand(30000, 200000),
                PropertyTypes::COMMERCIAL->value => mt_rand(50000, 500000),
                PropertyTypes::LAND->value => mt_rand(5000, 100000),
                default => mt_rand(10000, 50000),
            };

            if ($currency === Currencies::UAH->value) {
                $priceAmount *= 30;
            }

            $sizeValue = match($measurement) {
                'м²' => mt_rand(30, 500),
                'га' => mt_rand(1, 50),
                'сот' => mt_rand(6, 25),
                default => mt_rand(50, 200),
            };

            $latitude = mt_rand(4840, 5110) / 100;
            $longitude = mt_rand(2300, 3670) / 100;

            $address = self::ADDRESSES[array_rand(self::ADDRESSES)];
            $description = self::DESCRIPTIONS[array_rand(self::DESCRIPTIONS)];

            $createPropertyDTO = new CreatePropertyDTO(
                type: $propertyType,
                priceAmount: $priceAmount,
                priceCurrency: $currency,
                address: $address,
                latitude: $latitude,
                longitude: $longitude,
                agentId: $agentId instanceof Uuid ? $agentId : Uuid::fromString($agentId),
                sizeValue: $sizeValue,
                sizeMeasurement: $measurement,
                description: $description,
                status: $status
            );

            $property = $createPropertyDTO->toEntity();
            $property->setAgent($agent);

            $manager->persist($property);

            $this->addReference('property_' . $i, $property);
        }

        $manager->flush();
    }
}
