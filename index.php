<?php

    $tag = explode('/', $_GET['tag']);
    $path = explode('/', $_GET['path']);
 
    $paramId = "";

    if(count($path)>1){
        $paramId = $path[1];
    }
    
    if($tag != [""]){
        $paramTag = $tag[0];
    }

    $contents = file_get_contents('db.json');
    $json = json_decode($contents, true);

    $method = $_SERVER['REQUEST_METHOD'];
   
    header('Content-type: application/json');

    $body = file_get_contents('php://input');

    function findByTag($vector, $paramTag){

        $encontrado = [];

        foreach($vector as $key => $obj){
          
            if (in_array($paramTag, $obj['tags'], true)) {

                $encontrado[$key] = $obj;
            }
        }

        return $encontrado;
    }

    function findById($vector, $paramId){
        $encontrado = -1;

        foreach($vector as $key => $obj){     
                 
           
            if ($obj['id'] == $paramId[0]) {
           
                $encontrado = $obj;
            }
        }
        return $encontrado;
    }
   
    if($method === 'GET'){

        if($paramTag == null){
            echo json_encode($json['tools']);
        }else{
            $encontrado = findByTag($json['tools'], $paramTag);

            if($encontrado != []){
                echo json_encode($encontrado);
            }else{
                echo 'ERROR. Tag não localizada.';
                exit;
            }
        }
    }

    if($method === 'POST'){

        $id = [];

        foreach($json['tools'] as $key => $obj){     
                            
            $id[$key] = $obj['id']; 
        }
       
        $jsonBody = json_decode($body, true);
        $jsonBody['id'] = (max($id) + 1);

        if(!$json['tools']){
            $json['tools'] = [];
        }

        $json['tools'][] = $jsonBody;

        file_put_contents('db.json', json_encode($json));
        echo json_encode($jsonBody);
    }

    if($method === 'DELETE'){
        
        $id = explode('/', $_GET['id']);
       
        if($id == ""){
            echo 'Error. parametro id undefined';
        }else{
            $encontrado = findById($json['tools'], $id);
         
            if($encontrado>=0){    
                $idDel = ($id[0] - 1);           
                unset($json['tools'][$idDel]);
                file_put_contents('db.json', json_encode($json));
                echo json_encode("Tool com id = ".$id[0]." apagada.");

            }else{
                echo 'Error. ID não localizado.';
                exit;
            }
        }
    }

    if($method === 'PUT'){

        $id = explode('/', $_GET['id']);
     
        if($id[0] == ""){
            echo 'Error. parametro id undefined';
        }else{
            $encontrado = findById($json['tools'], $id);
            
            if($encontrado>=0){

                $idParam = ($id[0] - 1); 

                $jsonBody = json_decode($body, true);
                
                $jsonBody['id'] = $id[0];
                
                $json['tools'][$idParam] = $jsonBody;

                echo json_encode($json['tools'][$idParam]);
                file_put_contents('db.json', json_encode($json));
            }else{
                echo 'Error. ID não localizado.';
                exit;
            }
        }
    }