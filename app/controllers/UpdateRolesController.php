<?php
use Phalcon\Mvc\Controller;
use Phalcon\Http\Response;

class UpdateRolesController extends Controller
{
    private function handleCors()
    {
        // Add your CORS handling logic here
        $response = new Response();
        $response->setHeader('Access-Control-Allow-Origin', '*');
        $response->setHeader('Access-Control-Allow-Methods', 'GET, POST');
        $response->setHeader('Access-Control-Allow-Headers', 'Content-Type');
        return $response;
    }
       //get
    public function getUsersWithRolesAction()
    {
        $response = $this->handleCors();
        
        // Your logic to fetch users with their roles
        $users = Users::query()
            ->columns(['Users.id', 'Users.name', 'UserRoles.role_id'])
            ->join('UserRoles', 'UserRoles.user_id = Users.id')
            ->execute();

        // Convert to array or another format suitable for JSON response
        $usersArray = $users->toArray();

        // Return as JSON response
        $response->setJsonContent($usersArray);
        return $response;
    }
   // Method to update a user's role
   public function updateUserRoleAction()
   {
       $response = $this->handleCors();
       
       // Your logic to update a user's role
       $user_id = (int) $this->request->getPost('user_id');
       $new_role_id = (int) $this->request->getPost('role_id');

       $userRole = UserRoles::findFirst([
           'conditions' => 'user_id = :user_id:',
           'bind'       => ['user_id' => $user_id]
       ]);

       if ($userRole) {
           $userRole->role_id = $new_role_id;
           if ($userRole->save()) {
               // Handle success
               $response->setJsonContent(['message' => 'Role updated successfully']);
           } else {
               // Handle errors
               $errors = [];
               foreach ($userRole->getMessages() as $message) {
                   $errors[] = $message->getMessage();
               }
               $response->setJsonContent(['message' => 'Failed to update role', 'errors' => $errors]);
           }
       } else {
           // Handle case where UserRoles entry doesn't exist
           $response->setJsonContent(['message' => 'User role not found']);
       }

       return $response;
   }
}