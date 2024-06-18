<?php
use Phalcon\Mvc\Controller;
use Phalcon\Http\Response;

class UpdateRolesController extends Controller
{
    private function handleCors()
    {
        $response = new Response();
        $response->setHeader('Access-Control-Allow-Origin', '*');
        $response->setHeader('Access-Control-Allow-Methods', 'GET, POST');
        $response->setHeader('Access-Control-Allow-Headers', 'Content-Type');
        return $response;
    }
          //get all
          public function getUsersAllRolesAction()
          {
              $response = $this->handleCors();
              
        
              $users = Users::query()
                  ->columns(['Users.id', 'Users.name','Users.username','Users.email', 'UserRoles.role_id'])
                  ->join('UserRoles', 'UserRoles.user_id = Users.id')
                  ->execute();
      
            
              $usersArray = $users->toArray();
      
              $response->setJsonContent($usersArray);
              return $response;
          }
       //get users by name
       public function getUsersWithRolesAction($name = null)
       {
           $response = $this->handleCors();
           
          
           $query = Users::query()
           ->columns(['Users.id', 'Users.name','Users.username','Users.email', 'UserRoles.role_id'])
           ->join('UserRoles', 'UserRoles.user_id = Users.id');
       
           if ($name) {
               $query->where('Users.name = :name:', ['name' => $name]);
           }
       
           $users = $query->execute();
           $usersArray = $users->toArray();
       
           $response->setJsonContent($usersArray);
           return $response;
       }
       
       
   // Method to update 
   public function updateUserRoleAction()
   {
       $response = $this->handleCors();
       
       $user_id = (int) $this->request->getPost('user_id');
       $new_role_id = (int) $this->request->getPost('role_id');

       $userRole = UserRoles::findFirst([
           'conditions' => 'user_id = :user_id:',
           'bind'       => ['user_id' => $user_id]
       ]);

       if ($userRole) {
           $userRole->role_id = $new_role_id;
           if ($userRole->save()) {
           
               $response->setJsonContent(['message' => 'Role updated successfully']);
           } else {
               
               $errors = [];
               foreach ($userRole->getMessages() as $message) {
                   $errors[] = $message->getMessage();
               }
               $response->setJsonContent(['message' => 'Failed to update role', 'errors' => $errors]);
           }
       } else {

           $response->setJsonContent(['message' => 'User role not found']);
       }

       return $response;
   }
}