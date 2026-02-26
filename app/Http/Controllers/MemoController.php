<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Memo;

class MemoController extends Controller
{
    public function index()
    {
        // カテゴリとメモをそれぞれの並び順(sort_order)に従って取得
        $categories = Category::with([
            'memos' => function ($query) {
                $query->orderBy('sort_order', 'asc');
            }
        ])->orderBy('sort_order', 'asc')->get();

        return view('memos.index', compact('categories'));
    }

    // 大分類の保存（確実に一番下へ）
    public function storeCategory(Request $request)
    {
        $maxOrder = Category::max('sort_order') ?? 0;

        $category = new Category();
        $category->name = $request->name;
        $category->sort_order = $maxOrder + 1;
        $category->save(); // これで確実に保存されます

        return redirect('/#category-' . $category->id);
    }

    public function deleteCategory($id)
    {
        Category::destroy($id);
        return redirect('/')->with('status', 'カテゴリを削除しました');
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
            // 相手が見つかったら入れ替える
            $tempOrder = $currentCategory->sort_order;
            $currentCategory->sort_order = $targetCategory->sort_order;
            $targetCategory->sort_order = $tempOrder;
            $currentCategory->save();
            $targetCategory->save();
        } else {
            // 相手が見つからない（番号が重複している）場合の強制移動
            if ($direction === 'up') {
                $currentCategory->sort_order--;
            } else {
                $currentCategory->sort_order++;
            }
            $currentCategory->save();
        }
        return redirect('/#category-' . $currentCategory->id);
    }

    // 中分類の保存（確実に一番上へ）
    public function storeMemo(Request $request)
    {

        // --- ここから追加：二重登録防止チェック ---
        $exists = Memo::where('category_id', $request->category_id)
            ->where('content', $request->content)
            ->where('created_at', '>=', now()->subSeconds(10)) // 10秒以内の重複をチェック
            ->exists();

        if ($exists) {
            return redirect()->back()->with('error', '短時間に同じ内容は登録できません。');
        }
        // 既存のメモを一つずつ押し下げる
        Memo::where('category_id', $request->category_id)->increment('sort_order');

        $memo = new Memo();
        $memo->category_id = $request->category_id;
        $memo->content = $request->content;
        $memo->sort_order = 0;
        $memo->priority_color = 'white';
        $memo->save(); // これで確実に保存されます

        return redirect('/#category-' . $request->category_id);
    }

    public function checkMemo($id)
    {
        $memo = Memo::find($id);
        $memo->is_checked = !$memo->is_checked;
        $memo->save();
        return redirect('/#category-' . $memo->category_id);
    }

    public function deleteMemo($id)
    {
        $memo = Memo::find($id);
        $catId = $memo->category_id;
        $memo->delete();
        return redirect('/#category-' . $catId);
    }

    public function updateColor(Request $request, $id)
    {
        $memo = Memo::find($id);
        $memo->priority_color = $request->color;
        $memo->save();
        return redirect('/#category-' . $memo->category_id);
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
            // 強制移動
            if ($direction === 'up')
                $currentMemo->sort_order--;
            else
                $currentMemo->sort_order++;
            $currentMemo->save();
        }
        return redirect('/#category-' . $currentMemo->category_id);
    }
}