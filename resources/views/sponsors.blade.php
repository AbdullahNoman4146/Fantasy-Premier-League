<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sponsors</title>
    <style>
      body{
        margin:0;
        font-family: Arial, sans-serif;
        color:white;
        background:url('/images/back_pic.jpeg') no-repeat center center fixed;
        background-size:cover;
      }

      .overlay{
        background:rgba(0,0,0,0.75);
        min-height:100vh;
        padding:0;
      }

      .page{
        padding:30px;
      }

      .container{
        max-width:1100px;
        margin:25px auto;
        background:rgba(0,0,0,0.55);
        border:2px solid rgba(255,255,255,0.25);
        border-radius:12px;
        padding:18px;
      }

      table{
        width:100%;
        border-collapse:collapse;
        margin-top:12px;
      }

      th,td{
        padding:10px;
        border-bottom:1px solid rgba(255,255,255,0.15);
        text-align:left;
      }

      th{
        font-size:13px;
        opacity:.9;
        font-weight:800;
      }

      .badge{
        display:inline-block;
        padding:4px 10px;
        border-radius:999px;
        border:1px solid rgba(255,255,255,0.25);
        font-size:12px;
        opacity:.9;
      }
    </style>
</head>

<body>
@include('partials.navbar')

<div class="overlay">
    <div class="page">
        <div class="container">

            @php
                session()->forget('success');
            @endphp

            <h2 style="margin:0;">Sponsors</h2>
            <p style="opacity:.85; margin-top:6px;">Sponsor table data with team name</p>

            @if(($sponsors ?? collect())->count())
                <table>
                    <thead>
                    <tr style="background:rgba(255,255,255,0.12);">
                        <th>Sponsor ID</th>
                        <th>Sponsor Name</th>
                        <th>Team Name</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($sponsors as $s)
                        <tr>
                            <td><span class="badge">{{ $s->sponsor_id }}</span></td>
                            <td>{{ $s->sponsor_name ?? 'N/A' }}</td>
                            <td>{{ $s->team_name ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @else
                <p style="margin-top:12px;">No sponsors found. Add sponsors from Sponsor Admin.</p>
            @endif
        </div>
    </div>
</div>

</body>
</html>