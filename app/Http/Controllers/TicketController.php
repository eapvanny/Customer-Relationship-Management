<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Ticket;
use Illuminate\Http\Request;
use App\Http\Helpers\AppHelper;
use App\Models\ChatMessage;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class TicketController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view ticket', ['only' => ['index']]);
        $this->middleware('permission:create ticket', ['only' => ['create', 'store']]);
        $this->middleware('permission:update ticket', ['only' => ['update', 'edit']]);
        $this->middleware('permission:delete ticket', ['only' => ['destroy']]);
    }
    public $indexof = 1;
    public function index(Request $request)
    {
        $query = Ticket::with(['department', 'user']);
        $is_filter = false;
        if (auth()->user()->role_id !== AppHelper::USER_SUPER_ADMIN && auth()->user()->role_id !== AppHelper::USER_ADMIN) {
            $query->where('user_id', auth()->id());
        }

        if ($request->has('department_id') && $request->department_id != '') {
            $is_filter = true;
            $query->where('department_id', $request->department_id);
        }

        if ($request->has('status_id') && $request->status_id != '') {
            $is_filter = true;
            $query->where('status_id', $request->status_id);
        }

        if ($request->has('priority_id') && $request->priority_id != '') {
            $is_filter = true;
            $query->where('priority_id', $request->priority_id);
        }
        if ($request->ajax()) {
            $tickets = $query->get();

            return DataTables::of($tickets)
                ->addColumn('id', function ($data) {
                    return $this->indexof++;
                })
                ->addColumn('subject', function ($data) {
                    return $data->subject;
                })
                ->addColumn('department', function ($data) {
                    $language = session('user_lang', 'kh');
                    return $language == 'en' ? $data->department->name_in_latin : $data->department->name;
                })
                ->addColumn('id_card', function ($data) {
                    return __($data->id_card);
                })
                ->addColumn('employee_name', function ($data) {
                    return __($data->employee_name);
                })
                ->addColumn('description', function ($data) {
                    return __($data->description);
                })
                ->addColumn('status', function ($data) {
                    return AppHelper::STATUS[$data->status_id] ?? 'Unknown';
                })
                ->addColumn('priority', function ($data) {
                    return AppHelper::PRIORITY[$data->priority_id] ?? 'Unknown';
                })
                ->addColumn('action', function ($data) {
                    $button = '<div class="change-action-item">';
                    $actions = false;
                    if (auth()->user()->can('show ticket')) {
                        $button .= '<span class="change-action-item"><a href="javascript:void(0);" class="btn btn-primary btn-sm show-ticket" data-id="' . $data->id . '" title="Show" data-bs-toggle="modal"><i class="fa fa-fw fa-eye"></i></a></span>';
                        $actions = true;
                    }
                    if (auth()->user()->can('update ticket')) {
                        $button .= '<a title="Edit" href="' . route('ticket.edit', $data->id) . '" class="btn btn-primary btn-sm"><i class="fa fa-edit"></i></a>';
                        $actions = true;
                    }
                    if (auth()->user()->can('delete ticket')) {
                        $button .= '<a href="' . route('ticket.destroy', $data->id) . '" class="btn btn-danger btn-sm delete" title="Delete"><i class="fa fa-fw fa-trash"></i></a>';
                        $actions = true;
                    }
                    if (!$actions) {
                        $button .= '<span style="font-weight:bold; color:red;">No Action</span>';
                    }
                    $button .= '</div>';
                    return $button;
                })
                ->rawColumns(['photo', 'status', 'action'])
                ->make(true);
        }

        $departments = Department::pluck('name', 'id')->toArray();

        return view('backend.ticket.list', compact('is_filter', 'departments'));
    }

    public function create()
    {

        $departments = Department::pluck('name', 'id');
        $ticket = null;
        return view('backend.ticket.add', compact('departments', 'ticket'));
    }

    public function show($id)
    {
        $ticket = Ticket::find($id);

        if (!$ticket) {
            return response()->json(['error' => 'Ticket not found'], 404);
        }

        return response()->json(['ticket' => $ticket]);
    }

    public function getMessages($ticket_id)
    {
        $lastMessageId = request('lastMessageId', 0);

        $messages = ChatMessage::where('ticket_id', $ticket_id)
            ->where('id', '>', $lastMessageId)
            ->with('user')
            ->orderBy('created_at', 'asc')
            ->get();

        $messages->map(function ($message) {
            $message->user_photo_url = $message->user->photo_url;
            return $message;
        });

        return response()->json($messages);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
            'message' => 'required|string',
        ]);

        $message = ChatMessage::create([
            'ticket_id' => $request->ticket_id,
            'user_id' => Auth::id(),
            'message' => $request->message,
        ]);

        return response()->json($message);
    }




    public function store(Request $request)
    {
        $rules = [
            'department_id' => 'required',
            'subject' => 'required|min:2',
            'description' => 'required',
            'id_card' => 'required|min:3',
            'employee_name' => 'required',
        ];
        $this->validate($request, $rules);

        Ticket::create([
            'user_id' => auth()->id(),
            'department_id' => $request->department_id,
            'id_card' => $request->id_card,
            'employee_name' => $request->employee_name,
            'subject' => $request->subject,
            'status_id' => $request->status_id,
            'priority_id' => $request->priority_id,
            'description' => $request->description
        ]);

        return redirect()->route('ticket.index')->with('success', "Tickets has been created!");
    }
    public function edit($id)
    {
        $ticket = Ticket::find($id);
        $departments = Department::pluck('name', 'id');
        if (!$ticket) {
            return redirect()->route('ticket.index');
        }
        return view(
            'backend.ticket.add',
            compact(
                'ticket',
                'departments'
            )
        );
    }
    public function update(Request $request, $id)
    {
        $ticket = Ticket::find($id);
        $rules = [
            'department_id' => 'required',
            'subject' => 'required|min:2',
            'description' => 'required',
            'id_card' => 'required|min:3',
            'employee_name' => 'required',
        ];
        $this->validate($request, $rules);

        $ticket->update([
            'department_id' => $request->department_id,
            'id_card' => $request->id_card,
            'employee_name' => $request->employee_name,
            'subject' => $request->subject,
            'status_id' => $request->status_id,
            'priority_id' => $request->priority_id,
            'description' => $request->description
        ]);
        return redirect()->route('ticket.index')->with('success', "Department has been updated!");
    }
    public function destroy($id)
    {
        $ticket = Ticket::find($id);
        $ticket->delete();
        return redirect()->back()->with('success', "Ticket has been deleted!");
    }
}
