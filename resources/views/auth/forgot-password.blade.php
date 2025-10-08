<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Forgot Password</title>
    @vite('resources/css/app.css')
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --brand: #e68a2e;
            --brand-2: #cc7725;
            --bg-soft: #fff5c4;
            --text: #111111;
            --muted: #666666;
            --card-border: #f4d5a8;
        }

        /* ===== Base & Layout ===== */
        html,
        body {
            height: 100%;
        }

        body {
            margin: 0;
            font-family: 'Kanit', system-ui, -apple-system, Segoe UI, Roboto, sans-serif;
            color: var(--text);
            background: linear-gradient(135deg, var(--brand) 0%, var(--bg-soft) 100%);
            background-attachment: fixed;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card {
            width: 100%;
            max-width: 520px;
            background: rgba(255, 255, 255, .95);
            border: 1px solid var(--card-border);
            border-radius: 18px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, .12);
            backdrop-filter: blur(2px);
            padding: 28px;
            animation: floatIn .8s ease-out;
        }

        @keyframes floatIn {
            0% {
                opacity: 0;
                transform: translateY(30px) scale(.96)
            }

            100% {
                opacity: 1;
                transform: translateY(0) scale(1)
            }
        }

        /* ===== Typography ===== */
        .brand-title {
            margin: 0 0 4px 0;
            font-size: 1.5rem;
            /* ~ text-2xl */
            font-weight: 800;
            color: var(--brand);
        }

        .page-title {
            margin: 0 0 12px 0;
            font-size: 1.125rem;
            /* ~ text-lg */
            font-weight: 700;
            color: #1f2937;
            /* gray-800 */
        }

        .lead {
            margin: 0 0 24px 0;
            font-size: .9rem;
            line-height: 1.7;
            color: var(--muted);
        }

        /* ===== Form ===== */
        .form-group {
            margin-bottom: 16px;
        }

        .input {
            width: 100%;
            height: 42px;
            padding: 0 16px;
            border: 1px solid #d1d5db;
            /* gray-300 */
            border-radius: 10px;
            background: #ffffff;
            color: var(--text);
            transition: box-shadow .2s ease, border-color .2s ease;
            outline: none;
        }

        .input::placeholder {
            color: #9ca3af;
        }

        .input:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 3px rgba(230, 138, 46, .25);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 44px;
            padding: 0 16px;
            border: 1px solid rgba(0, 0, 0, 0.04);
            border-radius: 10px;
            background: var(--brand);
            color: #111;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .15);
            cursor: pointer;
            transition: background .2s ease, box-shadow .2s ease, transform .05s ease;
        }

        .btn:hover {
            background: var(--brand-2);
        }

        .btn:focus {
            box-shadow: 0 0 0 3px rgba(230, 138, 46, .35);
            outline: 2px solid transparent;
        }

        .btn:active {
            transform: translateY(1px);
        }

        .btn:disabled {
            opacity: .6;
            cursor: not-allowed;
        }

        .footer-link {
            display: inline-block;
            margin-top: 20px;
            font-size: .9rem;
            color: var(--brand);
            text-decoration: none;
        }

        .footer-link:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="card">
        <h1 class="brand-title">Market Booking</h1>
        <h2 class="page-title">Forgot Password</h2>

        <p class="lead">
            Enter your email and we’ll send you a link to reset your password.
        </p>

        <form method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="form-group">
                <input class="input" type="email" name="email" placeholder="Enter your email" required />
            </div>
            <button type="submit" class="btn">Send Reset Link</button>
        </form>

        <a href="{{ route('login') }}" class="footer-link">← Back to Login</a>
    </div>
</body>

</html>
