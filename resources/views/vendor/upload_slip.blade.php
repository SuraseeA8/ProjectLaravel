@extends('layouts.app_vendor')

@section('title', 'ยืนยันการจอง & อัปโหลดสลิป')

@section('content')
    <main class="container">
    @if ($errors->any())
        <div class="alert err">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
    @endif

    <h2>ยืนยันการจอง {{ $stall->stall_code }}</h2>
    <p>เดือนที่จอง: {{ \Carbon\Carbon::create($y,$m)->locale('th')->translatedFormat('F Y') }}</p>

    <form method="POST"
            action="{{ route('vendor.stall.checkout.submit', $stall->stall_id) }}"
            enctype="multipart/form-data" class="space-y-2">
        @csrf
        <input type="hidden" name="year"  value="{{ $y }}">
        <input type="hidden" name="month" value="{{ $m }}">

        <div>
        <label>จำนวนเงิน (ถ้ามี):</label>
        <input type="number" step="0.01" name="amount" class="form-control" placeholder="เช่น 7000.00">
        </div>

        <div>
        <label>แนบสลิปโอน <span style="color:red">*</span></label>
        <input type="file" name="slip" accept=".jpg,.jpeg,.png,.pdf" required class="form-control">
        <small class="text-muted">รองรับ: JPG, PNG, PDF ขนาดไม่เกิน 4MB</small>
        </div>

        <button type="submit" class="btn btn-primary">ยืนยันการจอง & อัปโหลดสลิป</button>
    </form>

    <a href="{{ route('vendor.stall.detail', $stall->stall_id) }}?year={{ $y }}&month={{ $m }}" class="btn mt-3">
        ← กลับไปหน้ารายละเอียด
    </a>
    </main>
@endsection
