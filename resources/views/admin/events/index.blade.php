@extends('layouts.app')

@section('title', 'จัดการกิจกรรม')

@section('content')
    <div class="report-container">
        <h2>จัดการกิจกรรม</h2>

        <div class="btn-add-container">
            <a href="{{ route('admin.events.create') }}" class="btn-add">เพิ่มกิจกรรมใหม่</a>
        </div>


        @if(session('success'))
            <p class="alert-success">{{ session('success') }}</p>
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
                            <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-edit">แก้ไข</a>

                            <form action="{{ route('admin.events.destroy', $event) }}" method="POST"
                                onsubmit="return confirm('ยืนยันลบ {{ $event->title }} ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-del">ลบ</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <p style="text-align:center;">ยังไม่มีกิจกรรม</p>
            @endforelse
        </div>
    </div>
@endsection

<style>
    body {
        background: #fffde7;
    }

    .report-container {
        max-width: 900px;
        margin: 20px auto;
        padding: 20px;
        background: #fffbe6;
        border: 2px solid #E68F36;
        border-radius: 12px;
    }

    /* หัวข้อ */
    h2 {
        text-align: center;
        color: #E68F36;
        margin-bottom: 20px;
    }

    /* ปุ่ม */
    .btn-add {
        display: inline-block;
        background: #4CAF50;
        color: white;
        padding: 8px 15px;
        border-radius: 20px;
        align-items: center;
        text-decoration: none;
        font-size: 14px;
        margin-bottom: 15px;
        transition: 0.3s;
    }

    .btn-add-container {
        text-align: center;
        /* จัดเนื้อหาภายใน div ให้อยู่กึ่งกลาง */
        margin-bottom: 15px;
    }


    .btn-add:hover {
        background: #45a049;
    }

    /* Alert */
    .alert-success {
        color: green;
        text-align: center;
        margin: 10px 0;
    }

    /* การ์ดกิจกรรม */
    .event-card {
        width: 100%;
        max-width: 700px;
        background: #fff;
        border-radius: 12px;
        border: 1px solid #ddd;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        margin: 15px auto;
        transition: 0.3s;
    }

    .event-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
    }

    .event-image img {
        width: 100%;
        height: auto;
        display: block;
    }

    .event-body {
        padding: 15px;
    }

    .event-body h3 {
        margin: 0 0 8px;
        color: #E68F36;
    }

    /* ปุ่มแก้ไข/ลบ */
    .actions {
        margin-top: 12px;
        display: flex;
        gap: 8px;
    }

    .btn-edit {
        background: #2196F3;
        color: #fff;
        padding: 6px 12px;
        border-radius: 6px;
        text-decoration: none;
    }

    .btn-del {
        background: #f44336;
        color: #fff;
        border: none;
        padding: 6px 12px;
        border-radius: 6px;
        cursor: pointer;
    }

    .btn-edit:hover {
        background: #1976D2;
    }

    .btn-del:hover {
        background: #d32f2f;
    }
</style>