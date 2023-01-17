<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use App\Entity\User;
use App\Entity\Country;
use App\Entity\Series;
use App\Entity\Rating;
use App\Entity\UserSearch;
use App\Form\UpdateFormType;
use App\Form\UserCreateFormType;
use App\Form\UserSearchFormType;
use App\Form\CommentNewFormType;

class UserController extends AbstractController
{
    #[Route('/user/favoris', name: 'app_user_favorite')]
    public function index(): Response
    {
        $numPage = Request::createFromGlobals()->query->get('numPage');
        $numPage = $numPage ? $numPage : 1;

        return $this->render('user/favorite.html.twig', [
            'controller_name' => 'UserController',
            'numPage' => $numPage,
        ]);
    }

    #[Route('/user/history', name: 'app_user_history')]
    public function history(EntityManagerInterface $entityManager, Request $request,
    PaginatorInterface $paginator): Response
    {
        $episodes = $this->getUser()->getEpisode();
        $episodes = $paginator->paginate($episodes, $request
        ->query->getInt('page', 1, 10));

        $numPage = Request::createFromGlobals()->query->get('numPage');
        $numPage = $numPage ? $numPage : 1;


        return $this->render('user/history.html.twig', [
            'episodes' => $episodes,
            'numPage' => $numPage,
        ]);
    }

    #[Route('/user/profile/{id}', name: 'app_user_profile')]
    public function profile( $id, EntityManagerInterface $entityManager, Request $request, PaginatorInterface $paginator): Response
    {
        $form = $this->createForm(UpdateFormType::class, $this->getUser());
        $form->handleRequest($request);

        $countries = $entityManager->getRepository(Country::class)->findAll();
        $user = $entityManager->getRepository(User::class)->findBy(['id' => $id])[0];
        $ratings = $entityManager->getRepository(Rating::class)->findBy(['user' => $user, 'verified' => true]);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('photo')->getData();
            if ($file) {
                $fileContent = file_get_contents($file);
                $this->getUser()->setPhoto($fileContent);
            }
            $entityManager->persist($this->getUser());
            
            $entityManager->flush();
            return $this->redirectToRoute('app_user_profile', [
                'id' => $id
                ]);
        }

        $series = $user->getSeries();
        $userEpisode = $user->getEpisode();

        $ratings = $paginator->paginate(
            $ratings, /* query NOT result */
            $request->query->getInt('page1', 1), /*page number*/
            3 /*limit per page*/,
            array(
                'pageParameterName' => 'page1',
            )
        );


        $series = $paginator->paginate(
            $series, /* query NOT result */
            $request->query->getInt('page2', 1), /*page number*/
            4 /*limit per page*/,
            array(
                'pageParameterName' => 'page2',
            )
        );
        $episodes = $paginator->paginate(
            $userEpisode, /* query NOT result */
            $request->query->getInt('page3', 1), /*page number*/
            4 /*limit per page*/,
            array(
                'pageParameterName' => 'page3',
            )
        );

        return $this->render('user/profile.html.twig', [
            'form' => $form->createView(),
            'countries' => $countries,
            'ratings' => $ratings,
            'user' => $user,
            'series' => $series,
            'episodes' => $episodes,
        ]);
    }

    #[Route('/photo/{id}', name: 'photo_user', methods: ['GET'])]
    public function showPoster(User $user): Response
    {
    return new Response(stream_get_contents($user->getPhoto()), 200, array ('Content-type' => 'image/jpeg'));
    }


    #[Route('/user/all', name: 'app_user_show_all')]
    public function allUser(EntityManagerInterface $entityManager, PaginatorInterface $paginator, Request $request): Response
    {
        $users = $entityManager->getRepository(User::class)->findAll();
        


        $userSearch = new UserSearch();
        $form = $this->createForm(UserSearchFormType::class, $userSearch);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // get the users that contain the search string in their name
            $queryBuilder = $entityManager->getRepository(User::class)->createQueryBuilder('u');
            $queryBuilder->where('u.name LIKE :name')
                ->setParameter('name', '%' . $userSearch->getNom() . '%');
            $users = $queryBuilder->getQuery()->getResult();
        }
    
        $users = $paginator->paginate($users, $request
        ->query->getInt('page', 1, 10));
        
        return $this->render('user/all.html.twig', [
            'users' => $users,
            'form' => $form->createView(),
            'pagination' => false,
        ]);
    }


    #[Route('/user/suspended/{id}/{yesno}', name: 'app_user_suspended')]
    public function suspended($id, $yesno, EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(User::class)->findBy(['id' => $id])[0];
        // if user is admin, he can't be suspended
        if ($user->getisAdmin() == true) {
            echo "<script>alert('Impossible de suspendre un administrateur')</script>";
            return $this->redirectToRoute('admin');
        }else{
            $user->setSuspendu($yesno);
            $entityManager->persist($user);
            $entityManager->flush();
            echo "<script>alert('Utilisateur suspendu')</script>";
            return $this->redirectToRoute('admin');
        }
    }
    #[Route('/user/admin/new', name: 'app_admin_user_new')]
    public function new(EntityManagerInterface $entityManager, Request $request): Response
    {
        $form = $this->createForm(UserCreateFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            for ($i = 0; $i < $data['number']; $i++) {
                $user = new User();
                $name = $data['name'] . $i;
                $user->setName($name);
                $email = $data['email'];
                $email = explode('@', $email);
                $new_email = $email[0] . $i . '@' . $email[1];
                $new_email_check = $entityManager->getRepository(User::class)->findBy(['email' => $new_email]);
                while($new_email_check){
                    $new_email = $email[0] . rand(0, 1000) . '@' . $email[1];
                    $new_email_check = $entityManager->getRepository(User::class)->findBy(['email' => $new_email]);
                }
                $user->setEmail($new_email);
                $hash = password_hash($name, PASSWORD_BCRYPT);       
                $user->setPassword($hash);
                $user->setRoles(['ROLE_USER']);
                $country = $entityManager->getRepository(Country::class)->find(rand(1, 19));
                if($country){
                    $user->setCountry($country);
                    $user->setIsBot(true);
                    $entityManager->persist($user);
                }else {
                    while(!$country){
                        $country = $entityManager->getRepository(Country::class)->find(rand(1, 19));
                    }
                    $user->setCountry($country);
                    $user->setIsBot(true);
                    $entityManager->persist($user);
                }
            }
            $entityManager->flush();

            $bot = $entityManager->getRepository(User::class)->findBy(['isBot' => true]);
            echo "<script> alert('Utilisateurs créés, Il y a " . count($bot) . " faux comptes');
                window.location.href = 'http://127.0.0.1:8000/admin';
            </script>";
        }

        return $this->render('user/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/user/admin/delete', name: 'app_admin_user_delete')]
    public function delete(EntityManagerInterface $entityManager, Request $request): Response
    {

        $user = $entityManager->getRepository(User::class)->findBy(['isBot' => true]);
        foreach ($user as $u) {
            $comment = $entityManager->getRepository(Rating::class)->findBy(['user' => $u]);
            foreach ($comment as $c) {
                $entityManager->remove($c);
            }
        }

        $entityManager->flush();
        $user = $entityManager->getRepository(User::class)->findBy(['isBot' => true]);
        foreach ($user as $u) {
            $entityManager->remove($u);
        }
        $entityManager->flush();

        echo "<script> alert('Utilisateurs supprimés');
        window.location.href = 'http://127.0.0.1:8000/admin';
        </script>";
    }


    #[Route('/user/comment/new', name: 'app_admin_user_comment_new')]
    public function new_comment(EntityManagerInterface $entityManager, Request $request): Response
    {
        $form = $this->createForm(CommentNewFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment_exemple = array();
            $comment_exemple[0] = "C'est vraiment un bon film";
            $comment_exemple[1] = "J'ai adoré ce film";
            $comment_exemple[2] = "Je n'ai pas aimé ce film";
            $comment_exemple[3] = "Je n'ai pas compris ce film";
            $data = $form->getData();
            $user = $entityManager->getRepository(User::class)->findBy(['isBot' => true]);
            if($user==null){
                echo "<script> alert('Pas de bot user');
                window.location.href = 'http://127.0.0.1:8000/admin/';
                </script>";
            }
            if(count($user)<$data['number']){
                echo "<script> alert('Pas assez d'utilisateurs');
                window.location.href = 'http://127.0.0.1:8000/admin/';
                </script>";
            }
            // get the serie id in the url 
            $serie = $request->query->get('id');
            $serie = $entityManager->getRepository(Series::class)->findOneBy(['id' => $serie]);
            for ($i = 0; $i < $data['number']; $i++) {
                $rating = $entityManager->getRepository(Rating::class)->findOneBy(['user' => $user[$i], 'series' => $serie]);
                if($rating==null){
                    $comment = new Rating();
                    $comment->setUser($user[$i]);
                    $comment->setSeries($serie);
                    $comment->setValue(rand(0,5));
                    $comment->setDate(new \DateTime());
                    $comment->setComment($comment_exemple[rand(0,3)]);
                    $entityManager->persist($comment);
                }
            }
            $entityManager->flush();

            echo "<script> alert('Commentaires ajoutés');
            window.location.href = 'http://127.0.0.1:8000/admin/';
            </script>";
        }

        return $this->render('user/commentaire_new.html.twig', [
            'form' => $form->createView(),
        ]);
        
    }

    #[Route('/user/comment/delete', name: 'app_admin_user_comment_delete')]
    public function delete_comment(EntityManagerInterface $entityManager, Request $request): Response
    {
        $user = $entityManager->getRepository(User::class)->findBy(['isBot' => true]);
        foreach ($user as $u) {
            $comment = $entityManager->getRepository(Rating::class)->findBy(['user' => $u]);
            foreach ($comment as $c) {
                $entityManager->remove($c);
            }
        }

        $entityManager->flush();

        echo "<script> alert('Commentaires supprimés');
        window.location.href = 'http://127.0.0.1:8000/admin/';
        </script>";

    }


    #[Route('/user/count/fake_account', name: 'app_admin_user_count_fake_accounts')]
    public function count_fake_account(EntityManagerInterface $entityManager, Request $request): Response
    {
        $user = $entityManager->getRepository(User::class)->findBy(['isBot' => true]);
        echo "<script>
        alert('Il y a " . count($user) . " faux comptes');
        window.location.href='admin';
        </script>";
    }


    
}
