<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\EmailAlertService;
use App\Models\EmailLog;
use Illuminate\Http\Request;

class EmailSettingsController extends Controller
{
    protected $emailService;

    public function __construct(EmailAlertService $emailService)
    {
        $this->emailService = $emailService;
    }

    /**
     * Show email settings page
     */
    public function index()
    {
        return view('admin.email-settings.index');
    }

    /**
     * Send test email
     */
    public function testEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $result = $this->emailService->sendTestEmail($request->email);

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        } else {
            return redirect()->back()->with('error', $result['message']);
        }
    }

    /**
     * Show email history
     */
    public function history(Request $request)
    {
        $query = EmailLog::with('user')->latest();

        // Filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $emails = $query->paginate(20)->appends($request->except('page'));

        return view('admin.email-settings.history', compact('emails'));
    }

    /**
     * Show email statistics
     */
    public function stats()
    {
        $stats = $this->emailService->getEmailStats(30);

        return view('admin.email-settings.stats', compact('stats'));
    }
}
