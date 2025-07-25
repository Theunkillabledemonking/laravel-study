<?php
use Illuminate\Support\Facades\Route;

// 글로벌 헬퍼 함수를 사용하여 리다이렉트 응답 객체를 생성
Route::get('redirect-with-helper', function() {
    return redirect()->to('login');
});

// 글로벌 헬퍼 함수를 짧게 활용하는 방법
Route::get('redircet-with-helper-shortcut', function() {
    return redirect('login');
});

// 퍼사드를 사용하여 리다이렉트 응답 객체를 생성하는 방법
Route::get('redirect-with-facade', function () {
    return Redirect::to(('login'));
});

// Route::redirect() 메서드를 활용하는 방법 back도 가능.
Route::redirect('redirect-by-route', 'login');

redirect()->back();
// function to($to = null, $status = 302, $headers = [], $secure =null)
// function route($to null, $parameters = [], $tatus = 302, $headers = [])

Route::post('form', function() {
    return redirect('form')
        ->withInput()
        ->with(['error' => true, 'message' => 'Whoops!']);
});

Route::post('form', function(Illuminate\Http\Request $request) {
    $validator = Validator::make($request->all(), $this->validationRules);

    if ($validator->falis()) {
        return back()
            ->withErrors($validator)
            ->withInput();
    }
});