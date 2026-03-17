<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Managers</title>
    <style>
        body{ margin:0; font-family:Arial,sans-serif; color:white; background:url('/images/back_pic.jpeg') no-repeat center center fixed; background-size:cover; }
        .overlay{ background:rgba(0,0,0,0.75); min-height:100vh; padding:0; }
        .page{ padding:30px; }
        .container{ max-width:1100px; margin:25px auto; background:rgba(0,0,0,0.55); border:2px solid rgba(255,255,255,0.25); border-radius:12px; padding:18px; }
        table{ width:100%; border-collapse:collapse; margin-top:12px; }
        th,td{ padding:10px; border-bottom:1px solid rgba(255,255,255,0.15); text-align:left; }
        th{ font-size:13px; opacity:.9; font-weight:800; }
        .badge{ display:inline-block; padding:4px 10px; border-radius:999px; border:1px solid rgba(255,255,255,0.25); font-size:12px; opacity:.9; }
    </style>
</head>
<body>
@include('partials.navbar')
<div class="overlay">
    <div class="page">
        <div class="container">
            <h2 style="margin:0;">Managers</h2>
            <p style="opacity:.85; margin-top:6px;">Current team managers and their details.</p>

            @if(($managers ?? collect())->count())
                <table>
                    <thead>
                        <tr style="background:rgba(255,255,255,0.12);">
                            <th>Manager</th>
                            <th>Team</th>
                            <th>Nationality</th>
                            <th>Experience</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($managers as $manager)
                        @php $fullName = trim(($manager->first_name ?? '') . ' ' . ($manager->last_name ?? '')); @endphp
                        <tr>
                            <td>{{ $fullName !== '' ? $fullName : 'N/A' }}</td>
                            <td>{{ $manager->team_name ?? 'Unassigned' }}</td>
                            <td>{{ $manager->nationality ?? 'N/A' }}</td>
                            <td><span class="badge">{{ $manager->experience_years ?? 0 }} years</span></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @else
                <p style="margin-top:12px;">No managers found yet.</p>
            @endif
        </div>
    </div>
</div>
</body>
</html>