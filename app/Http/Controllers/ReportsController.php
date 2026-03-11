<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function index()
    {
        $tickets = Ticket::with(['status', 'priority', 'department', 'user', 'user.userRole', 'agent', 'agent.userRole'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('reports', compact('tickets'));
    }
}

