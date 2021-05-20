<?php

use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;
use App\Course;


class FluxoCursoTest extends TestCase

{   
    // Verifica todos os Cursos
    public function testReturnAllCourses(){

        $this->get('courses');
        $this->seeStatusCode(200);
        $this->seeJsonStructure(
            ['data' => [ '*' =>
                [    
                    'id',
                    'name',
                    'description',
                    'body',
                    'price'
                ]
            ]
            ]);
    }

    // Cria o Curso 
    public function testCreateCourses(){

    $this->json('POST', 'courses', 
        [
            'name'          => 'Teste',
            'price'         => '10',
            
        ])
        
        ->seeJsonEquals(['data' => ['message' => 'Curso criado com sucesso']]);
    }

    // Tenta criar um curso com valor negativo 
    public function testCreateCoursesNegativePrice(){

        $this->json('POST', 'courses', 
            [
                'name'          => 'Teste',
                'price'         => '-10',
                
            ])
            
            ->seeJsonEquals(['data' => ['message' =>['The price must be at least 0.']]]);
    }

    // Tenta criar um curso com nome maior que o permitido 
    public function testCreateCoursesBigName(){

        $this->json('POST', 'courses', 
            [
                'name'          => 'TesteTesteTesteTesteTesteTesteTesteTesteTesteTeste',
                'price'         => '1',
                
            ])
            
            ->seeJsonEquals(['data' => ['message' =>['The name may not be greater than 20 characters.']]]);
    }

    // tenta criar curso com os campos obrigatórios vazios 
    public function testCreateCoursesNull(){

        $this->json('POST', 'courses', 
            [
                'name'          => '',
                'price'         => ''
                
            ])
            
            ->seeJsonEquals(['data' => 
            ['message' =>['The name field is required.', 'The price field is required.'
            ]]]);
    }

    // Verifica o id do ultimo curso criado 
    public function testReturnCourses(){
        $data = Course::orderBy('id','desc')->first();
        $id = $data->id;

        $this->get("courses/$id");
        $this->seeJsonStructure(
                [   
                    'name',
                    'description',
                    'body',
                    'price'
                ]    
        );
    }

    // Atualiza o curso criado 
    public function testUpdateCourses(){
        $data = Course::orderBy('id','desc')->first();
        $id = $data->id;

        $this->json('PUT', "courses/$id", 
            [
                'name'             => 'Teste Update',
                'price'            => '100'
            ])     
            ->seeJsonEquals(['data' => ['message' => 'Curso atualizado com sucesso']]);
            
    }

    // Verfica e faz as comparações se o curso foi atualizad com sucesso 
    public function testUpdateCheck(){
        $data = Course::orderBy('id','desc')->first();
        $id = $data->id;
        
        $this->json('get', "courses/$id");
        $this->seeJsonEquals($data->toArray());
        
    }

    // Atualizar curso para valores negativos 
    public function testUpdateCoursesNegativePrice(){

        $this->json('POST', 'courses', 
            [
                'name'          => 'Teste',
                'price'         => '-10',
                
            ])
            
            ->seeJsonEquals(['data' => ['message' =>['The price must be at least 0.']]]);
    }

    // tenta atualizar o nome do curso para acima da quantidade de caracteres permitidos
    public function testUpdateCoursesBigName(){

        $this->json('POST', 'courses', 
            [
                'name'          => 'TesteTesteTesteTesteTesteTesteTesteTesteTesteTeste',
                'price'         => '1',
                
            ])
            
            ->seeJsonEquals(['data' => ['message' =>['The name may not be greater than 20 characters.']]]);
    }

    // tenta atualizar o curso com os campos obrigatórios vazios 
    public function testUpdateCoursesNull(){

        $this->json('POST', 'courses', 
            [
                'name'          => '',
                'price'         => ''
                
            ])
            
            ->seeJsonEquals(['data' => 
            ['message' =>['The name field is required.', 'The price field is required.'
            ]]]);
    }

    // endpoint que deleta o curso 
    public function testDeleteCourses(){

        $this->testCreateCourses();

        //busca o ultimo curso criado 
        $data = Course::orderBy('id', 'desc')->first();
        $Id = $data->id; // salva o id do curso 
        
        $this->json('DELETE', "courses/$Id"); // deleta o curso 
        $this->seeJsonEquals( ['data' => ['message' => 'Curso deletado com sucesso']]);

        $this->json('get', "courses/$Id"); // verifica se o curso ainda existe buscando pelo id 
        $this->seeJsonEquals( ['data' => ['message' => 'Curso nao encontrado']]);

        $this->json('put', "courses/$Id", []);
        $this->seeJsonEquals( ['data' => ['message' => 'Curso nao encontrado']]);
        // tenta atualizar curso deletado p/ verificar novamente a exclusão

        $this->json('delete', "courses/$Id", []);
        $this->seeJsonEquals( ['data' => ['message' => 'Curso nao encontrado']]);
        // tenta deletar curso deletado p/ verificar novamente a exclusão

    }
    
}