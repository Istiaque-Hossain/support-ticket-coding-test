@extends('layouts.app')

@section('content')
    <div class="container">
        <h2 class="mb-3">Ticket Details</h2>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if (session('danger'))
            <div class="alert alert-danger">{{ session('danger') }}</div>
        @endif

        <div class="row">
            <div class="{{ $ticket->status === 'closed' ? 'col-md-12' : 'col-md-6' }}">
                <div class="card mb-3">
                    <div class="card-header">
                        <strong>Subject: </strong> {{ $ticket->subject }}
                    </div>
                    <div class="card-body">
                        <p><strong>Status: </strong>
                            <span class="badge {{ $ticket->status == 'open' ? 'badge-success' : 'badge-danger' }}">
                                {{ ucfirst($ticket->status) }}
                            </span>
                        </p>

                        <p><strong>Issue: </strong></p>
                        <p>{{ $ticket->issue }}</p>

                        <p><strong>Created at: </strong> {{ $ticket->created_at->format('d M Y, h:i:s A') }}</p>

                        <p><strong>User Name: </strong> {{ $ticket->user->name }}</p>

                        <p><strong>Email Address: </strong> {{ $ticket->user->email }}</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                @if ($ticket->status === 'open')
                    <form action="{{ route('tickets.respond', $ticket->id) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <textarea name="response" class="form-control" rows="3" placeholder="Write your response..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit Response</button>
                    </form>

                    @if (auth()->user()->role === 'admin')
                        <form action="{{ route('tickets.close', $ticket->id) }}" method="POST" class="mt-3">
                            @csrf
                            <button type="submit" class="btn btn-danger">Close Ticket</button>
                        </form>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <div class="container">
        <h4>Responses</h4>
        <div class="card mb-3">
            <div class="card-body">
                @forelse ($ticket->responses as $response)
                    <div class="mb-2">
                        <strong>{{ $response->user->name }} {{ $response->user->role === 'admin' ? '(Admin):' : '(User):' }}</strong>
                        <p>{{ $response->response }}</p>
                        <small>{{ $response->created_at->diffForHumans() }}</small>
                    </div>
                    <hr>
                @empty
                    <p>No responses yet.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection
