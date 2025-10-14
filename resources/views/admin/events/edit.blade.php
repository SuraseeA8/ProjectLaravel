@extends('layouts.app')

@section('titel', 'แก้ไขกิจกรรม')

@section('content')
    <div class="edit-container">
        <h2>แก้ไขกิจกรรม</h2>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-error">
                <ul>
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
            <input type="date" name="start_date"
                value="{{ old('start_date', \Carbon\Carbon::parse($event->start_date)->format('Y-m-d')) }}" required>

            <label>วันที่สิ้นสุด</label>
            <input type="date" name="end_date"
                value="{{ old('end_date', \Carbon\Carbon::parse($event->end_date)->format('Y-m-d')) }}" required>

            <label>อัปโหลดรูปภาพ (ถ้าต้องการเปลี่ยน)</label>
            <input type="file" name="img_path" accept="image/*">

            @if ($event->img_path)
                <div class="preview">
                    <img src="{{ asset('storage/' . $event->img_path) }}" alt="current image">
                    <div class="note">* ถ้าไม่อัปโหลดใหม่ จะใช้รูปเดิม</div>
                </div>
            @endif

            <button type="submit" class="btn-save">บันทึกการแก้ไข</button>
        </form>

        <div class="back-link">
            <a href="{{ route('admin.events.index') }}" class="btn-back">⬅ กลับไปหน้ารายการ</a>
        </div>
    </div>
@endsection

<style>
    body {
        background: #fffde7;
    }

    .edit-container {
        max-width: 600px;
        margin: 30px auto;
        padding: 25px;
        background: #fffbe6;
        border: 2px solid #E68F36;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    h2 {
        text-align: center;
        color: #E68F36;
        margin-bottom: 20px;
    }

    label {
        display: block;
        margin-top: 12px;
        font-weight: bold;
        color: #444;
    }

    input[type="text"],
    input[type="date"],
    input[type="file"],
    textarea {
        width: 100%;
        padding: 10px;
        margin-top: 6px;
        border: 1px solid #ccc;
        border-radius: 8px;
        font-size: 14px;
        box-sizing: border-box;
        transition: border 0.2s;
    }

    input:focus,
    textarea:focus {
        border: 1px solid #E68F36;
        outline: none;
    }

    textarea {
        resize: vertical;
    }

    .btn-save {
        display: block;
        margin: 20px auto;
        padding: 10px 18px;
        background: #4CAF50;
        color: #fff;
        border: none;
        border-radius: 20px;
        font-size: 15px;
        cursor: pointer;
        transition: 0.3s;
    }

    .btn-save:hover {
        background: #45a049;
    }

    .back-link {
        text-align: center;
        margin-top: 10px;
    }

    .btn-back {
        color: #E68F36;
        text-decoration: none;
        font-weight: bold;
    }

    .btn-back:hover {
        text-decoration: underline;
    }

    /* alert */
    .alert-success {
        background: #e8f5e9;
        border-left: 5px solid #4CAF50;
        padding: 10px;
        margin-bottom: 15px;
        color: #2e7d32;
        border-radius: 6px;
    }

    .alert-error {
        background: #ffebee;
        border-left: 5px solid #f44336;
        padding: 10px;
        margin-bottom: 15px;
        color: #c62828;
        border-radius: 6px;
    }

    .alert-error ul {
        margin: 0;
        padding-left: 20px;
    }

    /* preview image */
    .preview {
        margin-top: 12px;
        text-align: center;
    }

    .preview img {
        max-width: 200px;
        border-radius: 6px;
        margin-bottom: 6px;
        border: 1px solid #ddd;
    }

    .preview .note {
        font-size: 12px;
        color: #666;
    }
</style>