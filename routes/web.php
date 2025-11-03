<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\OTPController;
use App\Http\Controllers\LeaveFormController;
use App\Http\Controllers\NameController;
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

use App\Http\Controllers\MessageController;
use App\Http\Controllers\FadrfController;
use App\Http\Controllers\TravelClearanceController;
use App\Http\Controllers\AuditLogController;

use App\Http\Controllers\RsoController;

use App\Http\Controllers\CalendarController;


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
Route::get('/get_employees_json/{name_id}', [EmployeeController::class, 'show']);

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
Route::get('get_forms_json/{id}/', [FormController::class, 'show']); // Read specific
Route::get('get_forms_json/{name_id}/{status}/{limit}/{offset}', [FormController::class, 'getForm']); // Read specific
Route::get('get_forms_json/{name_id}/count', [FormController::class, 'getCount']); // Read specific

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

Route::get('/get_leave_json/{id}', [LeaveFormController::class, 'shows']);
Route::get('/get_leave_json/{name_id}/{status}/{limit}/{offset}', [LeaveFormController::class, 'show']);
Route::get('/get_leave_json/{name_id}/count', [LeaveFormController::class, 'getCount']); // Read specific

Route::get('/get_leave_json', function () {
    $leave = LeaveForm::all();
    return response()->json($leave);
});

Route::middleware('cors')->get('/get_names_json', function () {
    $names = Name::all();
    return response()->json($names);
});

Route::get('get_names_json/{name_id}', [NameController::class, 'show']);
Route::get('get_accounts_json/{name_id}', [AccountController::class, 'show']);

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
Route::get('services/{name_id}/{status?}/{typeOfService?}/{limit}/{offset}', [ServiceController::class, 'showService']); // Read one
Route::get('services/{name_id}/count', [ServiceController::class, 'getCount']); // Read specific
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

Route::post('message', [MessageController::class, 'store']); // Create
Route::get('message/{user1_id}/{user2_id}', [MessageController::class, 'index']); // Read all
Route::get('message/{user1_id}/', [MessageController::class, 'indexer']); // Read all
Route::get('readmessage/{sender_id}/{receiver_id}/', [MessageController::class, 'markAsRead']); // Read all

Route::get('api/calendar/events', [CalendarController::class, 'index']);

// Travel Clearance Routes
Route::post('travel_clearances', [TravelClearanceController::class, 'store']); // Create
Route::get('travel_clearances', [TravelClearanceController::class, 'index']); // Read all
Route::get('travel_clearances/{id}', [TravelClearanceController::class, 'show']); // Read one
Route::post('travel_clearances/update/{id}', [TravelClearanceController::class, 'update']); // Update
Route::put('travel_clearances/{id}/approve', [TravelClearanceController::class, 'approve']); // Approve
Route::delete('travel_clearances/{id}', [TravelClearanceController::class, 'destroy']); // Delete
Route::get('travel_clearances/generate/clearance_number', [TravelClearanceController::class, 'generateClearanceNumber']); // Generate clearance number
Route::get('travel_clearances/suggestions/{travel_order_id}', [TravelClearanceController::class, 'getSuggestions']); // Get similar travel orders for suggestions
Route::get('audit_logs/travel_clearances/{id}', [TravelClearanceController::class, 'getAuditLogs']); // Get audit logs for a specific travel clearance

// Audit Logs Routes
Route::get('audit_logs', [AuditLogController::class, 'index']); // Get all audit logs
Route::get('audit_logs/{model}', [AuditLogController::class, 'getByModel']); // Get audit logs by model
Route::get('audit_logs/forms/{id}', [FormController::class, 'getAuditLogs']); // Get audit logs for a specific form

Route::post('rso', [RsoController::class, 'store']);             // Create
Route::get('rso', [RsoController::class, 'index']);             // Read all
Route::get('rso/{rso_number}', [RsoController::class, 'show']); // Read one
Route::post('rso/update/{rso_number}', [RsoController::class, 'update']); // Update
Route::delete('rso/{rso_number}', [RsoController::class, 'destroy']);     // Delete

Route::get('/storage/RSO/{filename}', function ($filename) {
    $path = storage_path('app/public/RSO/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    return Response::make($file, 200)->header("Content-Type", $type);
});

// Specific route for files in the 'ictrequest' folder
Route::get('/storage/ictrequest/{filename}', function ($filename) {
    $path = storage_path('app/public/ictrequest/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    return Response::make($file, 200)->header("Content-Type", $type);
});

// General route for all other files in the 'images' folder
Route::get('/storage/{filename}', function ($filename) {
    $path = storage_path('app/public/images/' . $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    return Response::make($file, 200)->header("Content-Type", $type);
});