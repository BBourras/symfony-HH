<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\Tag;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher, private readonly SluggerInterface $slugger)  { }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');   //locale facultative, US par défaut

        $user = new User;
        $user->setName('Toto Lasticot');
        $user->setEmail('totolasticot@gmail.com');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword($this->passwordHasher->hashPassword($user, 'toto'));  //toto
        $manager->persist($user);

        $admin = new User();
        $admin->setName('Bob Admin');
        $admin->setEmail('admin@gmail.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'admin')); //admin
        $manager->persist($admin);

        for($i = 1; $i <= 10; ++$i) {
            $post = new Post;
            $post->setTitle($title = $faker->unique()->text(30));
            $post->setSlug($this->slugger->slug(mb_strtolower($title)));
            $post->setBody($faker->paragraph(6));
            $post->setPublishedAt(
                $faker->boolean(90)
                    ? \DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-50 days', '-10 days'))
                    : null    // "null" si l'article n'a pas encore été publié
            );
            $post->setAuthor($faker->boolean(75) ? $user : $admin);
            $manager->persist($post);

            for ($t = 1; $t < $faker->numberBetween(1, 3); ++$t) {
                $tag = new Tag();
                $tag ->setName($faker->unique()->word());
                $post->addTag($tag);
                $manager->persist($tag);
            }

            for ($c = 1; $c <= $faker->numberBetween(1, 5); ++$c) {
                $comment = new Comment();
                $comment->setName($faker->name);
                $comment->setEmail($faker->email);
                $comment->setBody($faker->paragraph(3));
                $comment->setIsActive($faker->boolean(80));
                $comment->setPost($post);
                $manager->persist($comment);
            }
        }
        $manager->flush();
    }
}
