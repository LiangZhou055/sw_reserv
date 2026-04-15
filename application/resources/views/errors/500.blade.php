<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>500 | Server Error</title>
    <style>
        :root {
            --bg: #0f172a;
            --bg-soft: #111c34;
            --card: rgba(15, 23, 42, 0.72);
            --line: rgba(148, 163, 184, 0.22);
            --text: #e2e8f0;
            --muted: #94a3b8;
            --danger: #ef4444;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: "Segoe UI", "Noto Sans", "Microsoft YaHei", sans-serif;
            color: var(--text);
            background:
                radial-gradient(900px 500px at 85% -10%, rgba(239, 68, 68, 0.28), transparent 60%),
                radial-gradient(760px 460px at 0% 100%, rgba(59, 130, 246, 0.16), transparent 65%),
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

        .badge {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 6px 10px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
            color: #fecaca;
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(248, 113, 113, 0.35);
        }

        .code {
            margin: 14px 0 0;
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
            max-width: 62ch;
        }
    </style>
</head>
<body>
    <section class="card">
        <span class="badge">Server Error</span>
        <p class="code">500</p>
        <h1 class="title">Something went wrong on the server</h1>
        <p class="desc">
            The request reached the server, but an internal error occurred while processing it.
            Please retry in a moment.
        </p>
    </section>
</body>
</html>
