<?php

use Illuminate\Support\Facades\Route;

use App\Models\Employee;

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\FormController;

use App\Models\Name;
use App\Models\Position;
use App\Models\Division;
use App\Models\Form;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/status', function () {
    return view('status');
});

Route::get('/get_employees_json', function () {
    $employee = Employee::all();
    return response()->json($employee);
});

Route::get('/get_namez_json', function () {
    $names = Name::all();
    return response()->json($names);
});

Route::get('/get_positions_json', function () {
    $positions = Position::all();
    return response()->json($positions);
});

Route::get('/get_divisions_json', function () {
    $divisions = Division::all();
    return response()->json($divisions);
});

Route::get('/get_forms_json', function () {
    $forms = Form::all();
    return response()->json($forms);
});

Route::middleware('cors')->get('/get_names_json', function () {
    $names = Name::all();
    return response()->json($names);
});


Route::get('/add_form', [FormController::class, 'showForm']);
Route::post('/add_form', [FormController::class, 'submitForm'])->name('submit.form');

Route::post('update_form/{id}', [FormController::class, 'update_via_post']);



