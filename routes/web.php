<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MemoController;

// 1. 一覧表示
Route::get('/', [MemoController::class, 'index']);

// 2. 大分類の保存
Route::post('/categories', [MemoController::class, 'storeCategory']);

// 3. メモ（中分類）の保存
Route::post('/memos', [MemoController::class, 'storeMemo']);

// 4. チェック・削除・色・移動（ここが重要！）
Route::get('/memos/{id}/check', [MemoController::class, 'checkMemo']);
Route::get('/memos/{id}/delete', [MemoController::class, 'deleteMemo']);
Route::get('/memos/{id}/color', [MemoController::class, 'updateColor']);
Route::get('/memos/{id}/move/{direction}', [MemoController::class, 'moveMemo']);

Route::get('/categories/{id}/move/{direction}', [MemoController::class, 'moveCategory']);

Route::get('/categories/{id}/delete', [MemoController::class, 'deleteCategory']);