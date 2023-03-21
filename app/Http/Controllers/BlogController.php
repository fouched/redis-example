<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class BlogController extends Controller
{
    public function showArticle(int $id)
    {
        $storage = Redis::connection();



        // basic increment
//        $views = $storage->incr('article:' . $id . ':views');
//        // using sorted set: setname, increment, member
//        $storage->zIncrBy('articleViews', 1, 'article:' . $id);

        // using pipeline to send multiple operation in single round trip
        $this->id = $id;
        $storage->pipeline(function ($pipe)
        {
           $pipe->incr('article:' . $this->id . ':views');
           $pipe->zIncrBy('articleViews', 1, 'article:' . $this->id); // sorted set
        });

        $views = $storage->get('article:' . $id . ':views');
        return "This is an article with id: " . $id . " it has " . $views . " views";
    }
}
