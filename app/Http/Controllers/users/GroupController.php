<?php

namespace App\Http\Controllers\users;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Group;
use App\Utils\BasicAuth;
use Illuminate\Support\Facades\Log;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = BasicAuth::getInstance()->getModel();
        $limit = $request->input('limit', null);
        $search_text = $request->input('search', null);
        $query = Group::where('user_id', $user->id)
            ->when($search_text, function ($q) use ($search_text) {
                return $q->where('name', 'like', '%' . $search_text . '%');
            });
        if ($limit) {
            $data = $query->paginate($limit);
        } else {
            $data = $query->get();
        }
        return response()->json($data, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $user = BasicAuth::getInstance()->getModel();
            $ob = new Group();
            $ob->user_id = $user->id;
            $ob->name = $request->input('name');
            $ob->description = $request->input('description', null);
            $ob->save();
            return response()->json($ob, 200);
        } catch(\Exception $e) {
            Log::error($e);
            return response()->json(['message' => 'error'], 500);
        }
        
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
        //
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
        try {
            $ob = Group::find($id);
            $ob->name = $request->input('name');
            $ob->description = $request->input('description', null);
            $ob->save();
            return response()->json($ob, 200);
        } catch(\Exception $e) {
            Log::error($e);
            return response()->json(['message' => 'error'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $ob = Group::find($id);
            $ob->delete();
            return response()->json($ob, 200);
        } catch(\Exception $e) {
            Log::error($e);
            return response()->json(['message' => 'error'], 500);
        }
    }
}
