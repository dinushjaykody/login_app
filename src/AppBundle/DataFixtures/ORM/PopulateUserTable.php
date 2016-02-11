<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class PopulateUserTable implements FixtureInterface, ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function load(ObjectManager $manager)
    {
        $user1 = new User();
        $user2 = new User();
        $user3 = new User();

        $user1->setUsername('cindy');
        $user1->setFirstname('Cindy');
        $user1->setLastname('Cinderson');
        $user1->setEmail('cindy@cindy.com');
        $user1->setPassword($this->hashPassword($user1,'cidypass'));

        $manager->persist($user1);

        $user2->setUsername('david');
        $user2->setFirstname('David');
        $user2->setLastname('Davidson');
        $user2->setEmail('david@david.com');
        $user1->setPassword($this->hashPassword($user1,'davidpass'));

        $manager->persist($user2);

        $user3->setUsername('tom');
        $user3->setFirstname('Tom');
        $user3->setLastname('Tompson');
        $user3->setEmail('tom@tom.com');
        $user1->setPassword($this->hashPassword($user1,'tompass'));

        $manager->persist($user3);

        $manager->flush();


    }

    /**
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * @return string
     *  returns the newly hashed password
     */
    public function hashPassword(User $user, $thePassword)
    {
        $encoder = $this->container->get('security.encoder_factory')
            ->getEncoder($user);

        return $encoder->encodePassword($thePassword, '123456789234567891234567');
    }


}