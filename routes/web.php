<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\OTPController;
use App\Http\Controllers\LeaveFormController;
use App\Http\Controllers\RequestController;

use App\Models\Employee;
use App\Models\Name;
use App\Models\Position;
use App\Models\Division;
use App\Models\Form;
use App\Models\Account;
use App\Models\Type;
use App\Models\OTP;
use App\Models\LeaveForm;
use App\Models\RequestForm;

use App\Http\Model\FadrfForm;

use App\Http\Controllers\ServiceController;
use App\Http\Controllers\FeedbackController;

use App\Http\Controllers\FadrfController;


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
Route::post('/acclogin', [AccountController::class, 'acclogin']);

Route::post('update_account/{id}', [AccountController::class, 'update_via_post']);

Route::post('/send-otp/{account_id}', [OTPController::class, 'sendOTP']);

Route::post('update_employee/{name_id}', [EmployeeController::class, 'update_via_post']);
Route::post('/add_employees', [EmployeeController::class, 'store']);
Route::post('edit_employee', [EmployeeController::class, 'edit_employee']);

Route::post('/addleave_form', [LeaveFormController::class, 'store']);

Route::post('updateleave_form/{id}', [LeaveFormController::class, 'update']);

// Service Routes
Route::post('services', [ServiceController::class, 'store']); // Create
Route::get('services', [ServiceController::class, 'index']); // Read all
Route::get('services/{id}', [ServiceController::class, 'show']); // Read one
Route::post('services/update/{id}', [ServiceController::class, 'update']); // Update
Route::delete('services/{id}', [ServiceController::class, 'destroy']); // Delete
// Feedback Routes
Route::post('feedbacks', [FeedbackController::class, 'store']); // Create
Route::get('feedbacks', [FeedbackController::class, 'index']); // Read all
Route::get('feedbacks/{id}', [FeedbackController::class, 'show']); // Read one
Route::post('feedbacks/update/{id}', [FeedbackController::class, 'update']); // Update
Route::delete('feedbacks/{id}', [FeedbackController::class, 'destroy']); // Delete
//fadrf request
Route::post('FADRFsubmit_request', [FadrfController::class, 'store']); // Create
Route::get('FADRFget_request', [FadrfController::class, 'index']); // Read all
Route::get('FADRFshow_request/{id}', [FadrfController::class, 'show']); // Read one
Route::post('FADRFupdate_request/{id}', [FadrfController::class, 'update']); // Update

// Route for request
Route::post('submit_request', [RequestController::class, 'store']); // Create
Route::get('get_request', [RequestController::class, 'index']); // Read all
Route::get('show_request/{id}', [RequestController::class, 'show']); // Read one
Route::post('update_request/{id}', [RequestController::class, 'update']); // Update

// Route to serve images
Route::get('/storage/{filename}', function ($filename) {
    $path = storage_path('app/public/images/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    return Response::make($file, 200)->header("Content-Type", $type);
});