<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Ticket;
use App\Mail\TicketClosed;
use Illuminate\Http\Request;
use App\Mail\NewTicketOpened;
use App\Models\TicketResponse;
use Illuminate\Support\Facades\Mail;

class TicketController extends Controller
{
    public function index()
    {
        $tickets = auth()->user()->role === 'admin'
            ? Ticket::all()
            : Ticket::where('user_id', auth()->id())->get();

        return view('tickets.index', compact('tickets'));
    }

    public function create()
    {
        return view('tickets.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'issue' => 'required|string',
        ]);

        Ticket::create([
            'user_id' => auth()->id(),
            'subject' => $request->subject,
            'issue' => $request->issue,
        ]);

        $adminEmail = User::Where('role', 'admin')->pluck('email')->first();
        Mail::to($adminEmail)->send(new NewTicketOpened());

        return redirect()->route('tickets.index')->with('success', 'Ticket created successfully!');
    }

    public function show(Ticket $ticket)
    {
        if (auth()->user()->role !== 'admin' && auth()->id() !== $ticket->user_id)
        {
            abort(403, 'Unauthorized action.');
        }

        return view('tickets.show', compact('ticket'));
    }

    public function respond(Request $request, Ticket $ticket)
    {
        if ($ticket->status === 'closed')
        {
            return redirect()->route('tickets.show', $ticket)->with('danger', 'The ticket has been closed');
        }

        $request->validate([
            'response' => 'required|string',
        ]);


        TicketResponse::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth()->id(),
            'response' => $request->input('response'),
        ]);

        return redirect()->route('tickets.show', $ticket)->with('success', 'Response sent!');
    }


    public function close(Ticket $ticket)
    {
        $ticket->update(['status' => 'closed']);

        Mail::to($ticket->user->email)->send(new TicketClosed($ticket));

        return redirect()->route('tickets.index')->with('success', 'Ticket closed successfully!');
    }
}
