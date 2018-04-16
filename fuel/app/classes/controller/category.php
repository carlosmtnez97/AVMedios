
<?php
use Firebase\JWT\JWT;
class Controller_Category extends Controller_Rest
{
	public function post_create()
    {
    	try {
            
            if ( empty($_POST['title']))
            {
                $json = $this->response(array(
                    'code' => 400,
                    'message' =>  'Falta algun campo'
                ));
                return $json;            }
            $category = $_POST['title'];
            if($this->isCategoryCreated($category))
            {
                $json = $this->response(array(
                    'code' => 400,
                    'message' => 'Categoria ya existe',
                    'data' => []
                ));
                return $json;
            }
            $input = $_POST;
            $category = new Model_Category();
            $category->title = $input['title'];
            $category->save();
            
            $json = $this->response(array(
                'code' => 200,
                'message' => 'Categoria creada',
                'data' => []
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
     public function get_category()
    {
        /*return $this->respuesta(500, 'trace');
        exit;*/
        $category = Model_Category::find('all');
        return $this->response(Arr::reindex($category));
    }
    public function isCategoryCreated($title)
    {
        $category = Model_Category::find('all', array(
            'where' => array(
                array('title', $title)
            )
        ));
        
        if(count($category) < 1)  {
            return false;
        }
        else 
        {
            return true;
        }
    }
    public function post_addUser()
    {
        try
        {
            
            if(empty($_POST['id_user']) || empty($_POST['id_category']))
            {
                $json = $this->response(array(
                    'code' => 400,
                    'message' => 'Campos vacíos',
                    'data' => []
                ));
                return $json;
            }
            else
            {
                $id_user = $_POST['id_user'];
                $id_category = $_POST['id_category'];
                $user = Model_Users::find($id_user);
                $category = Model_Category::find($id_category);
                if(isset($id_user) || !empty($id_user) || isset($id_category) || !empty($id_category))
                {
                    $belong = Model_Belong::find('all', array(
                        'where' => array(
                            array('id_user', $id_user),
                            array('id_category', $id_category)
                        )
                    ));
                    if(!empty($belong))
                    {
                        $json = $this->response(array(
                            'code' => 400,
                            'message' => 'El usuario ya pertenece a la categoria',
                            'data' => []
                        ));
                        return $json;
                    }
                    else
                    {
                        $belong = New Model_Belong();
                        $belong->id_user = $id_user;
                        $belong->id_category = $id_category;
                        $belong->save();
                        $json = $this->response(array(
                            'code' => 200,
                            'message' => 'Usuario agregado a categoria',
                            'data' => ['category' => $id_category, 'user' => $id_user]
                        ));
                        return $json;
                    }
                }
                else
                {
                    $json = $this->response(array(
                        'code' => 400,
                        'message' => 'No existe el usuario o la categoria',
                        'data' => []
                    ));
                    return $json;
                }
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
    public function post_quitUser()
    {
        try
        {
            /*$header = apache_request_headers();
            if (isset($header['Authorization'])) 
            {
                $token = $header['Authorization'];
                $dataJwtUser = JWT::decode($token, $this->key, array('HS256'));
            }*/
            
            if(empty($_POST['id_user']) || empty($_POST['id_category']))
            {
                $json = $this->response(array(
                    'code' => 400,
                    'message' => 'Campos vacíos',
                    'data' => []
                ));
                return $json;
            }
            else
            {
                $id_user = $_POST['id_user'];
                $id_category = $_POST['id_category'];
                $user = Model_Users::find($id_user);
                $category = Model_Category::find($id_category);
                if(isset($id_user) || !empty($id_user) || isset($id_category) || !empty($id_category))
                {
                    $belong = Model_Belong::find('first', array(
                        'where' => array(
                            array('id_user', $id_user),
                            array('id_cateogry', $id_category)
                        )
                    ));
                    if(!empty($belong))
                    {
                        $belong->delete();
                        $json = $this->response(array(
                            'code' => 200,
                            'message' => 'Usuario quitado de la categoria',
                            'data' => ['user' => $id_user]
                        ));
                        return $json; 
                    }
                    else
                    {
                       $json = $this->response(array(
                            'code' => 400,
                            'message' => 'El usuario ya está quitado de la categoria',
                            'data' => []
                        ));
                        return $json; 
                    }
                }
                else
                {
                    $json = $this->response(array(
                        'code' => 400,
                        'message' => 'No existe el usuario o la categoria',
                        'data' => []
                    ));
                    return $json;
                }
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
    public function get_users()
    {
        try 
        {
            /*$header = apache_request_headers();
            if (isset($header['Authorization'])) 
            {
                $token = $header['Authorization'];
                $dataJwtUser = JWT::decode($token, $this->key, array('HS256'));
            }*/
            if(empty($_GET['id_category']))
            {
                $json = $this->response(array(
                    'code' => 400,
                    'message' => 'Campos vacíos',
                    'data' => []
                ));
                return $json;
            }
            else
            {
                $id_category = $_GET['id_category'];
                $belonging = Model_Belong::find('all', array(
                    'where' => array(
                        array('id_category', $id_category),
                    ),
                ));
                if($belonging != null)
                {
                    foreach ($belonging as $key => $belong)
                    {   
                        $new = Model_Users::find('all', array(
                            'where' => array(
                                array('id', $belong->id_user),
                            ),
                        ));
                       
                        foreach ($new as $key => $belon)
                        {   
                            $user[] = $belon;
                        } 
                    }
                    
                    $json = $this->response(array(
                        'code' => 200,
                        'message' => 'Usuarios de la categoria',
                        'data' => ['usersCategory' => $user]
                    ));
                    return $json;
                }
                else
                {
                    $json = $this->response(array(
                        'code' => 400,
                        'message' => 'La categoria no existe',
                        'data' => []
                    ));
                    return $json;
                }
            }
        }
        catch(Exception $e)
        {
            $json = $this->response(array(
                'code' => 500,
                'message' => $e->getMessage(),
                'data' => []
            ));
            return $json;
        }
    }
}