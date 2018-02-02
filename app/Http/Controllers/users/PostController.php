<?php

namespace App\Http\Controllers\users;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Utils\BasicAuth;
use Illuminate\Support\Facades\Log;
use App\Models\Post;
use App\Models\Domain;
use App\Models\HistoryPost;
use \Curl\Curl;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = BasicAuth::getInstance()->getModel();
        $limit = $request->input('limit', null);
        $search_text = $request->input('search', null);
        $is_main = $request->input('is_main');
        $query = Post::where('user_id', $user->id)
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
            $ob = new Post();
            $ob->member_id = 0;
            $ob->user_id = $user->id;
            $ob->requirement_id = $request->input('requirement_id');
            // $ob_requirement = Requirement::find($request->input('requirement_id'));
            $domain_id = $request->input('domain_id');
            $ob->domain_id = $domain_id;
            $title = $request->input('title');
            $content = $request->input('content');
            $category_id = $request->input('category_id');
            $ob->title = $title;
            $ob->content = $content;
            $ob->category_id = $category_id;
            $ob->description = $request->input('description', null);
            $is_main = $request->input('is_main');
            $ob->is_main = $is_main;
            // create post in website
            if((int)$is_main == 1) {
                $curl = new Curl();
                $ob_domain = Domain::find($domain_id)->with('type');
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
                        "categories" => $category_id,
                        "status" => "publish"
                    ));
                    $ob->post_website_id = $data->id;
                    $ob->link = $data->link;
                    $ob->save();
                }
            }
            else if((int)$is_main == 0) {
                $ob->save();
            }
            return response()->json($ob, 200);
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
            $user = BasicAuth::getInstance()->getModel();
            $ob = Post::find($id);
            //history post
            $ob_history = new HistoryPost();
            $ob_history->post_id = $ob->id;
            $ob_history->title_old = $ob->title;
            $ob_history->content_old = $ob->content;
            $ob_history->category_old_id = $ob->category_id;
            $ob_history->member_id = 0;

            $title = $request->input('title');
            $content = $request->input('content');
            $category_id = $request->input('category_id');

            $ob->member_id = 0;
            $ob->title = $title;
            $ob->content = $content;
            $ob->categories = $category_id;
            $ob->description = $request->input('description', null);
            
            // is_main = 1 update to website
            if((int)$ob['is_main'] == 1) {
                $curl = new Curl();
                $ob_domain = Domain::find($ob['domain_id'])->with('type');
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
                    
                    $ob_history->save();
                    $ob->link = $data->link;
                    $ob->save();
                }
            }
            else if((int)$ob['is_main'] == 0) {
                $ob->save();
            }
            return response()->json($ob, 200);
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
        $ob = Post::find($id);
        $curl = new Curl();
        $ob_domain = Domain::find($ob['domain_id'])->with('type');
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
            $data = $curl_->delete($ob_domain['url'].'/wp-json/wp/v2/posts/'.$ob_domain['post_website_id']);
            
            $ob_history = HistoryPost::where("post_id", $ob['id'])->get();
            foreach($ob_history as $item) {
                $ob_history_delete = HistoryPost::find($item['id']);
                $ob_history_delete->delete();
            }
            $ob->delete();
        }
    }
}
