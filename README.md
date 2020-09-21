Installation
============

Make sure Composer is installed globally, as explained in the
[installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Applications that use Symfony Flex
----------------------------------

Open a command console, enter your project directory and execute:

```console
$ composer require thephpguys/light-crud-bundle
```

Applications that don't use Symfony Flex
----------------------------------------

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require thephpguys/light-crud-bundle
```

### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    TPG\LightCrudBundle\LightCrudBundle::class => ['all' => true],
];
```

Getting started
============

### Entity
Define your entity that implements `TPG\LightCrudBundle\JsonBodySerializable` interface: 

```php
use TPG\LightCrudBundle\JsonBodySerializable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="posts")
 */
class Post implements JsonBodySerializable
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid")
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class="Ramsey\Uuid\Doctrine\UuidGenerator")
     */
    public $id;


    /**
     * @ORM\Column(type="string")
     */
    public $title;
    
    /**
     * @ORM\Column(type="string")
     */
    public $description;

}
```

### Controller

Define your controller:

```
namespace App\Controller;

use App\Entity\Post;
use TPG\LightCrudBundle\Annotation\EntityClass;
use TPG\LightCrudBundle\Annotation\EntityView;
use TPG\LightCrudBundle\DataLoader\DataLoader;
use TPG\LightCrudBundle\DataLoader\LoadContext;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserController
 * @package App\Controller
 * @Route("/posts")
 * @EntityClass("App\Entity\Post")
 */
class PostsController
{

    /**
     * @Route("/",methods="POST")
     * @param Request $request
     */
    public function create(Post $post, EntityManagerInterface $em){
        $em->persist($post);
        $em->flush();
        return new Response(204);
    }

    /**
     * @Route("/{id}/",methods="PUT");
     */
    public function update(Post $post, EntityManagerInterface $em)
    {
        $em->flush();
        return new Response(204);
    }
    /**
     * @Route("/{id}/",methods="DELETE");
     */
    public function remove(Post $post, EntityManagerInterface $em)
    {
        $em->remove($post);
        $em->flush();
        return new Response(204);
    }

    /**
     * @Route("/")
     * @EntityView(fields={"title"})
     */
    public function list(LoadContext $context, DataLoader $dataLoader):JsonResponse
    {
        return new JsonResponse($dataLoader->loadCollection($context));
    }

    /**
     * @param string $id
     * @Route("/{id}/");
     * @EntityView(fields={"title","description"})
     */
    public function show(LoadContext $context, DataLoader $dataLoader, string $id):JsonResponse
    {
        return new JsonResponse($dataLoader->loadOne($id,$context));
    }

}
```

`@EntityClass` annotation indicates what entity used for.

`@EntityView` annotation indicates that what fields will be selected and displayed