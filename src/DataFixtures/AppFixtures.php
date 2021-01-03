<?php

namespace App\DataFixtures;

use App\Entity\RDV;
use App\Entity\TypeService;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $this->loadUsers($manager);
        $this->loadTypeService($manager);
        $this->loadRdv($manager);
    }


    public function loadUsers(ObjectManager $manager)
    {
        echo " \n\nles utilisateurs : \n";

        $admin = new User();
        $password = $this->passwordEncoder->encodePassword($admin, 'admin');
        $admin->setPassword($password);
        $admin->setRoles(['ROLE_ADMIN'])
            ->setUsername('admin')->setEmail('admin@example.com')->setIsActive('1');
        $manager->persist($admin);
        echo $admin."\n";

        $client = new User();
        $password = $this->passwordEncoder->encodePassword($client, 'client');
        $client->setPassword($password);
        $client->setRoles(['ROLE_CLIENT'])->setUsername('client')
            ->setEmail('client@example.com')->setIsActive('1');
        $manager->persist($client);
        echo $client."\n";

        $client2 = new User();
        $password = $this->passwordEncoder->encodePassword($client, 'client2');
        $client2->setPassword($password);
        $client2->setRoles(['ROLE_CLIENT'])->setUsername('client2')
            ->setEmail('client2@example.com')->setIsActive('1');
        $manager->persist($client2);
        echo $client2."\n";

        $manager->flush();
    }
    private function loadTypeService(ObjectManager $manager)
    {
        $typeServices = [
            ['id' => 1,'libelle' => 'type1'],
            ['id' => 2,'libelle' => 'type2'],

        ];
        foreach ($typeServices as $type)
        {
            $type_new = new TypeService();
            $type_new->setLibelle($type['libelle']);
            echo $type['libelle']."\n";
            $manager->persist($type_new);
            $manager->flush();
        }
    }
    private function loadRdv(ObjectManager $manager)
    {
        $rdvs = [
            ['id' => 1,'dateRdv' => '2020-12-29','heure' => '08:00','typeService' => 'type1','user'=>'client'],
            ['id' => 2,'dateRdv' => '2020-12-30','heure' => '09:00','typeService' => 'type1','user'=>'client'],
            ['id' => 3,'dateRdv' => '2020-12-31','heure' => '10:00','typeService' => 'type2','user'=>'client2'],
            ['id' => 4,'dateRdv' => '2021-01-01','heure' => '11:00','typeService' => 'type2','user'=>'client2'],

        ];
        foreach ($rdvs as $rdv)
        {
            $rdv_new = new RDV();
            $rdv_new->setDateRdv(\DateTime::createFromFormat('Y-m-d',$rdv['dateRdv']));
            $typeService = $manager->getRepository(TypeService::class)->findOneby(["libelle"  =>  $rdv['typeService']]);
            if($typeService != null)
                $rdv_new->setTypeService($typeService);
            $rdv_new->setHeure($rdv['heure']);
            $user = $manager->getRepository(User::class)->findOneby(["username"  =>  $rdv['user']]);
            if($user != null) {
                $rdv_new->setUser($user);
            }
            echo $rdv['dateRdv']."\n";

            $manager->persist($rdv_new);
            $manager->flush();
        }
    }
}