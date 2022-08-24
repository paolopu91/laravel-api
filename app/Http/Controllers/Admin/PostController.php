<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
class PostController extends Controller
{

    private function findBySlug($slug){
        $post = Post::where("slug" , $slug)->first();
        if(!$post){
            abort(404);
        }
        return $post;
    }




    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::orderBy("created_at" , "desc")->get();
        return view("admin.posts.index", compact("posts"));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view("admin.posts.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Valido i dati ricevuti
        $validatedData = $request->validate([
            "title"=>"required|min:10",
            "content" => "required|min:10"
        ]);

        //Salvo nel db i dati del nuovo post creato
        $post = new Post();

        $post->fill($validatedData);
        $post->user_id=Auth::user()->id;

        $post->slug= Str::slug($post->title);

        $toReturn = null;
        $counter = 0;
        //controllo se lo slug esiste già nel mio db con un ciclo do-while
        do {
            // generiamo uno slug partendo dal titolo
            $slug = Str::slug($post->title);

            // se il counter é maggiore di 0, concateno il suo valore allo slug
            if ($counter > 0) {
                $slug .= "-" . $counter;
            }

            // controllo a db se esiste già uno slug uguale
            $slug_esiste = Post::where("slug", $slug)->first();

            if ($slug_esiste) {
                // se esiste, incremento il contatore per il ciclo successivo
                $counter++;
            } else {
                // Altrimenti salvo lo slug nei dati del nuovo post
                $toReturn = $slug;
            }
        } while ($slug_esiste);
            $post->save();
        return $toReturn;
        
        //redirect su una pagina desiderata
        return redirect()->route("admin.posts.show" , $post->slug);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        $post = $this->findBySlug($slug);

        return view("admin.posts.show", compact("post"));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($slug)
    {
        
        $post = $this->findBySlug($slug);

        return view("admin.posts.edit", compact("post"));
        
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $slug)
    {
        $validatedData = $request->validate([
            "title"=>"required|min:10",
            "content" => "required|min:10"
        ]);

        $post = $this->findBySlug($slug);

        $post->update($validatedData);
        return redirect()->route("admin.posts.show" , $post->slug);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($slug)
    {
        $post = $this->findBySlug($slug);
        
    }
}
