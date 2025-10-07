@extends('layouts.app')

@section('title', 'รายละเอียดล็อก')

@section('content')
    <h2>จัดการกิจกรรม</h2>

    <a href="{{ route('admin.events.create') }}" class="btn-add">➕ เพิ่มกิจกรรมใหม่</a>

    @if(session('success'))
        <p style="color: green; text-align:center;">{{ session('success') }}</p>
    @endif

    <div class="event-grid">
        @forelse($events as $event)
            <div class="event-card">
                <div class="event-image">
                    @if($event->img_path)
                        <img src="{{ asset('storage/' . $event->img_path) }}" alt="{{ $event->title }}">
                    @else
                        <img src="https://via.placeholder.com/400x200?text=No+Image" alt="no image">
                    @endif
                </div>
                <div class="event-body">
                    <h3>{{ $event->title }}</h3>
                    <p>{{ $event->detail }}</p>
                    <p>เริ่ม: {{ \Carbon\Carbon::parse($event->start_date)->format('d/m/Y') }}</p>
                    <p>สิ้นสุด: {{ \Carbon\Carbon::parse($event->end_date)->format('d/m/Y') }}</p>

                    <div class="actions">
                        {{-- แก้ไข --}}
                        <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-edit">แก้ไข</a>

                        {{-- ลบ --}}
                        <form action="{{ route('admin.events.destroy', $event) }}" method="POST"onsubmit="return confirm('ยืนยันลบ \"{{ $event->title }}\" ?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-del">ลบ</button>
                    </form>

                    </div>
                </div>

        @empty
            <p style="text-align:center;">ยังไม่มีกิจกรรม</p>
        @endforelse
    </div>
@endsection