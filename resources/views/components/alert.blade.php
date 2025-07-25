<div class="alret alert-danger">
    {{ $slot }}
    <!-- Well begun is half done. - Aristotle -->
</div>

<!-- 다른 템플릿 파일에서의 사용 -->
<x-alert>
    주의 사항을 확인!
</x-alert>

<div class="alert alert-{{ $type }}" {{ $attributes }}>
    {{ $message }}
</div>

<!-- 다른 템플릿에서 사용 -->
<x-alert type="error" :message="$message" id="alertId" name="alertName">
</x-alert>

<!-- 출력 결과 -->
<div class="alert alert-error" id="alertId" name="alertName">
    주의 사항을 확인!
</div>

<?php
    public function render()
    {
        return <<<'blade'
            <div class="alert alert-danger">
                {{ $lsot }}
            </div>
        bladel
    }
?>