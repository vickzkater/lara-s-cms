<?php

namespace App\Http\Controllers\Admin\system;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

// LIBRARIES
use App\Libraries\Helper;

// MODELS
use App\Models\system\SysLog;

class LogController extends Controller
{
    // set this module
    private $module = 'System Logs';

    public function list()
    {
        // authorizing...
        $authorize = Helper::authorizing($this->module, 'View List');
        if ($authorize['status'] != 'true') {
            return back()->with('error', $authorize['message']);
        }

        // authorizing division...
        $allowed_divisions = [];
        $sessions = Session::all();
        foreach ($sessions['division'] as $item) {
            $authorize_division = Helper::authorizing_division($item);
            if ($authorize_division['status'] == 'true') {
                if ($authorize_division['message'] == 'all') {
                    break;
                } else {
                    $allowed_divisions[] = $authorize_division['message'];
                }
            } else {
                return back()->with('error', $authorize['message']);
            }
        }

        $data = SysLog::select('sys_logs.*', 'sys_users.username', 'sys_log_details.action AS act_name')
            ->leftJoin('sys_users', 'sys_logs.subject', 'sys_users.id')
            ->leftJoin('sys_log_details', 'sys_log_details.id', 'sys_logs.action')
            ->orderBy('sys_logs.id', 'desc')
            ->paginate(10);

        return view('admin.system.logs.list', compact('data'));
    }
}
