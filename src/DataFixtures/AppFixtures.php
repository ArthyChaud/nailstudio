<?php

namespace App\DataFixtures;

use App\Entity\Accounting;
use App\Entity\Calendar;
use App\Entity\CarouselLike;
use App\Entity\CategoryAccounting;
use App\Entity\Marque;
use App\Entity\Produit;
use App\Entity\RDV;
use App\Entity\TypeProduit;
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
        $this->loadTypeProduit($manager);
        $this->loadMarque($manager);
        $this->loadProduit($manager);
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
        $client->setRoles(['ROLE_CLIENT'])->setUsername('client1')
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
            ['id' => 1,'libelle' => 'Onglerie','color'=>'#2045CB'],
            ['id' => 2,'libelle' => 'Extension des cils','color'=>'#20CB25'],
            ['id' => 3,'libelle' => 'Épilation','color'=>'#E7A533'],
            ['id' => 4,'libelle' => 'Soins visage','color'=>'#33E7D7'],
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
            ['id' => 1,'dateRdv' => '2021-03-29','heure' => '08:00','typeService' => 'Onglerie','user'=>'client1','valider'=>false],
            ['id' => 2,'dateRdv' => '2021-03-30','heure' => '09:00','typeService' => 'Extension des cils','user'=>'client1','valider'=>false],
            ['id' => 3,'dateRdv' => '2021-03-31','heure' => '10:00','typeService' => 'Onglerie','user'=>'client2','valider'=>false],
            ['id' => 4,'dateRdv' => '2021-03-31','heure' => '11:00','typeService' => 'Épilation','user'=>'client2','valider'=>false],
            ['id' => 5,'dateRdv' => '2021-03-31','heure' => '10:30','typeService' => 'Onglerie','user'=>'client2','valider'=>false],
            ['id' => 6,'dateRdv' => '2021-03-31','heure' => '14:30','typeService' => 'Extension des cils','user'=>'client1','valider'=>false],
            ['id' => 7,'dateRdv' => '2021-04-01','heure' => '10:00','typeService' => 'Soins visage','user'=>'client2','valider'=>false],
            ['id' => 8,'dateRdv' => '2021-04-01','heure' => '15:00','typeService' => 'Épilation','user'=>'client2','valider'=>false],
            ['id' => 9,'dateRdv' => '2021-04-02','heure' => '12:30','typeService' => 'Onglerie','user'=>'client1','valider'=>false],
            ['id' => 10,'dateRdv' => '2021-04-02','heure' => '15:00','typeService' => 'Extension des cils','user'=>'client2','valider'=>false],

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
    private function loadTypeProduit(ObjectManager $manager)
    {
        $typeProduits = [
            ['id'=>1,'libelle' => "crème"],
            ['id'=>2,'libelle' => "vernis"],
            ['id'=>3,'libelle' => "shampoing"],

        ];
        foreach ($typeProduits as $typeProduit) {
            $new_type = new TypeProduit();
            $new_type->setLibelle($typeProduit['libelle']);
            $manager->persist($new_type);
            $manager->flush();
        }
    }
    private function loadMarque(ObjectManager $manager)
    {
        $marques = [
            ['id' => 1,'libelle' => 'sephora'],
            ['id' => 2,'libelle' => 'chanel'],
            ['id' => 3,'libelle' => 'oréal'],
            ['id' => 4,'libelle' => 'mixa']
        ];
        foreach ($marques as $marque)
        {
            $marque_new = new Marque();
            $marque_new->setLibelle($marque['libelle']);
            $manager->persist($marque_new);
            $manager->flush();
        }
    }
    private function loadProduit(ObjectManager $manager)
    {
        $produits = [
            ['id'=>1,'libelle' => "lait de toilette", 'typeProduit'=> 'crème', 'stocks'=>2, 'besoin'=>3,'prix'=>2.3,'marque'=>'mixa'],
            ['id'=>2,'libelle' => "anti-cernes", 'typeProduit'=> 'crème', 'stocks'=>4, 'besoin'=>0,'prix'=>5.4,'marque'=>'oréal'],
            ['id'=>3,'libelle' => "bleu", 'typeProduit'=> 'vernis', 'stocks'=>1, 'besoin'=>5,'prix'=>1.5,'marque'=>'chanel'],
            ['id'=>4,'libelle' => "rouge", 'typeProduit'=> 'vernis', 'stocks'=>6, 'besoin'=>0,'prix'=>1.5,'marque'=>'chanel'],
            ['id'=>5,'libelle' => "démêlant", 'typeProduit'=> 'shampoing', 'stocks'=>7, 'besoin'=>2,'prix'=>5.0,'marque'=>'sephora'],
            ['id'=>6,'libelle' => "couleur rouge", 'typeProduit'=> 'shampoing', 'stocks'=>3, 'besoin'=>3,'prix'=>2.2,'marque'=>'sephora']
        ];
        foreach ($produits as $produit) {

            $new_prod = new Produit();
            $new_prod->setLibelle($produit['libelle']);
            $typeProduit = $manager->getRepository(TypeProduit::class)->findOneby(["libelle"  =>  $produit['typeProduit']]);
            $new_prod->setTypeProduit($typeProduit);
            $new_prod->setStock($produit['stocks']);
            $new_prod->setBesoin($produit['besoin']);
            $new_prod->setPrix($produit['prix']);

            $marque = $manager->getRepository(Marque::class)->findOneby(["libelle"  =>  $produit['marque']]);
            $new_prod->setMarque($marque);

            $manager->persist($new_prod);
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
    private function loadCalender(ObjectManager $manager){/*
        $calendars = [
            ['id' => 1,'titre'=>'Manucure','start' => '2021-03-30T09:00','end' => '2021-03-30T09:30','description'=>'test','color'=>'#2045CB','rdv'=>2],
            ['id' => 2,'titre'=>'épilation','start' => '2021-04-02T11:00','end' => '2021-04-02T11:30','description'=>'test','color'=>'#20CB25','rdv'=>4],
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
            $rdv = $manager->getRepository(RDV::class)->find($calendar['rdv']);
            $calendar_new->setRdv($rdv);
            $manager->persist($calendar_new);
            $manager->flush();
        }*/
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
