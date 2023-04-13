<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


/**
 * @method User getUser()
 */
class UserCrudController extends AbstractCrudController
{
    public function __construct(       
        private EntityRepository $entityRepo,
        private UserPasswordHasherInterface $passwordHasher  #Pour pouvoir hacher le MDP
    )
    {
        
    }
    public static function getEntityFqcn(): string
    {
        return User::class;
    }


    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        $userId = $this->getUser()->getId();

        $qb = $this->entityRepo->createQueryBuilder($searchDto, $entityDto, $fields, $filters);
        $qb->andWhere('entity.id != :userId')->setParameter('userId', $userId);

        return $qb;
    }

    
    public function configureFields(string $pageName): iterable
    {
            yield TextField::new('username');

            yield TextField::new('password')
                ->setFormType(PasswordType::class)
                ->onlyOnForms();

            yield ChoiceField::new('roles')  #On affiche les roles dans le tableau de bord
                ->allowMultipleChoices()
                ->renderAsBadges([
                    'ROLE_ADMIN' => 'success', #le role administrateur sera afficher en vert
                    'ROLE_AUTHOR' => 'warning'
                ])
                ->setChoices([
                    'Administrateur' => 'ROLE_ADMIN',
                    'Auteur' => 'ROLE_AUTHOR'
                ]);
    }



    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        /** @var User $user */
        $user = $entityInstance;

        $plainPassword = $user->getPassword(); #On récupère le MDP
        $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword); #On hache le MDP
        
        $user->setPassword($hashedPassword);

        parent::persistEntity($entityManager, $entityInstance);
    }
    
}
