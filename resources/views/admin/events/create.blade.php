@extends('layouts.app')

@section('title', 'เพิ่มกิจกรรมใหม่')

@section('content')
    <div class="create-container">
        <h2>เพิ่มกิจกรรมใหม่</h2>

        <form action="{{ route('admin.events.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <label>ชื่อกิจกรรม</label>
            <input type="text" name="title" required>

            <label>รายละเอียด</label>
            <textarea name="detail" rows="4" required></textarea>

            <label>วันที่เริ่ม</label>
            <input type="date" name="start_date" required>

            <label>วันที่สิ้นสุด</label>
            <input type="date" name="end_date" required>

            <label>อัปโหลดรูปภาพ</label>
            <input type="file" name="img_path" accept="image/*">

            <button type="submit" class="btn-save">บันทึก</button>
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

    .create-container {
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
</style>
