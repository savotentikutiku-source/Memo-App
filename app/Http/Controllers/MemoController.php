<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Memo;

class MemoController extends Controller
{
    public function index()
    {
        $categories = Category::with([
            'memos' => function ($query) {
                $query->orderBy('sort_order', 'asc');
            }
        ])->orderBy('sort_order', 'asc')->get();

        return view('memos.index', compact('categories'));
    }

    // --- 大分類の保存（ここにも連打防止を追加！） ---
    public function storeCategory(Request $request)
    {
        // 10秒以内に同じ名前のカテゴリが作られていないかチェック
        $exists = Category::where('name', $request->name)
            ->where('created_at', '>=', now()->subSeconds(10))
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', '短時間に同じカテゴリは登録できません。');
        }

        $maxOrder = Category::max('sort_order') ?? 0;
        $category = new Category();
        $category->name = $request->name;
        $category->sort_order = $maxOrder + 1;
        $category->save();

        return redirect('/memo#category-' . $category->id);
    }

    public function deleteCategory($id)
    {
        Category::destroy($id);
        return redirect('/memo')->with('status', 'カテゴリを削除しました');
    }

    public function moveCategory($id, $direction)
    {
        $currentCategory = Category::find($id);
        if ($direction === 'up') {
            $targetCategory = Category::where('sort_order', '<', $currentCategory->sort_order)
                ->orderBy('sort_order', 'desc')->first();
        } else {
            $targetCategory = Category::where('sort_order', '>', $currentCategory->sort_order)
                ->orderBy('sort_order', 'asc')->first();
        }

        if ($targetCategory) {
            $tempOrder = $currentCategory->sort_order;
            $currentCategory->sort_order = $targetCategory->sort_order;
            $targetCategory->sort_order = $tempOrder;
            $currentCategory->save();
            $targetCategory->save();
        } else {
            if ($direction === 'up') $currentCategory->sort_order--;
            else $currentCategory->sort_order++;
            $currentCategory->save();
        }
        return redirect('/memo#category-' . $currentCategory->id);
    }

    // --- 中分類（メモ）の保存（ここも連打防止！） ---
    public function storeMemo(Request $request)
    {
        $exists = Memo::where('category_id', $request->category_id)
            ->where('content', $request->content)
            ->where('created_at', '>=', now()->subSeconds(10))
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', '短時間に同じ内容は登録できません。');
        }

        Memo::where('category_id', $request->category_id)->increment('sort_order');
        $memo = new Memo();
        $memo->category_id = $request->category_id;
        $memo->content = $request->content;
        $memo->sort_order = 0;
        $memo->priority_color = 'white';
        $memo->save();

        return redirect('/memo#category-' . $request->category_id);
    }

    public function checkMemo($id)
    {
        $memo = Memo::find($id);
        $memo->is_checked = !$memo->is_checked;
        $memo->save();
        return redirect('/memo#category-' . $memo->category_id);
    }

    public function deleteMemo($id)
    {
        $memo = Memo::find($id);
        $catId = $memo->category_id;
        $memo->delete();
        return redirect('/memo#category-' . $catId);
    }

    public function updateColor(Request $request, $id)
    {
        $memo = Memo::find($id);
        $memo->priority_color = $request->color;
        $memo->save();
        return redirect('/memo#category-' . $memo->category_id);
    }

    public function moveMemo($id, $direction)
    {
        $currentMemo = Memo::find($id);
        if ($direction === 'up') {
            $targetMemo = Memo::where('category_id', $currentMemo->category_id)
                ->where('sort_order', '<', $currentMemo->sort_order)
                ->orderBy('sort_order', 'desc')->first();
        } else {
            $targetMemo = Memo::where('category_id', $currentMemo->category_id)
                ->where('sort_order', '>', $currentMemo->sort_order)
                ->orderBy('sort_order', 'asc')->first();
        }

        if ($targetMemo) {
            $temp = $currentMemo->sort_order;
            $currentMemo->sort_order = $targetMemo->sort_order;
            $targetMemo->sort_order = $temp;
            $currentMemo->save();
            $targetMemo->save();
        } else {
            if ($direction === 'up') $currentMemo->sort_order--;
            else $currentMemo->sort_order++;
            $currentMemo->save();
        }
        return redirect('/memo#category-' . $currentMemo->category_id);
    }
}