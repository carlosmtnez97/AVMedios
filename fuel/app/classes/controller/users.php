 <?php 
use Firebase\JWT\JWT;
class Controller_Users extends Controller_Base
{
    private $key = "dejr334irj3irji3r4j3rji3jiSj3jri";
 public function post_preCreate()
    {
        try {
            $header = apache_request_headers();
            if (isset($header['Authorization'])) 
            {
                $token = $header['Authorization'];
                $dataJwtUser = JWT::decode($token, $this->key, array('HS256'));
            }
            
            if ( empty($_POST['email'])/* || empty($_POST['username'])*/) 
            {
                $json = $this->response(array(
                    'code' => 400,
                    'message' =>  'Campo vacio'
                ));
                return $json;
            }
            $email = $_POST['email'];
            $id_rol = $_POST['id_rol'];
            $id_category = $_POST['id_category'];
            if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL ) == false)
            {
                $json = $this->response(array(
                    'code' => 400,
                    'message' => 'La dirección de correo no es valida',
                    'data' => []
                ));
                return $json;
            }
            if($this->isEmailCreated($email))
            {
                $json = $this->response(array(
                    'code' => 400,
                    'message' => 'Email ya registrado',
                    'data' => []
                ));
                return $json;
            }
            $user = new Model_Users();
            $user->email = $email;
            $user->active = 0;
            $user->id_rol = $id_rol;
            $user->save();
            $category = Model_Category::find($id_category);
            $category->users[] = $user;
            $category->save();
            $json = $this->response(array(
                'code' => 200,
                'message' => 'Usuario creado',
                'data' => $user
            ));
            return $json;
        } 
        catch (Exception $e) 
        {
            $json = $this->response(array(
                'code' => 500,
                'message' => $e->getMessage()
            ));
            return $json;
        }
    }
    public function post_create()
    {
        try {
        
            if ( empty($_POST['username']) || empty($_POST['email']) || empty($_POST['password']) ) 
            {
                $json = $this->response(array(
                    'code' => 400,
                    'message' =>  'Falta algun campo'
                ));
                return $json;
            }
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            // Mínimo caracteres
            if (strlen($_POST['username']) < 4)
            {
                $json = $this->response(array(
                    'code' => 400,
                    'message' => 'El nombre debe contener cuatro caracteres minimo',
                    'data' => []
                ));
                return $json;
            }
            if (filter_var($_POST['email'], FILTER_VALIDATE_EMAIL ) == false)
            {
                $json = $this->response(array(
                    'code' => 400,
                    'message' => 'La dirección de correo no es valida',
                    'data' => []
                ));
                return $json;
            }
            if (strlen($_POST['password']) < 5)
            {
                $json = $this->response(array(
                    'code' => 400,
                    'message' => 'La contraseña tiene que tener al menos 5 caracteres',
                    'data' => []
                ));
                return $json;
            }
            if($this->isEmailCreated($email))
            {
                
               
                $user = Model_Users::find('first', array(
                   'where' => array(
                       array('email', $email)
                       ),
                   ));
                if ($user->active != 1){
                    
                    $input = $_POST;
                
                    $user->username = $input['username'];
                    $user->email = $input['email'];
                    $user->password = $input['password'];
                    $user->active = 1;
                    $user->save();
                    $settings = new Model_Settings();
                    $settings->location = 1;
                    $settings->notifications = 1;
                    $settings->information = 1;
                    $settings->id_user = $user->id;
                    $settings->save();
                    $dataToken = array(
                        "username" => $username,
                        "password" => $password
                     );
                    $token = JWT::encode($dataToken, $this->key);
                    $json = $this->response(array(
                        'code' => 200,
                        'message' => 'Usuario creado',
                        'data' => ['token' => $token, 'settings' => $settings]
                    ));
          
                    return $json;
                } else {
                    $json = $this->response(array(
                    'code' => 400,
                    'message' => 'El usuario ya esta registrado',
                    'data' => []
                ));
                return $json;
                }
             } else
              { 
            //Si el email no es valido ( no esta en la bbdd o ya esta registrado )
                $json = $this->response(array(
                    'code' => 400,
                    'message' => 'El email no esta registrado',
                    'data' => []
                ));
                return $json;
             } 
   
        } 
        catch (Exception $e) 
        {
            $json = $this->response(array(
                'code' => 500,
                'message' => $e->getMessage()
            ));
            return $json;
        }
    }
        
    public function post_emailValidate()
    {
        try {
            if ( empty($_POST['email'])) 
            {
                $json = $this->response(array(
                    'code' => 400,
                    'message' =>  'Email no introducido',
                    'data' => []
                ));
                return $json;
            }
            // Validación de e-mail
            $input = $_POST;
            $users = Model_Users::find('all', array(
                'where' => array(
                    array('email', $input['email'])
                )
            ));
            if ( ! empty($users) )
            {
                foreach ($users as $key => $value)
                {
                    $id = $users[$key]->id;
                    $username = $users[$key]->username;
                    $email = $users[$key]->email;
                }
            }
            else
            {
                return $this->response(array(
                    'code' => 400,
                    'message' => 'El email no existe'
                    ));
            }
            if ($email == $input['email'])
            {
                $tokendata = array(
                    "id" => $id,
                    "username" => $username,
                    "email" => $email
                );
                $token = JWT::encode($tokendata, $this->key);
                $json = $this->response(array(
                    'code' => 200,
                    'message' => 'Email existe',
                    'data' => ['token' => $token]
                ));
                return $json;
            }
        }
        catch (Exception $e)
        {
            $json = $this->response(array(
                'code' => 500,
                'message' => $e->getMessage()
            ));
            return $json;
        }
    }
    public function post_changePass()
    {
        try 
        {
            $header = apache_request_headers();
            if (isset($header['Authorization'])) 
            {
                $token = $header['Authorization'];
                $dataJwtUser = JWT::decode($token, $this->key, array('HS256'));
            }
            if ( empty($_POST['newpass']) || empty($_POST['repeatPass'])) 
            {
                $json = $this->response(array(
                    'code' => 400,
                    'message' =>  'Existen campos vacíos',
                    'data' => []
                ));
                return $json;
            }
            if(($_POST['newpass']) == ($_POST['repeatPass']))
            {
                $input = $_POST;
                $user = Model_Users::find($dataJwtUser->id);
                $user->password = $input['newpass'];
               
                $user->save();
                                
                $json = $this->response(array(
                    'code' => 200,
                    'message' =>  'Contraseña cambiada',
                    'data' => []
                ));
                return $json;
            }   
            else
            {
                $json = $this->response(array(
                    'code' => 400,
                    'message' =>  'Los campos no coinciden',
                    'data' => []
                ));
                return $json;
            }
        }
        catch (Exception $e)
        {
            $json = $this->response(array(
                'code' => 500,
                'message' => $e->getMessage(),
                'data' => []
            ));
            return $json;
        }
    }
    public function post_delete()
    {
        $user = Model_Users::find($_POST['id']);
        $userName = $user->username;
        $user->delete();
        $json = $this->response(array(
            'code' => 200,
            'message' => 'usuario borrado',
            'data' => $userName
        ));
        return $json;
    }
     public function get_users()
    {

        $users = Model_Users::find('all', array(
            'where' => array(
                array('active', 1),
            )
        ));
        
        return $this->response(Arr::reindex($users));
    }
    public function get_user()
    {
        $header = apache_request_headers();
        if (isset($header['Authorization'])) 
        {
            $token = $header['Authorization'];
            $dataJwtUser = JWT::decode($token, $this->key, array('HS256'));
        }
         $users = Model_Users::find('all', array(
            'where' => array(
                array('id', $dataJwtUser->id)
            )
        ));
         
        return $this->response(array(
                    'code' => 200,
                    'message' => 'Datos del usuario',
                    'data' => $users
                ));
    }
    public function get_usersInactive()
    {
        $header = apache_request_headers();
        if (isset($header['Authorization'])) 
        {
            $token = $header['Authorization'];
            $dataJwtUser = JWT::decode($token, $this->key, array('HS256'));
        }
         $users = Model_Users::query()->related('roles')->get();
         return $this->response(Arr::reindex($users));
    }
    public function isEmailCreated($email)
    {
        $users = Model_Users::find('all', array(
            'where' => array(
                array('email', $email),
             
            )
        ));
        
        if($users != null){
            return true;
        }
        else 
        {
            return false;
        }
    }
    
    public function get_login()
    { 
        try {
                if ( empty($_GET['username']) || empty($_GET['password']))
                {
                    return $this->response(array(
                        'code' => 400,
                        'message' => 'Existen campos vacíos',
                        'data' => []
                    ));
                }
                $input = $_GET;
                $users = Model_Users::find('all', array(
                    'where' => array(
                        array('username', $input['username']),array('password', $input['password'])
                    )
                ));
                if ( ! empty($users) )
                {
                    foreach ($users as $key => $value)
                    {
                        $id = $users[$key]->id;
                        $username = $users[$key]->username;
                        $password = $users[$key]->password;
                    }
                }
                else
                {
                    return $this->response(array(
                        'code' => 400,
                        'message' => 'Usuario y/o contraseña incorrectos',
                        'data' => []
                        ));
                }
                if ($username == $input['username'] and $password == $input['password'])
                {
                    $dataToken = array(
                        "id" => $id,
                        "username" => $username,
                        "password" => $password
                    );
                    $token = JWT::encode($dataToken, $this->key);
              
                    return $this->response(array(
                        'code' => 200,
                        'message'=> 'Login Correcto',
                        'data' => ['token' => $token, 'username' => $username, 'image_profile' => 'alvaroiocld']
                    ));
                }
            }
            catch (Exception $e)
            {
                $json = $this->response(array(
                    'code' => 500,
                    'message' => $e->getMessage(),
                    'data' => []
                ));
                return $json;
            }
    } 
    
    public function post_user()
    {
        try
        {
            /*$header = apache_request_headers();
            if (isset($header['Authorization'])) 
            {
                $token = $header['Authorization'];
                $dataJwtUser = JWT::decode($token, $this->key, array('HS256'));
            }
            else
            {
                return $this->respuesta(400, 'Usuario no logueado', []);
            }*/
            if(isset($_POST['id_user']))
            {
                if(empty($_POST['id_user']))
                {
                    return $this->respuesta(400, 'Usuario no introducido', []);
                }
                else
                {   
                    $id = $_POST['id_user'];
                    $users = Model_Users::find($id);
                    return $this->respuesta(200, 'Usuario devuelto', ['user' => $users]);
                }
            }
            else
            {
                return $this->respuesta(400, 'Usuario no existente', []);
            }
        }
        catch(Exception $e)
        {
            return $this->respuesta(500, $e->getMessage(), []);
        }
    }
    
}