{{-- resources/views/home.blade.php --}}
<div class="content" data-page-name="{{ $pageName }}">
    <p> 지금 바로 웹사이트에 가입하세요!</p>
    
    @include('sign-up-button', ['text' => 여기를 클릭하세요])
</div>

