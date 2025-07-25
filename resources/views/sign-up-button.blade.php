{{-- resources/views/sign-up-button.blade.php --}}
<a class="button buton--callout" data-page-name="{{ $pageName }}">
    <i class="exclamation-icon"></i>{{ $text }}
</a>

{{-- 해당 템플릿 파일이 있다면 포함하기 --}}
@includeIf('sidebars.admin', ['some' => 'data'])
{{-- 전달된 조건 값이 참이 경우에 해당 템플릿 파일을 포함하기 --}}
@includeWhen($user->isAdmin(), 'sidebars.admin', ['some' => 'data'])
{{-- 주어진 템플릿 배열 값에서 템플릿 파일이 존재하는지 확인하고 존재하는 첫 번째 템플릿을 포함 --}}
@includeFirst(['customs.header', 'header'], ['some' => 'data'])