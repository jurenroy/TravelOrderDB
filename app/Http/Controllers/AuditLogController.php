<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AuditLog;

class AuditLogController extends Controller
{
    public function index()
    {
        $auditLogs = AuditLog::orderBy('created_at', 'desc')->get();
        return response()->json($auditLogs);
    }

    public function getByModel($model)
    {
        $auditLogs = AuditLog::where('model', $model)->orderBy('created_at', 'desc')->get();
        return response()->json($auditLogs);
    }
}
