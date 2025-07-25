
<h1>{{$group -> title}}</h1>
{{!! $group->heroImageHtml() !!}}

@forelse ($users as $user)
    {{$user->first_name}} {{$user->last_name}}
@empty
    이 그룹에 사용자가 없습니다.
@endforelse

@if (count($talks) === 1)
    1개의 대화 메시지가 있습니다.
@elseif (count($talks) === 0)
    아무런 대화 메시지가 없습니다.
@else
    {{ count($talks) }} 개의 대화 메시지가 있습니다.
@endif

@unless($user->hasPaid())
    결제 탭으로 전환하여 결제를 완료할 수 있습니다.
@endunless

@for ($i =0; $i < $talk->slotsCount(); $i++)
    숫자 {{ $i }}<br>
@endfor

@foreach ($talks as $talk)
    {{ $talk->title }} ({{ $talk->length }} 분)<br>
@endforeach

@while ($item = array_pop($items))
    {{  $item->orSomething() }}<br>
@endwhile

@forelse($talks as $talk)
    {{--  $talks가 비어 있지 않은 경우 아래 코드 실행 --}}
    {{ $talk->title }}({{ $talk->length }}분)<br>
@empty
    {{-- $talks가 빈 경우 아래 메시지 출력 --}}
    확인된 대화 내용이 없습니다.
@endforelse

<ul>
@foreach($pages as $page)
    <li>
        {{ $loop->iteration }}: {{ $page->title }}
        @if ($page->hasChildren())
        <ul>
        @foreach($page->children() as $child)
            <li>{{ $loop->parent->iteration }}
                .{{ $loop->iteration }}:
                {{ $child->title }}
            </li>
        @endforeach
        </ul>
        @endif
    </li>
</ul>
@endforeach

