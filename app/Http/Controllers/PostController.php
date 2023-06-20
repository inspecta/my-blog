<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function showCreatePostForm()
    {
        return view('create-post');
    }

    public function createNewPost(Request $request)
    {
        $incomingPostFields = $request->validate([
            'title' => 'required',
            'body' => 'required',
        ]);

        // Strip off any HTML a malicious user
        // might want to input
        $incomingPostFields['title'] = strip_tags($incomingPostFields['title']);
        $incomingPostFields['body'] = strip_tags($incomingPostFields['body']);

        // Get the ID of the logged in user
        $incomingPostFields['user_id'] = auth()->id();

        $newPost = Post::create($incomingPostFields);

        return redirect("/post/{$newPost->id}")->with('success', 'You have successfully created a new post. ');
    }

    public function viewSinglePost(Post $post)
    {
        // Allow markdowns for the body
        // Use strip_tags() to exclude HTML elements
        // Only include the elements to include in the second argument of strip_tags()
        $post->body = strip_tags(Str::markdown($post->body), '<p><li><ul><br><h1>');
        return view('single-post', ['post' => $post]);
    }

    public function deletePost(Post $post)
    {
        // if (auth()->user()->cannot('delete', $post)) {
        //     return 'You are not allowed to delete this post';
        // }
        $post->delete();
        return redirect('/profile/' . auth()->user()->username)->with('success', 'You have successfully deleted the post.');
    }

    public function showEditPostForm(Post $post)
    {
        return view('edit-post', ['post' => $post]);
    }

    public function updatePost(Post $post, Request $request)
    {
        $incomingUpdateFields = $request->validate([
            'title' => 'required',
            'body' => 'required'
        ]);

        $incomingUpdateFields['title'] = strip_tags($incomingUpdateFields['title']);
        $incomingUpdateFields['body'] = strip_tags($incomingUpdateFields['body']);

        $post->update($incomingUpdateFields);
        return redirect('/post/' . $post->id)->with('success', 'The post has been updated!');
    }
}
