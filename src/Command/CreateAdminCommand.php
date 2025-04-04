<?php
declare(strict_types=1);

namespace App\Command;

use App\Entity\Admin;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[AsCommand(
    name: 'app:create-admin',
    description: 'Creates a new admin user.',
    aliases: ['app:add-admin'],
    hidden: false
)]
class CreateAdminCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPasswordHasherInterface $passwordHarsher,
        private readonly ValidatorInterface $validator,
    ){
        parent::__construct();
    }

    protected function configure():void{
        $this
            ->setDescription('Creates a new admin user.')
            ->setHelp('This command allows you to create a admin user...')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output):int{
        $helper = $this->getHelper('question');
        $emailQuestion = new Question('Enter your email: ');
        $emailQuestion->setValidator(
            function ($email) {
                if(empty($email)) {
                    throw new \Exception('Please enter the email.');
                }
                $errors = $this->validator->validate($email, new Assert\Email());
                if(count($errors) > 0) {
                    throw new \Exception('The email is invalid.');
                }
                $existingAdmin = $this->entityManager->getRepository(Admin::class)->findOneBy(['email' => $email]);
                if($existingAdmin) {
                    throw new \Exception('The current email already exists.');
                }
                return $email;
            }
        );

        $nameQuestion = new Question('Please enter the name: ');
        $nameQuestion->setValidator(
            function ($name) {
                if(empty($name)) {
                    throw new \Exception('Please enter the name.');
                }
                return $name;
            }
        );

        $passwordQuestion = new Question('Please enter the  password: ');
        $passwordQuestion->setHidden(true);
        $passwordQuestion->setHiddenFallback(false);
        $passwordQuestion->setValidator(
            function ($password) {
                if(empty($password)) {
                    throw new \Exception('Please enter the password.');
                }
                return $password;
            }
        );

        $phoneQuestion = new Question('Please enter the phone number: ');
        $phoneQuestion->setValidator(
            function ($phone) {
                if(empty($phone)) {
                    throw new \Exception('Please enter the phone number.');
                }
                $errors = $this->validator->validate($phone, new Assert\Length(13));
                if(count($errors) > 0) {
                    throw new \Exception('The phone number is invalid.');
                }
                return $phone;
            }
        );

        $email = $helper->ask($input, $output, $emailQuestion);
        $name = $helper->ask($input, $output, $nameQuestion);
        $password = $helper->ask($input,$output,$passwordQuestion);
        $phone = $helper->ask($input, $output, $phoneQuestion);

        $user = new Admin();

        $user->setName($name);
        $user->setEmail($email);
        $hashed_password = $this->passwordHarsher->hashPassword($user, $password);
        $user->setPassword($hashed_password);
        $user->setPhone($phone);
        $user->setIsBlocked(false);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $output->writeln('<info>Admin user successfully generated!</info>');
        $output->writeln([
            'Email: ' . $email,
            'Name: ' . $name,
            'Phone: ' . $phone,
        ]);

        return Command::SUCCESS;
    }
}
