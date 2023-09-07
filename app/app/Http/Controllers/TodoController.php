<?php

namespace App\Http\Controllers;

use App\Models\TodoItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TodoController extends Controller
{
    //TodoControllerの認証を有効にする
    public function __construct()
    {
        $this->middleware('auth');
    }

    //Todo一覧を取得する
    public function index(Request $request)
    {
        //DoneのTodo一覧を作成日順で取得する
        //もし、UDone
        if ($request->has('done')) {
            $todos = TodoItem::where(['user_id' => Auth::id(), 'is_done' => true])
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $todos = TodoItem::where(['user_id' => Auth::id(), 'is_done' => false])
                ->orderBy('created_at', 'desc')
                ->get();
        }
        return view('todo.index', compact('todos'));
    }

    //Todo新規作成画面
    public function create()
    {
        return view('todo.create');
    }

    //Todo新規作成処理
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
        ]);

        // Todoを作成する
        //  user_idは、Auth::id()でログインしているユーザーのIDを取得できる
        TodoItem::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'is_done' => false,
        ]);

        //route()で指定したURLにリダイレクトする
        return redirect()->route('todo.index');
    }

    //Todoの表示
    public function show($id)
    {
        $todo = TodoItem::find($id);

        return view('todo.show', compact('todo'));
    }

    //Todoの編集
    public function edit($id)
    {
        $todo = TodoItem::find($id);

        return view('todo.edit', compact('todo'));
    }

    //Todoの更新
    public function update($id, Request $request)
    {
        $request->validate([
            'title' => 'required|max:255',
        ]);

        $todo = TodoItem::find($id);
        $todo->title = $request->title;
        $todo->save();

        return redirect()->route('todo.index');
    }

    //Todoの削除
    public function destroy($id)
    {
        TodoItem::find($id)->delete();

        return redirect()->route('todo.index');
    }

    //Todoを「完了」にする
    public function done($id)
    {
        //updateメソッドは、指定した項目だけを更新する
        TodoItem::find($id)->update(['is_done' => true]);

        // route()で指定したURLにリダイレクトする
        //  第一引数には、ルーティング名を指定する
        //  第二引数に配列でGetパラメーターを指定する
        return redirect()->route('todo.index', ['done' => true]);
    }

    public function undone($id)
    {

        TodoItem::find($id)->update(['is_done' => false]);

        return redirect()->route('todo.index', ['done' => true]);
    }
}
