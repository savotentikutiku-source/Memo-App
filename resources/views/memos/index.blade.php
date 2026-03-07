<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="apple-touch-icon" href="{{ asset('memo-icon.png') }}">
    <meta name="apple-mobile-web-app-title" content="ささっとメモ">
    <title>ささっとメモ</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        html {
            scroll-behavior: auto;
        }

        body {
            background-color: #f2f1eb;
            color: #334155;
        }
    </style>
</head>

<body class="pb-32 font-sans antialiased">

    <div class="fixed top-4 right-4 z-50">
        <a href="#footer"
            class="bg-[#eab308] text-white px-5 py-3 rounded-2xl font-bold shadow-xl shadow-yellow-900/10 flex items-center gap-2 hover:bg-[#ca8a04] transition active:scale-95">
            <span>新規入力</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 13l-7 7-7-7"></path>
            </svg>
        </a>
    </div>

    <div class="max-w-md mx-auto px-4 py-8 relative">
        <h1 class="text-3xl font-black mb-10 tracking-tighter text-slate-900">ささっとメモ</h1>

        @foreach($categories as $category)
            <div class="mb-10" id="category-{{ $category->id }}">
                <div class="flex items-center justify-between mb-3 px-2">
                    <div class="flex items-center gap-1">
                        <h2 class="text-sm font-bold text-slate-500 uppercase tracking-[0.2em] mr-2">{{ $category->name }}
                        </h2>
                        <div class="flex text-slate-300">
                            <a href="/memo/categories/{{ $category->id }}/move/up"
                                class="hover:text-slate-600 px-3 py-2 text-base font-bold active:scale-150 transition">▲</a>
                            <a href="/memo/categories/{{ $category->id }}/move/down"
                                class="hover:text-slate-600 px-3 py-2 text-base font-bold active:scale-150 transition">▼</a>
                        </div>
                    </div>
                    <a href="/memo/categories/{{ $category->id }}/delete"
                        class="p-3 -mr-2 text-slate-200 hover:text-red-400" onclick="return confirm('削除しますか？')">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                            </path>
                        </svg>
                    </a>
                </div>

                <div class="bg-[#fcfcf9] rounded-[2.5rem] shadow-sm border border-slate-200/60 overflow-hidden">
                    <form action="/memo/memos" method="POST" class="border-b border-slate-100 flex bg-white/50">
                        @csrf
                        <input type="hidden" name="category_id" value="{{ $category->id }}">
                        <input type="text" name="content" placeholder="メモを追加..." required
                            class="flex-1 border-none px-6 py-5 text-base bg-transparent rounded-l-[2.5rem] focus:outline-none focus:ring-2 focus:ring-slate-900/10 focus:bg-white/80 transition-all">
                        <button class="px-6 text-yellow-600 font-bold text-base">追加</button>
                    </form>

                    <div class="divide-y divide-slate-100">
                        @foreach($category->memos as $memo)
                            <div class="flex items-center px-4 py-3 transition
                                {{ $memo->priority_color === 'red' ? 'bg-red-200' : '' }}
                                {{ $memo->priority_color === 'yellow' ? 'bg-yellow-200' : '' }}
                                {{ $memo->priority_color === 'white' ? 'bg-transparent' : '' }}">

                                <a href="/memo/memos/{{ $memo->id }}/check" class="flex-1 flex items-center min-w-0 py-2">
                                    <div
                                        class="w-6 h-6 rounded-full border-[1.5px] flex-shrink-0 mr-4 {{ $memo->is_checked ? 'bg-green-500 border-green-500' : 'border-slate-300' }}">
                                        @if($memo->is_checked)
                                            <svg class="w-4 h-4 text-white mx-auto mt-0.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                    d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        @endif
                                    </div>
                                    <span
                                        class="text-base leading-relaxed truncate {{ $memo->is_checked ? 'line-through text-slate-300' : 'text-slate-700' }}">
                                        {{ $memo->content }}
                                    </span>
                                </a>

                                <div class="flex items-center gap-2 ml-1">
                                    <div class="flex gap-2 pr-3 border-r border-slate-200">
                                        <button onclick="updateColor({{ $memo->id }}, 'red')"
                                            class="w-6 h-6 rounded-full bg-red-400 active:scale-90 transition"></button>
                                        <button onclick="updateColor({{ $memo->id }}, 'yellow')"
                                            class="w-6 h-6 rounded-full bg-yellow-400 active:scale-90 transition"></button>
                                        <button onclick="updateColor({{ $memo->id }}, 'white')"
                                            class="w-6 h-6 rounded-full bg-slate-200 active:scale-90 transition"></button>
                                    </div>
                                    <div class="flex flex-col text-slate-300 gap-1">
                                        <a href="/memo/memos/{{ $memo->id }}/move/up"
                                            class="hover:text-slate-600 px-2 py-1 text-sm font-bold active:scale-150 transition">▲</a>
                                        <a href="/memo/memos/{{ $memo->id }}/move/down"
                                            class="hover:text-slate-600 px-2 py-1 text-sm font-bold active:scale-150 transition">▼</a>
                                    </div>
                                    <a href="/memo/memos/{{ $memo->id }}/delete"
                                        class="p-3 -mr-2 text-slate-200 hover:text-red-400 transition"
                                        onclick="return confirm('削除しますか？')">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach

        <div id="footer" class="pt-24 pb-10">
            <div class="bg-white/40 rounded-[2.5rem] p-8 border border-white/60">
                <h2 class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-4 px-1 text-center">新しい分類を作成
                </h2>
                <form action="/categories" method="POST" class="flex flex-col gap-3">
                    @csrf
                    <input type="text" name="name" placeholder="読みたい本、旅行の計画..." required
                        class="w-full border-none bg-white shadow-sm rounded-2xl px-5 py-4 focus:ring-2 focus:ring-yellow-200 outline-none text-base">
                    <button
                        class="w-full bg-slate-800 text-white py-4 rounded-2xl font-bold hover:bg-black transition text-lg">カテゴリを追加する</button>
                </form>
            </div>
            <div class="mt-12 text-center">
                <a href="#" class="text-[10px] font-bold text-slate-400 hover:text-slate-600 tracking-[0.3em] p-4">PAGE
                    TOP ▲</a>
            </div>
        </div>
    </div>

    <script>
        function updateColor(memoId, color) {
            fetch(`/memo/memos/${memoId}/color?color=${color}`, {
                method: 'GET',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            }).then(response => {
                if (response.ok) { window.location.reload(); }
            });
        }

        window.onload = function () {
            if (window.location.hash) {
                const categoryId = window.location.hash.split('-')[1];
                const input = document.querySelector(`#category-${categoryId} input[name="content"]`);
                if (input) {
                    input.focus();
                    input.setSelectionRange(input.value.length, input.value.length);
                }
            }
        };
    </script>
</body>

</html>