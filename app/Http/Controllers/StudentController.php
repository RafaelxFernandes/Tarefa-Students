<?php

namespace App\Http\Controllers;

use App\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\CreateStudent;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $students = Student::all();
        return $students->toJson();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateStudent $request)
    {
        $newStudent = new Student;

        $newStudent->nome = $request->nome;
        $newStudent->idade = $request->idade;
        $newStudent->email = $request->email;
        $newStudent->cpf = $request->cpf;
        $newStudent->telefone = $request->telefone;

        if(!Storage::exists('localDocs/')){
            Storage::makeDirectory('localDocs/', 0775, true);
        }

        $documento = base64_decode($request->boletim);

        $docName = uniqid().'.pdf';

        $path = storage_path('/app/localDocs/'.$docName);

        file_put_contents($path,$documento);
        
        $newStudent->boletim = $docName;

        $newStudent->save();
        return response()->json('Estudante criado com sucesso!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function show(Student $student)
    {
        if(!Storage::exists('localDocs/')){
            Storage::makeDirectory('localDocs/', 0775, true);
        }

        $documento = base64_decode($student->boletim);

        $docName = uniqid().'.pdf';

        $path = storage_path('/app/localDocs/'.$docName);

        file_put_contents($path,$documento);
        
        $student->boletim = $docName;
        
        return response()->download($path, $student->boletim);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Student $student)
    {
        if($request->nome){
          $student->nome = $request->nome;
        }
        if($request->idade){
          $student->idade = $request->idade;
        }
        if($request->email){
          $student->email = $request->email;
        }
        if($request->cpf){
          $student->cpf = $request->cpf;
        }
        if($request->telefone){
          $student->telefone = $request->telefone;
        }

        if($request->boletim){
            Storage::delete('localDocs/'.$student->boletim);

            if(!Storage::exists('localDocs/')){
                Storage::makeDirectory('localDocs/', 0775, true);
            }

            $documento = base64_decode($request->boletim);

            $docName = uniqid().'.pdf';

            $path = storage_path('/app/localDocs/'.$docName);

            file_put_contents($path,$documento);
            
            $student->boletim = $docName;
        }

        $student->save();
        return response()->json('Estudante atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function destroy(Student $student)
    {
        Storage::delete('localDocs/'.$student->boletim);
        Student::destroy($student);
        return response()->json('Estudante deletado com sucesso!');
    }

    public function download(Student $student)
    {
        $filePath = storage_path('app/localDocs/'.$student->boletim);
        return response()->download($filePath, $student->boletim);   
    }
}