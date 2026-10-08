<?php
use Illuminate\Support\Facades\Route;
use App\Models\Bab;
use App\Models\Materi;
use App\Models\Video;
use App\Models\Referensi;
use Illuminate\Http\Request;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('home', ['babs'=>Bab::with(['materi.videos','materi.referensi'])->orderBy('urutan')->get()]);
})->name('home');


Route::get('/cari', function (Request $request) {
    $q = trim((string) $request->get('q', ''));

    $babs = collect();
    $materis = collect();
    $videos = collect();
    $referensi = collect();

    if ($q !== '') {
        $like = "%{$q}%";

        $babs = Bab::where(function ($query) use ($like) {
            $query->where('judul', 'like', $like)
                  ->orWhere('deskripsi', 'like', $like);
        })->orderBy('urutan')->get();

        $materis = Materi::with('bab')
            ->where(function ($query) use ($like) {
                $query->where('judul', 'like', $like)
                      ->orWhere('isi', 'like', $like);
            })
            ->orderBy('bab_id')
            ->orderBy('urutan')
            ->get();

        $videos = Video::with('materi.bab')
            ->where(function ($query) use ($like) {
                $query->where('judul', 'like', $like)
                      ->orWhere('deskripsi', 'like', $like);
            })
            ->orderBy('materi_id')
            ->get();

        $referensi = Referensi::with('materi.bab')
            ->where(function ($query) use ($like) {
                $query->where('judul', 'like', $like)
                      ->orWhere('deskripsi', 'like', $like);
            })
            ->orderBy('materi_id')
            ->get();
    }

    $totalHasil = $babs->count() + $materis->count() + $videos->count() + $referensi->count();

    return view('search', compact('q', 'babs', 'materis', 'videos', 'referensi', 'totalHasil'));
})->name('search');

Route::get('/bab/{bab}', function (Bab $bab) {
    $bab->load('materi');
    return view('bab', compact('bab'));
})->name('bab.show');

Route::get('/materi/{materi}', function (Materi $materi) {
    $materi->load(['bab','videos','referensi']);

    $materisBab = Materi::where('bab_id', $materi->bab_id)
        ->orderBy('urutan')
        ->orderBy('id')
        ->get(['id','bab_id','judul','urutan']);

    $currentIndex = $materisBab->search(fn ($item) => $item->id === $materi->id);
    $previous = $currentIndex !== false && $currentIndex > 0 ? $materisBab[$currentIndex - 1] : null;
    $next = $currentIndex !== false && $currentIndex < $materisBab->count() - 1 ? $materisBab[$currentIndex + 1] : null;
    $position = $currentIndex !== false ? $currentIndex + 1 : null;
    $totalMateriBab = $materisBab->count();

    return view('materi', compact('materi','previous','next','materisBab','position','totalMateriBab'));
})->name('materi.show');

Route::get('/login', [AuthController::class,'showLogin'])->name('login');
Route::post('/login', [AuthController::class,'login'])->name('login.attempt');
Route::post('/logout', [AuthController::class,'logout'])->name('logout');

Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/', [AdminController::class,'index'])->name('index');
    Route::post('/bab', [AdminController::class,'storeBab'])->name('bab.store');
    Route::put('/bab/{bab}', [AdminController::class,'updateBab'])->name('bab.update');
    Route::delete('/bab/{bab}', [AdminController::class,'destroyBab'])->name('bab.destroy');
    Route::post('/materi', [AdminController::class,'storeMateri'])->name('materi.store');
    Route::put('/materi/{materi}', [AdminController::class,'updateMateri'])->name('materi.update');
    Route::delete('/materi/{materi}', [AdminController::class,'destroyMateri'])->name('materi.destroy');
    Route::post('/video', [AdminController::class,'storeVideo'])->name('video.store');
    Route::put('/video/{video}', [AdminController::class,'updateVideo'])->name('video.update');
    Route::delete('/video/{video}', [AdminController::class,'destroyVideo'])->name('video.destroy');
    Route::post('/referensi', [AdminController::class,'storeReferensi'])->name('referensi.store');
    Route::put('/referensi/{referensi}', [AdminController::class,'updateReferensi'])->name('referensi.update');
    Route::delete('/referensi/{referensi}', [AdminController::class,'destroyReferensi'])->name('referensi.destroy');
});
