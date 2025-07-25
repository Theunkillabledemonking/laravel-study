{{-- resourece/views/partgials/modal.blade.php --}}
<div class="modal">
    <div>{{ $body }}</div>
    <div class="close button etc">...</div>
</div>

<!-- 다른 템플릿 파일 -->
@include('partals.modal', [
    'body' => '<p> 비밀번호가 유효하지 않습니다. 비밀번호는 다음과 같은 형식이여야 합니다. : [...]</p>
    <p><a href="#">....</p>'
])

<!-- 다른 템플릿 파일 -->
@component('partials.modal')
    <p>비밀번호가 유효하지 않습니다. 비밀번호는 다음과 같은 형식이어야 합니다.:</p>
    <p><a href="#">...</a></p>
@endcomponent

{{-- 두개 이상의 스롯을 컴포넌트에 전달 --}}
<div class="modal">
    <div class="modal-header">{{ $title }}</div>
    <div>{{ $slot }}</div>
    <div class="close button etc">///</div>
</div>

@component('partials.modal')
    @slot('title')
        비밀번호 유효 검사 실패.
    @endslot
    <p>비밀번호가 유효하지 않습니다. 비밀번호는 다음과 같은 형식이어야 합니다.</p>

    <p><a href="#"></a></p>
@endcomponent