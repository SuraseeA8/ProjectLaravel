@extends('layouts.app_vendor')

@section('title', 'ยืนยันการจอง & อัปโหลดสลิป')

@section('content')
    <main class="container">

    {{-- สรุป error รวม --}}
    @if ($errors->any())
        <div class="alert err">
        @foreach($errors->all() as $e)
            <div>{{ $e }}</div>
        @endforeach
        </div>
    @endif

    <h2 class="mb-1">ยืนยันการจอง {{ $stall->stall_code }}</h2>
    <p>เดือนที่จอง: {{ \Carbon\Carbon::create($y,$m)->locale('th')->translatedFormat('F Y') }}</p>

        <form method="POST"
                action="{{ route('vendor.stall.checkout.submit', $stall->stall_id) }}"
                enctype="multipart/form-data" class="space-y-2">
            @csrf
            <input type="hidden" name="year"  value="{{ $y }}">
            <input type="hidden" name="month" value="{{ $m }}">

            {{-- ชื่อบัญชีผู้โอน (required) --}}
            <div class="mb-2">
                <label>ชื่อบัญชีผู้โอน <span style="color:red">*</span></label>
                <input type="text" name="acc_name" class="form-control"
                    value="{{ old('acc_name') }}" required>
                @error('acc_name')<small class="text-danger">{{ $message }}</small>@enderror
            </div>

            {{-- ธนาคาร (required) --}}
            <div class="mb-2">
                <label>ธนาคาร <span style="color:red">*</span></label>
                <input type="text" name="bank" class="form-control"
                    placeholder="เช่น กสิกรไทย" value="{{ old('bank') }}" required>
                @error('bank')<small class="text-danger">{{ $message }}</small>@enderror
            </div>

            {{-- วันที่โอน (required) --}}
            <div class="mb-2">
                <label>วันที่โอน <span style="color:red">*</span></label>
                <input type="date" name="payment_date" class="form-control"
                    value="{{ old('payment_date', \Carbon\Carbon::now('Asia/Bangkok')->toDateString()) }}" required>
                @error('payment_date')<small class="text-danger">{{ $message }}</small>@enderror
            </div>

            {{-- จำนวนเงิน (required) --}}
            <div class="mb-2">
                <label>จำนวนเงิน <span style="color:red">*</span></label>
                <input type="number" step="0.01" min="0.01" name="amount" class="form-control"
                    placeholder="เช่น 7000.00" value="{{ old('amount') }}" required>
                @error('amount')<small class="text-danger">{{ $message }}</small>@enderror
            </div>

            {{-- แนบสลิป (required) --}}
            <div class="mb-3">
                <label>แนบสลิปโอน <span style="color:red">*</span></label>
                <input type="file" name="slip" accept=".jpg,.jpeg,.png,.pdf" required class="form-control">
                <small class="text-muted">รองรับ: JPG, PNG, PDF ขนาดไม่เกิน 4MB</small>
                @error('slip')<br><small class="text-danger">{{ $message }}</small>@enderror
            </div>

            <button type="submit" class="btn btn-primary">ยืนยันการจอง & อัปโหลดสลิป</button>
            <a href="{{ route('vendor.stall.detail', $stall->stall_id) }}?year={{ $y }}&month={{ $m }}" class="btn ms-2">
                ← กลับไปหน้ารายละเอียด
            </a>
        </form>

    </main>
@endsection
