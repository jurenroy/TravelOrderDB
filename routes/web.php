<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\OTPController;
use App\Http\Controllers\LeaveFormController;

use App\Models\Employee;
use App\Models\Name;
use App\Models\Position;
use App\Models\Division;
use App\Models\Form;
use App\Models\Account;
use App\Models\Type;
use App\Models\OTP;
use App\Models\LeaveForm;

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

Route::get('/get_accounts_json', function () {
    $accounts = Account::all();
    return response()->json($accounts);
});

Route::get('/get_type_json', function () {
    $types = Type::all();
    return response()->json($types);
});

Route::get('/get_otp_json', function () {
    $otps = OTP::all();
    return response()->json($otps);
});

Route::get('/get_leave_json', function () {
    $leave = LeaveForm::all();
    return response()->json($leave);
});

Route::middleware('cors')->get('/get_names_json', function () {
    $names = Name::all();
    return response()->json($names);
});

Route::get('/add_form', [FormController::class, 'showForm']);
Route::post('/add_form', [FormController::class, 'submitForm'])->name('submit.form');

Route::post('update_form/{id}', [FormController::class, 'update_via_post']);

Route::get('/add_account', [AccountController::class, 'showForm']);
Route::post('/add_account', [AccountController::class, 'submitForm'])->name('submit.form');

Route::post('update_account/{id}', [AccountController::class, 'update_via_post']);

Route::post('/send-otp/{account_id}', [OTPController::class, 'sendOTP']);

Route::post('update_employee/{name_id}', [EmployeeController::class, 'update_via_post']);
Route::post('/add_employees', [EmployeeController::class, 'store']);
Route::post('edit_employee', [EmployeeController::class, 'edit_employee']);

Route::post('/addleave_form', [LeaveFormController::class, 'store']);

Route::post('updateleave_form/{id}', [LeaveFormController::class, 'update']);


