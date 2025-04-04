<?php
declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Agent;
use App\Entity\Customer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixture extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $hasher)
    {}

    public function load(ObjectManager $manager): void{
        for($i = 0; $i < 15; $i++){
            if ($i % 2 === 0) {
                $user = new Agent();
            } else {
                $user = new Customer();
            }
            $user -> setName('user '.$i);
            $user -> setEmail('user '.$i.'@gmail.com');
            $user -> setIsBlocked(false);
            $user -> setPhone('098716033'.$i);
            $user -> setPassword($this->hasher->hashPassword($user, 'newpass1234'));

            $manager->persist($user);
        }
        $manager->flush();
    }
}
