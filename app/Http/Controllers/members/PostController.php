<?php

namespace App\Http\Controllers\members;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Requirement;
use App\Utils\BasicAuth;
use Illuminate\Support\Facades\Log;
use App\Models\Domain;
use App\Models\HistoryPost;
use \Curl\Curl;
use Carbon\Carbon;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $member = BasicAuth::getInstance()->getModel();
        $limit = $request->input('limit', null);
        $search_text = $request->input('search', null);
        $is_main = $request->input('is_main');
        $query = Post::where('member_id', $member->id)
            ->where('is_main', $is_main)
            ->when($search_text, function ($q) use ($search_text) {
                return $q->where('name', 'like', '%' . $search_text . '%')
                    ->orWhere('name', 'like', '%' . $search_text . '%');
            });
        if ($limit) {
            $data = $query->paginate($limit);
        } else {
            $data = $query->get();
        }
        return response()->json($data, 200);
    }

    public function index_one(Request $request, $id) {
        $member = BasicAuth::getInstance()->getModel();
        $data = Post::where('id', $id)->where('member_id', $member->id)->first();
        return response()->json($data, 200);
    }

    public function index_deleted(Request $request) {
        $member = BasicAuth::getInstance()->getModel();
        $from_date = new Carbon($request->input('from_date'));
        $to_date = new Carbon($request->input('to_date'));
        $data = Post::where('member_id', $member->id)
            ->where('is_main', 2)
            ->whereDate('from_date', '<=', $to_date->toDateTimeString())
            ->whereDate('to_date', '>=', $from_date->toDateTimeString())
            ->get();
        return response()->json($data, 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
      
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
            $member = BasicAuth::getInstance()->getModel();
            $requirement_id = $request->input('requirement_id');
            $title = $request->input('title');
            $content = $request->input('content');
            $description = $request->input('description', null);
            $is_main = $request->input('is_main');
            $ob_requirement = Requirement::where('id', $requirement_id)->with('domains')->first();
            $ob_reponse = [];
            $count_success = 0;
            $count_erorr = 0;
            foreach($ob_requirement['domains'] as $item) {
                $ob = new Post();
                $ob->member_id = $member->id;
                $ob->user_id = $member->user_id;
                $ob->requirement_id = $requirement_id;
                $domain_id = $item['id'];
                $ob->domain_id = $domain_id;
                $ob->title = $title;
                $ob->content = $content;
                $ob->category_id = 0;
                $ob->description = $description;
                $ob->is_main = $is_main;
                // create post in website
                if((int)$is_main == 1) {
                    $curl = new Curl();
                    $ob_domain = Domain::where('id', $domain_id)->with('type')->first();
                    //website is wordpress
                    if($ob_domain['type']['id']==1) {
                        //get token from wordpress
                        $data_token = $curl->post($ob_domain['url'].'/wp-json/jwt-auth/v1/token', array(
                            "username" => $ob_domain['username'],
                            "password" => $ob_domain['password']
                        ));
                        //get data from wordpress
                        $curl_ = new Curl();
                        $curl_->setHeader('Content-Type', 'application/json');
                        $curl_->setHeader('Authorization', 'Bearer '.$data_token->token);
                        $data = $curl_->post($ob_domain['url'].'/wp-json/wp/v2/posts', array(
                            "title" => $title,
                            "content" => $content,
                            "status" => "publish"
                        ));
                        if($curl_->error) {
                            // return response()->json([
                            //     'message' => 'error from website'
                            // ], 500);
                            $count_erorr++;
                        }
                        else {
                            $ob->post_website_id = $data->id;
                            $ob->link = $data->link;
                            $ob->save();
                            $ob_reponse = $ob;
                            $count_success++;
                            // return response()->json([
                            //     'message' => 'success',
                            //     'data' => $ob
                            // ], 200);
                        }
                    }
                }
                else if ((int)$is_main == 0) {
                    $ob->save();
                    $ob_reponse = $ob;
                    $count_success++;
                    // return response()->json([
                    //     'message' => 'success',
                    //     'data' => $ob
                    // ], 200);
                }
            }
            return response()->json([
                'list' => $ob_reponse,
                'count_success' => $count_success,
                'count_error' => $count_erorr
            ], 200);
        }
        catch (\Exception $e) {
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
            $member = BasicAuth::getInstance()->getModel();
            $ob = Post::find($id);
            //history post
            $ob_history = new HistoryPost();
            $ob_history->post_id = $ob->id;
            $ob_history->title_old = $ob->title;
            $ob_history->content_old = $ob->content;
            $ob_history->category_old_id = $ob->category_id;
            $ob_history->member_id = $member->id;

            $title = $request->input('title');
            $content = $request->input('content');
            $category_id = $request->input('category_id');

            $ob->member_id = $member->id;
            $ob->title = $title;
            $ob->content = $content;
            $ob->categories = $category_id;
            $ob->description = $request->input('description', null);
            
            // is_main = 1 update to website
            if((int)$ob['is_main'] == 1) {
                $curl = new Curl();
                $ob_domain = Domain::where('id', $domain_id)->with('type')->first();
                //website is wordpress
                if($ob_domain['type']['id']==1) {
                    //get token from wordpress
                    $data_token = $curl->post($ob_domain['url'].'/wp-json/jwt-auth/v1/token', array(
                        "username" => $ob_domain['username'],
                        "password" => $ob_domain['password']
                    ));
                    //get data from wordpress
                    $curl_ = new Curl();
                    $curl_->setHeader('Content-Type', 'application/json');
                    $curl_->setHeader('Authorization', 'Bearer '.$data_token->token);
                    $data = $curl_->put($ob_domain['url'].'/wp-json/wp/v2/posts/'.$ob_domain['post_website_id'], array(
                        "title" => $title,
                        "content" => $content,
                        "categories" => $category_id,
                        "status" => "publish"
                    ));
                    if($curl_->error) {
                        return response()->json([
                            'message' => 'error from website'
                        ], 500);
                    }
                    else {
                        $ob_history->save();
                        $ob->link = $data->link;
                        $ob->save();
                        return response()->json([
                            'message' => 'success',
                            'data' => $ob
                        ], 200);
                    }
                }
            }
            else if((int)$ob['is_main'] == 0) {
                $ob->save();
                return response()->json([
                    'message' => 'success',
                    'data' => $ob
                ], 200);
            }
        }
        catch (\Exception $e) {
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
        
    }
}
