@extends('layouts.app')

@section('title', 'รายการกิจกรรม')

@section('content')
    <div class="event-container">
        <h2>รายการกิจกรรม</h2>

        <div class="event-grid">
            @forelse($events as $event)
                <div class="event-card">
                    <div class="event-image">
                        @if($event->img_path)
                            <img src="{{ asset('storage/' . $event->img_path) }}" alt="{{ $event->title }}">
                        @else
                            <img src="https://via.placeholder.com/800x400?text=No+Image" alt="no image">
                        @endif
                    </div>
                    <div class="event-body">
                        <h3>{{ $event->title }}</h3>
                        <p class="event-detail">{{ $event->detail }}</p>
                        <p>เริ่ม: {{ \Carbon\Carbon::parse($event->start_date)->format('d/m/Y') }}</p>
                        <p>สิ้นสุด: {{ \Carbon\Carbon::parse($event->end_date)->format('d/m/Y') }}</p>
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

    .event-container {
        max-width: 1200px;
        margin: 30px auto;
        padding: 20px;
    }

    h2 {
        text-align: center;
        color: #E68F36;
        margin-bottom: 20px;
    }

    .event-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 30px;
    }

    .event-card {
        background: #fff;
        border-radius: 15px;
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
        overflow: hidden;
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .event-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 25px rgba(0, 0, 0, 0.25);
    }

    .event-image img {
        width: 100%;
        height: 220px;
        object-fit: cover;
    }

    .event-body {
        padding: 15px;
        text-align: center;
    }

    .event-body h3 {
        font-size: 22px;
        margin-bottom: 10px;
        color: #E68F36;
    }

    .event-detail {
        color: #555;
        font-size: 15px;
        margin-bottom: 12px;
        line-height: 1.6;
    }

    .event-body p {
        font-size: 14px;
        color: #666;
        margin: 4px 0;
    }
</style>