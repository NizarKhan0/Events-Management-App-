<?php

namespace App\Http\Controllers\Api;

use App\Models\Event;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Http\Traits\CanLoadRelationships;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers;
use Illuminate\Support\Facades\Gate;

// class EventController extends \Illuminate\Routing\Controller
class EventController extends Controller
{
    use CanLoadRelationships;
    private array $relations = ['user', 'attendees', 'attendees.user'];

    // public static function middleware(): array
    // {
    //     return [
    //         new Middleware(middleware: 'auth:sanctum', except: ['index', 'show']),
    //     ];
    // } ini kalau guna HassMiddleware/Middleware

    public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
        $this->middleware('throttle:api')->only(['store', 'update', 'destroy']);
        $this->authorizeResource(Event::class, 'event');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Gate::authorize('viewAny', Event::class);

        $query = $this->loadRelationships(Event::query());

        return EventResource::collection(
            $query->latest()->paginate()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $event = Event::create([
            ...$request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'start_time' => 'required|date',
                'end_time' => 'required|date|after:start_time'
            ]),
            'user_id' => $request->user()->id
        ]);

        return new EventResource($this->loadRelationships($event));
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        // $event->load('user', 'attendees');
        return new EventResource($this->loadRelationships($event));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {

        // if(Gate::denies('update-event', $event)) {
        //     abort(403, 'Unauthorized action');
        // }

        // Gate::authorize('update-event', $event);

        $event->update(
                $request->validate([
                    'name' => 'sometimes|string|max:255',
                    'description' => 'nullable|string',
                    'start_time' => 'sometimes|date',
                    'end_time' => 'sometimes|date|after:start_time'
            ])
        );

        return new EventResource($this->loadRelationships($event));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        // if(Gate::denies('delete-event', $event)) {
        //     abort(403, 'Unauthorized action');
        // }

        // Gate::authorize('delete-event', $event);

        $event->delete();

        return response(status: 204);

        // return response()->json([
        //     'message' => 'Event deleted successfully'
        // ]);
    }
}