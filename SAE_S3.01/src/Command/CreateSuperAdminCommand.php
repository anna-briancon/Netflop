<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;
use App\Entity\Country;

#[AsCommand(
    name: 'create:super-admin',
    description: 'Permet de créer un super-admin',
)]
    
class CreateSuperAdminCommand extends Command
{
    private $entityManager;
    private $userPasswordHasher;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher)
    {
        //Permet d'accéder à la base de donnée
        $this->entityManager = $entityManager;
        $this->userPasswordHasher = $userPasswordHasher;

        parent::__construct();
    }

    protected function configure(): void
    {
        //Permet d'ajouter à la suite de la commande symfony plusieurs champs
        $this
            ->addArgument('email', InputArgument::REQUIRED, 'email')
            ->addArgument('nom', InputArgument::REQUIRED, 'nom')
            ->addArgument('mot_de_passe', InputArgument::REQUIRED, 'mot de passe')
            ->addArgument('pays', InputArgument::REQUIRED, 'pays')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $entityManager = $this->entityManager;
        $io = new SymfonyStyle($input, $output);
        $email = $input->getArgument('email');
        $nom = $input->getArgument('nom');
        $mdp = $input->getArgument('mot_de_passe');
        $pays = $input->getArgument('pays');

        //Permet à partir du nom du pays, d'obtenir le pays en lui même
        $pays = $entityManager->getRepository(Country::class)->findBy(['name' => $pays])[0];
        
        //Permet de créer un nouvel utilisateur qui sera ici un super-admin
        $user = new User();

        //Permet de hasher le mot de passe
        $user->setPassword(
            $this->userPasswordHasher->hashPassword(
                $user,
                $mdp
            )
        );
        $user->setName($nom);
        $user->setEmail($email);
        $user->setCountry($pays);
        $user->setRegisterDate(new \DateTime('@'.strtotime('Europe/Paris')));
        $roles = ['ROLE_ADMIN','ROLE_SUPER_ADMIN'];
        $user->setRoles($roles);
        $user->setIsSuperAdmin(1);
        $user->setisAdmin(1);
        $entityManager->persist($user);
        $entityManager->flush();


        $io->success('Votre super-admin est crée email: '.$email.
        ' mot de passe: '.$mdp.' nom: '.$nom);

        return Command::SUCCESS;
    }
}
