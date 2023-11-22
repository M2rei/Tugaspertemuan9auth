<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use App\Http\Controllers\Controller;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use App\Models\Post;
use Illuminate\Support\Facades\Validator;

class posttController extends Controller
{
 /**
    * @OA\Get(
    * path="/api/gallery",
    * tags={"gallery"},
    * summary="Tampilkan Gallery",
    * description="Ini adalah dokumentasi untuk menampilkan gallery",
    * operationId="gallery_index",

    * @OA\Response(
    * response="default",
    * description="Proses Berhasil"
    * )
    * )

    * @OA\Post(
    * path="/api/gallery/store",
    * tags={"gallery"},
    * summary="Tambah Gambar",
    * description="Ini adalah dokumentasi untuk menambah gambar pada gallery",
    * operationId="galllery.store",
    * @OA\RequestBody(
     *         required=true,
     *         description="Data untuk mengunggah gambar",
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="title",
     *                     description="Judul Upload",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     description="Deskripsi Gambar",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="picture",
     *                     description="File Gambar",
     *                     type="string",
     *                     format="binary"
     *                 ),
     *             )
     *         )
     *     ),
    * @OA\Response(
    * response="default",
    * description="Proses Berhasil"
    * )
    * )

    */

    public function index()
    {
        //get posts
        $posts = Post::latest()->paginate(5);

        return response()->json([
            'status' => true,
            'message' => 'Data ditemukan',
            'data' => $posts
        ], 200);
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

    public function store(Request $request)
    {
    
        //validate form
        $rules = [
            'image'     => 'required|image|mimetypes:image/jpeg,image/jpg,image/png|max:2048',
            'title'     => 'required|min:5',
            'content'   => 'required|min:10'
        ];

        $validator = Validator::make($request->all(), $rules);

        if($validator->fails()){
            return response()->json([
                'status'=>false,
                'message'=>'Gagal upload gambar',
                'data'=>$validator->errors()
            ]);
        }
    
        //upload image
        $image = $request->file('image');
        $image->storeAs('posts', $image->hashName());
        $thumbnail = $request->file('image')->storeAs('posts/thumbnail', $image->hashName());
        $thumbnailpath = public_path('storage/posts/thumbnail/' . $image->hashName());
        $this->resize($thumbnailpath, 150, 93);

        $squere = $request->file('image')->storeAs('posts/square', $image->hashName());
        $thumbnailpath = public_path('storage/posts/square/' . $image->hashName());
        $this->resize($thumbnailpath, 250, 250);

        //create post
        Post::create([
            'image'     => $image->hashName(),
            'title'     => $request->title,
            'content'   => $request->content,
            'thumbnail' => $image->hashName(),
            'squere' => $image->hashName()
        ]);

        //redirect to index
        return response()->json([
            'status' => true,
            'message' => 'Berhasil Menambahkan Gambar'
        ]);

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
