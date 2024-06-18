<?php

use Phalcon\Mvc\Router;

$router = $di->getRouter();

// get items
$router->add(
    '/items',
    [
        'controller' => 'items',
        'action' => 'index',
    ]
);
// post items
$router->add(
    '/items/create',
    [
        'controller' => 'items',
        'action' => 'create',
    ]
);
  //search items by name
  $router->add(
    '/search/{item_name}',
    [
        'controller' => 'Search',
        'action' => 'index',
        'item_name' => 1, // Positional parameter
    ]
);
//search by category
$router->add(
    '/items/search/{subcategoryId}',
     [
        'controller' => 'items',
        'action' => 'search',
     ]
    );

    //search by id
    $router->add(
        '/items/id/{id:[0-9]+}', 
        [
          'controller' => 'items',
          'action' => 'post',
        ]
      );
//post users
$router->add(
    '/users/register',
 [
    'controller' => 'users',
    'action' => 'post',
]
);
//login 
$router->add(
    '/login',
 [
    'controller' => 'login',
    'action' => 'index',
]
);

//add orders

$router->add(
    '/orders',
    [
        'controller' => 'orders',
        'action' => 'create',
    ]
);
//get orders
$router->add(
    '/orders/get',
    [
        'controller' => 'orders',
        'action' => 'index',
    ]
);
//get all users
$router->add(
    '/roles',
    [
        'controller' => 'updateroles',
        'action' => 'getUsersAllRoles',
    ]
);
//get roles with name
$router->add(
    '/roles/get/{name}',
    [
        'controller' => 'updateroles',
        'action' => 'getUsersWithRoles',
    ]
);
//update roles
$router->add(
    '/roles/update',
    [
        'controller' => 'updateroles',
        'action' => 'updateUserRole',
    ]
);
$router->handle($_SERVER['REQUEST_URI']);
