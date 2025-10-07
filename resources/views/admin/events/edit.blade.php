@extends('layouts.app')

@section('title', 'รายละเอียดล็อก')

@section('content')
    

    {{-- ข้อความสำเร็จ/ผิดพลาด --}}
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-error">
            <ul style="margin-left:18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.events.update', $event) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <label>ชื่อกิจกรรม</label>
        <input type="text" name="title" value="{{ old('title', $event->title) }}" required>

        <label>รายละเอียด</label>
        <textarea name="detail" rows="4" required>{{ old('detail', $event->detail) }}</textarea>

        <label>วันที่เริ่ม</label>
        <input type="date" name="start_date" value="{{ old('start_date', \Carbon\Carbon::parse($event->start_date)->format('Y-m-d')) }}" required>

        <label>วันที่สิ้นสุด</label>
        <input type="date" name="end_date"value="{{ old('end_date', \Carbon\Carbon::parse($event->end_date)->format('Y-m-d')) }}" required>

        <label>อัปโหลดรูปภาพ (ถ้าต้องการเปลี่ยน)</label>
        <input type="file" name="img_path" accept="image/*">

        @if ($event->img_path)
            <img class="thumb" src="{{ asset('storage/' . $event->img_path) }}" alt="current image">
            <div style="font-size:12px;color:#666;">* ถ้าไม่อัปโหลดใหม่ จะใช้รูปเดิม</div>
        @endif

        <button type="submit">บันทึกการแก้ไข</button>
    </form>

    <div style="text-align:center;">
        <a class="btn-back" href="{{ route('admin.events.index') }}">⬅ กลับไปหน้ารายการ</a>
    </div>
@endsection