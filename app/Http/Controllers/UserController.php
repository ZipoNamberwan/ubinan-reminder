<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('user/index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $supervisor = User::role('PML')->get();
        return view('user/create', ['supervisors' => $supervisor]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'role' => 'required',
            'phone_number' => 'required',
            'password' => 'required',
            'supervisor' => 'required_if:role,PPL'
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'supervisor_id' => $request->role == 'PPL' ? $request->supervisor : null,
            'phone_number' => $request->phone_number,
        ]);
        $user->assignRole($request->role);

        return redirect('/users')->with('success-create', 'Pengguna telah ditambah!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::find($id);
        $supervisor = User::role('PML')->get()->except($user->id);
        return view('user/edit', ['supervisors' => $supervisor, 'user' => $user]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required',
            'phone_number' => 'required',
            'password' => 'required',
            'supervisor' => 'required_if:role,PPL'
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password != $user->password ?  bcrypt($request->password) : $user->password,
            'supervisor_id' => $request->role == 'PPL' ? $request->supervisor : null,
            'phone_number' => $request->phone_number,
        ]);
        $user->removeRole($user->roles[0]->name);
        $user->assignRole($request->role);

        return redirect('/users')->with('success-create', 'Pengguna telah diubah!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if ($id == 1) {
            return abort(403);
        }
        User::destroy($id);
        return redirect('/users')->with('success-delete', 'Pengguna telah dihapus!');
    }

    public function getData(Request $request)
    {
        $recordsTotal = User::all()->count();
        $orderColumn = 'name';
        $orderDir = 'desc';
        if ($request->order != null) {
            if ($request->order[0]['dir'] == 'asc') {
                $orderDir = 'asc';
            } else {
                $orderDir = 'desc';
            }
            if ($request->order[0]['column'] == '1') {
                $orderColumn = 'name';
            } else if ($request->order[0]['column'] == '2') {
                $orderColumn = 'email';
            } else if ($request->order[0]['column'] == '3') {
                $orderColumn = 'phone_number';
            }
        }

        $searchkeyword = $request->search['value'];
        $users = User::all();
        if ($searchkeyword != null) {
            $users = $users->filter(function ($q) use (
                $searchkeyword
            ) {
                return Str::contains(strtolower($q->name), strtolower($searchkeyword)) ||
                    Str::contains(strtolower($q->email), strtolower($searchkeyword)) ||
                    Str::contains(strtolower($q->phone_number), strtolower($searchkeyword));
            });
        }
        $recordsFiltered = $users->count();

        if ($orderDir == 'asc') {
            $users = $users->sortBy($orderColumn);
        } else {
            $users = $users->sortByDesc($orderColumn);
        }

        if ($request->length != -1) {
            $users = $users->skip($request->start)
                ->take($request->length);
        }

        $usersArray = array();
        $i = $request->start + 1;
        foreach ($users as $user) {
            $userData = array();
            $userData["index"] = $i;
            $userData["id"] = $user->id;
            $userData["name"] = $user->name;
            $userData["email"] = $user->email;
            $userData["phone_number"] = '+62' . $user->phone_number;
            $userData["role"] = $user->roles->first()->name;
            $userData["supervisor_id"] = $user->getPML != null ? $user->getPML->id : '';
            $userData["supervisor_name"] = $user->getPML != null ? $user->getPML->name : '';
            $usersArray[] = $userData;
            $i++;
        }
        return json_encode([
            "draw" => $request->draw,
            "recordsTotal" => $recordsTotal,
            "recordsFiltered" => $recordsFiltered,
            "data" => $usersArray
        ]);
    }
}
