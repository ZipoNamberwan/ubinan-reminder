<?php

namespace App\Http\Controllers;

use App\Http\Resources\SentMessagesResource;
use App\Models\SentMessages;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;

class SentMessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('message/index');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'receiver'     => 'required',
            'type'     => 'required',
            'message'   => 'required',
            'phone_number' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        //create post
        $post = SentMessages::create([
            'receiver' => $request->receiver,
            'type' => $request->type,
            'message' => $request->message,
            'phone_number' => $request->phone_number,
        ]);

        return new SentMessagesResource(true, 'Data Post Berhasil Ditambahkan!', $post);
    }

    public function getData(Request $request)
    {
        if (Auth::user() == null) {
            abort(403);
        }

        $user = User::find(Auth::user()->id);
        if (!($user->hasRole('Admin') | $user->hasRole('PML'))) {
            abort(403);
        }

        $recordsTotal = SentMessages::all()->count();
        if ($user->hasRole('PML')) {
            $recordsTotal = SentMessages::whereIn('receiver_id', $user->getPPLs->pluck('id'))->count();
        }

        $orderColumn = 'created_at';
        $orderDir = 'desc';
        if ($request->order != null) {
            if ($request->order[0]['dir'] == 'asc') {
                $orderDir = 'asc';
            } else {
                $orderDir = 'desc';
            }
            if ($request->order[0]['column'] == '1') {
                $orderColumn = 'receiver';
            } else if ($request->order[0]['column'] == '2') {
                $orderColumn = 'message';
            } else if ($request->order[0]['column'] == '3') {
                $orderColumn = 'created_at';
            }
        }

        $searchkeyword = $request->search['value'];
        $messages = SentMessages::all();
        if ($user->hasRole('PML')) {
            $messages = SentMessages::whereIn('receiver_id', $user->getPPLs->pluck('id'))->get();
        }

        if ($searchkeyword != null) {
            $messages = $messages->filter(function ($q) use (
                $searchkeyword
            ) {
                return Str::contains(strtolower($q->receiver), strtolower($searchkeyword)) ||
                    Str::contains(strtolower($q->message), strtolower($searchkeyword)) ||
                    Str::contains(strtolower($q->phone_number), strtolower($searchkeyword));
            });
        }
        $recordsFiltered = $messages->count();

        if ($orderDir == 'asc') {
            $messages = $messages->sortBy($orderColumn);
        } else {
            $messages = $messages->sortByDesc($orderColumn);
        }

        if ($request->length != -1) {
            $messages = $messages->skip($request->start)
                ->take($request->length);
        }

        $messagesArray = array();
        $i = $request->start + 1;
        foreach ($messages as $message) {
            $messageData = array();
            $messageData["index"] = $i;
            $messageData["id"] = $message->id;
            $messageData["receiver"] = $message->receiver;
            $messageData["phone_number"] = '+62' . $message->phone_number;
            $messageData["message"] = $message->message;
            $messageData["time"] = $message->created_at != null ? date_format($message->created_at, "d M Y H:i") : null;
            $messagesArray[] = $messageData;
            $i++;
        }
        return json_encode([
            "draw" => $request->draw,
            "recordsTotal" => $recordsTotal,
            "recordsFiltered" => $recordsFiltered,
            "data" => $messagesArray
        ]);
    }
}
