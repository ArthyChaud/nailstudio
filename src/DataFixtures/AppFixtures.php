<?php

namespace App\DataFixtures;

use App\Entity\Accounting;
use App\Entity\Calendar;
use App\Entity\CarouselLike;
use App\Entity\CategoryAccounting;
use App\Entity\RDV;
use App\Entity\TypeService;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use phpDocumentor\Reflection\Types\False_;
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
        $this->loadCategoryAccounting($manager);
        $this->loadAccounting($manager);
        $this->loadCarousel($manager);
        $this->loadCalender($manager);

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
            ['id' => 1,'libelle' => 'Manucure','color'=>'#000000'],
            ['id' => 2,'libelle' => 'épilation','color'=>'#111111']
        ];
        foreach ($typeServices as $type)
        {
            $type_new = new TypeService();
            $type_new->setLibelle($type['libelle']);
            echo $type['libelle']."\n";
            $type_new->setColor($type['color']);
            echo $type['color']."\n";
            $manager->persist($type_new);
            $manager->flush();
        }
    }
    private function loadRdv(ObjectManager $manager)
    {
        $rdvs = [
            ['id' => 1,'dateRdv' => '2021-03-27','heure' => '08:00','typeService' => 'Manucure','user'=>'client','valider'=>false],
            ['id' => 2,'dateRdv' => '2021-03-26','heure' => '09:00','typeService' => 'Manucure','user'=>'client','valider'=>true],
            ['id' => 3,'dateRdv' => '2021-03-25','heure' => '10:00','typeService' => 'épilation','user'=>'client2','valider'=>false],
            ['id' => 4,'dateRdv' => '2021-03-24','heure' => '11:00','typeService' => 'épilation','user'=>'client2','valider'=>true],
        ];
        foreach ($rdvs as $rdv)
        {
            $rdv_new = new RDV();
            $rdv_new->setDateRdv(DateTime::createFromFormat('Y-m-d',$rdv['dateRdv']));
            $typeService = $manager->getRepository(TypeService::class)->findOneby(["libelle"  =>  $rdv['typeService']]);
            if($typeService != null)
                $rdv_new->setTypeService($typeService);
            $rdv_new->setHeure($rdv['heure']);
            $user = $manager->getRepository(User::class)->findOneby(["username"  =>  $rdv['user']]);
            if($user != null) {
                $rdv_new->setUser($user);
            }
            $rdv_new->setValider($rdv['valider']);
            echo $rdv['dateRdv']."\n";

            $manager->persist($rdv_new);
            $manager->flush();
        }
    }

    private function loadCategoryAccounting(ObjectManager $manager)
    {
        $categoryAccounting = [
            ['categorie' => "Charges exploitation"],
            ['categorie' => 'Charges financières'],
            ['categorie' => 'Charges exceptionnelles'],
            ['categorie' => "Produits exploitation"],
            ['categorie' => 'Produits financiers'],
            ['categorie' => 'Produits exceptionnels'],
        ];
        foreach ($categoryAccounting as $cat) {
            $new_cat = new CategoryAccounting();
            $new_cat->setCategorie($cat['categorie']);
            $manager->persist($new_cat);
            $manager->flush();
        }
    }
    private function loadCalender(ObjectManager $manager){
        $calendars = [
            ['id' => 2,'titre'=>'Manucure','start' => '2021-03-26T09:00','end' => '2021-03-26T09:30','description'=>'test','color'=>'#2045CB'],
            ['id' => 4,'titre'=>'épilation','start' => '2021-03-24T11:00','end' => '2021-03-24T11:30','description'=>'test','color'=>'#20CB25'],
        ];
        foreach ($calendars as $calendar){
            $dateStart = new \DateTime($calendar['start']);
            $dateEnd = new \DateTime($calendar['end']);
            $calendar_new = new Calendar();
            $calendar_new->setTitre($calendar['titre']);
            $calendar_new->setDescription($calendar['description']);
            $calendar_new->setStart($dateStart);
            $calendar_new->setEnd($dateEnd);
            $calendar_new->setBackgroundColor($calendar['color']);
            $manager->persist($calendar_new);
            $manager->flush();
        }
    }

    private function loadAccounting(ObjectManager $manager)
    {
        $accounting = [
            ['libelle' => 'Paypal', 'prix' => 0.35, 'categorie' => 'Charges exploitation'],
            ['libelle' => 'Paypal', 'prix' => 0.37, 'categorie' => 'Produits exploitation'],
            ['libelle' => 'Actions', 'prix' => 500, 'categorie' => 'Charges financières'],
            ['libelle' => 'Actions', 'prix' => 1500, 'categorie' => 'Produits financiers'],
            ['libelle' => 'Hébergement internet', 'prix' => 11.01, 'categorie' => 'Charges exceptionnelles'],
            ['libelle' => 'Abonnements', 'prix' => 200, 'categorie' => 'Produits exceptionnels'],
        ];
        foreach ($accounting as $value) {
            $new_acc = new Accounting();
            $new_acc->setLibelle($value['libelle'])
                ->setPrix($value['prix'])
                ->setDate(DateTime::createFromFormat('Y-m-d', date('Y-m-d')))
                ->setCategoryAccounting($manager->getRepository(CategoryAccounting::class)->findOneBy(['categorie'  => $value['categorie']]));
            $manager->persist($new_acc);
            $manager->flush();
        }
    }

    private function loadCarousel(ObjectManager $manager)
    {
        $carousel = [
            ['name' => 'Onglerie','type' => 'Onglerie'],
            ['name' => 'Extension de cils','type' => 'Extension de cils'],
            ['name' => 'Epilation sourcils et lèvre','type' => 'Epilation sourcils et lèvre'],
            ['name' => 'Soins visage','type' => 'Soins visage'],
        ];
        foreach ($carousel as $cat) {
            $new_cat = new CarouselLike();
            $new_cat->setDescription("")
                ->setName($cat['name'])
                ->setType($cat['type']);
            $manager->persist($new_cat);
            $manager->flush();
        }
    }
}
