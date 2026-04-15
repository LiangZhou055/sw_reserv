<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 | Page Not Found</title>
    <style>
        :root {
            --bg: #0f172a;
            --bg-soft: #111c34;
            --card: rgba(15, 23, 42, 0.72);
            --line: rgba(148, 163, 184, 0.22);
            --text: #e2e8f0;
            --muted: #94a3b8;
            --primary: #3b82f6;
            --primary-hover: #2563eb;
            --ghost-bg: rgba(148, 163, 184, 0.08);
            --ghost-hover: rgba(148, 163, 184, 0.16);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Segoe UI", "Noto Sans", "Microsoft YaHei", sans-serif;
            color: var(--text);
            background:
                radial-gradient(900px 500px at 85% -10%, rgba(59, 130, 246, 0.3), transparent 60%),
                radial-gradient(700px 420px at 0% 100%, rgba(14, 165, 233, 0.18), transparent 65%),
                linear-gradient(180deg, var(--bg-soft), var(--bg));
            display: grid;
            place-items: center;
            padding: 24px;
        }

        .card {
            width: 100%;
            max-width: 780px;
            border: 1px solid var(--line);
            background: var(--card);
            border-radius: 20px;
            box-shadow: 0 22px 60px rgba(2, 6, 23, 0.35);
            backdrop-filter: blur(8px);
            padding: 28px;
        }

        .code {
            margin: 0;
            font-size: clamp(44px, 10vw, 88px);
            line-height: 0.95;
            font-weight: 800;
            letter-spacing: -0.04em;
            color: #fff;
        }

        .title {
            margin: 10px 0 0;
            font-size: clamp(20px, 3.8vw, 30px);
            line-height: 1.2;
            font-weight: 700;
        }

        .desc {
            margin: 12px 0 0;
            color: var(--muted);
            line-height: 1.65;
            font-size: 15px;
            max-width: 60ch;
        }

    </style>
</head>
<body>
    <section class="card">
        <p class="code">404</p>
        <h1 class="title">Page not found</h1>
        <p class="desc">
            The page you requested does not exist, or the URL is no longer valid.
            Please check the address and try again.
        </p>
    </section>
</body>
</html>
