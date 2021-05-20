<?php

namespace App\Http\Controllers;

use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Course;

class CourseController extends Controller
{
    private $course;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Course $course)
    {
        $this->course = $course;
    }

    public function index()
    {
        return $this->course->paginate(10);
    }

    public function show($courseId)
    {
        $course = $this->course->find($courseId);

        if ($course) {
            return $course;
        } 

        return response(['data' => [
            'message' => 'Curso nao encontrado' ]
            ]);
    }

    public function store(Request $request)
    {
        
        $validator = Validator::make(
            $request->all(),
            [
                'name'          => 'required | max:20',
                'price'         => 'required | numeric | min:0'
            ]
        );

        if ($validator->fails()){
            return response(['data' => [ 'message' => $validator->messages()->all()]]);
        }
              
        $this->course->create($request->all());

        return response()
            ->json(['data' => ['message' => 'Curso criado com sucesso']]);
    }

    public function update($course, Request $request){ 
         $course = $this->course->find($course);

         $validator = Validator::make(
            $request->all(),
            [
                'name'          => 'required | max:20',
                'price'         => 'required | numeric | min:0'
            ]
        );

        if ($course) {
            $course->update($request->all());      

                return response()
                    ->json(['data' => ['message' => 'Curso atualizado com sucesso']]);
        } 

        return response()
        ->json(['data' => [
            'message' => 'Curso nao encontrado' ]
            ]);
    }


    public function delete($course)
    {
        $course = $this->course->find($course);

        if ($course) {
            $course->delete();

            return response()
            ->json(['data' => ['message' => 'Curso deletado com sucesso']]);
        } 

        return response()
        ->json(['data' => [
            'message' => 'Curso nao encontrado' ]
            ]);
        
    }

}