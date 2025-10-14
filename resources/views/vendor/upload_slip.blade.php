@extends('layouts.app')

@section('title', 'ยืนยันการจอง & อัปโหลดสลิป')

@section('content')
    <main class="container">

        <h2 class="mb-1">ยืนยันการจอง {{ $stall->stall_code }}</h2>
        <p>เดือนที่จอง: {{ \Carbon\Carbon::create($y, $m)->locale('th')->translatedFormat('F Y') }}</p>

        <form method="POST" action="{{ route('vendor.stall.checkout.submit', $stall->stall_id) }}"
            enctype="multipart/form-data" class="space-y-2">
            @csrf
            <input type="hidden" name="year" value="{{ $y }}">
            <input type="hidden" name="month" value="{{ $m }}">

            <div class="mb-2">
                <label>ชื่อบัญชีผู้โอน <span style="color:red">*</span></label>
                <input type="text" name="acc_name" class="form-control" value="{{ old('acc_name') }}" required>
                @error('acc_name')<small class="text-danger">{{ $message }}</small>@enderror
            </div>

            <div class="mb-2">
                <label>ธนาคาร <span style="color:red">*</span></label>
                <input type="text" name="bank" class="form-control" placeholder="เช่น กสิกรไทย" value="{{ old('bank') }}"
                    required>
                @error('bank')<small class="text-danger">{{ $message }}</small>@enderror
            </div>

            <div class="mb-2">
                <label>วันที่โอน <span style="color:red">*</span></label>
                <input type="date" name="payment_date" class="form-control"
                    value="{{ old('payment_date', \Carbon\Carbon::now('Asia/Bangkok')->toDateString()) }}" required>
                @error('payment_date')<small class="text-danger">{{ $message }}</small>@enderror
            </div>

            <div class="mb-2">
                <label>จำนวนเงิน <span style="color:red">*</span></label>
                <input type="number" step="0.01" min="0.01" name="amount" class="form-control" placeholder="เช่น 7000.00"
                    value="{{ old('amount') }}" required>
                @error('amount')<small class="text-danger">{{ $message }}</small>@enderror
            </div>

            <div class="mb-3">
                <label>แนบสลิปโอน <span style="color:red">*</span></label>
                <input type="file" name="slip" accept=".jpg,.jpeg,.png,.pdf" required class="form-control">
                <small class="text-muted">รองรับ: JPG, PNG, PDF ขนาดไม่เกิน 4MB</small>
                @error('slip')<br><small class="text-danger">{{ $message }}</small>@enderror
            </div>

            <div class="form-actions">
                <a href="{{ route('vendor.stall.detail', $stall->stall_id) }}?year={{ $y }}&month={{ $m }}"
                    class="btn back-btn">
                    ← กลับไปหน้ารายละเอียด
                </a>
                <button type="submit" class="btn btn-primary">ยืนยันการจอง</button>
            </div>
        </form>

    </main>
@endsection

<style>
    body {
        background: #fffde7;
        font-family: 'Kanit', sans-serif;
    }

    main.container {
        max-width: 700px;
        margin: 40px auto;
        padding: 25px;
        background: #fff;
        border-radius: 12px;
        border: 2px solid #E68F36;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
    }

    h2.mb-1 {
        text-align: center;
        font-size: 24px;
        font-weight: bold;
        color: #E68F36;
        margin-bottom: 10px;
    }

    main.container>p {
        text-align: center;
        font-size: 15px;
        margin-bottom: 25px;
        color: #555;
    }

    .alert.err {
        background: #ffebee;
        border: 1px solid #f44336;
        color: #b71c1c;
        padding: 12px;
        border-radius: 10px;
        margin-bottom: 20px;
    }

    form label {
        font-weight: 600;
        color: #333;
        display: block;
        margin-bottom: 6px;
    }

    .form-control {
        width: 100%;
        padding: 10px 12px;
        border: 1.5px solid #ddd;
        border-radius: 8px;
        font-size: 14px;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .form-control:focus {
        outline: none;
        border-color: #E68F36;
        box-shadow: 0 0 6px rgba(230, 143, 54, 0.4);
    }

    .text-danger {
        color: #d32f2f;
        font-size: 13px;
    }

    .btn {
        display: inline-block;
        padding: 10px 18px;
        border-radius: 25px;
        font-size: 14px;
        font-weight: 500;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: 0.3s;
    }

    .btn-primary {
        background: #E68F36;
        color: #fff;
    }

    .btn-primary:hover {
        background: #d87c2e;
    }

    .back-btn {
        background: #fff;
        border: 1px solid #ccc;
        color: #333;
    }

    .back-btn:hover {
        background: #f9f9f9;
    }

    .form-actions {
        display: flex;
        justify-content: center;
        gap: 15px;
        margin-top: 20px;
    }
</style>