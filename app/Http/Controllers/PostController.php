<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

// risize image
use Intervention\Image\Facades\Image;

//return type View
use Illuminate\View\View;

//return type redirectResponse
use Illuminate\Http\RedirectResponse;

//import Facade "Storage"
use Illuminate\Support\Facades\Storage;

use App\Models\Post;
use GuzzleHttp\Client;

class PostController extends Controller
{

    /**
     * index
     *
     * @return View
     */
    public function index(): View
    {
        $client = new Client();
        $url =  "http://localhost:8000/api/gallery";
        $response = $client->request('GET', $url);
        $content = $response->getBody()->getContents();
        $content_array = json_decode($content, true);
        $posts = $content_array['data']['data'];
        return view('posts.index', compact('posts'));
    }
    public function dashboard2()
    {
        return view('dashboard');
    }



    /**
     * create
     *
     * @return View
     */
    public function create(): View
    {
        return view('posts.create');
    }

    /**
     * store
     *
     * @param  mixed $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        // //validate form
        // this->validate($request, [
        //     'image'     => 'required|image|mimetypes:image/jpeg,image/jpg,image/png|max:2048',
        //     'title'     => 'required|min:5',
        //     'content'   => 'required|min:10'
        // ]);

        //upload image
        // $image = $request->file('image');
        // $image->storeAs('posts', $image->hashName());
        // $thumbnail = $request->file('image')->storeAs('posts/thumbnail', $image->hashName());
        // $thumbnailpath = public_path('storage/posts/thumbnail/' . $image->hashName());
        // $this->resize($thumbnailpath, 150, 93);

        // $squere = $request->file('image')->storeAs('posts/square', $image->hashName());
        // $thumbnailpath = public_path('storage/posts/square/' . $image->hashName());
        // $this->resize($thumbnailpath, 250, 250);

        //create post
        // Post::create([
        //     'image'     => $image->hashName(),
        //     'title'     => $request->title,
        //     'content'   => $request->content,
        //     'thumbnail' => $image->hashName(),
        //     'squere' => $image->hashName()
        // ]);
        $title = $request->input('title');
        $description = $request->input('content');

        $picture = $request->file('image');
        // dd($title,$description,$picture );

        $client = new Client();
        $url = "http://localhost:8000/api/gallery/store";
        $response = $client->request('POST', $url, [
            'multipart' => [
                [
                    'name' => 'title',
                    'contents' => $title,
                ],
                [
                    'name' => 'content',
                    'contents' => $description,
                ],
                [
                    'name' => 'image',
                    'contents' => fopen($picture->getPathname(), 'r'),
                    'filename' => $picture->getClientOriginalName(),
                ],
            ],
        ]);

        // $content = $response->getBody()->getContents();
        // $content_array = json_decode($content, true);
        // $galleries = $content_array;

        // dd($content_array);
        return redirect()->route('posts.index')->with(['success' => 'Data Berhasil Disimpan!']);
    }

    /**
     * show
     *
     * @param  mixed $id
     * @return View
     */
    public function show(string $id): View
    {
        //get post by ID
        $post = Post::findOrFail($id);

        //render view with post
        return view('posts.show', compact('post'));
    }

    /**
     * edit
     *
     * @param  mixed $id
     * @return void
     */
    public function edit(int $id): View
    {
        //get post by ID
        $post = Post::findOrFail($id);

        //render view with post
        return view('posts.edit', compact('post'));
    }

    /**
     * update
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return RedirectResponse
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        //validate form
        $this->validate($request, [
            'image'     => 'image|mimes:jpeg,jpg,png|max:2048',
            'title'     => 'required|min:5',
            'content'   => 'required|min:10'
        ]);

        //get post by ID
        $post = Post::findOrFail($id);

        //check if image is uploaded
        if ($request->hasFile('image')) {

            //delete old image
            Storage::delete('public/posts/' . $post->image);

            //upload new image
            $image = $request->file('image');
            $image->storeAs('public/posts', $image->hashName());

            $thumbnail = $request->file('image')->storeAs('posts/thumbnail', $image->hashName());
            $thumbnailpath = public_path('storage/posts/thumbnail/' . $image->hashName());
            $this->resize($thumbnailpath, 150, 93);

            $squere = $request->file('image')->storeAs('posts/square', $image->hashName());
            $thumbnailpath = public_path('storage/posts/square/' . $image->hashName());
            $this->resize($thumbnailpath, 250, 250);

            //update post with new image
            $post->update([
                'image'     => $image->hashName(),
                'title'     => $request->title,
                'content'   => $request->content,
                'thumbnail' => $image->hashName(),
                'square' => $image->hashName()
            ]);
        } else {

            //update post without image
            $post->update([
                'title'     => $request->title,
                'content'   => $request->content
            ]);
        }

        //redirect to index
        return redirect()->route('posts.index')->with(['success' => 'Data Berhasil Diubah!']);
    }

    /**
     * destroy
     *
     * @param  mixed $post
     * @return void
     */
    public function destroy(string $id): RedirectResponse
    {
        //get post by ID
        $post = Post::findOrFail($id);

        //delete image
        Storage::delete('public/posts/' . $post->image);

        //delete post
        $post->delete();

        //redirect to index
        return redirect()->route('posts.index')->with(['success' => 'Data Berhasil Dihapus!']);
    }
    public function resize($path, $lebar, $tinggi)
    {
        $image = Image::make($path)->resize($lebar, $tinggi, function ($contrain) {
            $contrain->aspectRatio();
        });
        $image->save($path);
    }
}
